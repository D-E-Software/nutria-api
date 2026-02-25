<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Order::where('clinic_id', $request->user()->clinic_id)
            ->with('program:id,title')
            ->orderByDesc('created_at');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(20);

        return response()->json($orders);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        if ($order->clinic_id !== $request->user()->clinic_id) {
            return response()->json(['message' => 'Yetkisiz.'], 403);
        }

        $order->load(['program:id,title,duration', 'emails']);

        return response()->json($order);
    }
}
