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
        $origin = $request->headers->get('Origin');
        $host = $origin
            ? strtolower(parse_url($origin, PHP_URL_HOST))
            : strtolower($request->getHost());

        $clinic = Clinic::where('domain', $host)->first();

        if (!$clinic && $fallbackSlug = config('app.default_clinic_slug')) {
            $clinic = Clinic::where('slug', $fallbackSlug)->first();
        }

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
