<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'nom'      => 'required|string|max:100',
            'prenom'   => 'required|string|max:100',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Tout nouveau compte public = visiteur. Seul l'admin peut upgrader le rôle.
        $user = User::create([
            'nom'      => $data['nom'],
            'prenom'   => $data['prenom'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'visiteur',
        ]);

        $token = auth('api')->login($user);

        return response()->json([
            'message' => 'Inscription réussie',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['message' => 'Email ou mot de passe incorrect'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(auth('api')->user());
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Déconnexion réussie']);
    }

    public function updateProfile(Request $request)
    {
        $user = auth('api')->user();

        $data = $request->validate([
            'nom'      => 'sometimes|string|max:100',
            'prenom'   => 'sometimes|string|max:100',
            'email'    => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if (isset($data['password'])) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json(['message' => 'Profil mis à jour.', 'user' => $user->fresh()]);
    }

    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $user = auth('api')->user();

        // Supprimer l'ancienne photo
        if ($user->photo) {
            Storage::disk('public')->delete('avatars/' . $user->photo);
        }

        $ext      = $request->file('photo')->extension();
        $filename = 'user_' . $user->id . '_' . time() . '.' . $ext;
        $request->file('photo')->storeAs('avatars', $filename, 'public');

        $user->update(['photo' => $filename]);

        return response()->json([
            'message'   => 'Photo mise à jour.',
            'photo_url' => asset('storage/avatars/' . $filename),
            'user'      => $user->fresh(),
        ]);
    }

    public function deletePhoto()
    {
        $user = auth('api')->user();
        if ($user->photo) {
            Storage::disk('public')->delete('avatars/' . $user->photo);
            $user->update(['photo' => null]);
        }
        return response()->json(['message' => 'Photo supprimée.', 'user' => $user->fresh()]);
    }

    private function respondWithToken(string $token)
    {
        return response()->json([
            'token'      => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user'       => auth('api')->user(),
        ]);
    }
}
