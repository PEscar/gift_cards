<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
	const TIPO_GIFTCARD = 1;
    const TIPO_NORMAL = 2;

    protected $fillable = ['nombre', 'descripcion', 'sku'];

    public function scopeGiftCards($query)
    {
        return $query->where('tipo_producto', self::TIPO_GIFTCARD);
    }

    public function scopeVisibles($query)
    {
        return $query->where('visible', 1);
    }
}
