<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Airport;

class AirportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/airports.csv');
        $file = fopen($path, 'r');

        // Pular o cabeçalho
        fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {
            // Ignorar aeroportos sem código IATA (ex: heliportos ou militares)
            if (empty($row[13])) {
                continue;
            }

            Airport::create([
                'name'      => $row[3],   // name
                'city'      => $row[10],  // municipality
                'country'   => $row[8],   // iso_country
                'iata_code' => $row[13],  // iata_code
            ]);
        }

        fclose($file);
    }
}
