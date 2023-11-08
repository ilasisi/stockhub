<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\OwnedByMyBranch;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    use HasFactory, HasUuids, OwnedByMyBranch;

    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function discount(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->discount_amount > 0) {
                    return $this->discount_amount;
                }

                if ($this->discount_percentage > 0) {
                    return round($this->discount_percentage / 100 * $this->grand_total, 2);
                }
            }
        );
    }

    public function vat(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->vat_amount > 0) {
                    return $this->vat_amount;
                }

                if ($this->vat_percentage > 0) {
                    return round($this->vat_percentage / 100 * $this->grand_total, 2);
                }
            }
        );
    }
}
