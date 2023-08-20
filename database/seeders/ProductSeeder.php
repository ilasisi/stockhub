<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Closure;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Helper\ProgressBar;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->warn(PHP_EOL . 'Creating categories...');

        $categories = $this->withProgressBar(10, fn () => Category::factory(1)->create());

        $this->command->info('Categories created successfully.');

        $this->withProgressBar(50, fn () => Product::factory(1)
            ->create([
                'category_id' => $categories->pluck('id')->random(),
                'sku' => str(str($categories->pluck('name')->random())->substr(0, 3) . '-' . str()->random(9))->upper(),
            ]));

        $this->command->info('Products created successfully.');
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
