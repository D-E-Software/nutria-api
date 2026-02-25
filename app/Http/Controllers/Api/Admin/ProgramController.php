<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $programs = Program::where('clinic_id', $request->user()->clinic_id)
            ->orderBy('sort_order')
            ->get();

        return response()->json($programs);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'currency' => 'sometimes|string|size:3',
            'duration' => 'required|string|max:50',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'is_active' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer|min:0',
        ]);

        $program = Program::create([
            'clinic_id' => $request->user()->clinic_id,
            ...$data,
        ]);

        return response()->json($program, 201);
    }

    public function update(Request $request, Program $program): JsonResponse
    {
        if ($program->clinic_id !== $request->user()->clinic_id) {
            return response()->json(['message' => 'Yetkisiz.'], 403);
        }

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'currency' => 'sometimes|string|size:3',
            'duration' => 'sometimes|string|max:50',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'is_active' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer|min:0',
        ]);

        $program->update($data);

        return response()->json($program);
    }

    public function uploadPdf(Request $request, Program $program): JsonResponse
    {
        if ($program->clinic_id !== $request->user()->clinic_id) {
            return response()->json(['message' => 'Yetkisiz.'], 403);
        }

        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:10240',
        ]);

        $path = $request->file('pdf')->store(
            "programs/{$program->clinic_id}",
            'local'
        );

        $program->update(['pdf_path' => $path]);

        return response()->json(['pdf_path' => $path]);
    }
}
