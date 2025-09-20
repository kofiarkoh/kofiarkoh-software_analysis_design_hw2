<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait Sluggable
{
    /**
     * Generate a unique slug for $modelClass using $sourceText.
     * Uses a random base-62 lower-case suffix only when there's a collision.
     *
     * @param class-string<Model> $modelClass
     * @param string $sourceText
     * @param string $column
     * @param int $suffixLen
     * @return string
     */
    public function generateUniqueSlug(string $modelClass, string $sourceText, string $column = 'slug', int $suffixLen = 6): string
    {
        $base = Str::slug($sourceText);

        // If the name can't be slugged (e.g., only symbols), fall back to random base.
        if ($base === '') {
            $base = Str::lower(Str::random($suffixLen));
        }

        $slug = $base;

        // Only append a short random suffix if a collision exists.
        while ($modelClass::where($column, $slug)->exists()) {
            $slug = $base . '-' . Str::lower(Str::random($suffixLen));
        }

        return $slug;
    }
}
