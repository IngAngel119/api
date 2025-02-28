<?php

namespace App\Http\Controllers;

use App\Models\SeparateMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SeparateMovementController extends Controller
{
    /**
     * Mostrar todos los movimientos separados de una cuenta.
     */
    public function index($accountId)
    {
        $movements = SeparateMovement::where('account_id', $accountId)
            ->whereNull('deleted_at')
            ->get();

        return response()->json($movements);
    }

    /**
     * Crear un nuevo movimiento separado.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0',
            'separate_name' => 'required|string|max:255',
            'payment_date' => 'nullable|date', 
            'created_by' => 'nullable|integer|exists:users,id',
        ]);

        // Generar automáticamente la fecha
        $validated['payment_date'] = $validated['payment_date'] ?? now();

        // Asignar el usuario autenticado como creador
        $validated['created_by'] = $validated['created_by'] ?? Auth::id();

        // Crear el movimiento separado
        $movement = SeparateMovement::create($validated);

        return response()->json([
            'message' => 'Movimiento separado creado con éxito',
            'movement' => $movement,
        ], 201);
    }

    /**
     * Mostrar un movimiento separado específico.
     */
    public function show($id)
    {
        $movement = SeparateMovement::where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        if (!$movement) {
            return response()->json(['error' => 'Movimiento no encontrado'], 404);
        }

        return response()->json($movement);
    }

    /**
     * Actualizar un movimiento separado.
     */
    public function update(Request $request, $id)
    {
        $movement = SeparateMovement::find($id);

        if (!$movement || $movement->deleted_at) {
            return response()->json(['error' => 'Movimiento no encontrado o inactivo'], 404);
        }

        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:0',
            'separate_name' => 'nullable|string|max:255',
            'payment_date' => 'nullable|date',
            'created_by' => 'nullable|integer|exists:users,id',
        ]);

        // Actualizar los campos
        $movement->update($validated);

        return response()->json([
            'message' => 'Movimiento separado actualizado con éxito',
            'movement' => $movement,
        ]);
    }

    /**
     * Eliminar un movimiento separado (soft delete).
     */
    public function destroy($id)
    {
        $movement = SeparateMovement::whereNull('deleted_at')
        ->find($id);

        if (!$movement || $movement->deleted_at) {
            return response()->json(['error' => 'Movimiento no encontrado o ya eliminado'], 404);
        }

        // Soft delete
        $movement->deleted_at = now();
        $movement->save();

        return response()->json(['message' => 'Movimiento separado eliminado (soft delete)']);
    }

    /**
     * Restaurar un movimiento separado eliminado.
     */
    public function restore($accountId, $id)
    {
        $movement = SeparateMovement::where('account_id', $accountId)
            ->whereNotNull('deleted_at')
            ->find($id);

        if (!$movement) {
            return response()->json(['error' => 'Movimiento no encontrado o no eliminado'], 404);
        }

        // Restaurar el movimiento
        $movement->deleted_at = null;
        $movement->save();

        return response()->json(['message' => 'Movimiento separado restaurado']);
    }
}