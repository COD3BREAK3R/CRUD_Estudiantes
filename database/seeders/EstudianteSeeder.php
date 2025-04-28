<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Estudiante;
use Faker\Factory as Faker;

class EstudianteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_ES');
        
        // Crear 20 estudiantes con datos aleatorios
        for ($i = 0; $i < 20; $i++) {
            Estudiante::create([
                'nombre' => $faker->name(),
                'edad' => $faker->numberBetween(6, 25),
            ]);
        }
    }
}