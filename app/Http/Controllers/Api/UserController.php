<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Liste tous les utilisateurs (admin)
    public function index()
    {
        $users = User::select('id', 'nom', 'prenom', 'email', 'role', 'created_at')
                     ->orderBy('created_at', 'desc')
                     ->get();
        return response()->json($users);
    }

    /**
     * Créer un compte avec n'importe quel rôle — réservé à l'admin.
     * Contrairement à /auth/register (public, toujours visiteur),
     * cet endpoint permet de choisir directement le rôle.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'      => 'required|string|max:100',
            'prenom'   => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,user,visiteur',
        ]);

        $user = User::create([
            'nom'      => $data['nom'],
            'prenom'   => $data['prenom'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
        ]);

        return response()->json([
            'message' => 'Compte créé avec succès.',
            'user'    => $user,
        ], 201);
    }

    // Changer le rôle d'un utilisateur (admin)
    public function updateRole(Request $request, string $id)
    {
        $request->validate(['role' => 'required|in:admin,user,visiteur']);

        $user = User::findOrFail($id);

        if ($user->id === auth('api')->id()) {
            return response()->json(['message' => 'Vous ne pouvez pas modifier votre propre rôle.'], 403);
        }

        $user->update(['role' => $request->role]);
        return response()->json(['message' => 'Rôle mis à jour.', 'user' => $user]);
    }

    /**
     * Mise à jour complète d'un agent (nom, prenom, email, password, role) — admin.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth('api')->id()) {
            return response()->json(['message' => 'Utilisez /api/auth/profile pour modifier votre propre compte.'], 403);
        }

        $data = $request->validate([
            'nom'      => 'sometimes|string|max:100',
            'prenom'   => 'sometimes|string|max:100',
            'email'    => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'role'     => 'sometimes|in:admin,user,visiteur',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'Utilisateur mis à jour.',
            'user'    => $user->fresh(),
        ]);
    }

    // Supprimer un utilisateur (admin)
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth('api')->id()) {
            return response()->json(['message' => 'Vous ne pouvez pas supprimer votre propre compte.'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'Utilisateur supprimé.']);
    }
}
