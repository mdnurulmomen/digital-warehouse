<?php

namespace App\Http\Resources\Web;

use App\Http\Resources\Web\WarehouseResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class WarehouseCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            
            'current_page' => $this->currentPage(),
            'data' => WarehouseResource::collection($this->collection),
            'first_page_url' => $this->url(1),
            // 'from' => $this->meta->from,
            'last_page' => $this->lastPage(),
            'last_page_url' => $this->url($this->lastPage()),
            'next_page_url' => $this->nextPageUrl(),
            // 'path' => $this->lastPage(),
            'per_page' => $this->perPage(),
            'prev_page_url' => $this->previousPageUrl(),
            'to' => $this->lastPage(),
            // 'total' => $this->total(),

        ];

    }
}
