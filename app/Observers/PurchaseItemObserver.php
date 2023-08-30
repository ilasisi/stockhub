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
        Product::query()
            ->find($purchaseItem->product_id)
            ->decrement('available_quantity', $purchaseItem->quantity);
    }
}
