<?php

namespace App\Console\Commands;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Validation\Rules\Email;

class CreateUserCommand extends Command
{
    protected $signature = 'user:create {email?}
        {--name= : User full name}
        {--password= : Plain-text password}
        {--clinic= : Clinic ID, slug, or domain}
        {--role=owner : owner or staff}
        {--inactive : Create the user as inactive}
        {--force : Update the user if the email already exists}';

    protected $description = 'Create or update an admin user for a clinic';

    public function handle(): int
    {
        $email = $this->argument('email') ?: $this->ask('Email');
        $name = $this->option('name') ?: $this->ask('Full name');
        $password = $this->option('password') ?: $this->secret('Password');
        $role = strtolower($this->option('role') ?: 'owner');
        $clinicInput = $this->option('clinic');
        $isActive = ! $this->option('inactive');

        validator(
            [
                'email' => $email,
                'name' => $name,
                'password' => $password,
                'role' => $role,
            ],
            [
                'email' => ['required', new Email(), 'max:255'],
                'name' => ['required', 'string', 'max:255'],
                'password' => ['required', 'string', 'min:8'],
                'role' => ['required', 'in:owner,staff'],
            ],
        )->validate();

        $clinic = $this->resolveClinic($clinicInput);

        if (! $clinic) {
            $this->error('Clinic not found.');
            return self::FAILURE;
        }

        $user = User::where('email', $email)->first();

        if ($user && ! $this->option('force')) {
            $this->error('A user with that email already exists. Re-run with --force to update it.');
            return self::FAILURE;
        }

        if (! $user) {
            $user = new User();
        }

        $user->fill([
            'clinic_id' => $clinic->id,
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'is_active' => $isActive,
        ]);
        $user->save();

        $this->info($this->option('force') ? 'User saved.' : 'User created.');
        $this->table(
            ['ID', 'Clinic', 'Email', 'Role', 'Active'],
            [[
                $user->id,
                sprintf('%s (%s)', $clinic->name, $clinic->slug),
                $user->email,
                $user->role,
                $user->is_active ? 'yes' : 'no',
            ]]
        );

        return self::SUCCESS;
    }

    private function resolveClinic(mixed $clinicInput): ?Clinic
    {
        if ($clinicInput) {
            return Clinic::query()
                ->when(is_numeric($clinicInput), fn ($query) => $query->orWhere('id', (int) $clinicInput))
                ->orWhere('slug', $clinicInput)
                ->orWhere('domain', $clinicInput)
                ->first();
        }

        $clinics = Clinic::query()->orderBy('name')->get();

        if ($clinics->isEmpty()) {
            $this->error('No clinics found. Create a clinic before creating a user.');
            return null;
        }

        $selected = $this->choice(
            'Select clinic',
            $clinics->map(fn (Clinic $item) => sprintf('%d | %s | %s', $item->id, $item->slug, $item->domain))->all(),
        );

        $clinicId = (int) strtok($selected, ' ');

        return $clinics->firstWhere('id', $clinicId);
    }
}
