<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * Crear un usuario.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        // Crear la cuenta
        $user = User::create($validated);

        return response()->json($user, 201);
    }

    /**
     * Mostrar un usuario.
     */
    public function show($id)
    {
        $user = User::whereNull('deleted_at')->find($id);

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        return response()->json($user);
    }

    /**
     * Actualiza un usuario.
     */
    public function update(Request $request, $id)
    {
        $user = Account::find($id);

        if (!$user || $user->deleted_at) {
            return response()->json(['error' => 'Usuario no encontrada o inactiva'], 404);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
        ]);

        // Actualizar los campos
        $user->update($validated);

        return response()->json($user);
    }

    /**
     * Elimina un usuario (soft delete).
     */
    public function destroy($id)
    {
        $user = Account::find($user);

        if (!$user || $user->deleted_at) {
            return response()->json(['error' => 'Usuario no encontrada o ya eliminada'], 404);
        }

        $user->deleted_at = now();
        $user->save();

        return response()->json(['message' => 'Usuario eliminada (soft delete)']);
    }

    /**
     * Restaura un usuario eliminado.
     */
    public function restore($id)
    {
        $user = Account::whereNotNull('deleted_at')->find($id);

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrada o no eliminada'], 404);
        }

        $user->deleted_at = null;
        $user->save();

        return response()->json(['message' => 'Usuario restaurada']);
    }
}
