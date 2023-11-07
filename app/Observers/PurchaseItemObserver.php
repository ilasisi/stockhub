<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Product;
use App\Models\PurchaseItem;

class PurchaseItemObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    public function created(PurchaseItem $purchaseItem): void
    {
        $product = Product::query()
            ->find($purchaseItem->product_id);

        $product->decrement('available_quantity', $purchaseItem->quantity);

        $product->increment('items_sold', $purchaseItem->quantity);
    }
}
