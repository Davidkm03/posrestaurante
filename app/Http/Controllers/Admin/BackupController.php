<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener backups del directorio de backups si existe
        $backups = new Collection();
        
        $backupPath = storage_path('app/backups');
        if (is_dir($backupPath)) {
            $files = glob($backupPath . '/*.{sql,zip,sqlite}', GLOB_BRACE);
            foreach ($files as $file) {
                $size = filesize($file);
                $backups->push((object) [
                    'id' => md5($file),
                    'name' => basename($file),
                    'filename' => basename($file),
                    'path' => $file,
                    'size' => $size,
                    'size_formatted' => $this->formatBytes($size),
                    'type' => str_contains(basename($file), 'auto') ? 'auto' : 'manual',
                    'created_at' => Carbon::createFromTimestamp(filemtime($file)),
                ]);
            }
        }

        $backups = $backups->sortByDesc('created_at');
        
        // Variables adicionales para la vista
        $lastBackup = $backups->first();
        $totalSize = $backups->sum('size');
        $backupCount = $backups->count();

        return view('admin.backups.index', compact('backups', 'lastBackup', 'totalSize', 'backupCount'));
    }
    
    /**
     * Format bytes to human readable format
     */
    protected function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Create a new backup
     */
    public function create(Request $request)
    {
        try {
            // Crear directorio si no existe
            $backupPath = storage_path('app/backups');
            if (!is_dir($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            // Nombre del archivo de backup
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = "backup_manual_{$timestamp}.sql";
            $filepath = "{$backupPath}/{$filename}";

            // Obtener la configuración de la base de datos
            $database = config('database.default');
            $dbConfig = config("database.connections.{$database}");

            if ($database === 'sqlite') {
                // Para SQLite, simplemente copiamos el archivo
                $sqlitePath = $dbConfig['database'];
                if (file_exists($sqlitePath)) {
                    copy($sqlitePath, str_replace('.sql', '.sqlite', $filepath));
                    $filename = str_replace('.sql', '.sqlite', $filename);
                }
            } else {
                // Para MySQL/PostgreSQL, generamos un dump SQL
                $tables = $this->getAllTables($database, $dbConfig);
                $sql = $this->generateSqlDump($tables);
                file_put_contents($filepath, $sql);
            }

            Log::info('Backup creado exitosamente', ['filename' => $filename]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Backup creado exitosamente',
                    'filename' => $filename,
                ]);
            }

            return back()->with('success', 'Backup creado exitosamente: ' . $filename);

        } catch (\Exception $e) {
            Log::error('Error creando backup', ['error' => $e->getMessage()]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el backup: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Error al crear el backup: ' . $e->getMessage());
        }
    }

    /**
     * Get all tables from database
     */
    protected function getAllTables(string $driver, array $config): array
    {
        $tables = [];
        
        if ($driver === 'mysql') {
            $result = DB::select('SHOW TABLES');
            $key = 'Tables_in_' . $config['database'];
            foreach ($result as $row) {
                $tables[] = $row->$key;
            }
        } elseif ($driver === 'pgsql') {
            $result = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
            foreach ($result as $row) {
                $tables[] = $row->tablename;
            }
        } elseif ($driver === 'sqlite') {
            $result = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            foreach ($result as $row) {
                $tables[] = $row->name;
            }
        }

        return $tables;
    }

    /**
     * Generate SQL dump for tables
     */
    protected function generateSqlDump(array $tables): string
    {
        $sql = "-- Backup generado el " . now()->format('Y-m-d H:i:s') . "\n";
        $sql .= "-- POS Restaurante\n\n";

        foreach ($tables as $table) {
            // Obtener estructura de la tabla
            $sql .= "-- Tabla: {$table}\n";
            
            // Obtener datos
            $rows = DB::table($table)->get();
            
            if ($rows->count() > 0) {
                foreach ($rows as $row) {
                    $values = array_map(function ($value) {
                        if (is_null($value)) {
                            return 'NULL';
                        }
                        return "'" . addslashes($value) . "'";
                    }, (array) $row);
                    
                    $columns = implode(', ', array_keys((array) $row));
                    $values = implode(', ', $values);
                    
                    $sql .= "INSERT INTO `{$table}` ({$columns}) VALUES ({$values});\n";
                }
            }
            
            $sql .= "\n";
        }

        return $sql;
    }

    /**
     * Download a backup file
     */
    public function download(string $backup)
    {
        $backupPath = storage_path('app/backups');
        
        // Buscar el archivo por hash ID
        if (is_dir($backupPath)) {
            $files = glob($backupPath . '/*.{sql,zip,sqlite}', GLOB_BRACE);
            foreach ($files as $file) {
                if (md5($file) === $backup) {
                    return response()->download($file);
                }
            }
        }

        return back()->with('error', 'Archivo de backup no encontrado');
    }

    /**
     * Remove the specified backup
     */
    public function destroy(string $backup)
    {
        try {
            $backupPath = storage_path('app/backups');
            
            // Buscar el archivo por hash ID
            if (is_dir($backupPath)) {
                $files = glob($backupPath . '/*.{sql,zip,sqlite}', GLOB_BRACE);
                foreach ($files as $file) {
                    if (md5($file) === $backup) {
                        unlink($file);
                        
                        if (request()->expectsJson()) {
                            return response()->json(['success' => true, 'message' => 'Backup eliminado']);
                        }
                        
                        return back()->with('success', 'Backup eliminado exitosamente');
                    }
                }
            }

            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Archivo no encontrado'], 404);
            }

            return back()->with('error', 'Archivo de backup no encontrado');

        } catch (\Exception $e) {
            Log::error('Error eliminando backup', ['error' => $e->getMessage()]);

            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error al eliminar'], 500);
            }

            return back()->with('error', 'Error al eliminar el backup');
        }
    }

    /**
     * Store a newly created resource in storage (alias for create)
     */
    public function store(Request $request)
    {
        return $this->create($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->download($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Restore the database from a backup
     */
    public function restore(string $backup)
    {
        try {
            $backupPath = storage_path('app/backups');
            $databasePath = database_path('database.sqlite');
            $backupFile = null;
            
            // Buscar el archivo por hash ID
            if (is_dir($backupPath)) {
                $files = glob($backupPath . '/*.{sql,zip,sqlite}', GLOB_BRACE);
                foreach ($files as $file) {
                    if (md5($file) === $backup) {
                        $backupFile = $file;
                        break;
                    }
                }
            }

            if (!$backupFile || !file_exists($backupFile)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Archivo de backup no encontrado'
                ], 404);
            }

            // Crear un backup de seguridad antes de restaurar
            $safetyBackup = $backupPath . '/pre_restore_' . date('Y-m-d_H-i-s') . '.sqlite';
            if (file_exists($databasePath)) {
                copy($databasePath, $safetyBackup);
            }

            // Verificar que es un archivo SQLite válido
            $extension = pathinfo($backupFile, PATHINFO_EXTENSION);
            if ($extension !== 'sqlite') {
                return response()->json([
                    'success' => false, 
                    'message' => 'Solo se pueden restaurar backups .sqlite directamente'
                ], 400);
            }

            // Cerrar todas las conexiones a la base de datos
            \DB::disconnect();

            // Copiar el backup sobre la base de datos actual
            if (!copy($backupFile, $databasePath)) {
                // Si falla, restaurar el backup de seguridad
                if (file_exists($safetyBackup)) {
                    copy($safetyBackup, $databasePath);
                }
                
                return response()->json([
                    'success' => false, 
                    'message' => 'Error al restaurar el backup'
                ], 500);
            }

            // Reconectar a la base de datos
            \DB::reconnect();

            Log::info('Backup restaurado exitosamente', [
                'backup_file' => basename($backupFile),
                'safety_backup' => basename($safetyBackup)
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Backup restaurado exitosamente. Se creó un respaldo de seguridad: ' . basename($safetyBackup)
            ]);

        } catch (\Exception $e) {
            Log::error('Error restaurando backup', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false, 
                'message' => 'Error al restaurar: ' . $e->getMessage()
            ], 500);
        }
    }
}
