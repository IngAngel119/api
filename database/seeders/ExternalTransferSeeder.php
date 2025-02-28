<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExternalTransferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = storage_path('../database/CSV/transferencias_externas.csv');
        $handle = fopen($file, 'r');

        if ($handle !== false) {
            $headers = fgetcsv($handle, 1000, ','); 

            $map = [
                'id_transferencia' => 'id',
                'id_cuenta' => 'account_id',
                'cantidad' => 'amount',
                'motivo' => 'reason',
                'cuenta_receptora' => 'receptor_account',
                'banco_receptor' => 'receiving_bank',
                'fecha_movimiento' => 'movement_date',
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
                    DB::table('external_transfers')->insert($row);
                }
            }
            fclose($handle);
        }
    }
}
