<?php

namespace App\Http\Controllers;

use App\Models\CreditCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreditCardController extends Controller
{
    // Obtener todas las tarjetas de crédito de un cliente
    public function index($clientId)
    {
        $creditCards = CreditCard::where('client_id', $clientId)
            ->whereNull('deleted_at')
            ->get();

        return response()->json($creditCards);
    }

    // Obtener una tarjeta de crédito por ID de cliente
    public function show($clientId, $id)
    {
        $creditCard = CreditCard::where('client_id', $clientId)
            ->whereNull('deleted_at')
            ->find($id);

        if (!$creditCard) {
            return response()->json(['error' => 'Tarjeta de crédito no encontrada'], 404);
        }

        return response()->json($creditCard);
    }

    // Crear una nueva tarjeta de crédito
    public function store(Request $request)
    {
        // Validar los campos requeridos
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'credit_limit' => 'required|numeric|min:0',
            'created_by' => 'nullable|integer|exists:users,id',
        ]);

        // Generar un número de tarjeta único
        do {
            $cardNumber = mt_rand(1000000000000000, 9999999999999999); // Número de 16 dígitos
        } while (CreditCard::where('card_number', $cardNumber)->exists());

        // Establecer la fecha de expiración (5 años a partir de hoy)
        $expirationDate = now()->addYears(5)->format('Y-m-d');

        // Asignar el usuario autenticado como creador
        $validated['created_by'] = $validated['created_by'] ?? Auth::id();

        // Agregar el número de tarjeta y la fecha de expiración a los datos validados
        $validated['card_number'] = $cardNumber;
        $validated['expiration_date'] = $expirationDate;

        // Crear la tarjeta de crédito
        $creditCard = CreditCard::create($validated);

        return response()->json([
            'message' => 'Tarjeta de crédito creada con éxito',
            'credit_card' => $creditCard,
        ], 201);
    }

    // Actualizar una tarjeta de crédito
    public function update(Request $request, $id)
    {
        $creditCard = CreditCard::whereNull('deleted_at')
            ->find($id);

        if (!$creditCard) {
            return response()->json(['error' => 'Tarjeta de crédito no encontrada o inactiva'], 404);
        }

        $validated = $request->validate([
            'card_number' => 'nullable|integer',
            'expiration_date' => 'nullable|date',
            'credit_limit' => 'nullable|numeric|min:0',
            'updated_by' => 'nullable|integer|exists:users,id',
        ]);

        // Actualizar los campos
        $creditCard->update($validated);

        return response()->json([
            'message' => 'Tarjeta de crédito actualizada con éxito',
            'credit_card' => $creditCard,
        ]);
    }

    // Eliminar una tarjeta de crédito (soft delete)
    public function destroy($id)
    {
        $creditCard = CreditCard::whereNull('deleted_at')
        ->find($id);
        if (!$creditCard) {
            return response()->json(['error' => 'Tarjeta de crédito no encontrada o ya eliminada'], 404);
        }

        // Soft delete
        $creditCard->deleted_at = now();
        $creditCard->save();

        return response()->json(['message' => 'Tarjeta de crédito eliminada (soft delete)']);
    }

    // Restaurar una tarjeta de crédito eliminada
    public function restore($clientId, $id)
    {
        $creditCard = CreditCard::where('client_id', $clientId)
            ->whereNotNull('deleted_at')
            ->find($id);

        if (!$creditCard) {
            return response()->json(['error' => 'Tarjeta de crédito no encontrada o no eliminada'], 404);
        }

        // Restaurar la tarjeta de crédito
        $creditCard->deleted_at = null;
        $creditCard->save();

        return response()->json(['message' => 'Tarjeta de crédito restaurada']);
    }
}
