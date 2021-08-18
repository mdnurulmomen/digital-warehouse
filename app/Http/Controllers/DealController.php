<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\MerchantDeal;
use Illuminate\Http\Request;
use App\Models\MerchantPayment;
use App\Models\WarehouseContainerStatus;
use App\Http\Resources\Web\DealCollection;
use App\Models\RentPeriod;
use App\Models\WarehouseContainerShelfStatus;
use App\Models\WarehouseContainerShelfUnitStatus;

class DealController extends Controller
{
    public function __construct()
    {   
        // Product-Stock
        $this->middleware("permission:view-merchant-deal-index")->only(['showMerchantAllDeals', 'searchMerchantAllDeals']);
        $this->middleware("permission:create-merchant-deal")->only('storeMerchantDeal');
        $this->middleware("permission:update-merchant-deal")->only('updateMerchantDeal');
        $this->middleware("permission:delete-merchant-deal")->only('deleteMerchantDeal');      
    }

    // Product-Stock
    public function showMerchantAllDeals($merchant, $perPage)
    {
        return new DealCollection(MerchantDeal::where('merchant_id', $merchant)->with(['spaces'])->paginate($perPage));
    }

    public function storeMerchantDeal(Request $request, $perPage)
    {
        $request->validate([

            'active' => 'required|boolean',
            'auto_renewal' => 'required|boolean',
            'e_commerce_fulfillment' => 'required|boolean',
            'merchant_id' => 'required|exists:merchants,id',
            'sale_percentage' => 'required_if:e_commerce_fulfillment,1|min:0|max:100',
            
            'payments' => 'required|array|min:1',
            'payments.*.current_due' => 'required|numeric',
            'payments.*.discount' => 'required|numeric|between:0,100',
            'payments.*.net_payable' => 'required|numeric',
            'payments.*.paid_amount' => 'required|numeric',
            'payments.*.previous_due' => 'required|numeric',
            'payments.*.total_rent' => 'required|numeric',

            'warehouses' => 'required|array|min:1',
            'warehouses.*.id' => 'required|exists:warehouses',
            'warehouses.*.spaces' => 'required|array|min:1',
            'warehouses.*.spaces.*.type' => 'required|string|in:containers,shelves,units',

            'warehouses.*.spaces.*.containers' => 'required_if:warehouses.*.spaces.*.type,containers|array|min:1',
            'warehouses.*.spaces.*.containers.*.id' => 'required_if:warehouses.*.spaces.*.type,containers|exists:warehouse_container_statuses,id',
            // 'warehouses.*.spaces.*.containers.*.rent_period_id' => 'required_if:warehouses.*.spaces.*.type,containers|exists:rent_periods,id',
            'warehouses.*.spaces.*.containers.*.selected_rent' => 'required_if:warehouses.*.spaces.*.type,containers',
            'warehouses.*.spaces.*.containers.*.selected_rent.id' => 'required_if:warehouses.*.spaces.*.type,containers|exists:rents,id',
            // 'warehouses.*.spaces.*.containers.*.selected_rent.storing_price' => 'required_if:warehouses.*.spaces.*.type,containers|numeric',
            
            'warehouses.*.spaces.*.container' => 'required_if:warehouses.*.spaces.*.type,shelves|required_if:warehouses.*.spaces.*.type,units',
            'warehouses.*.spaces.*.container.id' => 'required_if:warehouses.*.spaces.*.type,shelves|required_if:warehouses.*.spaces.*.type,units|exists:warehouse_container_statuses,id',
            'warehouses.*.spaces.*.container.shelves' => 'required_if:warehouses.*.spaces.*.type,shelves|array|min:1',
            'warehouses.*.spaces.*.container.shelves.*.id' => 'required_if:warehouses.*.spaces.*.type,shelves|exists:warehouse_container_shelf_statuses,id',
            'warehouses.*.spaces.*.container.selected_rent' => 'required_if:warehouses.*.spaces.*.type,shelves|required_if:warehouses.*.spaces.*.type,units',
            'warehouses.*.spaces.*.container.selected_rent.id' => 'required_if:warehouses.*.spaces.*.type,shelves|required_if:warehouses.*.spaces.*.type,units|exists:rents,id',
            // 'warehouses.*.spaces.*.container.selected_rent.storing_price' => 'required_if:warehouses.*.spaces.*.type,containers|numeric',

            'warehouses.*.spaces.*.container.shelf' => 'required_if:warehouses.*.spaces.*.type,units',
            'warehouses.*.spaces.*.container.shelf.id' => 'required_if:warehouses.*.spaces.*.type,units|exists:warehouse_container_shelf_statuses,id',
            'warehouses.*.spaces.*.container.shelf.units' => 'required_if:warehouses.*.spaces.*.type,units|array|min:1',
            'warehouses.*.spaces.*.container.shelf.units.*.id' => 'required_if:warehouses.*.spaces.*.type,units|exists:warehouse_container_shelf_unit_statuses,id',
        
        ],

        [
            'payments' => 'Payment is required !',
            // 'payments.*.current_due' => 'Stock quantity is required !',
            'payments.*.discount' => 'Discount rate should be between 0 to 100',
            'payments.*.net_payable' => 'Net payable is invalid',
            'payments.*.paid_amount' => 'Paid amount is invalid',
            'payments.*.previous_due' => 'Previous due is invalid',
            'payments.*.total_rent' => 'Total rent is invalid',

            'warehouses' => 'Warehouse is required',
            'warehouses.*.id' => 'Warehouse id is Invalid',
            'warehouses.*.spaces' => 'Warehouse spaces is required',
            'warehouses.*.spaces.*.type' => 'Warehouse invalid space type',

            'warehouses.*.spaces.*.containers.*' => 'Containers is required',
            'warehouses.*.spaces.*.containers.*.id.*' => 'Container id is invalid',
            'warehouses.*.spaces.*.containers.*.selected_rent.*' => 'Container rent is required',
            'warehouses.*.spaces.*.containers.*.selected_rent.id.*' => 'Invalid container rent',

            'warehouses.*.spaces.*.container.*' => 'Container is required',
            'warehouses.*.spaces.*.container.id.*' => 'Invalid container id',
            'warehouses.*.spaces.*.container.shelves.*' => 'Shelves are required',
            'warehouses.*.spaces.*.container.shelves.*.id.*' => 'Shelf id is invalid',
            'warehouses.*.spaces.*.container.selected_rent.*' => 'Shelf rent is required',
            'warehouses.*.spaces.*.container.selected_rent.id.*' => 'Shelf rent is invalid ',

            'warehouses.*.spaces.*.container.shelf.*' => 'Container shelf is required',
            'warehouses.*.spaces.*.container.shelf.id.*' => 'Shelf id is invalid',
            'warehouses.*.spaces.*.container.shelf.units.*' => 'Units are required',
            'warehouses.*.spaces.*.container.shelf.units.*.id.*' => 'Unit id is invalid',
        ]);

        $newDeal = MerchantDeal::create([
            'active' => $request->active,
            'e_commerce_fulfillment' => $request->e_commerce_fulfillment,
            'auto_renewal' => $request->auto_renewal,
            'sale_percentage' => $request->e_commerce_fulfillment ? $request->sale_percentage : NULL,
            'merchant_id' => $request->merchant_id
        ]);

        $request['payments'] = json_decode(json_encode($request->payments)); 

        $dealNewPayment = $newDeal->payments()->create([
            'invoice_no' => 'inv-'.$newDeal->id.'-#-'.($newDeal->payments->count() + 1),
            'previous_due' => 0,
            'total_rent' => $request->payments[0]->total_rent,
            'discount' => $request->payments[0]->discount,
            'net_payable' => $request->payments[0]->net_payable,
            'paid_amount' => $request->payments[0]->paid_amount,
            'current_due' => ($request->payments[0]->net_payable - $request->payments[0]->paid_amount),
        ]);

        if ($newDeal->active) {
            
            $request['warehouses'] = json_decode(json_encode($request->warehouses));

            foreach ($request->warehouses as $merchantWarehouseIndex => $merchantWarehouse) {
                
                foreach ($merchantWarehouse->spaces as $warehouseSpaceIndex => $warehouseSpace) {
                    
                    if($warehouseSpace->type == 'containers') {

                        $this->setDealtContainers($warehouseSpace->containers, $newDeal, $dealNewPayment);
                    }
                    else if($warehouseSpace->type == 'shelves') {
        
                        $this->setDealtShelves($warehouseSpace, $newDeal, $dealNewPayment);
                    }
                    else if ($warehouseSpace->type == 'units') {
                        
                        $this->setDealtUnits($warehouseSpace, $newDeal, $dealNewPayment);
                    }
                    else {
                        // \Log::info("No Type");
                    }

                }

            }

        }

        return $this->showMerchantAllDeals($request->merchant_id, $perPage);
    }

