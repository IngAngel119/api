<?php

namespace App\Http\Controllers;

use App\Models\PaymentService;
use Illuminate\Http\Request;

class PaymentServiceController extends Controller
{
    // Obtener todos los pagos de servicios de una cuenta
    public function index($accountId)
    {
        $paymentServices = PaymentService::where('account_id', $accountId)
            ->whereNull('deleted_at')
            ->get();

        return response()->json($paymentServices);
    }

    // Crear un nuevo pago de servicio
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0',
            'service_category' => 'required|string|max:255',
            'destination_company' => 'required|string|max:255',
            'is_domiciled' => 'required|boolean',
            'payment_date' => 'nullable|date', 
            'created_by' => 'nullable|integer|exists:users,id',
        ]);

        // Generar automáticamente la fecha
        $validated['payment_date'] = $validated['payment_date'] ?? now();

        // Crear el pago de servicio
        $paymentService = PaymentService::create($validated);

        return response()->json([
            'message' => 'Pago de servicio creado exitosamente',
            'payment_service' => $paymentService,
        ], 201);
    }

    // Actualizar un pago de servicio
    public function update(Request $request, $paymentServiceId)
    {
        $paymentService = PaymentService::find($paymentServiceId);

        if (!$paymentService || $paymentService->deleted_at) {
            return response()->json(['error' => 'Pago de servicio no encontrado o inactivo'], 404);
        }

        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:0',
            'service_category' => 'nullable|string|max:255',
            'destination_company' => 'nullable|string|max:255',
            'is_domiciled' => 'nullable|boolean',
            'payment_date' => 'nullable|date',
            'updated_by' => 'nullable|integer|exists:users,id',
        ]);

        // Actualizar los campos
        $paymentService->update($validated);

        return response()->json($paymentService);
    }

    // Eliminar (soft delete) un pago de servicio
    public function destroy($id)
    {
        $paymentService = PaymentService::whereNull('deleted_at')
        ->find($id);

        if (!$paymentService || $paymentService->deleted_at) {
            return response()->json(['error' => 'Pago de servicio no encontrado o ya eliminado'], 404);
        }

        $paymentService->deleted_at = now();
        $paymentService->save();

        return response()->json(['message' => 'Pago de servicio eliminado (soft delete)']);
    }
}