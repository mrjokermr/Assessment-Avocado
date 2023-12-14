<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    public const PAGINATION_DEFAULT = 10;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'product_image_id',
    ];

    public static $rules = [
        'name' => 'required',
        'price' => 'required|Decimal:2',
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240'
    ];

    public static $updateRules = [
        'price' => 'required|Decimal:2',
    ];

    public function image(): HasOne {
        return $this->hasOne(ProductImage::class);
    }

    /**
     * Set the image for this product
     *
     * @param $file image file that should be jpeg, jpg or png with a max size of 10mb (Use Product::$rules for validation)
     *
     * @returns ProductImage
     */
    public function setImage($file): ProductImage {
        $dirPath = public_path('ProductImages');
        $uniqueFileName = time().'.'.$file->extension();

        $file->move($dirPath,$uniqueFileName);
        $oNewProductImage = ProductImage::create([
            'name' => $this->name . ' image',
            'path' => 'ProductImages/' . $uniqueFileName,
            'product_id' => $this->id,
        ]);

        $this->product_image_id = $oNewProductImage->id;
        $this->save();

        return $oNewProductImage;
    }

}
