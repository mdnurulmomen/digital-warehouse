<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'company_name' => $this->company_name,
            'user_name' => $this->user_name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'active' => $this->active,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'products_count' => $this->products_count,
            'space_deals_count' => $this->space_deals_count,
            'profile_preview' => $this->profilePreview,
            'support_deal' => new MerchantSupportDealResource($this->supportDeal)        // support deal
            
        ];
    }
}
