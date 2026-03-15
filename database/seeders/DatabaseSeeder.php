<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BeWellSeeder::class,
            OrderSeeder::class,
            EmailSeeder::class
        ]);


        $this->command->info('🎉 Database seeding completed!');
        $this->command->table(
            ['Table', 'Count'],
            [
                ['Clinics', \App\Models\Clinic::count()],
                ['Programs', \App\Models\Program::count()],
                ['Orders', \App\Models\Order::count()],
                ['Emails', \App\Models\Email::count()],
            ]
        );

    }
}