    public function updateMerchantDeal(Request $request, $deal, $perPage)
    {
        $request->validate([

            'active' => 'required|boolean',
            'auto_renewal' => 'required|boolean',
            'e_commerce_fulfillment' => 'required|boolean',
            'merchant_id' => 'required|exists:merchants,id',
            'sale_percentage' => 'required_if:e_commerce_fulfillment,1|min:0|max:100',
            
            'payments' => 'required|array|min:1',
            'payments.*.current_due' => 'required|numeric',
            'payments.*.discount' => 'required|numeric|between:0,100',
            'payments.*.net_payable' => 'required|numeric',
            'payments.*.paid_amount' => 'required|numeric',
            'payments.*.previous_due' => 'required|numeric',
            'payments.*.total_rent' => 'required|numeric',

            'warehouses' => 'required|array|min:1',
            'warehouses.*.id' => 'required|exists:warehouses',
            'warehouses.*.spaces' => 'required|array|min:1',
            'warehouses.*.spaces.*.type' => 'required|string|in:containers,shelves,units',

            'warehouses.*.spaces.*.containers' => 'required_if:warehouses.*.spaces.*.type,containers|array|min:1',
            'warehouses.*.spaces.*.containers.*.id' => 'required_if:warehouses.*.spaces.*.type,containers|exists:warehouse_container_statuses,id',
            // 'warehouses.*.spaces.*.containers.*.rent_period_id' => 'required_if:warehouses.*.spaces.*.type,containers|exists:rent_periods,id',
            'warehouses.*.spaces.*.containers.*.selected_rent' => 'required_if:warehouses.*.spaces.*.type,containers',
            'warehouses.*.spaces.*.containers.*.selected_rent.id' => 'required_if:warehouses.*.spaces.*.type,containers|exists:rents,id',
            // 'warehouses.*.spaces.*.containers.*.selected_rent.storing_price' => 'required_if:warehouses.*.spaces.*.type,containers|numeric',
            
            'warehouses.*.spaces.*.container' => 'required_if:warehouses.*.spaces.*.type,shelves|required_if:warehouses.*.spaces.*.type,units',
            'warehouses.*.spaces.*.container.id' => 'required_if:warehouses.*.spaces.*.type,shelves|required_if:warehouses.*.spaces.*.type,units|exists:warehouse_container_statuses,id',
            'warehouses.*.spaces.*.container.shelves' => 'required_if:warehouses.*.spaces.*.type,shelves|array|min:1',
            'warehouses.*.spaces.*.container.shelves.*.id' => 'required_if:warehouses.*.spaces.*.type,shelves|exists:warehouse_container_shelf_statuses,id',
            'warehouses.*.spaces.*.container.selected_rent' => 'required_if:warehouses.*.spaces.*.type,shelves|required_if:warehouses.*.spaces.*.type,units',
            'warehouses.*.spaces.*.container.selected_rent.id' => 'required_if:warehouses.*.spaces.*.type,shelves|required_if:warehouses.*.spaces.*.type,units|exists:rents,id',
            // 'warehouses.*.spaces.*.container.selected_rent.storing_price' => 'required_if:warehouses.*.spaces.*.type,containers|numeric',

            'warehouses.*.spaces.*.container.shelf' => 'required_if:warehouses.*.spaces.*.type,units',
            'warehouses.*.spaces.*.container.shelf.id' => 'required_if:warehouses.*.spaces.*.type,units|exists:warehouse_container_shelf_statuses,id',
            'warehouses.*.spaces.*.container.shelf.units' => 'required_if:warehouses.*.spaces.*.type,units|array|min:1',
            'warehouses.*.spaces.*.container.shelf.units.*.id' => 'required_if:warehouses.*.spaces.*.type,units|exists:warehouse_container_shelf_unit_statuses,id',
        
        ],

        [
            'payments' => 'Payment is required !',
            // 'payments.*.current_due' => 'Stock quantity is required !',
            'payments.*.discount' => 'Discount rate should be between 0 to 100',
            'payments.*.net_payable' => 'Net payable is invalid',
            'payments.*.paid_amount' => 'Paid amount is invalid',
            'payments.*.previous_due' => 'Previous due is invalid',
            'payments.*.total_rent' => 'Total rent is invalid',

            'warehouses' => 'Warehouse is required',
            'warehouses.*.id' => 'Warehouse id is Invalid',
            'warehouses.*.spaces' => 'Warehouse spaces is required',
            'warehouses.*.spaces.*.type' => 'Warehouse invalid space type',

            'warehouses.*.spaces.*.containers.*' => 'Containers is required',
            'warehouses.*.spaces.*.containers.*.id.*' => 'Container id is invalid',
            'warehouses.*.spaces.*.containers.*.selected_rent.*' => 'Container rent is required',
            'warehouses.*.spaces.*.containers.*.selected_rent.id.*' => 'Invalid container rent',

            'warehouses.*.spaces.*.container.*' => 'Container is required',
            'warehouses.*.spaces.*.container.id.*' => 'Invalid container id',
            'warehouses.*.spaces.*.container.shelves.*' => 'Shelves are required',
            'warehouses.*.spaces.*.container.shelves.*.id.*' => 'Shelf id is invalid',
            'warehouses.*.spaces.*.container.selected_rent.*' => 'Shelf rent is required',
            'warehouses.*.spaces.*.container.selected_rent.id.*' => 'Shelf rent is invalid ',

            'warehouses.*.spaces.*.container.shelf.*' => 'Container shelf is required',
            'warehouses.*.spaces.*.container.shelf.id.*' => 'Shelf id is invalid',
            'warehouses.*.spaces.*.container.shelf.units.*' => 'Units are required',
            'warehouses.*.spaces.*.container.shelf.units.*.id.*' => 'Unit id is invalid',
        ]);

        $dealToUpdate = MerchantDeal::findOrFail($deal);
        $dealRecentPayment = $dealToUpdate->payments()->latest('paid_at')->first();

        $dealToUpdate->update([
            'active' => $request->active,
            'e_commerce_fulfillment' => $request->e_commerce_fulfillment,
            'auto_renewal' => $request->auto_renewal,
            'sale_percentage' => $request->e_commerce_fulfillment ? $request->sale_percentage : NULL,
            'merchant_id' => $request->merchant_id
        ]);

        $request['payments'] = json_decode(json_encode($request->payments)); 

        $dealRecentPaymentUpdated = $dealRecentPayment->update([
            'previous_due' => $dealToUpdate->payments()->latest('paid_at')->skip(1)->take(1)->first()->previous_due ?? 0,
            'total_rent' => $request->payments[0]->total_rent,
            'discount' => $request->payments[0]->discount,
            'net_payable' => $request->payments[0]->net_payable,
            'paid_amount' => $request->payments[0]->paid_amount,
            'current_due' => ($request->payments[0]->net_payable - $request->payments[0]->paid_amount),
        ]);

        if ($dealToUpdate->payments->count() < 2 && $dealToUpdate->active) {
            
            // Reseting current spaces
            $this->resetDealtSpaces($dealToUpdate, $dealRecentPayment);

            $request['warehouses'] = json_decode(json_encode($request->warehouses));

            foreach ($request->warehouses as $merchantWarehouseIndex => $merchantWarehouse) {
                
                foreach ($merchantWarehouse->spaces as $warehouseSpaceIndex => $warehouseSpace) {
                    
                    if($warehouseSpace->type == 'containers') {

                        $this->setDealtContainers($warehouseSpace->containers, $dealToUpdate, $dealRecentPayment);
                    }
                    else if($warehouseSpace->type == 'shelves') {
        
                        $this->setDealtShelves($warehouseSpace, $dealToUpdate, $dealRecentPayment);
                    }
                    else if ($warehouseSpace->type == 'units') {
                        
                        $this->setDealtUnits($warehouseSpace, $dealToUpdate, $dealRecentPayment);
                    }
                    else {
                        // \Log::info("No Type");
                    }

                }

            }
           
        }
        // Deactivated
        else if (! $dealToUpdate->active) {
            // Reseting current spaces
            $this->retrieveDealtSpaces($dealToUpdate, $dealRecentPayment);
        }

        return $this->showMerchantAllDeals($request->merchant_id, $perPage);
    }

