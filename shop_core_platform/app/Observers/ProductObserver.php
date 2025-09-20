<?php

namespace App\Observers;

use App\Models\Vendor\Product;
use Illuminate\Support\Facades\Storage;

class ProductObserver
{

    /**

     * Handle the Product "updated" event.

     */

    public function updated(Product $product): void
    {

        // Only proceed if photos were changed
        if ($product->isDirty('photos')) {
            $oldPhotos = $product->getOriginal('photos') ?? [];
            $newPhotos = $product->photos ?? [];

            // Determine which photos were removed
            $removedPhotos = array_diff($oldPhotos, $newPhotos);

            foreach ($removedPhotos as $photoPath) {
                if (Storage::disk('public')->exists($photoPath)) {
                    Storage::disk('public')->delete($photoPath);
                    logger("Deleted removed photo: {$photoPath}");
                }
            }
        }

    }

}
