<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Pastéis Salgados',
                'description' => 'Pastéis com recheios salgados tradicionais',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Pastéis Doces',
                'description' => 'Pastéis com recheios doces',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Bebidas',
                'description' => 'Refrigerantes, sucos e outras bebidas',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Porções',
                'description' => 'Porções para acompanhamento',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Combos',
                'description' => 'Combinações de pastéis com bebidas',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        DB::table('product_types')->insert($types);
    }
}
