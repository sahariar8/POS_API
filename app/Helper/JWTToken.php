<?php

namespace App\Helper;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PhpParser\Node\Stmt\Catch_;

class JWTToken
{

    public static function generateToken($user)
    {

        $key = env('JWT_SECRET_KEY');
        $payload = [
            'iss' => config('app.name'), // Issuer
            'iat' => time(), // Issued at
            'exp' => time() + 60 * 60,
            'userEmail' =>  $user->email// Expiration time (1 hour)
        ];

        return JWT::encode($payload, $key,  'HS256');
        

    }

    public static function verifyToken($token)
    {
       try{
        $key = env('JWT_SECRET_KEY');
        $decode = JWT::decode($token, new Key($key, 'HS256'));
        $userEmail = $decode->userEmail;
        return $userEmail;
       }
        catch(\Exception $e){
          return response()->json(['status' => 'UnAuthorized','error' => $e->getMessage()], 400);
        }
    }

    public static function createVerifyToken($user)
    {
        $key = env('JWT_SECRET_KEY');
        $payload = [
            'iss' => config('app.name'), // Issuer
            'iat' => time(), // Issued at
            'exp' => time() + 60 * 5, // Expiration time (m minutes)
            'userEmail' =>  $user->email
        ];

        return JWT::encode($payload, $key,  'HS256');
    }
        
}
