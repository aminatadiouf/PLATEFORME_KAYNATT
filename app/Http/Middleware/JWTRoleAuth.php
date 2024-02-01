<?php

namespace App\Http\Middleware;



use auth;
use Closure;
use Exception;


use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\JWTAuth; // Importez la facade JWTAuth



class JWTRoleAuth extends BaseMiddleware
{ 
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    
    public function handle(Request $request, Closure $next, $acces = null)
    {
        try {
            (array)$token_acces = $this->auth->parseToken()->getClaim('acces');
            
        } 
        catch (JWTException $e) {
            return response()->json(['error'=>'Unuthenticated.'],403);
        }
            if($token_acces && $token_acces != $acces){
                return response()->json(['error'=>'vous n\'êtes pas autorisés à accéder à cette page.'],403);
            }
           
        
        return $next($request);
    }


//     public function handle(Request $request, Closure $next, $role = null)
// {
//     try {
//         $token_role = $this->auth->parseToken()->getClaim('role');
//     } catch (JWTException $e) {
//         return response()->json(['error' => 'Unauthenticated.'], 401);
//     }
//     if ($token_role && $token_role != $role) {
//         return response()->json(['error' => 'Unauthenticated.'], 401);
//     }

//     return $next($request);
// }


// public function handle(Request $request, Closure $next, $roles = null)
// {
//     try {
//         $token_roles = $this->auth->parseToken()->getClaim('role');
        
//         if (!in_array($roles, (array)$token_roles)) {
//             return response()->json(['error' => 'Unauthenticated.'], 401);
//         }

//     } catch (JWTException $e) {
//         return response()->json(['error' => 'Unauthenticated.'], 401);
//     }

//     return $next($request);
// }

}
