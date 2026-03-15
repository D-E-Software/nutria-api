<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Program;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        // Get existing or create clinic/program for valid foreign keys
        $clinic = Clinic::inRandomOrder()->first() ?? Clinic::factory()->create();
        $program = Program::where('clinic_id', $clinic->id)
            ->inRandomOrder()
            ->first() ?? Program::factory()->create(['clinic_id' => $clinic->id]);

        // ✅ Use ONLY valid ENUM values from your migration
        $status = $this->faker->randomElement(['pending', 'completed', 'failed', 'refunded']);

        // Set paid_at only for completed/refunded orders
        $paidAt = null;
        if ($status === 'completed' || $status === 'refunded') {
            $paidAt = $this->faker->dateTimeBetween('-1 year', 'now');
        }

        // Set refunded_at only for refunded orders
        $refundedAt = null;
        if ($status === 'refunded' && $paidAt) {
            $refundedAt = $this->faker->dateTimeBetween($paidAt, 'now');
        }

        return [
            'clinic_id' => $clinic->id,
            'program_id' => $program->id,
            'order_ref' => 'ORD-' . strtoupper($this->faker->unique()->bothify('??###???')),
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->unique()->safeEmail(),
            'customer_phone' => $this->faker->optional()->phoneNumber(),
            'amount' => $program->price,
            'currency' => $program->currency,
            'status' => $status,
            'gateway_ref' => $this->faker->optional()->uuid(),
            'gateway_status' => $this->faker->randomElement(['pending', 'success', 'failed', 'cancelled']),
            'paid_at' => $paidAt,
            'refunded_at' => $refundedAt,
        ];
    }

    // 🔹 State: Completed Order
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'paid_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'refunded_at' => null,
            'gateway_status' => 'success',
        ]);
    }

    // 🔹 State: Failed Order (NOT cancelled!)
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'paid_at' => null,
            'refunded_at' => null,
            'gateway_status' => 'failed',
        ]);
    }

    // 🔹 State: Pending Order
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'paid_at' => null,
            'refunded_at' => null,
            'gateway_status' => 'pending',
        ]);
    }

    // 🔹 State: Refunded Order
    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'refunded',
            'paid_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'refunded_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'gateway_status' => 'success',
        ]);
    }
}
