<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreditCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = storage_path('../database/CSV/tarjetas_credito.csv');
        $handle = fopen($file, 'r');

        if ($handle !== false) {
            $headers = fgetcsv($handle, 1000, ','); 

            $map = [
                'id_tarjeta' => 'id',
                'id_cliente' => 'client_id',
                'numero_tarjeta' => 'card_number',
                'fecha_vencimiento' => 'expiration_date',
                'limite_credito' => 'credit_limit',
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
                    DB::table('credit_cards')->insert($row);
                }
            }
            fclose($handle);
        }
    }
}
