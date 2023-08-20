<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\Customer;
use App\Models\PaymentType;
use App\Models\Product;
use App\Models\User;
use Closure;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Helper\ProgressBar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->warn(PHP_EOL . 'Creating user...');

        User::factory()->create([
            'name' => 'Ibrahim Lasisi',
            'email' => 'ilasisi90@gmail.com',
        ]);

        $this->command->info('User created successfully.');

        $this->command->warn(PHP_EOL . 'Creating categories...');

        $categories = $this->withProgressBar(10, fn () => Category::factory(1)->create());

        $this->command->info('Categories created successfully.');

        $this->command->warn(PHP_EOL . 'Creating products...');

        $this->withProgressBar(50, fn () => Product::factory(1)
            ->create([
                'category_id' => $categories->pluck('id')->random(),
                'sku' => str(str($categories->pluck('name')->random())->substr(0, 3) . '-' . str()->random(9))->upper(),
            ]));

        $this->command->info('Products created successfully.');

        $this->command->warn(PHP_EOL . 'Creating customers...');

        $this->withProgressBar(5, fn () => Customer::factory(1)->create());

        $this->command->info('Customers created successfully.');

        $this->command->warn(PHP_EOL . 'Creating payment type...');

        PaymentType::factory(1)->create();

        $this->command->info('Payment type created successfully.');
    }

    protected function withProgressBar(int $amount, Closure $createCollectionOfOne): Collection
    {
        $progressBar = new ProgressBar($this->command->getOutput(), $amount);

        $progressBar->start();

        $items = new Collection();

        foreach (range(1, $amount) as $i) {
            $items = $items->merge(
                $createCollectionOfOne()
            );
            $progressBar->advance();
        }

        $progressBar->finish();

        $this->command->getOutput()->writeln('');

        return $items;
    }
}
