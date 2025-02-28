<?php

namespace App\Http\Controllers;

use App\Models\InvestmentMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvestmentMovementController extends Controller
{
    // Obtener todos los movimientos de inversión de una cuenta
    public function index($accountId)
    {
        $investmentMovements = InvestmentMovement::where('account_id', $accountId)
            ->whereNull('deleted_at')
            ->get();

        return response()->json($investmentMovements);
    }

    // Obtener un movimiento de inversión por ID de cuenta
    public function show($accountId, $id)
    {
        $investmentMovement = InvestmentMovement::where('account_id', $accountId)
            ->whereNull('deleted_at')
            ->find($id);

        if (!$investmentMovement) {
            return response()->json(['error' => 'Movimiento de inversión no encontrado'], 404);
        }

        return response()->json($investmentMovement);
    }

    // Crear un nuevo movimiento de inversión
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0',
            'inversion_type' => 'required|string|max:255',
            'payment_date' => 'nullable|date', 
            'created_by' => 'nullable|integer|exists:users,id',
        ]);

        // Generar automáticamente la fecha
        $validated['payment_date'] = $validated['payment_date'] ?? now();

        // Asignar el usuario autenticado como creador
        $validated['created_by'] = $validated['created_by'] ?? Auth::id();

        // Crear el movimiento de inversión
        $investmentMovement = InvestmentMovement::create($validated);

        return response()->json([
            'message' => 'Movimiento de inversión creado con éxito',
            'investment_movement' => $investmentMovement,
        ], 201);
    }

    // Actualizar un movimiento de inversión
    public function update(Request $request,$id)
    {
        $investmentMovement = InvestmentMovement::whereNull('deleted_at')
            ->find($id);

        if (!$investmentMovement) {
            return response()->json(['error' => 'Movimiento de inversión no encontrado o inactivo'], 404);
        }

        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:0',
            'inversion_type' => 'nullable|string|max:255',
            'payment_date' => 'nullable|date',
            'updated_by' => 'nullable|integer|exists:users,id',
        ]);

        // Actualizar los campos
        $investmentMovement->update($validated);

        return response()->json([
            'message' => 'Movimiento de inversión actualizado con éxito',
            'investment_movement' => $investmentMovement,
        ]);
    }

    // Eliminar un movimiento de inversión (soft delete)
    public function destroy($id)
    {
        $investmentMovement = InvestmentMovement::whereNull('deleted_at')
        ->find($id);

        if (!$investmentMovement) {
            return response()->json(['error' => 'Movimiento de inversión no encontrado o ya eliminado'], 404);
        }

        // Soft delete
        $investmentMovement->deleted_at = now();
        $investmentMovement->save();

        return response()->json(['message' => 'Movimiento de inversión eliminado (soft delete)']);
    }

    // Restaurar un movimiento de inversión eliminado
    public function restore($accountId, $id)
    {
        $investmentMovement = InvestmentMovement::where('account_id', $accountId)
            ->whereNotNull('deleted_at')
            ->find($id);

        if (!$investmentMovement) {
            return response()->json(['error' => 'Movimiento de inversión no encontrado o no eliminado'], 404);
        }

        // Restaurar el movimiento de inversión
        $investmentMovement->deleted_at = null;
        $investmentMovement->save();

        return response()->json(['message' => 'Movimiento de inversión restaurado']);
    }
}