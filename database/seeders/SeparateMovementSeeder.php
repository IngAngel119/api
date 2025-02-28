<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeparateMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = storage_path('../database/CSV/movimientos_apartados.csv');
        $handle = fopen($file, 'r');

        if ($handle !== false) {
            $headers = fgetcsv($handle, 1000, ','); 

            $map = [
                'id_movimiento' => 'id',
                'id_cuenta' => 'account_id',
                'cantidad' => 'amount',
                'nombre_apartado' => 'separate_name',
                'fecha_movimiento' => 'payment_date',
            ];

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $row = [];
                foreach ($headers as $index => $header) {
                    if (isset($map[$header])) {
                        $row[$map[$header]] = $data[$index];
                    }
                }

                $row['deleted_at'] = $row['deleted_at'] ?? null;

                if (isset($row['account_id']) && is_numeric($row['account_id'])) {
                    $row['account_id'] = (int) $row['account_id'];
                } else {
                    continue;
                }

                if (!empty($row)) {
                    DB::table('separate_movements')->insert($row);
                }
            }
            fclose($handle);
        }
    }
}
