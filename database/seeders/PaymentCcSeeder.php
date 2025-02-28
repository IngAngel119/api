<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentCcSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = storage_path('../database/CSV/pagos_tdc.csv');
        $handle = fopen($file, 'r');

        if ($handle !== false) {
            $headers = fgetcsv($handle, 1000, ','); 

            $map = [
                'id_pago' => 'id',
                'id_tarjeta' => 'card_id',
                'cantidad_minima_pago' => 'minimum_payment_amount',
                'cantidad_sin_intereses' => 'interest_free_amount',
                'cantidad_total' => 'total_amount',
                'fecha_corte' => 'cut_off_date',
                'fecha_pago' => 'payment_date',
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

                if (isset($row['cut_off_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $row['cut_off_date'])) {
                    $row['cut_off_date'] = new \DateTime($row['cut_off_date']);
                } else {
                    continue;
                }

                if (!empty($row)) {
                    DB::table('payment_ccs')->insert($row);
                }
            }
            fclose($handle);
        }
    }
}
