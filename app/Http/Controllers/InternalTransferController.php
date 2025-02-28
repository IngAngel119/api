<?php

namespace App\Http\Controllers;

use App\Models\InternalTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InternalTransferController extends Controller
{
    // Obtener todas las transferencias internas de una cuenta
    public function index($accountId)
    {
        $internalTransfers = InternalTransfer::where('account_id', $accountId)
            ->whereNull('deleted_at')
            ->get();

        return response()->json($internalTransfers);
    }

    // Obtener una transferencia interna por ID de cuenta
    public function show($accountId, $id)
    {
        $internalTransfer = InternalTransfer::where('account_id', $accountId)
            ->whereNull('deleted_at')
            ->find($id);

        if (!$internalTransfer) {
            return response()->json(['error' => 'Transferencia interna no encontrada'], 404);
        }

        return response()->json($internalTransfer);
    }

    // Crear una nueva transferencia interna
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0',
            'receptor_account' => 'required|string|max:255',
            'movement_date' => 'nullable|date', 
            'created_by' => 'nullable|integer|exists:users,id',
        ]);

        // Generar automáticamente la fecha
        $validated['movement_date'] = $validated['movement_date'] ?? now();

        // Asignar el usuario autenticado como creador
        $validated['created_by'] = $validated['created_by'] ?? Auth::id();

        // Crear la transferencia interna
        $internalTransfer = InternalTransfer::create($validated);

        return response()->json([
            'message' => 'Transferencia interna creada con éxito',
            'internal_transfer' => $internalTransfer,
        ], 201);
    }

    // Actualizar una transferencia interna
    public function update(Request $request,$id)
    {
        $internalTransfer = InternalTransfer::whereNull('deleted_at')
            ->find($id);

        if (!$internalTransfer) {
            return response()->json(['error' => 'Transferencia interna no encontrada o inactiva'], 404);
        }

        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:0',
            'receptor_account' => 'nullable|string|max:255',
            'movement_date' => 'nullable|date',
            'updated_by' => 'nullable|integer|exists:users,id',
        ]);

        // Actualizar los campos
        $internalTransfer->update($validated);

        return response()->json([
            'message' => 'Transferencia interna actualizada con éxito',
            'internal_transfer' => $internalTransfer,
        ]);
    }

    // Eliminar una transferencia interna (soft delete)
    public function destroy($id)
    {
        $internalTransfer = InternalTransfer::whereNull('deleted_at')
        ->find($id);

        if (!$internalTransfer) {
            return response()->json(['error' => 'Transferencia interna no encontrada o ya eliminada'], 404);
        }

        // Soft delete
        $internalTransfer->deleted_at = now();
        $internalTransfer->save();

        return response()->json(['message' => 'Transferencia interna eliminada (soft delete)']);
    }

    // Restaurar una transferencia interna eliminada
    public function restore($accountId, $id)
    {
        $internalTransfer = InternalTransfer::where('account_id', $accountId)
            ->whereNotNull('deleted_at')
            ->find($id);

        if (!$internalTransfer) {
            return response()->json(['error' => 'Transferencia interna no encontrada o no eliminada'], 404);
        }

        // Restaurar la transferencia interna
        $internalTransfer->deleted_at = null;
        $internalTransfer->save();

        return response()->json(['message' => 'Transferencia interna restaurada']);
    }
}