<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request){
        $credenciais = $request->all();

        $token = auth('api')->attempt($credenciais);
        
        if($token){
            return response()->json(['token'=>$token], 200);
        }else{
            return response()->json(['erro'=>'Usuário ou Senha incorretos'], 403);

            //401 = Unauthorized -> não autorizado
            //403 = Forbidden -> proibido
        }
    }

    public function logout(){
        auth('api')->logout();
        return response()->json(['msg'=>'O logout foi realizado com sucesso'], 200);
    }

    public function refresh(){
        $token = auth('api')->refresh(); 
        return response()->json(['token' => $token], 200);
    }

    public function me(){
        return response()->json(auth()->user(), 200);
    }
}
