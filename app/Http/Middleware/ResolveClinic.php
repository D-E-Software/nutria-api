<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Clinic;

class ResolveClinic
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = strtolower($request->getHost());

        $clinic = Clinic::where('domain', $host)->first();

        if (!$clinic) {
            return response()->json([
                'message' => 'Clinic not found for domain',
                'host' => $host,
            ], 404);
        }

        $request->attributes->set('clinic', $clinic);

        app()->instance(Clinic::class, $clinic);

        return $next($request);
    }
}
