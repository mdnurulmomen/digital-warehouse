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
            'is_perishable' => $this->is_perishable,
            'deleted_at' => $this->deleted_at,
            'products_count' => $this->products_count,
            'parent_category_id' => $this->when($this->parent_category_id, $this->parent_category_id),
            
            'parent' => new ProductCategoryResource($this->whenLoaded('parent')),

            'childs' => $this->when($this->relationLoaded('childs'), $this->childs ? ProductCategoryResource::collection($this->childs->loadMissing('childs')) : []),
            
            // 'childs' => $this->whenLoaded('nestedChilds'),
        ];
    }
}
