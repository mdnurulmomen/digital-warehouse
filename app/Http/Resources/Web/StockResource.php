<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
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
            'invoice_no' => $this->invoice_no,
            'keeper' => new StockKeeperResource($this->keeper),
            'has_approval' => $this->has_approval,
            'approver' => $this->when($this->has_approval, new StockApproverResource($this->approver)),    // -1 / 1
            'warehouse_id' => $this->warehouse_id,
            'warehouse' => new StockWarehouseResource($this->warehouse),
            'merchant' => /*$this->stocks->first()->merchantProduct->merchant*/ $this->merchant,
            // 'addresses' => new ProductAddressCollection($this->addresses),
            'products' => StockProductResource::collection($this->stocks),
            'created_at' => $this->created_at->format('Y.m.d H:i:s'),
            'updated_at' => $this->updated_at->format('Y.m.d H:i:s')
        ];
    }
}
