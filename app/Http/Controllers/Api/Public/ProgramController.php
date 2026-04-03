<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Program;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index(Request $request, Clinic $clinic): JsonResponse
    {
        $locale = $request->string('locale')->toString() ?: 'tr';

        $programs = Program::query()
            ->where('clinic_id', $clinic->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $payload = $programs->map(function (Program $program) use ($locale) {
            return [
                'id' => $program->id,
                'title' => $program->translate('title', $locale),
                'price' => $program->price,
                'currency' => $program->currency,
                'duration' => $program->duration,
                'description' => $program->translate('description', $locale),
                'features' => $program->translate('features', $locale) ?? [],
                'pdf_path' => $program->pdf_path,
                'is_featured' => $program->is_featured,
                'is_active' => $program->is_active,
            ];
        })->values();

        return response()->json($payload);
    }
}
