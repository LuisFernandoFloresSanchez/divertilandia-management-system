<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ToyTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $toyTypes = [
            [
                'name' => 'Inflables',
                'description' => 'Juguetes inflables como brincolines, toboganes y castillos',
                'is_active' => true,
            ],
            [
                'name' => 'Juegos de Mesa',
                'description' => 'Juegos de mesa para entretenimiento en eventos',
                'is_active' => true,
            ],
            [
                'name' => 'Juguetes de Exterior',
                'description' => 'Juguetes y equipos para actividades al aire libre',
                'is_active' => true,
            ],
            [
                'name' => 'Juguetes Educativos',
                'description' => 'Juguetes que combinan diversión con aprendizaje',
                'is_active' => true,
            ],
            [
                'name' => 'Juguetes Electrónicos',
                'description' => 'Juguetes que requieren baterías o electricidad',
                'is_active' => true,
            ],
            [
                'name' => 'Juguetes de Construcción',
                'description' => 'Bloques, legos y juguetes de construcción',
                'is_active' => true,
            ],
            [
                'name' => 'Juguetes de Rol',
                'description' => 'Disfraces, accesorios y juguetes para juegos de rol',
                'is_active' => true,
            ],
            [
                'name' => 'Juguetes Deportivos',
                'description' => 'Pelotas, raquetas y equipos deportivos para niños',
                'is_active' => true,
            ],
            [
                'name' => 'Juguetes de Agua',
                'description' => 'Juguetes para actividades acuáticas y piscinas',
                'is_active' => true,
            ],
            [
                'name' => 'Juguetes Musicales',
                'description' => 'Instrumentos musicales y juguetes que producen sonidos',
                'is_active' => true,
            ],
        ];

        foreach ($toyTypes as $toyType) {
            \DB::table('toy_types')->updateOrInsert(
                ['name' => $toyType['name']],
                $toyType
            );
        }
    }
}
