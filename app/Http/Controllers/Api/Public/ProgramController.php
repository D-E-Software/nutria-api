<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Program;
use Illuminate\Http\JsonResponse;

class ProgramController extends Controller
{
    public function index(Clinic $clinic): JsonResponse
    {

        $programs = Program::query()
            ->where('clinic_id', $clinic->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get([
                'id',
                'title',
                'price',
                'currency',
                'duration',
                'description',
                'features',
                'is_featured',
                'is_active',
            ]);

        return response()->json($programs);
    }
}
