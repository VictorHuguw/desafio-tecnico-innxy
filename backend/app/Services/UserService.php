<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function createUser($data)
    {
        try {
            User::create($data);
            return ['success' => true, 'message' => 'Usuário criado com sucesso'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Erro ao criar usuário: ' . $e->getMessage()];
        }
    }

    public function login($data)
    {
        try {
            $user = User::where('email', $data['email'])->first();

            if ($user && Hash::check($data['password'], $user->password)) {
                $token = auth()->login($user);
                return response()->json(['success' => true, 'message' => 'Login feito com sucesso','token' => $token], 200);
            }else{
                return response()->json(['success' => false, 'message' => 'Credenciais inválidas'], 401);
            }
           
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Erro ao fazer login: ' . $e->getMessage()];
        }
    }

}
