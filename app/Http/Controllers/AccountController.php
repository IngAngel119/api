<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    // Obtener todas las cuentas activas de un cliente
    public function getClientAccounts($clientId)
    {
        $client = Client::find($clientId);

        if (!$client) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        // Solo cuentas activas
        $accounts = $client->accounts()->whereNull('deleted_at')->get();

        return response()->json($accounts);
    }

    // Crear una nueva cuenta para un cliente
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'balance' => 'required|numeric|min:0',
            'created_by' => 'nullable|integer|exists:users,id',
        ]);

        // Generar automáticamente el número de cuenta usando UUID
        $validated['account_number'] = Str::uuid();

        // Crear la cuenta
        $account = Account::create($validated);

        return response()->json($account, 201);
    }

    // Actualizar una cuenta existente
    public function update(Request $request, $accountId)
    {
        $account = Account::find($accountId);

        if (!$account || $account->deleted_at) {
            return response()->json(['error' => 'Cuenta no encontrada o inactiva'], 404);
        }

        $validated = $request->validate([
            'balance' => 'nullable|numeric|min:0',
            'updated_by' => 'nullable|integer|exists:users,id',
        ]);
        
        $account->update($validated);

        return response()->json($account);
    }

    // Eliminar (soft delete) una cuenta
    public function destroy($accountId)
    {
        $account = Account::find($accountId);

        if (!$account || $account->deleted_at) {
            return response()->json(['error' => 'Cuenta no encontrada o ya eliminada'], 404);
        }

        $account->deleted_at = now();
        $account->save();

        return response()->json(['message' => 'Cuenta eliminada (soft delete)']);
    }

    // Restaurar una cuenta eliminada
    public function restore($accountId)
    {
        $account = Account::whereNotNull('deleted_at')->find($accountId);

        if (!$account) {
            return response()->json(['error' => 'Cuenta no encontrada o no eliminada'], 404);
        }

        $account->deleted_at = null;
        $account->save();

        return response()->json(['message' => 'Cuenta restaurada']);
    }
}