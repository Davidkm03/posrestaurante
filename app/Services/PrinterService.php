<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Invoice;
use App\Models\CashSession;
use Illuminate\Support\Str;

class PrinterService
{
    protected string $printerType;
    protected ?string $printerIp;
    protected ?int $printerPort;
    protected int $paperWidth = 48; // caracteres para 80mm, 32 para 58mm

    // ESC/POS Commands
    const ESC = "\x1B";
    const GS = "\x1D";
    const LF = "\n";

    // Initialize
    const INIT = "\x1B\x40";

    // Text formatting
    const ALIGN_LEFT = "\x1B\x61\x00";
    const ALIGN_CENTER = "\x1B\x61\x01";
    const ALIGN_RIGHT = "\x1B\x61\x02";

    // Text size
    const TEXT_NORMAL = "\x1B\x21\x00";
    const TEXT_BOLD = "\x1B\x21\x08";
    const TEXT_DOUBLE_HEIGHT = "\x1B\x21\x10";
    const TEXT_DOUBLE_WIDTH = "\x1B\x21\x20";
    const TEXT_DOUBLE_SIZE = "\x1B\x21\x30";

    // Cut paper
    const CUT_FULL = "\x1D\x56\x00";
    const CUT_PARTIAL = "\x1D\x56\x01";

    // Feed
    const FEED_LINE = "\x1B\x64\x02";

    // Beep
    const BEEP = "\x1B\x42\x03\x02";

    public function __construct(?string $type = 'escpos', ?string $ip = null, ?int $port = 9100)
    {
        $this->printerType = $type;
        $this->printerIp = $ip;
        $this->printerPort = $port;
    }

    /**
     * Configura el ancho del papel
     */
    public function setPaperWidth(int $mm): self
    {
        $this->paperWidth = $mm === 58 ? 32 : 48;
        return $this;
    }

    /**
     * Genera ticket de venta
     */
    public function generateReceipt(Order $order): string
    {
        $receipt = $this->initPrinter();
        
        // Header - Logo/Nombre del negocio
        $receipt .= $this->centerText($this->doubleSize("RESTAURANTE"));
        $receipt .= $this->centerText("================================");
        
        // Info del negocio
        $branch = $order->branch;
        $receipt .= $this->centerText($branch->name ?? 'Sucursal Principal');
        $receipt .= $this->centerText($branch->address ?? '');
        $receipt .= $this->centerText("NIT: " . ($branch->tax_id ?? ''));
        $receipt .= $this->centerText("Tel: " . ($branch->phone ?? ''));
        
        $receipt .= self::LF;
        $receipt .= $this->line('-');
        
        // Info de la orden
        $receipt .= $this->bold("ORDEN: " . $order->order_number) . self::LF;
        $receipt .= "Fecha: " . $order->created_at->format('d/m/Y H:i') . self::LF;
        
        if ($order->table) {
            $receipt .= "Mesa: " . $order->table->number . self::LF;
        }
        
        if ($order->waiter) {
            $receipt .= "Mesero: " . $order->waiter->name . self::LF;
        }
        
        $receipt .= $this->line('-');
        
        // Items
        $receipt .= $this->bold("PRODUCTOS:") . self::LF;
        
        foreach ($order->items as $item) {
            $receipt .= $this->formatItem(
                $item->quantity . "x " . $item->product->name,
                $this->formatMoney($item->total)
            );
            
            // Modificadores
            if ($item->modifiers && count($item->modifiers) > 0) {
                foreach ($item->modifiers as $mod) {
                    $receipt .= "   + " . $mod['name'] . self::LF;
                }
            }
            
            // Notas
            if ($item->notes) {
                $receipt .= "   > " . $item->notes . self::LF;
            }
        }
        
        $receipt .= $this->line('-');
        
        // Totales
        $receipt .= $this->formatItem("Subtotal:", $this->formatMoney($order->subtotal));
        
        if ($order->discount > 0) {
            $receipt .= $this->formatItem("Descuento:", "-" . $this->formatMoney($order->discount));
        }
        
        $receipt .= $this->formatItem("IVA:", $this->formatMoney($order->tax));
        
        $receipt .= $this->line('=');
        $receipt .= $this->bold($this->formatItem("TOTAL:", $this->formatMoney($order->total)));
        $receipt .= $this->line('=');
        
        // Pagos
        if ($order->payments->count() > 0) {
            $receipt .= self::LF . "PAGOS:" . self::LF;
            foreach ($order->payments as $payment) {
                $receipt .= $this->formatItem(
                    $payment->paymentMethod->name ?? 'Efectivo',
                    $this->formatMoney($payment->amount)
                );
            }
            
            // Cambio si aplica
            $totalPaid = $order->payments->sum('amount');
            if ($totalPaid > $order->total) {
                $receipt .= $this->formatItem("Cambio:", $this->formatMoney($totalPaid - $order->total));
            }
        }
        
        // Footer
        $receipt .= self::LF;
        $receipt .= $this->line('-');
        $receipt .= $this->centerText("¡Gracias por su visita!");
        $receipt .= $this->centerText("Vuelva pronto");
        $receipt .= self::LF;
        $receipt .= $this->centerText($order->created_at->format('d/m/Y H:i:s'));
        
        // Cortar papel
        $receipt .= self::FEED_LINE . self::FEED_LINE;
        $receipt .= self::CUT_PARTIAL;
        
        return $receipt;
    }

