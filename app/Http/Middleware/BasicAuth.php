<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BasicAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Hardcoded admin users
        $validUsers = [
            'admin_myrthe' => '19888888',
            'admin_matthew' => 'helloworld'
        ];
        
        // Get username and password from request
        $username = $request->getUser();
        $password = $request->getPassword();
        
        // Check if user is authenticated
        if (!$username || !$password || !isset($validUsers[$username]) || $validUsers[$username] !== $password) {
            // Request authentication
            return response('Unauthorized.', 401, [
                'WWW-Authenticate' => 'Basic realm="Admin Area"'
            ]);
        }
        
        return $next($request);
    }
}