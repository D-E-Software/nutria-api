<?php

namespace Database\Factories;

use App\Models\Email;
use App\Models\Clinic;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailFactory extends Factory
{
    protected $model = Email::class;

    public function definition(): array
    {
        // Get a random clinic for valid foreign key
        $clinic = Clinic::inRandomOrder()->first() ?? Clinic::factory()->create();

        $type = $this->faker->randomElement(['program_delivery', 'contact_form']);
        $status = $this->faker->randomElement(['sent', 'failed']);

        // Set sent_at based on status
        $sentAt = $status === 'sent'
            ? $this->faker->dateTimeBetween('-1 year', 'now')
            : null;

        return [
            'clinic_id' => $clinic->id,
            // Nullable order_id - 70% chance to link to an order
            'order_id' => fake()->boolean(70)
                ? Order::where('clinic_id', $clinic->id)->inRandomOrder()->value('id')
                : null,
            'to_email' => $this->faker->safeEmail(),
            'subject' => $this->faker->sentence(6),
            'type' => $type,  // ✅ Valid ENUM
            'body' => $this->faker->optional(0.8)->paragraphs(3, true),
            'status' => $status,  // ✅ Valid ENUM
            'sent_at' => $sentAt,
        ];
    }

    public function programDelivery(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'program_delivery',
            'subject' => 'Your Program Access Details',
            'body' => $this->generateProgramDeliveryBody(),
        ]);
    }

    public function contactForm(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'contact_form',
            'subject' => 'New Customer Inquiry',
            'body' => $this->generateContactFormBody(),
        ]);
    }

    // 🔹 State: Sent Email
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
            'sent_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    // 🔹 State: Failed Email
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'sent_at' => null,
        ]);
    }

    // 🔹 State: Linked to an Order
    public function forOrder(): static
    {
        return $this->state(fn (array $attributes) => [
            'order_id' => Order::inRandomOrder()->value('id'),
        ]);
    }

    /**
     * Generate realistic program delivery email body
     */
    private function generateProgramDeliveryBody(): string
    {
        return <<<HTML
        <html>
        <body>
            <h2>Thank you for your purchase!</h2>
            <p>Your program access details are ready.</p>
            <p>Please log in to your dashboard to get started.</p>
            <p>Best regards,<br>Our Team</p>
        </body>
        </html>
        HTML;
    }

    /**
     * Generate realistic contact form email body
     */
    private function generateContactFormBody(): string
    {
        return <<<HTML
        <html>
        <body>
            <h2>New Customer Inquiry</h2>
            <p><strong>Message:</strong></p>
            <p>{$this->faker->paragraph(3)}</p>
            <p><strong>Contact:</strong> {$this->faker->safeEmail()}</p>
        </body>
        </html>
        HTML;
    }
}
