<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariationParentResource extends JsonResource
{
    private $selectedChildVariation;

    public function subVariation($mostChildVariation)
    {
        $this->selectedChildVariation = $mostChildVariation;
        return $this;
    }

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
            'sub_variation' => isset($this->selectedChildVariation) ? $this->selectedChildVariation : NULL,
            'variation_childs' => $this->when($this->variationChilds->count(), $this->variationChilds),
        ];
    }
}
