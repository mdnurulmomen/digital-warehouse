<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantWarehouseResource extends JsonResource
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
            
            'emptyContainers' => 
                
                $this->containerStatuses()->whereHas('deals', function ($query1) {
                    $query1->where('engaged', 0.0)->whereHas('validities', function ($query2) {
                        $query2->where('expired_at', '>', now());
                    });
                })
                ->get(),
            
            

            'emptyShelfContainers' => 
                
                $this->containerStatuses()->whereHas('deals', function ($query1) {
                    $query1->where(function ($query2) {
                        $query2->where('engaged', 0.0)->orWhere('engaged', 0.5);
                    })->whereHas('validities', function ($query3) {
                        $query3->where('expired_at', '>', now());
                    });
                })
                ->whereHas('containerShelfStatuses', function ($query1) {
                    $query1->whereHas('deals', function ($query2) {
                        $query2->where('engaged', 0.0)->whereHas('validities', function ($query3) {
                            $query3->where('expired_at', '>', now());
                        });
                    });
                })
                ->with([
                    'containerShelfStatuses' => 
                        function ($query1) {
                            $query1->whereHas('deals', function ($query2) {
                                $query2->where('engaged', 0.0)->whereHas('validities', function ($query3) {
                                    $query3->where('expired_at', '>', now());
                                });
                            });
                        }
                ])
                ->get(),

           

            'emptyUnitContainers' => 
                
                $this->containerStatuses()->whereHas('deals', function ($query1) {
                    $query1->where(function ($query2) {
                        $query2->where('engaged', 0.0)->orWhere('engaged', 0.5);
                    })->whereHas('validities', function ($query3) {
                        $query3->where('expired_at', '>', now());
                    });
                })
                ->whereHas('containerShelfUnitStatuses', function ($query1) {
                    $query1->whereHas('deals', function ($query2) {
                        $query2->where('engaged', 0.0)->whereHas('validities', function ($query3) {
                            $query3->where('expired_at', '>', now());
                        });
                    });
                })
                ->with([
                    'containerShelfStatuses' => 
                        function ($query) {
                            $query->whereHas('deals', function ($query1) {
                                $query1->where(function ($query2) {
                                    $query2->where('engaged', 0.0)->orWhere('engaged', 0.5);
                                })->whereHas('validities', function ($query2) {
                                    $query2->where('expired_at', '>', now());
                                });
                            })
                            ->whereHas('containerShelfUnitStatuses', function ($query1) {
                                $query1->whereHas('deals', function ($query2) {
                                    $query2->where('engaged', 0.0)->whereHas('validities', function ($query2) {
                                        $query2->where('expired_at', '>', now());
                                    });
                                });
                            });
                        },
    
                    'containerShelfStatuses.containerShelfUnitStatuses' => 
                        function ($query1) {
                            $query1->whereHas('deals', function ($query2) {
                                $query2->where('engaged', 0.0)->whereHas('validities', function ($query2) {
                                    $query2->where('expired_at', '>', now());
                                });
                            });
                        },
                ])
                ->get()
         

        ];
    }
}
