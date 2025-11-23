<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\CashRegister;
use App\Models\Category;
use App\Models\ModifierGroup;
use App\Models\Modifier;
use App\Models\Product;
use App\Models\Zone;
use App\Models\Table;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::first();

        // Crear caja registradora
        CashRegister::create([
            'branch_id' => $branch->id,
            'name' => 'Caja Principal',
            'code' => 'CAJA01',
            'is_active' => true,
        ]);

        // Crear zonas y mesas
        $zones = [
            ['name' => 'Salón Principal', 'color' => '#3B82F6', 'tables' => 10],
            ['name' => 'Terraza', 'color' => '#10B981', 'tables' => 6],
            ['name' => 'Barra', 'color' => '#F59E0B', 'tables' => 4],
            ['name' => 'VIP', 'color' => '#8B5CF6', 'tables' => 3],
        ];

        $tableNumber = 1;
        foreach ($zones as $zoneData) {
            $zone = Zone::create([
                'branch_id' => $branch->id,
                'name' => $zoneData['name'],
                'color' => $zoneData['color'],
                'is_active' => true,
            ]);

            for ($i = 1; $i <= $zoneData['tables']; $i++) {
                Table::create([
                    'zone_id' => $zone->id,
                    'branch_id' => $branch->id,
                    'number' => (string) $tableNumber,
                    'capacity' => rand(2, 6),
                    'status' => 'free',
                    'position_x' => ($i - 1) % 5 * 120,
                    'position_y' => floor(($i - 1) / 5) * 120,
                    'is_active' => true,
                ]);
                $tableNumber++;
            }
        }

        // Crear categorías
        $categories = [
            ['name' => 'Entradas', 'slug' => 'entradas', 'color' => '#EF4444', 'icon' => 'appetizer'],
            ['name' => 'Platos Fuertes', 'slug' => 'platos-fuertes', 'color' => '#F97316', 'icon' => 'dish'],
            ['name' => 'Bebidas', 'slug' => 'bebidas', 'color' => '#3B82F6', 'icon' => 'drink'],
            ['name' => 'Postres', 'slug' => 'postres', 'color' => '#EC4899', 'icon' => 'dessert'],
            ['name' => 'Cervezas', 'slug' => 'cervezas', 'color' => '#F59E0B', 'icon' => 'beer'],
            ['name' => 'Licores', 'slug' => 'licores', 'color' => '#8B5CF6', 'icon' => 'liquor'],
        ];

        foreach ($categories as $index => $cat) {
            Category::create([
                'name' => $cat['name'],
                'slug' => $cat['slug'],
                'color' => $cat['color'],
                'icon' => $cat['icon'],
                'sort_order' => $index,
                'is_active' => true,
                'show_in_pos' => true,
            ]);
        }

        // Crear grupos de modificadores
        $termino = ModifierGroup::create([
            'name' => 'Término de la carne',
            'is_required' => true,
            'min_selections' => 1,
            'max_selections' => 1,
        ]);

        foreach (['Término 1/4', 'Término medio', 'Término 3/4', 'Bien asado'] as $i => $mod) {
            Modifier::create([
                'modifier_group_id' => $termino->id,
                'name' => $mod,
                'price' => 0,
                'is_default' => $i === 1,
                'sort_order' => $i,
            ]);
        }

        $extras = ModifierGroup::create([
            'name' => 'Extras',
            'is_required' => false,
            'min_selections' => 0,
            'max_selections' => 5,
        ]);

        foreach ([
            ['Queso extra', 3000],
            ['Tocineta', 4000],
            ['Huevo frito', 2500],
            ['Aguacate', 3500],
            ['Doble carne', 8000],
        ] as $i => $mod) {
            Modifier::create([
                'modifier_group_id' => $extras->id,
                'name' => $mod[0],
                'price' => $mod[1],
                'sort_order' => $i,
            ]);
        }

        $bebidas = ModifierGroup::create([
            'name' => 'Opciones de bebida',
            'is_required' => false,
            'min_selections' => 0,
            'max_selections' => 1,
        ]);

        foreach ([
            ['Sin hielo', 0],
            ['Menos azúcar', 0],
            ['Sin azúcar', 0],
        ] as $i => $mod) {
            Modifier::create([
                'modifier_group_id' => $bebidas->id,
                'name' => $mod[0],
                'price' => $mod[1],
                'sort_order' => $i,
            ]);
        }

        // Crear productos de ejemplo
        $products = [
            // Entradas
            ['category' => 'entradas', 'name' => 'Patacones con Hogao', 'price' => 12000],
            ['category' => 'entradas', 'name' => 'Empanadas (3 und)', 'price' => 9000],
            ['category' => 'entradas', 'name' => 'Deditos de Queso', 'price' => 15000],
            ['category' => 'entradas', 'name' => 'Chicharrón', 'price' => 18000],

            // Platos fuertes
            ['category' => 'platos-fuertes', 'name' => 'Bandeja Paisa', 'price' => 32000, 'modifiers' => [$termino->id, $extras->id]],
            ['category' => 'platos-fuertes', 'name' => 'Lomo de Cerdo', 'price' => 28000, 'modifiers' => [$termino->id, $extras->id]],
            ['category' => 'platos-fuertes', 'name' => 'Pechuga a la Plancha', 'price' => 25000, 'modifiers' => [$extras->id]],
            ['category' => 'platos-fuertes', 'name' => 'Mojarra Frita', 'price' => 35000],
            ['category' => 'platos-fuertes', 'name' => 'Churrasco', 'price' => 38000, 'modifiers' => [$termino->id, $extras->id]],
            ['category' => 'platos-fuertes', 'name' => 'Costillas BBQ', 'price' => 42000],

            // Bebidas
            ['category' => 'bebidas', 'name' => 'Limonada Natural', 'price' => 6000, 'modifiers' => [$bebidas->id]],
            ['category' => 'bebidas', 'name' => 'Limonada de Coco', 'price' => 8000, 'modifiers' => [$bebidas->id]],
            ['category' => 'bebidas', 'name' => 'Jugo de Maracuyá', 'price' => 7000, 'modifiers' => [$bebidas->id]],
            ['category' => 'bebidas', 'name' => 'Gaseosa Personal', 'price' => 4000],
            ['category' => 'bebidas', 'name' => 'Agua Botella', 'price' => 3000],

            // Postres
            ['category' => 'postres', 'name' => 'Tres Leches', 'price' => 12000],
            ['category' => 'postres', 'name' => 'Brownie con Helado', 'price' => 14000],
            ['category' => 'postres', 'name' => 'Flan de Caramelo', 'price' => 10000],

            // Cervezas
            ['category' => 'cervezas', 'name' => 'Poker', 'price' => 5000, 'tax_type' => '04', 'tax_percentage' => 8],
            ['category' => 'cervezas', 'name' => 'Águila', 'price' => 5000, 'tax_type' => '04', 'tax_percentage' => 8],
            ['category' => 'cervezas', 'name' => 'Club Colombia', 'price' => 7000, 'tax_type' => '04', 'tax_percentage' => 8],
            ['category' => 'cervezas', 'name' => 'Corona', 'price' => 10000, 'tax_type' => '04', 'tax_percentage' => 8],

            // Licores
            ['category' => 'licores', 'name' => 'Aguardiente Botella', 'price' => 45000, 'tax_type' => '04', 'tax_percentage' => 8],
            ['category' => 'licores', 'name' => 'Ron Medellín Botella', 'price' => 55000, 'tax_type' => '04', 'tax_percentage' => 8],
            ['category' => 'licores', 'name' => 'Whisky Old Parr', 'price' => 180000, 'tax_type' => '04', 'tax_percentage' => 8],
        ];

        foreach ($products as $index => $productData) {
            $category = Category::where('slug', $productData['category'])->first();

            $product = Product::create([
                'category_id' => $category->id,
                'name' => $productData['name'],
                'slug' => \Str::slug($productData['name']),
                'price' => $productData['price'],
                'tax_type' => $productData['tax_type'] ?? '01',
                'tax_percentage' => $productData['tax_percentage'] ?? 19,
                'tax_included' => true,
                'is_active' => true,
                'show_in_pos' => true,
                'sort_order' => $index,
            ]);

            if (isset($productData['modifiers'])) {
                foreach ($productData['modifiers'] as $order => $modifierId) {
                    $product->modifierGroups()->attach($modifierId, ['sort_order' => $order]);
                }
            }
        }
    }
}