    public function deleteMerchantDeal($deal, $perPage)
    {
        $dealToDelete = MerchantDeal::findOrFail($deal);
        $merchantId = $dealToDelete->merchant_id;

        if ($dealToDelete->payments->count() > 1) {
            
           return response()->json(['errors'=>["undeletableDeal" => "Deal has multiple payments"]], 422);
            
        }
        else {
            
            $dealRecentPayment = $dealToDelete->payments()->latest('id')->first();

            $this->resetDealtSpaces($dealToDelete, $dealRecentPayment);

            $dealRecentPayment->rents()->delete();
            $dealRecentPayment->delete();
            $dealToDelete->delete();

        }

        return $this->showMerchantAllDeals($merchantId, $perPage);
    }

    public function searchMerchantAllDeals($merchant, $search, $perPage)
    {
        $query = MerchantDeal::with([ 'spaces', 'payments' ])
                ->where('merchant_id', $merchant)
                ->where(function($query5) use ($search) {
                    $query5->whereHas('spaces', function ($query2) use ($search) {
                        $query2->whereHasMorph(
                            'space',
                            [ WarehouseContainerStatus::class, WarehouseContainerShelfStatus::class, WarehouseContainerShelfUnitStatus::class ],
                            function ($query3) use ($search) {
                                $query3->where('name', 'like', "%$search%");
                            }
                        );
                    })
                    ->orWhereHas('payments', function ($query4) use ($search) {
                        $query4->where('invoice_no', 'like', "%$search%")
                        ->orWhere('previous_due', 'like', "%$search%")
                        ->orWhere('total_rent', 'like', "%$search%")
                        ->orWhere('discount', 'like', "%$search%")
                        ->orWhere('net_payable', 'like', "%$search%")
                        ->orWhere('paid_amount', 'like', "%$search%")
                        ->orWhere('current_due', 'like', "%$search%");
                    });
                });

        return response()->json([
            'all' => new DealCollection($query->paginate($perPage)),  
        ], 200);
    }

