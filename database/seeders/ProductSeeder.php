<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            // Pastéis Salgados (ID: 1)
            [
                'name' => 'Pastel de Carne',
                'price' => 8.00,
                'photo' => 'products/pastel-carne.jpg',
                'product_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Pastel de Queijo',
                'price' => 7.50,
                'photo' => 'products/pastel-queijo.jpg',
                'product_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Pastel de Frango',
                'price' => 8.00,
                'photo' => 'products/pastel-frango.jpg',
                'product_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Pastel de Pizza',
                'price' => 8.00,
                'photo' => 'products/pastel-pizza.jpg',
                'product_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Pastel de Calabresa',
                'price' => 8.00,
                'photo' => 'products/pastel-calabresa.jpg',
                'product_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Pastéis Doces (ID: 2)
            [
                'name' => 'Pastel de Chocolate',
                'price' => 9.00,
                'photo' => 'products/pastel-chocolate.jpg',
                'product_type_id' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Pastel Romeu e Julieta',
                'price' => 9.00,
                'photo' => 'products/pastel-romeu-julieta.jpg',
                'product_type_id' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Bebidas (ID: 3)
            [
                'name' => 'Coca-Cola 350ml',
                'price' => 5.00,
                'photo' => 'products/coca-cola-lata.jpg',
                'product_type_id' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Guaraná Antarctica 350ml',
                'price' => 5.00,
                'photo' => 'products/guarana-lata.jpg',
                'product_type_id' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Suco de Laranja Natural',
                'price' => 7.00,
                'photo' => 'products/suco-laranja.jpg',
                'product_type_id' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Caldo de Cana 500ml',
                'price' => 6.00,
                'photo' => 'products/caldo-cana.jpg',
                'product_type_id' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Porções (ID: 4)
            [
                'name' => 'Batata Frita',
                'price' => 15.00,
                'photo' => 'products/batata-frita.jpg',
                'product_type_id' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Mandioca Frita',
                'price' => 15.00,
                'photo' => 'products/mandioca-frita.jpg',
                'product_type_id' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Combos (ID: 5)
            [
                'name' => 'Combo Família',
                'price' => 45.00,
                'photo' => 'products/combo-familia.jpg',
                'product_type_id' => 5,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Combo Individual',
                'price' => 12.00,
                'photo' => 'products/combo-individual.jpg',
                'product_type_id' => 5,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Combo Doce',
                'price' => 25.00,
                'photo' => 'products/combo-doce.jpg',
                'product_type_id' => 5,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('products')->insert($products);
    }
} 