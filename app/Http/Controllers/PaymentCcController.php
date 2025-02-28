<?php

namespace App\Http\Controllers;

use App\Models\PaymentCc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentCcController extends Controller
{
    // Obtener todos los pagos de CC de una tarjeta
    public function index($cardId)
    {
        $paymentCcs = PaymentCc::where('card_id', $cardId)
            ->whereNull('deleted_at')
            ->get();

        return response()->json($paymentCcs);
    }

    // Obtener un pago de CC por ID de tarjeta
    public function show($cardId, $id)
    {
        $paymentCc = PaymentCc::where('card_id', $cardId)
            ->whereNull('deleted_at')
            ->find($id);

        if (!$paymentCc) {
            return response()->json(['error' => 'Pago de CC no encontrado'], 404);
        }

        return response()->json($paymentCc);
    }

    // Crear un nuevo pago de CC
    public function store(Request $request, $cardId)
    {
        // Validar los campos requeridos
        $validated = $request->validate([
            'minimum_payment_amount' => 'nullable|numeric|min:0',
            'interest_free_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'cut_off_date' => 'nullable|date',
            'payment_date' => 'nullable|date',
            'movement_date' => 'nullable|date',
            'created_by' => 'nullable|integer|exists:users,id',
        ]);
    
        // Asignar el card_id desde la URL
        $validated['card_id'] = $cardId;
    
        // Asignar valores predeterminados
        $validated['payment_date'] = $validated['payment_date'] ?? now();
        $validated['movement_date'] = $validated['movement_date'] ?? now();
        $validated['cut_off_date'] = $validated['cut_off_date'] ?? now();
    
        // Asignar el usuario autenticado
        $validated['created_by'] = $validated['created_by'] ?? Auth::id();
    
        // Crear el pago de CC
        $paymentCc = PaymentCc::create($validated);
    
        return response()->json([
            'message' => 'Pago de CC creado con éxito',
            'payment_cc' => $paymentCc,
        ], 201);
    }

    // Actualizar un pago de CC
    public function update(Request $request, $cardId, $id)
    {
        $paymentCc = PaymentCc::where('card_id', $cardId)
            ->whereNull('deleted_at')
            ->find($id);

        if (!$paymentCc) {
            return response()->json(['error' => 'Pago de CC no encontrado o inactivo'], 404);
        }

        $validated = $request->validate([
            'minimum_payment_amount' => 'nullable|numeric|min:0',
            'interest_free_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'cut_off_date' => 'nullable|date',
            'payment_date' => 'nullable|date',
            'movement_date' => 'nullable|date',
            'updated_by' => 'nullable|integer|exists:users,id',
        ]);

        // Actualizar los campos
        $paymentCc->update($validated);

        return response()->json([
            'message' => 'Pago de CC actualizado con éxito',
            'payment_cc' => $paymentCc,
        ]);
    }

    // Eliminar un pago de CC (soft delete)
    public function destroy($id)
    {
        $paymentCc = PaymentCc::whereNull('deleted_at')
        ->find($id);

        if (!$paymentCc) {
            return response()->json(['error' => 'Pago de CC no encontrado o ya eliminado'], 404);
        }

        // Soft delete
        $paymentCc->deleted_at = now();
        $paymentCc->save();

        return response()->json(['message' => 'Pago de CC eliminado (soft delete)']);
    }

    // Restaurar un pago de CC eliminado
    public function restore($cardId, $id)
    {
        $paymentCc = PaymentCc::where('card_id', $cardId)
            ->whereNotNull('deleted_at')
            ->find($id);

        if (!$paymentCc) {
            return response()->json(['error' => 'Pago de CC no encontrado o no eliminado'], 404);
        }

        // Restaurar el pago de CC
        $paymentCc->deleted_at = null;
        $paymentCc->save();

        return response()->json(['message' => 'Pago de CC restaurado']);
    }
}