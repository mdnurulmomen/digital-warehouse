<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Web\WarhouseStorageResource;
use App\Http\Resources\Web\WarhouseContainerResource;

class WarhouseResource extends JsonResource
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
            'code' => $this->code,
            'user_name' => $this->user_name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'site_map_preview' => $this->site_map_preview,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'warhouse_deal' => $this->warhouse_deal,
            'active' => $this->active,
            'warhouse_owner_id' => $this->warhouse_owner_id,
            'deleted_at' => $this->deleted_at,

            'owner' => $this->owner,
            'previews' => $this->previews,
            'feature' => $this->feature,
            'storages' => WarhouseStorageResource::collection($this->storages),
            'containers' => WarhouseContainerResource::collection($this->containers),
        ];
    }
}
