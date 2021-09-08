<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantWarehouseResource extends JsonResource
{
    private static $merchant;

    //I made custom function that returns collection type
    public static function customCollection($resource, $merchant) : \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        //you can add as many params as you want.
        self::$merchant = $merchant;
        return parent::collection($resource);
    }

    //I made custom function that returns resource type
    public function customResource($merchant)
    {
        //you can add as many params as you want.
        self::$merchant = $merchant;
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
            
            'emptyContainers' => 
                
                $this->containerStatuses()->where('occupied', 0.0)->whereHas('deals', function ($query1) {
                    $query1->whereHas('deal', function ($query2) {
                        $query2->where('merchant_id', self::$merchant);
                    })
                    ->whereHas('validities', function ($query3) {
                        $query3->where('expired_at', '>', now());
                    });
                })
                ->get(),

            'emptyShelfContainers' => 
                
                $this->containerStatuses()->whereHas('containerShelfStatuses', function ($query1) {
                    $query1->where('occupied', 0.0)->whereHas('deals', function ($query2) {
                        $query2->whereHas('deal', function ($query3) {
                            $query3->where('merchant_id', self::$merchant);
                        })
                        ->whereHas('validities', function ($query4) {
                            $query4->where('expired_at', '>', now());
                        });
                    });
                })
                ->with([
                    'containerShelfStatuses' => 
                        function ($query1) {
                            $query1->where('occupied', 0.0)->whereHas('deals', function ($query2) {
                                $query2->whereHas('deal', function ($query3) {
                                    $query3->where('merchant_id', self::$merchant);
                                })
                                ->whereHas('validities', function ($query4) {
                                    $query4->where('expired_at', '>', now());
                                });
                            });
                        }
                ])
                ->get(),   

            'emptyUnitContainers' => 
                
                $this->containerStatuses()->whereHas('containerShelfUnitStatuses', function ($query1) {
                    $query1->where('warehouse_container_shelf_unit_statuses.occupied', 0.0)->whereHas('deals', function ($query2) {
                        $query2->whereHas('deal', function ($query3) {
                            $query3->where('merchant_id', self::$merchant);
                        })
                        ->whereHas('validities', function ($query4) {
                            $query4->where('expired_at', '>', now());
                        });
                    });
                })
                ->with([
                    'containerShelfStatuses' => 
                        function ($query) {
                            $query->whereHas('containerShelfUnitStatuses', function ($query1) {
                                $query1->where('warehouse_container_shelf_unit_statuses.occupied', 0.0)->whereHas('deals', function ($query2) {
                                    $query2->whereHas('deal', function ($query3) {
                                        $query3->where('merchant_id', self::$merchant);
                                    })
                                    ->whereHas('validities', function ($query4) {
                                        $query4->where('expired_at', '>', now());
                                    });
                                });
                            });
                        },
    
                    'containerShelfStatuses.containerShelfUnitStatuses' => 
                        function ($query1) {
                            $query1->where('occupied', 0.0)->whereHas('deals', function ($query2) {
                                $query2->whereHas('deal', function ($query3) {
                                    $query3->where('merchant_id', self::$merchant);
                                })
                                ->whereHas('validities', function ($query4) {
                                    $query4->where('expired_at', '>', now());
                                });
                            });
                        },
                ])
                ->get()
        
        ];
    }
}
