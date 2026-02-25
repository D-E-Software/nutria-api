<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\JsonResponse;

class ProgramController extends Controller
{
    public function index(string $clinicSlug): JsonResponse
    {
        $programs = Program::whereHas('clinic', fn ($q) => $q->where('slug', $clinicSlug))
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'title', 'price', 'currency', 'duration', 'description', 'features', 'is_featured']);

        return response()->json($programs);
    }
}