    protected function retrieveDealtSpaces(MerchantDeal $deal, MerchantPayment $payment)
    {
        // containers
        foreach ($deal->containers as $merchantContainerKey => $merchantContainer) {
               
            $addedContainer = WarehouseContainerStatus::find($merchantContainer->space_id);

            if (!empty($addedContainer) && $merchantContainer->engaged==0.0) {
                
                $addedContainer->update([
                    'engaged' => 0
                ]);

                $addedContainer->updateContainerStatus(0);

                // $payment->rents()->where('dealt_space_id', $merchantContainer->id)->delete();

                // $merchantContainer->delete();   // delete dealt space

            }

        }

        // shelves
        if ($deal->shelves->count()) {

            foreach ($deal->shelves as $merchantShelfKey => $merchantShelf) {
               
                $addedShelf = WarehouseContainerShelfStatus::find($merchantShelf->space_id);

                if (! empty($addedShelf) && $merchantShelf->engaged==0.0) {

                    $addedShelf->update([
                        'engaged' => 0
                    ]);

                    $addedShelf->parentContainer->updateChildUnits($addedShelf, 0);

                    // $payment->rents()->where('dealt_space_id', $merchantShelf->id)->delete();

                    // $merchantShelf->delete();

                }

            }

            $addedShelf->parentContainer->updateParentContainer($addedShelf->parentContainer);

        }

        // units
        if ($deal->units->count()) {

            foreach ($deal->units as $merchantUnitfKey => $merchantUnit) {
               
                $addedUnit = WarehouseContainerShelfUnitStatus::find($merchantUnit->space_id);

                if (! empty($addedUnit) && $merchantUnit->engaged==0.0) {

                    $addedUnit->update([
                        'engaged' => 0
                    ]);

                    // $payment->rents()->where('dealt_space_id', $merchantUnit->id)->delete();

                    // $merchantUnit->delete();

                }

            }

            // Parent Shelf
            $addedUnit->parentShelf->parentContainer->updateParentShelf($addedUnit->parentShelf);

        }
    }

