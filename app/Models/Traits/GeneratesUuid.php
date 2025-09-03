<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait GeneratesUuid
{
	protected static function bootGeneratesUuid(): void
	{
		static::creating(function ($model) {
			if (empty($model->{$model->getKeyName()})) {
				$model->{$model->getKeyName()} = (string) Str::uuid();
			}
		});
	}
}
