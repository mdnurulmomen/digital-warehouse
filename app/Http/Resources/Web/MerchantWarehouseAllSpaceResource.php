<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantWarehouseAllSpaceResource extends JsonResource
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

            'id' => $this->id,      // warehouse id
            'name' => $this->name,  // warehouse name
            
            'emptyContainers' => 
                
                $this->containerStatuses()->whereHas('deals', function ($query1) {
                    $query1->whereHas('deal', function ($query2) {
                        $query2->where('merchant_id', self::$merchant)
                        ->whereHas('rents', function ($query3) {
                            $query3->whereDate('date_to', '>=', today());
                        });
                    });
                })
                ->with(['containerShelfStatuses', 'containerShelfStatuses.containerShelfUnitStatuses'])
                ->get(),

            'emptyShelfContainers' => 
                
                $this->containerStatuses()->where(function ($query) {
                    $query->whereHas('deals', function ($query1) {
                        $query1->whereHas('deal', function ($query2) {
                            $query2->where('merchant_id', self::$merchant)
                            ->whereHas('rents', function ($query3) {
                                $query3->whereDate('date_to', '>=', today());
                            });
                        });
                    })
                    ->has('containerShelfStatuses');
                })
                ->orWhereHas('containerShelfStatuses', function ($query1) {
                    $query1->whereHas('deals', function ($query2) {
                        $query2->whereHas('deal', function ($query3) {
                            $query3->where('merchant_id', self::$merchant)
                            ->whereHas('rents', function ($query4) {
                                $query4->whereDate('date_to', '>=', today());
                            });
                        });
                    });
                })
                ->with([
                    'containerShelfStatuses' => 
                        function ($query1) {
                            $query1->where(function ($query) {
                                $query->whereHas('parentContainer.deals', function ($query1) {
                                    $query1->whereHas('deal', function ($query2) {
                                        $query2->where('merchant_id', self::$merchant)
                                        ->whereHas('rents', function ($query3) {
                                            $query3->whereDate('date_to', '>=', today());
                                        });
                                    });
                                })
                                ->orWhereHas('deals', function ($query2) {
                                    $query2->whereHas('deal', function ($query3) {
                                        $query3->where('merchant_id', self::$merchant)
                                        ->whereHas('rents', function ($query4) {
                                            $query4->whereDate('date_to', '>=', today());
                                        });
                                    });
                                });
                            });
                        },
                        
                    'containerShelfStatuses.containerShelfUnitStatuses' => 
                        function ($query1) {
                            $query1->where(function ($query) {
                                $query->whereHas('parentShelf.parentContainer.deals', function ($query1) {
                                    $query1->whereHas('deal', function ($query2) {
                                        $query2->where('merchant_id', self::$merchant)
                                        ->whereHas('rents', function ($query3) {
                                            $query3->whereDate('date_to', '>=', today());
                                        });
                                    });
                                })
                                ->orWhereHas('parentShelf.deals', function ($query2) {
                                    $query2->whereHas('deal', function ($query3) {
                                        $query3->where('merchant_id', self::$merchant)
                                        ->whereHas('rents', function ($query4) {
                                            $query4->whereDate('date_to', '>=', today());
                                        });
                                    });
                                });
                            });
                        },
                ])
                ->get(),   

            'emptyUnitContainers' => 
                
                $this->containerStatuses()->where(function ($query) {
                    $query->whereHas('deals', function ($query1) {
                        $query1->whereHas('deal', function ($query2) {
                            $query2->where('merchant_id', self::$merchant)
                            ->whereHas('rents', function ($query3) {
                                $query3->whereDate('date_to', '>=', today());
                            });
                        });
                    });
                })
                ->orWhereHas('containerShelfStatuses', function ($query1) {
                    $query1->whereHas('deals', function ($query2) {
                        $query2->whereHas('deal', function ($query3) {
                            $query3->where('merchant_id', self::$merchant)
                            ->whereHas('rents', function ($query4) {
                                $query4->whereDate('date_to', '>=', today());
                            });
                        });
                    });
                })
                ->orWhereHas('containerShelfUnitStatuses', function ($query1) {
                    $query1->whereHas('deals', function ($query2) {
                        $query2->whereHas('deal', function ($query3) {
                            $query3->where('merchant_id', self::$merchant)
                            ->whereHas('rents', function ($query4) {
                                $query4->whereDate('date_to', '>=', today());
                            });
                        });
                    });
                })
                ->with([
                    'containerShelfStatuses' => 
                        function ($query) {
                            $query->whereHas('containerShelfUnitStatuses', function ($query1) {
                                $query1->where(function ($query2) {
                                    $query2->whereHas('deals', function ($query3) {
                                        $query3->whereHas('deal', function ($query4) {
                                            $query4->where('merchant_id', self::$merchant)
                                            ->whereHas('rents', function ($query5) {
                                                $query5->whereDate('date_to', '>=', today());
                                            });
                                        });
                                    })
                                    ->orWhereHas('parentShelf.deals', function ($query1) {
                                        $query1->whereHas('deal', function ($query2) {
                                            $query2->where('merchant_id', self::$merchant)
                                            ->whereHas('rents', function ($query3) {
                                                $query3->whereDate('date_to', '>=', today());
                                            });
                                        });
                                    })
                                    ->orWhereHas('parentShelf.parentContainer.deals', function ($query1) {
                                        $query1->whereHas('deal', function ($query2) {
                                            $query2->where('merchant_id', self::$merchant)
                                            ->whereHas('rents', function ($query3) {
                                                $query3->whereDate('date_to', '>=', today());
                                            });
                                        });
                                    });
                                });
                            });
                        },
    
                    'containerShelfStatuses.containerShelfUnitStatuses' => 
                        function ($query1) {
                            $query1->where(function ($query) {
                                $query->whereHas('deals', function ($query2) {
                                    $query2->whereHas('deal', function ($query3) {
                                        $query3->where('merchant_id', self::$merchant)
                                        ->whereHas('rents', function ($query4) {
                                            $query4->whereDate('date_to', '>=', now());
                                        });
                                    });
                                })
                                ->orWhereHas('parentShelf.deals', function ($query1) {
                                    $query1->whereHas('deal', function ($query2) {
                                        $query2->where('merchant_id', self::$merchant)
                                        ->whereHas('rents', function ($query3) {
                                            $query3->whereDate('date_to', '>=', today());
                                        });
                                    });
                                })
                                ->orWhereHas('parentShelf.parentContainer.deals', function ($query1) {
                                    $query1->whereHas('deal', function ($query2) {
                                        $query2->where('merchant_id', self::$merchant)
                                        ->whereHas('rents', function ($query3) {
                                            $query3->whereDate('date_to', '>=', today());
                                        });
                                    });
                                });
                            });
                        },
                ])
                ->get()
        
        ];
    }
}
