<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = storage_path('../database/CSV/clientes.csv');
        $handle = fopen($file, 'r');

        if ($handle !== false) {
            $headers = fgetcsv($handle, 1000, ','); // Lee la primera fila como encabezados

            // Define el mapeo entre nombres del CSV y nombres de la BD
            $map = [
                'id_cliente' => 'id',
                'nombre' => 'name',
            ];

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $row = [];
                foreach ($headers as $index => $header) {
                    if (isset($map[$header])) {
                        $row[$map[$header]] = $data[$index];
                    }
                }

                $row['deleted_at'] = $row['deleted_at'] ?? null;

                if (!empty($row)) {
                    DB::table('clients')->insert($row);
                }
            }
            fclose($handle);
        }
    }
}
