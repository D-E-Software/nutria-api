<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Email;
use App\Models\Clinic;
use App\Models\Program;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        if (Clinic::count() === 0 || Program::count() === 0) {
            $this->command->warn('⚠️ Please run ClinicSeeder and ProgramSeeder first.');
            return;
        }

        $this->command->info('📦 Seeding orders...');

        // 🔹 Create random orders
        Order::factory(20)->create();

        // 🔹 Create specific status orders (using correct ENUM values)
        Order::factory()->completed()->count(5)->create();
        Order::factory()->pending()->count(3)->create();
        Order::factory()->failed()->count(2)->create();      // ✅ Changed from cancelled()
        Order::factory()->refunded()->count(2)->create();

        // 🔹 Create orders with emails
        $this->createOrdersWithEmails();

        $this->command->info('✅ ' . Order::count() . ' orders created!');
    }

    private function createOrdersWithEmails(): void
    {
        $this->command->info('📧 Creating order emails...');

        $orders = Order::where('status', 'completed')
            ->with(['clinic', 'program'])
            ->limit(5)
            ->get();

        foreach ($orders as $order) {
            Email::create([
                'clinic_id' => $order->clinic_id,
                'order_id' => $order->id,
                'to_email' => $order->customer_email,
                'subject' => 'Your Program Access - ' . $order->order_ref,
                'type' => 'program_delivery',
                'body' => $this->getProgramDeliveryBody($order),
                'status' => 'sent',
                'sent_at' => $order->paid_at ?? now(),
            ]);
        }

        $this->command->info('✅ Emails linked to orders!');
    }

    private function getProgramDeliveryBody(Order $order): string
    {
        // Extract complex expressions to avoid heredoc interpolation issues
        $duration = $order->program->duration ?? 'N/A';
        $clinicName = $order->clinic->name ?? 'Our Team';

        return <<<HTML
        <html>
        <body>
            <h2>Thank you, {$order->customer_name}!</h2>
            <p>Order <strong>{$order->order_ref}</strong> confirmed.</p>
            <h3>Program: {$order->program->title}</h3>
            <p>Price: {$order->amount} {$order->currency}</p>
            <p>Duration: {$duration}</p>
            <p>Best regards,<br>{$clinicName}</p>
        </body>
        </html>
        HTML;
    }
}