    protected function resetDealtSpaces(MerchantDeal $deal, MerchantPayment $payment)
    {
        // containers
        foreach ($deal->containers as $merchantContainerKey => $merchantContainer) {
               
            $addedContainer = WarehouseContainerStatus::find($merchantContainer->space_id);

            if (!empty($addedContainer) && $merchantContainer->engaged==0.0) {
                
                $addedContainer->update([
                    'engaged' => 0
                ]);

                $addedContainer->updateContainerStatus(0);

                $payment->rents()->where('dealt_space_id', $merchantContainer->id)->delete();

                $merchantContainer->delete();   // delete dealt space

            }

        }

        // shelves
        if ($deal->shelves->count()) {

            foreach ($deal->shelves as $merchantShelfKey => $merchantShelf) {
               
                $addedShelf = WarehouseContainerShelfStatus::find($merchantShelf->space_id);

                if (! empty($addedShelf) && $merchantShelf->engaged==0.0) {

                    $addedShelf->update([
                        'engaged' => 0
                    ]);

                    $addedShelf->parentContainer->updateChildUnits($addedShelf, 0);

                    $payment->rents()->where('dealt_space_id', $merchantShelf->id)->delete();

                    $merchantShelf->delete();

                }

            }

            $addedShelf->parentContainer->updateParentContainer($addedShelf->parentContainer);

        }

        // units
        if ($deal->units->count()) {

            foreach ($deal->units as $merchantUnitfKey => $merchantUnit) {
               
                $addedUnit = WarehouseContainerShelfUnitStatus::find($merchantUnit->space_id);

                if (! empty($addedUnit) && $merchantUnit->engaged==0.0) {

                    $addedUnit->update([
                        'engaged' => 0
                    ]);

                    $payment->rents()->where('dealt_space_id', $merchantUnit->id)->delete();

                    $merchantUnit->delete();

                }

            }

            // Parent Shelf
            $addedUnit->parentShelf->parentContainer->updateParentShelf($addedUnit->parentShelf);

        }
    }

