<?php namespace Lutfiyapr\KehadiranPegawai\Middleware;

use Closure;
use Lutfiyapr\KehadiranPegawai\Models\Users;

class Authenticate
{
    public function handle($request, Closure $next, $role = null)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak ditemukan. Silakan login terlebih dahulu.'
            ], 401);
        }

        $user = Users::where('api_token', $token)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid'
            ], 401);
        }

        // Cek role jika diperlukan
        if ($role && $user->role !== $role) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses'
            ], 403);
        }

        // Attach user ke request
        $request->attributes->add(['auth_user' => $user]);

        return $next($request);
    }
}
