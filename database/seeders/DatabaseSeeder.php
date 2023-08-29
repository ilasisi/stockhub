<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->warn(PHP_EOL . 'Seeding data...');

        User::factory()
            ->has(Branch::factory()->state(['name' => 'Bloomy Dev', 'slug' => 'bloomy-dev']))
            ->create([
                'name' => 'Ibrahim Lasisi',
                'email' => 'ilasisi90@gmail.com',
            ]);

        $this->command->info('Data seeded successfully.');
    }
}
