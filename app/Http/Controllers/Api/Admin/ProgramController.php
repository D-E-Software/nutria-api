<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class ProgramController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $programs = Program::query()
            ->where('clinic_id', $user->clinic_id)
            ->orderBy('sort_order')
            ->get([
                'id',
                'title',
                'price',
                'currency',
                'duration',
                'description',
                'features',
                'is_active',
                'is_featured',
                'sort_order',
                'pdf_path',
                'created_at',
                'updated_at',
            ]);

        return response()->json($programs);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'title'                      => ['required', 'string', 'max:255'],
            'price'                      => ['required', 'numeric', 'min:0'],
            'currency'                   => ['required', 'in:EUR,TRY,GBP,USD'],
            'duration'                   => ['required', 'string', 'max:100'],
            'description'                => ['nullable', 'string'],
            'features'                   => ['nullable', 'array'],
            'features.*'                 => ['string'],
            'is_active'                  => ['boolean'],
            'is_featured'                => ['boolean'],
            'sort_order'                 => ['nullable', 'integer', 'min:0'],
            'translations'               => ['nullable', 'array'],
            'translations.*.title'       => ['nullable', 'string', 'max:255'],
            'translations.*.description' => ['nullable', 'string'],
            'translations.*.features'    => ['nullable', 'array'],
            'translations.*.features.*'  => ['string'],
        ]);

        $program = Program::create([
            'clinic_id' => $user->clinic_id, // ← was $clinic->id (undefined)
            ...$data,
        ]);

        return response()->json($program, 201);
    }

    public function update(Request $request, Program $program): JsonResponse
    {
        $user = $request->user();

        if ((int) $program->clinic_id !== (int) $user->clinic_id) {
            return response()->json(['message' => 'Yetkisiz.'], 403);
        }

        $data = $request->validate([
            'title'                      => ['sometimes', 'string', 'max:255'],
            'price'                      => ['sometimes', 'numeric', 'min:0'],
            'currency'                   => ['sometimes', 'in:EUR,TRY,GBP,USD'],
            'duration'                   => ['sometimes', 'string', 'max:50'],
            'description'                => ['nullable', 'string'],
            'features'                   => ['nullable', 'array'],
            'features.*'                 => ['string'],
            'is_active'                  => ['sometimes', 'boolean'],
            'is_featured'                => ['sometimes', 'boolean'],
            'sort_order'                 => ['sometimes', 'integer', 'min:0'],
            'translations'               => ['nullable', 'array'],       // ← added
            'translations.*.title'       => ['nullable', 'string', 'max:255'],
            'translations.*.description' => ['nullable', 'string'],
            'translations.*.features'    => ['nullable', 'array'],
            'translations.*.features.*'  => ['string'],
        ]);

        $program->update($data);

        return response()->json($program);
    }


    public function destroy(Program $program): JsonResponse
    {
        $user = request()->user();

        if ((int) $program->clinic_id !== (int) $user->clinic_id) {
            return response()->json(['message' => 'Yetkisiz.'], 403);
        }

        if ($program->pdf_path) {
            Storage::disk('public')->delete($program->pdf_path);
        }

        $program->delete();

        return response()->json(null, 204);
    }

    public function uploadPdf(Request $request, Program $program): JsonResponse
    {
        $user = $request->user();

        if ((int) $program->clinic_id !== (int) $user->clinic_id) {
            return response()->json(['message' => 'Yetkisiz.'], 403);
        }

        $request->validate([
            'pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        // Use 'public' disk if you need to download/view from frontend/admin easily.
        $path = $request->file('pdf')->store(
            "programs/{$program->clinic_id}",
            'public'
        );

        $program->update(['pdf_path' => $path]);

        return response()->json(['pdf_path' => $path]);
    }
}
