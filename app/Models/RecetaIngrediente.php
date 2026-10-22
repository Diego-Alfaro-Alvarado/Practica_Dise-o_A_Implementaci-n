<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RecetaIngrediente extends Pivot
{
    protected $table = 'receta_ingrediente';

    public $incrementing = false;

    protected function casts(): array
    {
        return [
            'cantidad' => 'decimal:2',
        ];
    }
}