<?php

namespace App\Http\Controllers;

use App\Models\ExternalTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExternalTransferController extends Controller
{
    // Obtener todas las transferencias externas de una cuenta
    public function index($accountId)
    {
        $externalTransfers = ExternalTransfer::where('account_id', $accountId)
            ->whereNull('deleted_at')
            ->get();

        return response()->json($externalTransfers);
    }

    // Obtener una transferencia externa por ID de cuenta
    public function show($accountId, $id)
    {
        $externalTransfer = ExternalTransfer::where('account_id', $accountId)
            ->whereNull('deleted_at')
            ->find($id);

        if (!$externalTransfer) {
            return response()->json(['error' => 'Transferencia externa no encontrada'], 404);
        }

        return response()->json($externalTransfer);
    }

    // Crear una nueva transferencia externa
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0',
            'reason' => 'required|string|max:255',
            'receptor_account' => 'required|string|max:255',
            'receiving_bank' => 'required|string|max:255',
            'movement_date' => 'nullable|date',
            'created_by' => 'nullable|integer|exists:users,id',
        ]);

        // Generar automáticamente la fecha
        $validated['movement_date'] = $validated['movement_date'] ?? now();

        // Asignar el usuario autenticado como creador
        $validated['created_by'] = $validated['created_by'] ?? Auth::id();

        // Crear la transferencia externa
        $externalTransfer = ExternalTransfer::create($validated);

        return response()->json([
            'message' => 'Transferencia externa creada con éxito',
            'external_transfer' => $externalTransfer,
        ], 201);
    }

    // Actualizar una transferencia externa
    public function update(Request $request, $id)
    {
        $externalTransfer = ExternalTransfer::whereNull('deleted_at')
        ->find($id);

        if (!$externalTransfer) {
            return response()->json(['error' => 'Transferencia externa no encontrada o inactiva'], 404);
        }

        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:0',
            'reason' => 'nullable|string|max:255',
            'receptor_account' => 'nullable|string|max:255',
            'receiving_bank' => 'nullable|string|max:255',
            'movement_date' => 'nullable|date',
            'updated_by' => 'nullable|integer|exists:users,id',
        ]);

        // Actualizar los campos
        $externalTransfer->update($validated);

        return response()->json([
            'message' => 'Transferencia externa actualizada con éxito',
            'external_transfer' => $externalTransfer,
        ]);
    }

    // Eliminar una transferencia externa (soft delete)
    public function destroy($id)
    {
        $externalTransfer = ExternalTransfer::whereNull('deleted_at')
        ->find($id);

        if (!$externalTransfer) {
            return response()->json(['error' => 'Transferencia externa no encontrada o ya eliminada'], 404);
        }

        // Soft delete
        $externalTransfer->deleted_at = now();
        $externalTransfer->save();

        return response()->json(['message' => 'Transferencia externa eliminada (soft delete)']);
    }

    // Restaurar una transferencia externa eliminada
    public function restore($accountId, $id)
    {
        $externalTransfer = ExternalTransfer::where('account_id', $accountId)
            ->whereNotNull('deleted_at')
            ->find($id);

        if (!$externalTransfer) {
            return response()->json(['error' => 'Transferencia externa no encontrada o no eliminada'], 404);
        }

        // Restaurar la transferencia externa
        $externalTransfer->deleted_at = null;
        $externalTransfer->save();

        return response()->json(['message' => 'Transferencia externa restaurada']);
    }
}