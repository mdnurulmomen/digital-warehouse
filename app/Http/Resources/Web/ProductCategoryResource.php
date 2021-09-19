<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'deleted_at' => $this->deleted_at,
            'products_count' => $this->products_count,
            
            'parent' => $this->when($this->relationLoaded('parent'), new ProductCategoryResource($this->parent ? $this->parent->loadMissing('parent') : NULL)),

            'childs' => $this->when($this->relationLoaded('childs'), $this->childs ? ProductCategoryResource::collection($this->childs->loadMissing('childs')) : []),
            
            // 'childs' => $this->whenLoaded('nestedChilds'),
        ];
    }
}
