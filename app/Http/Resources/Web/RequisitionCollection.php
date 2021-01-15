<?php

namespace App\Http\Resources\Web;

use App\Http\Resources\Web\RequisitionResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RequisitionCollection extends ResourceCollection
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
            'data' => RequisitionResource::collection($this->collection),
            'first_page_url' => url('api/requisitions').'/'.$this->perPage().'?page=1',
            'from' => $this->firstItem(),
            'last_page' => $this->lastPage(),
            'last_page_url' => url('api/requisitions').'/'.$this->perPage().'?page='.$this->lastPage(),
            'next_page_url' => $this->nextPageUrl(),
            'path' => url('api/requisitions').'/'.$this->perPage(),
            'per_page' => $this->perPage(),
            'prev_page_url' => $this->previousPageUrl(),
            'to' => $this->lastItem(),
            'total' => $this->total(),
        ];
    }
}