    protected function setDealtContainers($containers = [], MerchantDeal $deal, MerchantPayment $payment)
    {
        if (count($containers)) {
            
            foreach ($containers as $warehouseContainerIndex => $warehouseContainer) {
                        
                $expectedContainer = WarehouseContainerStatus::find($warehouseContainer->id);
                
                if (!empty($expectedContainer) && $expectedContainer->engaged==0.0) {
                    
                    $newSpaces = $expectedContainer->deals()->create([
                        'rent_id' => $warehouseContainer->selected_rent->id,
                        'warehouse_id' => $expectedContainer->warehouseContainer->warehouse_id,
                        'merchant_deal_id' => $deal->id,
                    ]);
        
                    $expectedContainer->update([
                        'engaged' => 1
                    ]);
                    
                    $expectedContainer->updateContainerStatus(1); 

                    $rentPeriod = RentPeriod::find($warehouseContainer->selected_rent->rent_period_id);
                    // $lastPayment = $payment->rents()->where('dealt_space_id', )->latest()->first();
                    
                    $containerNewRent = $payment->rents()->create([
                        'issued_from' => $deal->created_at,
                        'expired_at' => $this->getRentExpiredDate($deal, $rentPeriod->name),
                        'rent' => $warehouseContainer->selected_rent->storing_price,
                        'dealt_space_id' => $newSpaces->id
                    ]);
    
                }
    
            }

        }

    }

