<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAdminDpm
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin_dpm')) {
            abort(403, 'Akses ditolak. Wilayah khusus Admin DPM.');
        }
        return $next($request);
    }
}