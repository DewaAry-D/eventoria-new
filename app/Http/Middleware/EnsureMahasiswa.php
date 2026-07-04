<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureMahasiswa
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->hasRole('mahasiswa')) {
            abort(403, 'Akses khusus mahasiswa.');
        }
        return $next($request);
    }
}