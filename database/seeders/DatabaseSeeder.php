<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->warn(PHP_EOL . 'Seeding data...');

        $permissions = Permission::all();

        $user = User::factory()->state([
            'name' => 'Ibrahim Lasisi',
            'email' => 'ilasisi90@gmail.com',
        ]);

        $branch = Branch::factory()
            ->has($user)
            ->create([
                'name' => 'Bloomy Dev',
                'slug' => 'bloomy-dev',
            ]);

        // DB::table('model_has_roles')->insert([
        //     'branch_id' => 1,
        //     'model_id' => $user->id,
        //     'model_type' => User::class,
        //     'role_id' => $role->id
        // ]);

        $role = Role::create([
            'name' => 'Super Admin',
            'branch_id' => $branch->id,
        ]);

        $user->make()->assignRole($role);

        $role->syncPermissions($permissions);

        $this->command->info('Data seeded successfully.');
    }
}
