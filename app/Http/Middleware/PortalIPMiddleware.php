<?php

namespace App\Http\Middleware;

use App\Models\AttendancePortal;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PortalIPMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public $baseIp = [];

    public function handle(Request $request, Closure $next): Response
    {
        $get = AttendancePortal::first();
        $ip = $request->cookie('portal_token');
        if ($get) {
            if ($ip == $get->portal_token) {
                return $next($request);
            }
        }
        return new JsonResponse([
            'success' => false,
            'message' => 'Access denied.',
        ], JsonResponse::HTTP_FORBIDDEN);
    }
}
