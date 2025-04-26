<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Patient;
use Faker\Factory as Faker;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $examinationTypes = ['MCU'];
        $statuses = ['Delivered', 'Process', 'Cancelled'];
        $genders = ['Laki-laki', 'Perempuan'];

        foreach (range(1, 15) as $i) {
            $birthDate = $faker->dateTimeBetween('-60 years', '-18 years');
            $age = now()->year - $birthDate->format('Y');

            Patient::create([
                'med_record_id' => $faker->unique()->bothify('RM####'),
                'patient_id' => $faker->unique()->bothify('PSN####'),
                'name' => $faker->name,
                'examination_date' => $faker->dateTimeBetween('-1 year', 'now'),
                'examination_type' => $faker->randomElement($examinationTypes),
                'unit' => $faker->company,
                'status' => $faker->randomElement($statuses),
                'gender' => $gender = $faker->randomElement($genders),
                'age' => $age,
                'birth_date' => $birthDate->format('Y-m-d'),
                'birth_place' => $faker->city,
                'address' => $faker->address,
            ]);
        }
    }
}
