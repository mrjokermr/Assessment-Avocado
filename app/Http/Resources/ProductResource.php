<?php

namespace App\Http\Resources;

use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($this->product_image_id !== null) {
            $oProductImage = ProductImage::find($this->product_image_id);
            if(isset($oProductImage)) {
                return [
                    'id' => $this->id,
                    'name' => $this->name,
                    'description' => $this->description,
                    'price' => $this->price,
                    'image' => $oProductImage->path,
                ];
            }
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
        ];
    }
}
