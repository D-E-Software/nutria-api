<?php

namespace Database\Seeders;

use App\Models\Email;
use App\Models\Order;
use App\Models\Clinic;
use Illuminate\Database\Seeder;

class EmailSeeder extends Seeder
{
    public function run(): void
    {
        if (Clinic::count() === 0) {
            $this->command->warn('⚠️ No clinics found. Run ClinicSeeder first.');
            return;
        }

        $this->command->info('📧 Seeding emails...');

        Email::factory(30)->create();

        Email::factory()->programDelivery()->sent()->count(10)->create();
        Email::factory()->contactForm()->count(5)->create();
        Email::factory()->failed()->count(3)->create();

        $this->linkEmailsToOrders();

        $this->command->info('✅ ' . Email::count() . ' emails created!');
    }

    private function linkEmailsToOrders(): void
    {
        $orders = Order::where('status', 'completed')
            ->with(['clinic', 'program'])
            ->limit(5)
            ->get();

        foreach ($orders as $order) {
            Email::factory()->programDelivery()->sent()->create([
                'clinic_id' => $order->clinic_id,
                'order_id' => $order->id,
                'to_email' => $order->customer_email,
                'subject' => 'Your Program: ' . $order->program->title,
            ]);
        }
    }
}
