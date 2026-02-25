<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Order;
use App\Models\Program;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request, string $clinicSlug): JsonResponse
    {
        $clinic = Clinic::where('slug', $clinicSlug)
            ->where('is_active', true)
            ->firstOrFail();

        $data = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:30',
        ]);

        $program = Program::where('id', $data['program_id'])
            ->where('clinic_id', $clinic->id)
            ->where('is_active', true)
            ->firstOrFail();

        $order = Order::create([
            'clinic_id' => $clinic->id,
            'program_id' => $program->id,
            'order_ref' => strtoupper(Str::random(8)),
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'] ?? null,
            'amount' => $program->price,
            'currency' => $program->currency,
            'status' => 'pending',
        ]);

        // TODO: Generate bank payment URL and redirect
        return response()->json([
            'order' => $order,
            'payment_url' => null,
        ], 201);
    }

    public function paymentCallback(Request $request, Order $order): JsonResponse
    {
        // TODO: Verify callback signature from bank

        $status = $request->input('status') === 'approved' ? 'completed' : 'failed';

        $order->update([
            'status' => $status,
            'gateway_ref' => $request->input('transaction_id'),
            'gateway_status' => $request->input('status'),
            'paid_at' => $status === 'completed' ? now() : null,
        ]);

        // TODO: If completed, send PDF email

        return response()->json(['status' => $order->status]);
    }
}