    protected function setDealtShelves($warehouseSpace, MerchantDeal $deal, MerchantPayment $payment)
    {
        if (count($warehouseSpace->container->shelves)) {
            
            if (count($warehouseSpace->container->shelves) === WarehouseContainerStatus::find($warehouseSpace->container->id)->containerShelfStatuses()->count()) {
                
                $this->setDealtContainers([ $warehouseSpace->container ], $deal, $payment);

            }
            else {

                $rentPeriod = RentPeriod::find($warehouseSpace->container->selected_rent->rent_period_id);
                
                foreach ($warehouseSpace->container->shelves as $warehouseContainerShelfIndex => $warehouseContainerShelf) {
                                
                    $containerExpectedShelf = WarehouseContainerShelfStatus::find($warehouseContainerShelf->id);
                    
                    if (!empty($containerExpectedShelf) && $containerExpectedShelf->engaged==0.0) {
                        
                        $dealtNewShelf = $containerExpectedShelf->deals()->create([
                            'rent_id' => $warehouseSpace->container->selected_rent->id,
                            'warehouse_id' => $containerExpectedShelf->warehouseContainer->warehouse_id,
                            'merchant_deal_id' => $deal->id
                        ]);
        
                        $containerExpectedShelf->update([
                            'engaged' => 1
                        ]);
            
                        $containerExpectedShelf->parentContainer->updateChildUnits($containerExpectedShelf, 1);
                        
                        $shelfNewRent = $payment->rents()->create([
                            'issued_from' => now(),
                            'expired_at' => $this->getRentExpiredDate($deal, $rentPeriod->name),
                            'rent' => $warehouseSpace->container->selected_rent->storing_price,
                            'dealt_space_id' => $dealtNewShelf->id
                        ]);
        
                    }
        
                }
        
                $containerExpectedShelf->parentContainer->updateParentContainer($containerExpectedShelf->parentContainer);

            }

        }

    }

    protected function setDealtUnits($warehouseSpace, MerchantDeal $deal, MerchantPayment $payment) 
    {
        if ($warehouseSpace->container->shelf->units) {

            if (count($warehouseSpace->container->shelf->units) === WarehouseContainerShelfStatus::find($warehouseSpace->container->shelf->id)->containerShelfUnitStatuses()->count()) {
                
                $warehouseSpace->container->{"shelves"} = [ $warehouseSpace->container->shelf ];
                
                $this->setDealtShelves($warehouseSpace, $deal, $payment);

            }
            else {

                $rentPeriod = RentPeriod::find($warehouseSpace->container->selected_rent->rent_period_id);
                
                foreach ($warehouseSpace->container->shelf->units as $containerShelfUnit) {
                    
                    $warehouseContainerShelfExpectedUnit = WarehouseContainerShelfUnitStatus::find($containerShelfUnit->id);

                    if (!empty($warehouseContainerShelfExpectedUnit) && $warehouseContainerShelfExpectedUnit->engaged==0.0) {

                        $dealtNewUnit = $warehouseContainerShelfExpectedUnit->deals()->create([
                            'rent_id' => $warehouseSpace->container->selected_rent->id,
                            'warehouse_id' => $warehouseContainerShelfExpectedUnit->warehouseContainer->warehouse_id,
                            'merchant_deal_id' => $deal->id
                        ]);

                        $warehouseContainerShelfExpectedUnit->update([
                            'engaged' => 1
                        ]);

                        $unitNewRent = $payment->rents()->create([
                            'issued_from' => now(),
                            'expired_at' => $this->getRentExpiredDate($deal, $rentPeriod->name),
                            'rent' => $warehouseSpace->container->selected_rent->storing_price,
                            'dealt_space_id' => $dealtNewUnit->id
                        ]);

                    }

                }

                // Parent Shelf
                $warehouseContainerShelfExpectedUnit->parentShelf->parentContainer->updateParentShelf($warehouseContainerShelfExpectedUnit->parentShelf);

            }

        }

    }

    protected function getRentExpiredDate(MerchantDeal $deal, $rentName) 
    {   
        $dealIssueDate = Carbon::parse($deal->created_at);

        if (strpos($rentName,"daily") !== false) {
            
            return $dealIssueDate->addDay();
        }
        else if (strpos($rentName,"week") !== false) {
            
            return $dealIssueDate->addDays(7);
        }
        else if (strpos($rentName,"month") !== false) {
            
            return $dealIssueDate->addDays(30);
        }
        else if (strpos($rentName,"year") !== false) {
            
            return $dealIssueDate->addYear();
        }
        else {
            return now();
        }
    }

}
