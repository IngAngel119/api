<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = storage_path('../database/CSV/pagos_servicios.csv');
        $handle = fopen($file, 'r');

        if ($handle !== false) {
            $headers = fgetcsv($handle, 1000, ','); 

            $map = [
                'id_pago' => 'id',
                'id_cuenta' => 'account_id',
                'cantidad' => 'amount',
                'categoria_servicio' => 'service_category',
                'empresa_destino' => 'destination_company',
                'es_domiciliado' => 'is_domiciled',
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
                    DB::table('payment_services')->insert($row);
                }
            }
            fclose($handle);
        }
    }
}
