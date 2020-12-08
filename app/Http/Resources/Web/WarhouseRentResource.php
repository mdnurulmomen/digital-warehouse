<?php

namespace App\Http\Resources\Web;

use App\Models\RentPeriod;
use Illuminate\Http\Resources\Json\JsonResource;
// use App\Http\Resources\Web\WarhouseContainerSelfResource;

class WarhouseRentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $collection = collect([]);

        foreach (RentPeriod::all() as $rentPeriod) {
            
            ${'container_'.$rentPeriod->name.'_price'} = $this->rents()->where('rent_period_id', $rentPeriod->id)->first();

            $collection->put("container_storing_price_$rentPeriod->name", ${'container_'.$rentPeriod->name.'_price'}->storing_price ?? NULL);
            $collection->put("container_selling_price_$rentPeriod->name", ${'container_'.$rentPeriod->name.'_price'}->selling_price ?? NULL);

            if ($this->container->has_shelve) {
                
                ${'shelf_'.$rentPeriod->name.'_price'} = $this->shelf->rents()->where('rent_period_id', $rentPeriod->id)->first();

                $collection->put("shelf_storing_price_$rentPeriod->name", ${'shelf_'.$rentPeriod->name.'_price'}->storing_price ?? NULL);
                $collection->put("shelf_selling_price_$rentPeriod->name", ${'shelf_'.$rentPeriod->name.'_price'}->selling_price ?? NULL);

                if ($this->container->shelf->has_units) {
                    
                    ${'unit_'.$rentPeriod->name.'_price'} = $this->shelf->unit->rents()->where('rent_period_id', $rentPeriod->id)->first();

                    $collection->put("unit_storing_price_$rentPeriod->name", ${'unit_'.$rentPeriod->name.'_price'}->storing_price ?? NULL);
                    $collection->put("unit_selling_price_$rentPeriod->name", ${'unit_'.$rentPeriod->name.'_price'}->selling_price ?? NULL);


                }

            }
        
        }
        
        return $collection->all();
    }
}