    /**
     * Genera comanda para cocina
     */
    public function generateKitchenTicket(Order $order, ?string $destination = null): string
    {
        $ticket = $this->initPrinter();
        
        // Header grande
        $ticket .= self::BEEP; // Alertar
        $ticket .= $this->centerText($this->doubleSize("** COMANDA **"));
        $ticket .= self::LF;
        
        // Info de la orden
        $ticket .= $this->bold($this->doubleSize("Mesa: " . ($order->table->number ?? 'N/A')));
        $ticket .= self::LF;
        $ticket .= $this->bold("Orden: " . $order->order_number) . self::LF;
        $ticket .= "Hora: " . $order->created_at->format('H:i') . self::LF;
        $ticket .= "Mesero: " . ($order->waiter->name ?? 'N/A') . self::LF;
        
        $ticket .= $this->line('=');
        
        // Items (filtrados por destino si aplica)
        foreach ($order->items as $item) {
            if ($destination && $item->product->printer_destination !== $destination) {
                continue;
            }
            
            // Cantidad y nombre en grande
            $ticket .= $this->doubleSize($item->quantity . "x " . Str::upper($item->product->name));
            $ticket .= self::LF;
            
            // Modificadores
            if ($item->modifiers && count($item->modifiers) > 0) {
                foreach ($item->modifiers as $mod) {
                    $ticket .= $this->bold("   + " . $mod['name']) . self::LF;
                }
            }
            
            // Notas importantes
            if ($item->notes) {
                $ticket .= $this->bold($this->doubleHeight("   NOTA: " . $item->notes));
                $ticket .= self::LF;
            }
            
            $ticket .= self::LF;
        }
        
        $ticket .= $this->line('=');
        $ticket .= $this->centerText($order->created_at->format('d/m/Y H:i:s'));
        
        // Cortar
        $ticket .= self::FEED_LINE . self::FEED_LINE;
        $ticket .= self::CUT_PARTIAL;
        
        return $ticket;
    }

