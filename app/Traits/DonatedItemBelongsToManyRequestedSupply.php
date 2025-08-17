<?php

namespace App\Traits;

use App\Models\RequestedSupply;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait DonatedItemBelongsToManyRequestedSupply {
    public function requestedSupplies() {
        return $this->requestedSuppliesRelation;
    }

    public function requestedSuppliesRelation(): BelongsToMany {
        return $this->belongsToMany(
            RequestedSupply::class,
            'donated_supplies',
            'donated_item_id',
            'requested_supply_id'
        )->withPivot([
            'quantity'
        ])->withTimestamps();
    }

    public function alreadyHasSupply(RequestedSupply $model) {
        return $this->requestedSupplies()->contains($model);
    }

    public function attachSupply(RequestedSupply $supply, int $amount = 1) {
        // update donated item qty
        $totalQuantity = $this->quantity() + $amount;
        $this->update([
            'quantity' => $totalQuantity
        ]);

        if (!$this->alreadyHasSupply($supply)) {
            $this->requestedSuppliesRelation()->attach($supply->id(), [
                'quantity' => $amount
            ]);
            return $this->save();
        }

        $supplyQuantity = $this->requestedSupplies()
            ->where('requested_supply_id', $supply->id())
            ->first()
            ->pivot
            ->quantity;
        $this->requestedSuppliesRelation()->updateExistingPivot($supply->id(), [
            'quantity' => $supplyQuantity + $amount
        ]);
        return $this->save();
    }
}
