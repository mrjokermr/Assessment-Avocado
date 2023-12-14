<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'path',
        'product_id',
    ];

    public static $rules = [
        'name' => 'required|string',
        'path' => 'required',
        'product_id' => 'required|Integer',
    ];

    public function product(): BelongsTo {
        return $this->belongsTo(ProductImage::class);
    }
}