    /**
     * Genera ticket de cierre de caja
     */
    public function generateCashCloseTicket(CashSession $session): string
    {
        $ticket = $this->initPrinter();
        
        $ticket .= $this->centerText($this->bold("CIERRE DE CAJA"));
        $ticket .= $this->line('=');
        
        $ticket .= "Caja: " . $session->cashRegister->name . self::LF;
        $ticket .= "Usuario: " . $session->user->name . self::LF;
        $ticket .= self::LF;
        $ticket .= "Apertura: " . $session->opened_at->format('d/m/Y H:i') . self::LF;
        $ticket .= "Cierre: " . ($session->closed_at ? $session->closed_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i')) . self::LF;
        
        $ticket .= $this->line('-');
        
        $ticket .= $this->formatItem("Monto Inicial:", $this->formatMoney($session->opening_amount));
        
        // Ventas por método de pago
        $ticket .= self::LF . $this->bold("VENTAS:") . self::LF;
        
        $salesByMethod = $session->getSalesByPaymentMethod();
        foreach ($salesByMethod as $method => $amount) {
            $ticket .= $this->formatItem("  " . $method . ":", $this->formatMoney($amount));
        }
        
        $ticket .= $this->line('-');
        
        $ticket .= $this->formatItem("Total Ventas:", $this->formatMoney($session->total_sales));
        $ticket .= $this->formatItem("Efectivo Esperado:", $this->formatMoney($session->expected_cash));
        $ticket .= $this->formatItem("Efectivo Contado:", $this->formatMoney($session->closing_amount ?? 0));
        
        $difference = ($session->closing_amount ?? 0) - $session->expected_cash;
        $ticket .= $this->formatItem(
            $difference >= 0 ? "Sobrante:" : "Faltante:",
            $this->formatMoney(abs($difference))
        );
        
        $ticket .= $this->line('=');
        
        if ($session->notes) {
            $ticket .= "Notas: " . $session->notes . self::LF;
        }
        
        $ticket .= self::LF;
        $ticket .= $this->centerText("Firma: _______________");
        $ticket .= self::LF . self::LF;
        
        $ticket .= self::FEED_LINE;
        $ticket .= self::CUT_PARTIAL;
        
        return $ticket;
    }

    /**
     * Envía a impresora de red
     */
    public function printToNetwork(string $data): bool
    {
        if (!$this->printerIp) {
            throw new \Exception('No se ha configurado la IP de la impresora');
        }

        $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        
        if (!$socket) {
            throw new \Exception('No se pudo crear el socket');
        }

        $result = @socket_connect($socket, $this->printerIp, $this->printerPort);
        
        if (!$result) {
            socket_close($socket);
            throw new \Exception('No se pudo conectar a la impresora');
        }

        socket_write($socket, $data, strlen($data));
        socket_close($socket);

        return true;
    }

    /**
     * Retorna los datos para imprimir via JavaScript (WebUSB/WebBluetooth)
     */
    public function getDataForBrowser(string $data): array
    {
        return [
            'raw' => base64_encode($data),
            'type' => 'escpos',
        ];
    }

    // Helper methods

    public function initPrinter(): string
    {
        return self::INIT . self::ALIGN_LEFT . self::TEXT_NORMAL;
    }

    /**
     * Genera un ticket de prueba
     */
    public function generateTestTicket(): string
    {
        $ticket = $this->initPrinter();
        $ticket .= $this->centerText("** PRUEBA DE IMPRESION **");
        $ticket .= $this->centerText(now()->format('d/m/Y H:i:s'));
        $ticket .= self::LF . self::LF . self::LF;
        $ticket .= self::CUT_PARTIAL;
        
        return $ticket;
    }

    public function centerText(string $text): string
    {
        return self::ALIGN_CENTER . $text . self::LF . self::ALIGN_LEFT;
    }

    protected function bold(string $text): string
    {
        return self::TEXT_BOLD . $text . self::TEXT_NORMAL;
    }

    protected function doubleSize(string $text): string
    {
        return self::TEXT_DOUBLE_SIZE . $text . self::TEXT_NORMAL;
    }

    protected function doubleHeight(string $text): string
    {
        return self::TEXT_DOUBLE_HEIGHT . $text . self::TEXT_NORMAL;
    }

    protected function line(string $char = '-'): string
    {
        return str_repeat($char, $this->paperWidth) . self::LF;
    }

    protected function formatItem(string $left, string $right): string
    {
        $maxLeft = $this->paperWidth - strlen($right) - 1;
        $leftTruncated = mb_substr($left, 0, $maxLeft);
        $padding = $this->paperWidth - mb_strlen($leftTruncated) - strlen($right);
        
        return $leftTruncated . str_repeat(' ', max(1, $padding)) . $right . self::LF;
    }

    protected function formatMoney(float|int|null $amount): string
    {
        return '$' . number_format((float)($amount ?? 0), 0, ',', '.');
    }
}
