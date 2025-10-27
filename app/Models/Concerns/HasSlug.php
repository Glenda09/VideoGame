<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::saving(function ($model): void {
            $source = $model->getSlugSourceValue();

            if (blank($model->slug) && $source) {
                $model->slug = Str::slug($source);
            }
        });
    }

    protected function getSlugSourceValue(): ?string
    {
        $attribute = property_exists($this, 'slugSource') ? $this->slugSource : 'name';

        return $this->{$attribute} ?? null;
    }
}
