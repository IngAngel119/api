<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        return response()->json(Client::all());
    }

    public function show($id)
    {
        $client = Client::whereNull('deleted_at')->find($id);
        return $client ? response()->json($client) : response()->json(['error' => 'Client not found'], 404);
    }

    public function store(Request $request)
    {
        try {
            // Validar los datos del cliente
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'created_by' => 'nullable|integer',
            ]);

            // Crear el cliente
            $client = Client::create($validated);

            // Crear una cuenta para el cliente recién creado
            $account = Account::create([
                'client_id' => $client->id,
                'account_number' => 0,
                'balance' => 0, 
                'created_by' => $request->input('created_by'),
            ]);

            // Actualizar el número de cuenta con el ID generado
            $account->update(['account_number' => $account->id]);

            return response()->json([
                'message' => 'Cliente y cuenta creados con éxito',
                'client' => $client,
                'account' => $account,
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear el cliente y la cuenta', 'details' => $e->getMessage()], 500);
        }
    }

    

    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'created_by' => 'nullable|integer|exists:users,id',
        ]);

        $client->update($validated);
        return response()->json($client);
    }

    public function destroy($id)
    {
        $client = Client::find($id);

        if (!$client || $client->deleted_at) {
            return response()->json(['error' => 'Cliente no encontrado o ya eliminado'], 404);
        }

        $client->deleted_at = now();
        $client->save();

        return response()->json(['message' => 'Cliente eliminado (soft delete)']);
    }
}