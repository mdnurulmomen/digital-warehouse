<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Rent;
use App\Models\DealtSpace;
use App\Models\RentPeriod;
use Illuminate\Http\Request;
use App\Models\MerchantRent;
use App\Models\MerchantPayment;
use Illuminate\Validation\Rule;
use App\Models\MerchantSpaceDeal;
use App\Models\WarehouseContainerStatus;
use App\Models\WarehouseContainerShelfStatus;
use App\Models\WarehouseContainerShelfUnitStatus;
use App\Http\Resources\Web\MerchantRentCollection;
use App\Http\Resources\Web\MerchantPaymentCollection;
use App\Http\Resources\Web\MerchantSpaceDealCollection;

class DealController extends Controller
{
    public function __construct()
    {   
        // Product-Stock
        $this->middleware("permission:view-merchant-space-deal-index")->only(['showMerchantAllSpaceDeals', 'searchMerchantAllSpaceDeals']);
        $this->middleware("permission:create-merchant-space-deal")->only('storeMerchantSpaceDeal');
        $this->middleware("permission:update-merchant-space-deal")->only('updateMerchantSpaceDeal');
        $this->middleware("permission:delete-merchant-space-deal")->only('deleteMerchantSpaceDeal');

        // Deal-Payment
        $this->middleware("permission:view-merchant-payment-index")->only(['showDealAllRents', 'searchDealAllRents', 'showRentalAllPayments', 'searchRentalAllPayments']);
        $this->middleware("permission:create-merchant-payment")->only(['storeDealNewRent', 'storeRentalNewPayment']);
        $this->middleware("permission:update-merchant-payment")->only(['updateDealRent', 'updateRentalPayment']);
        $this->middleware("permission:delete-merchant-payment")->only(['deleteDealRent', 'deleteRentalPayment']);
    }

    // Merchant-Deal
    public function showMerchantAllSpaceDeals($merchant, $perPage)
    {
        return new MerchantSpaceDealCollection(
            MerchantSpaceDeal::where('merchant_id', $merchant)
            ->with(['spaces', 'rents'])
            ->with([
                'rentPeriod' => function($query) {
                    $query->withTrashed();
                }
            ])
            ->latest()->paginate($perPage)
        );
    }

    public function storeMerchantSpaceDeal(Request $request, $perPage)
    {
        $request->validate([

            'active' => 'required|boolean',
            'auto_renewal' => 'required|boolean',
            // 'e_commerce_fulfillment' => 'required|boolean',
            'merchant_id' => 'required|exists:merchants,id',
            // 'sale_percentage' => 'required_if:e_commerce_fulfillment,1|min:0|max:100',
            'rent_period_id' => 'required|exists:rent_periods,id|exists:rents,rent_period_id',
            
            'rents' => 'required|array|min:1',
            'rents.*.number_installment' => 'required|numeric',
            'rents.*.date_from' => 'required|date|after_or_equal:1970-01-01 00:00:01|before_or_equal:2038-01-19 03:14:07',
            'rents.*.date_to' => 'required|date|after_or_equal:1970-01-01 00:00:01|before_or_equal:2038-01-19 03:14:07',
            'rents.*.total_rent' => 'required|numeric',
            'rents.*.discount' => 'required|numeric|between:0,100',
            // 'rents.*.previous_due' => 'numeric|min:0',
            'rents.*.net_payable' => 'required|numeric',
            'rents.*.total_paid_amount' => 'required|numeric|min:1',
            // 'rents.*.current_due' => 'required|numeric',

            'warehouses' => 'required|array|min:1',
            'warehouses.*.id' => 'required|exists:warehouses',
            'warehouses.*.spaces' => 'required|array|min:1',
            'warehouses.*.spaces.*.type' => 'required|string|in:containers,shelves,units',

            'warehouses.*.spaces.*.containers' => 'required_if:warehouses.*.spaces.*.type,containers|array|min:1',
            'warehouses.*.spaces.*.containers.*.id' => 'required_if:warehouses.*.spaces.*.type,containers|exists:warehouse_container_statuses,id',
            'warehouses.*.spaces.*.containers.*.engaged' => 'required_if:warehouses.*.spaces.*.type,containers|numeric|max:0',
            // 'warehouses.*.spaces.*.containers.*.rent_period_id' => 'required_if:warehouses.*.spaces.*.type,containers|exists:rent_periods,id',
            'warehouses.*.spaces.*.containers.*.selected_rent' => 'required_if:warehouses.*.spaces.*.type,containers',

            'warehouses.*.spaces.*.containers.*.selected_rent.id' => [
                'required_if:warehouses.*.spaces.*.type,containers',
                Rule::exists('rents', 'id')->where(function ($query) use ($request) {
                    return $query->where('rent_period_id', $request->rent_period_id);
                }),
            ],

            'warehouses.*.spaces.*.containers.*.selected_rent.rent_period_id' => [
                'required_if:warehouses.*.spaces.*.type,containers', 
                'exists:rent_periods,id', 
                'exists:rents,rent_period_id',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value != $request->rent_period_id) {
                        $fail('The '.$attribute.' is invalid.');
                    }
                },
            ],

            'warehouses.*.spaces.*.containers.*.selected_rent.rent' => 'required_if:warehouses.*.spaces.*.type,containers|numeric',
            
            'warehouses.*.spaces.*.container' => 'required_if:warehouses.*.spaces.*.type,shelves|required_if:warehouses.*.spaces.*.type,units',
            'warehouses.*.spaces.*.container.id' => 'required_if:warehouses.*.spaces.*.type,shelves|required_if:warehouses.*.spaces.*.type,units|exists:warehouse_container_statuses,id',
            'warehouses.*.spaces.*.container.shelves' => 'required_if:warehouses.*.spaces.*.type,shelves|array|min:1',
            'warehouses.*.spaces.*.container.shelves.*.id' => 'required_if:warehouses.*.spaces.*.type,shelves|exists:warehouse_container_shelf_statuses,id',
            'warehouses.*.spaces.*.container.shelves.*.engaged' => 'required_if:warehouses.*.spaces.*.type,shelves|numeric|max:0',
            'warehouses.*.spaces.*.container.selected_rent' => 'required_if:warehouses.*.spaces.*.type,shelves|required_if:warehouses.*.spaces.*.type,units',
            
            'warehouses.*.spaces.*.container.selected_rent.id' => [
                'required_if:warehouses.*.spaces.*.type,shelves', 
                'required_if:warehouses.*.spaces.*.type,units', 
                Rule::exists('rents', 'id')->where(function ($query) use ($request) {
                    return $query->where('rent_period_id', $request->rent_period_id);
                }),
            ],

            'warehouses.*.spaces.*.container.selected_rent.rent_period_id' => [
                'required_if:warehouses.*.spaces.*.type,shelves', 
                'required_if:warehouses.*.spaces.*.type,units',
                'exists:rent_periods,id', 
                'exists:rents,rent_period_id',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value != $request->rent_period_id) {
                        $fail('The '.$attribute.' is invalid.');
                    }
                },
            ],

            'warehouses.*.spaces.*.container.selected_rent.rent' => 'required_if:warehouses.*.spaces.*.type,shelves|required_if:warehouses.*.spaces.*.type,units|numeric',

            'warehouses.*.spaces.*.container.shelf' => 'required_if:warehouses.*.spaces.*.type,units',
            'warehouses.*.spaces.*.container.shelf.id' => 'required_if:warehouses.*.spaces.*.type,units|exists:warehouse_container_shelf_statuses,id',
            'warehouses.*.spaces.*.container.shelf.units' => 'required_if:warehouses.*.spaces.*.type,units|array|min:1',
            'warehouses.*.spaces.*.container.shelf.units.*.id' => 'required_if:warehouses.*.spaces.*.type,units|exists:warehouse_container_shelf_unit_statuses,id',
            'warehouses.*.spaces.*.container.shelf.units.*.engaged' => 'required_if:warehouses.*.spaces.*.type,units|numeric|max:0',
        
        ],

        [
            'rents' => 'Rent is required !',
            // 'rents.*.current_due' => 'Stock quantity is required !',
            'rents.*.number_installment' => 'Number rent is required',
            'rents.*.date_from' => 'Rent counting date is required',
            'rents.*.date_to' => 'Rent ending date is required',
            'rents.*.discount' => 'Discount rate should be between 0 to 100',
            'rents.*.net_payable' => 'Net payable is invalid',
            'rents.*.total_paid_amount' => 'Paid amount is invalid',
            // 'rents.*.previous_due' => 'Previous due is invalid',
            'rents.*.total_rent' => 'Total rent is invalid',

            'warehouses' => 'Warehouse is required',
            'warehouses.*.id' => 'Warehouse id is Invalid',
            'warehouses.*.spaces' => 'Warehouse spaces is required',
            'warehouses.*.spaces.*.type' => 'Warehouse invalid space type',

            'warehouses.*.spaces.*.containers.*' => 'Containers is required',
            'warehouses.*.spaces.*.containers.*.id.*' => 'Container id is invalid',
            'warehouses.*.spaces.*.containers.*.engaged.*' => 'Selected container should be vacant',
            'warehouses.*.spaces.*.containers.*.selected_rent.*' => 'Container rent is required',
            'warehouses.*.spaces.*.containers.*.selected_rent.id.*' => 'Container rent is invalid',
            'warehouses.*.spaces.*.containers.*.selected_rent.rent_period_id.*' => 'Container rent is invalid',
            'warehouses.*.spaces.*.containers.*.selected_rent.rent.*' => 'Container rent is invalid',

            'warehouses.*.spaces.*.container.*' => 'Container is required',
            'warehouses.*.spaces.*.container.id.*' => 'Invalid container id',
            'warehouses.*.spaces.*.container.shelves.*' => 'Shelves are required',
            'warehouses.*.spaces.*.container.shelves.*.id.*' => 'Shelf id is invalid',
            'warehouses.*.spaces.*.container.shelves.*.engaged.*' => 'Selected shelf should be vacant',
            'warehouses.*.spaces.*.container.selected_rent.*' => 'Shelf or Unit rent is required',
            'warehouses.*.spaces.*.container.selected_rent.id.*' => 'Shelf or Unit rent is invalid',
            'warehouses.*.spaces.*.container.selected_rent.rent_period_id.*' => 'Shelf or Unit rent is invalid',
            'warehouses.*.spaces.*.container.selected_rent.rent.*' => 'Shelf or Unit rent is invalid',

            'warehouses.*.spaces.*.container.shelf.*' => 'Container shelf is required',
            'warehouses.*.spaces.*.container.shelf.id.*' => 'Shelf id is invalid',
            'warehouses.*.spaces.*.container.shelf.units.*' => 'Units are required',
            'warehouses.*.spaces.*.container.shelf.units.*.id.*' => 'Unit id is invalid',
            'warehouses.*.spaces.*.container.shelf.units.*.engaged.*' => 'Selected unit should be vacant',
        ]);

        $newDeal = MerchantSpaceDeal::create([
            'active' => $request->active,
            // 'e_commerce_fulfillment' => $request->e_commerce_fulfillment,
            'auto_renewal' => $request->auto_renewal,
            // 'sale_percentage' => $request->e_commerce_fulfillment ? $request->sale_percentage : NULL,
            'rent_period_id' => $request->rent_period_id,
            'merchant_id' => $request->merchant_id,
            'created_at' => now()
        ]);

        $request['rents'] = json_decode(json_encode($request->rents)); 

        $dealNewRent = $newDeal->rents()->create([
            'name' => ($newDeal->name.'R'.($newDeal->rents->count() + 1)),
            'number_installment' => $request->rents[0]->number_installment,
            'date_from' => $request->rents[0]->date_from,
            'date_to' => $request->rents[0]->date_to,
            'total_rent' => $request->rents[0]->total_rent,
            'discount' => $request->rents[0]->discount,
            'net_payable' => $request->rents[0]->net_payable,
            'total_paid_amount' => $request->rents[0]->total_paid_amount
        ]);

        if ($request->rents[0]->total_paid_amount > 0) {
            
            $user = $request->user();

            $merchantPayment = $dealNewRent->payments()->create([
                'invoice_no' => ($dealNewRent->name.'P'.($dealNewRent->payments->count() + 1)),
                'paid_amount' => $request->rents[0]->total_paid_amount,
                'current_due' => ($request->rents[0]->net_payable - $request->rents[0]->total_paid_amount),
                'issuer_type' => get_class($user),
                'issuer_id' => $user->id,
            ]);

        }

        if ($newDeal->active) {
            
            $request['warehouses'] = json_decode(json_encode($request->warehouses));

            foreach ($request->warehouses as $merchantWarehouseIndex => $merchantWarehouse) {
                
                foreach ($merchantWarehouse->spaces as $warehouseSpaceIndex => $warehouseSpace) {
                    
                    if($warehouseSpace->type == 'containers') {

                        $this->setDealtContainers($warehouseSpace->containers, $newDeal, $dealNewRent);
                    }
                    else if($warehouseSpace->type == 'shelves') {
        
                        $this->setDealtShelves($warehouseSpace, $newDeal, $dealNewRent);
                    }
                    else if ($warehouseSpace->type == 'units') {
                        
                        $this->setDealtUnits($warehouseSpace, $newDeal, $dealNewRent);
                    }
                    else {
                        // \Log::info("No Type");
                    }

                }

            }

        }

        return $this->showMerchantAllSpaceDeals($request->merchant_id, $perPage);
    }

    public function updateMerchantSpaceDeal(Request $request, $deal, $perPage)
    {
        $request->validate([

            'active' => 'required|boolean',
            'auto_renewal' => 'required|boolean',
            // 'e_commerce_fulfillment' => 'required|boolean',
            'merchant_id' => 'required|exists:merchants,id',
            // 'sale_percentage' => 'required_if:e_commerce_fulfillment,1|min:0|max:100',
            'rent_period_id' => 'required|exists:rent_periods,id|exists:rents,rent_period_id',
            
            'rents' => 'required|array|min:1',
            'rents.*.number_installment' => 'required|numeric',
            'rents.*.date_from' => 'required|date|after_or_equal:1970-01-01 00:00:01|before_or_equal:2038-01-19 03:14:07',
            'rents.*.date_to' => 'required|date|after_or_equal:1970-01-01 00:00:01|before_or_equal:2038-01-19 03:14:07',
            'rents.*.total_rent' => 'required|numeric',
            'rents.*.discount' => 'required|numeric|between:0,100',
            // 'rents.*.previous_due' => 'required|numeric',
            'rents.*.net_payable' => 'required|numeric',
            'rents.*.total_paid_amount' => 'required|numeric|min:1',
            // 'rents.*.current_due' => 'required|numeric',

            'warehouses' => 'required|array|min:1',
            'warehouses.*.id' => 'required|exists:warehouses',
            'warehouses.*.spaces' => 'required|array|min:1',
            'warehouses.*.spaces.*.type' => 'required|string|in:containers,shelves,units',

            'warehouses.*.spaces.*.containers' => 'required_if:warehouses.*.spaces.*.type,containers|array|min:1',
            'warehouses.*.spaces.*.containers.*.id' => 'required_if:warehouses.*.spaces.*.type,containers|exists:warehouse_container_statuses,id',
            // 'warehouses.*.spaces.*.containers.*.engaged' => 'required_if:warehouses.*.spaces.*.type,containers|numeric',
            // 'warehouses.*.spaces.*.containers.*.rent_period_id' => 'required_if:warehouses.*.spaces.*.type,containers|exists:rent_periods,id',
            'warehouses.*.spaces.*.containers.*.selected_rent' => 'required_if:warehouses.*.spaces.*.type,containers',

            'warehouses.*.spaces.*.containers.*.selected_rent.id' => [
                'required_if:warehouses.*.spaces.*.type,containers',
                Rule::exists('rents', 'id')->where(function ($query) use ($request) {
                    return $query->where('rent_period_id', $request->rent_period_id);
                }),
            ],

            'warehouses.*.spaces.*.containers.*.selected_rent.rent_period_id' => [
                'required_if:warehouses.*.spaces.*.type,containers', 
                'exists:rent_periods,id', 
                'exists:rents,rent_period_id',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value != $request->rent_period_id) {
                        $fail('The '.$attribute.' is invalid.');
                    }
                },
            ],

            'warehouses.*.spaces.*.containers.*.selected_rent.rent' => 'required_if:warehouses.*.spaces.*.type,containers|numeric',
            
            'warehouses.*.spaces.*.container' => 'required_if:warehouses.*.spaces.*.type,shelves|required_if:warehouses.*.spaces.*.type,units',
            'warehouses.*.spaces.*.container.id' => 'required_if:warehouses.*.spaces.*.type,shelves|required_if:warehouses.*.spaces.*.type,units|exists:warehouse_container_statuses,id',
            'warehouses.*.spaces.*.container.shelves' => 'required_if:warehouses.*.spaces.*.type,shelves|array|min:1',
            'warehouses.*.spaces.*.container.shelves.*.id' => 'required_if:warehouses.*.spaces.*.type,shelves|exists:warehouse_container_shelf_statuses,id',
            // 'warehouses.*.spaces.*.container.shelves.*.engaged' => 'required_if:warehouses.*.spaces.*.type,shelves|numeric',
            'warehouses.*.spaces.*.container.selected_rent' => 'required_if:warehouses.*.spaces.*.type,shelves|required_if:warehouses.*.spaces.*.type,units',
            
            'warehouses.*.spaces.*.container.selected_rent.id' => [
                'required_if:warehouses.*.spaces.*.type,shelves', 
                'required_if:warehouses.*.spaces.*.type,units', 
                Rule::exists('rents', 'id')->where(function ($query) use ($request) {
                    return $query->where('rent_period_id', $request->rent_period_id);
                }),
            ],

            'warehouses.*.spaces.*.container.selected_rent.rent_period_id' => [
                'required_if:warehouses.*.spaces.*.type,shelves', 
                'required_if:warehouses.*.spaces.*.type,units',
                'exists:rent_periods,id', 
                'exists:rents,rent_period_id',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value != $request->rent_period_id) {
                        $fail('The '.$attribute.' is invalid.');
                    }
                },
            ],

            'warehouses.*.spaces.*.container.selected_rent.rent' => 'required_if:warehouses.*.spaces.*.type,shelves|required_if:warehouses.*.spaces.*.type,units|numeric',

            'warehouses.*.spaces.*.container.shelf' => 'required_if:warehouses.*.spaces.*.type,units',
            'warehouses.*.spaces.*.container.shelf.id' => 'required_if:warehouses.*.spaces.*.type,units|exists:warehouse_container_shelf_statuses,id',
            'warehouses.*.spaces.*.container.shelf.units' => 'required_if:warehouses.*.spaces.*.type,units|array|min:1',
            'warehouses.*.spaces.*.container.shelf.units.*.id' => 'required_if:warehouses.*.spaces.*.type,units|exists:warehouse_container_shelf_unit_statuses,id',
            // 'warehouses.*.spaces.*.container.shelf.units.*.engaged' => 'required_if:warehouses.*.spaces.*.type,units|numeric',
        
        ],

        [
            'rents' => 'Rent is required !',
            // 'rents.*.current_due' => 'Stock quantity is required !',
            'rents.*.number_installment' => 'Number rent is required',
            'rents.*.date_from' => 'Rent counting date is required',
            'rents.*.date_to' => 'Rent ending date is required',
            'rents.*.discount' => 'Discount rate should be between 0 to 100',
            'rents.*.net_payable' => 'Net payable is invalid',
            'rents.*.total_paid_amount' => 'Paid amount is invalid',
            // 'rents.*.previous_due' => 'Previous due is invalid',
            'rents.*.total_rent' => 'Total rent is invalid',

            'warehouses' => 'Warehouse is required',
            'warehouses.*.id' => 'Warehouse id is Invalid',
            'warehouses.*.spaces' => 'Warehouse spaces is required',
            'warehouses.*.spaces.*.type' => 'Warehouse invalid space type',

            'warehouses.*.spaces.*.containers.*' => 'Containers is required',
            'warehouses.*.spaces.*.containers.*.id.*' => 'Container id is invalid',
            // 'warehouses.*.spaces.*.containers.*.engaged.*' => 'Selected container should be vacant',
            'warehouses.*.spaces.*.containers.*.selected_rent.*' => 'Container rent is required',
            'warehouses.*.spaces.*.containers.*.selected_rent.id.*' => 'Container rent is invalid',
            'warehouses.*.spaces.*.containers.*.selected_rent.rent_period_id.*' => 'Container rent is invalid',
            'warehouses.*.spaces.*.containers.*.selected_rent.rent.*' => 'Container rent is invalid',

            'warehouses.*.spaces.*.container.*' => 'Container is required',
            'warehouses.*.spaces.*.container.id.*' => 'Invalid container id',
            'warehouses.*.spaces.*.container.shelves.*' => 'Shelves are required',
            'warehouses.*.spaces.*.container.shelves.*.id.*' => 'Shelf id is invalid',
            // 'warehouses.*.spaces.*.container.shelves.*.engaged.*' => 'Selected shelf should be vacant',
            'warehouses.*.spaces.*.container.selected_rent.*' => 'Shelf or Unit rent is required',
            'warehouses.*.spaces.*.container.selected_rent.id.*' => 'Shelf or Unit rent is invalid',
            'warehouses.*.spaces.*.container.selected_rent.rent_period_id.*' => 'Shelf or Unit rent is invalid',
            'warehouses.*.spaces.*.container.selected_rent.rent.*' => 'Shelf or Unit rent is invalid',

            'warehouses.*.spaces.*.container.shelf.*' => 'Container shelf is required',
            'warehouses.*.spaces.*.container.shelf.id.*' => 'Shelf id is invalid',
            'warehouses.*.spaces.*.container.shelf.units.*' => 'Units are required',
            'warehouses.*.spaces.*.container.shelf.units.*.id.*' => 'Unit id is invalid',
            // 'warehouses.*.spaces.*.container.shelf.units.*.engaged.*' => 'Selected unit should be vacant',
        ]);

        $dealToUpdate = MerchantSpaceDeal::findOrFail($deal);

        $dealToUpdate->update([
            'active' => $request->active,
            // 'e_commerce_fulfillment' => $request->e_commerce_fulfillment,
            'auto_renewal' => $request->active ? $request->auto_renewal : false,
            // 'sale_percentage' => $request->e_commerce_fulfillment ? $request->sale_percentage : NULL,
            'rent_period_id' => $request->rent_period_id,
            'merchant_id' => $request->merchant_id,
        ]);

        $dealRecentRent = $dealToUpdate->rents()->firstOrFail();
            
        if ($dealToUpdate->rents->count() == 1 && $dealToUpdate->active) {

            $request['rents'] = json_decode(json_encode($request->rents)); 

            $dealRecentRent->update([
                'number_installment' => $request->rents[0]->number_installment,
                'date_from' => $request->rents[0]->date_from,
                'date_to' => $request->rents[0]->date_to,
                'total_rent' => $request->rents[0]->total_rent,
                'discount' => $request->rents[0]->discount,
                'net_payable' => $request->rents[0]->net_payable,
                'total_paid_amount' => $dealRecentRent->payments->count() > 1 ? $dealRecentRent->total_paid_amount : $request->rents[0]->total_paid_amount,
            ]);

            if ($dealRecentRent->payments->count() == 1) {
                
                $user = $request->user();

                $dealRecentRent->payments()->first()->update([
                    'paid_amount' => $request->rents[0]->total_paid_amount,
                    'current_due' => ($request->rents[0]->net_payable - $request->rents[0]->total_paid_amount),
                    'updater_type' => get_class($user),
                    'updater_id' => $user->id,
                ]);

            }

            // Reseting current spaces
            $this->resetDealtSpaces($dealToUpdate, $dealRecentRent);

            $request['warehouses'] = json_decode(json_encode($request->warehouses));

            foreach ($request->warehouses as $merchantWarehouseIndex => $merchantWarehouse) {
                
                foreach ($merchantWarehouse->spaces as $warehouseSpaceIndex => $warehouseSpace) {
                    
                    if($warehouseSpace->type == 'containers') {

                        $this->setDealtContainers($warehouseSpace->containers, $dealToUpdate, $dealRecentRent);
                    }
                    else if($warehouseSpace->type == 'shelves') {
        
                        $this->setDealtShelves($warehouseSpace, $dealToUpdate, $dealRecentRent);
                    }
                    else if ($warehouseSpace->type == 'units') {
                        
                        $this->setDealtUnits($warehouseSpace, $dealToUpdate, $dealRecentRent);
                    }
                    else {
                        // \Log::info("No Type");
                    }

                }

            }
           
        }
        
        // Only Deactivated
        else if (! $dealToUpdate->active) {

            // Reseting current spaces
            $this->retrieveDealtSpaces($dealToUpdate, $dealRecentRent);
            
        }

        return $this->showMerchantAllSpaceDeals($request->merchant_id, $perPage);
    }

    public function deleteMerchantSpaceDeal($deal, $perPage)
    {
        $dealToDelete = MerchantSpaceDeal::findOrFail($deal);

        if ($dealToDelete->rents->count() > 1) {
            
           return response()->json(['errors'=>["undeletableDeal" => "Deal has multiple rents"]], 422);
            
        }
        else if ($dealToDelete->spaces->count() && $dealToDelete->spaces()->whereHasMorph('space',[ WarehouseContainerStatus::class, WarehouseContainerShelfStatus::class, WarehouseContainerShelfUnitStatus::class ],function ($query1) {$query1->where('occupied', '!=', 0.0);})->exists()) {

           return response()->json(['errors'=>["undeletableDeal" => "Dealt space is occupied"]], 422);
            
        }
        else {

            $merchantId = $dealToDelete->merchant_id;
            
            $dealRecentRent = $dealToDelete->rents()->firstOrFail();      // rents
 
            $dealRecentRent->spaceRents()->delete();                           
            $dealRecentRent->payments()->delete();
            $this->resetDealtSpaces($dealToDelete, $dealRecentRent);      // spaces
            $dealRecentRent->delete();
            
            $dealToDelete->delete();

        }

        return $this->showMerchantAllSpaceDeals($merchantId, $perPage);
    }

    public function searchMerchantAllSpaceDeals(Request $request, $perPage)
    {
        $request->validate([
            'search' => 'nullable|required_without_all:dateTo,dateFrom|string', 
            'dateTo' => 'nullable|required_without_all:search,dateFrom|date',
            'dateFrom' => 'nullable|required_without_all:search,dateTo|date',
            // 'showPendingRequisitions' => 'nullable|boolean',
            // 'showCancelledRequisitions' => 'nullable|boolean',
            // 'showDispatchedRequisitions' => 'nullable|boolean',
            // 'showProduct' => 'nullable|string', 
        ]);


        $query = MerchantSpaceDeal::with(['spaces', 'rents'])->where('merchant_id', $request->merchant_id);

        if ($request->search) {
            
            $query->where(function($query5) use ($request) {
                $query5->whereHas('merchant', function ($query6) use ($request) {
                    $query6->where('first_name', 'like', "%$request->search%")
                    ->orWhere('last_name', 'like', "%$request->search%")
                    ->orWhere('user_name', 'like', "%$request->search%")
                    ->orWhere('email', 'like', "%$request->search%")
                    ->orWhere('mobile', 'like', "%$request->search%");
                })
                ->orWhereHas('spaces', function ($query2) use ($request) {
                    $query2->whereHasMorph(
                        'space',
                        [ WarehouseContainerStatus::class, WarehouseContainerShelfStatus::class, WarehouseContainerShelfUnitStatus::class ],
                        function ($query3) use ($request) {
                            $query3->where('name', 'like', "%$request->search%")
                            ->orWhereHas('warehouseContainer.container', function ($query8) use ($request) {
                                $query8->where('name', 'like', "%$request->search%");
                            });
                        }
                    );
                })
                ->orWhereHas('rents', function ($query4) use ($request) {
                    $query4->where('number_installment', 'like', "%$request->search%")
                    ->orWhereDate('date_from', '=', "$request->search")
                    ->orWhereDate('date_to', '=', "$request->search")
                    ->orWhere('total_rent', 'like', "%$request->search%")
                    ->orWhere('discount', 'like', "%$request->search%")
                    // ->orWhere('previous_due', 'like', "%$request->search%")
                    ->orWhere('net_payable', 'like', "%$request->search%")
                    ->orWhere('total_paid_amount', 'like', "%$request->search%")
                    // ->orWhere('current_due', 'like', "%$request->search%")
                    ->orWhereHas('payments', function ($query3) use ($request) {
                        $query3->where('invoice_no', 'like', "%$request->search%")
                        ->orWhere('paid_amount', 'like', "%$request->search%")
                        ->orWhere('current_due', 'like', "%$request->search%");
                    });
                });
            });

        }

        if ($request->dateFrom) {
            
            $query->whereDate('created_at', '>=', $request->dateFrom);

        }

        if ($request->dateTo) {
            
            $query->whereDate('created_at', '<=', $request->dateTo);

        }

        return response()->json([
            'all' => new MerchantSpaceDealCollection($query->latest()->paginate($perPage)),  
        ], 200);
    }

    // Deal-Rents
    public function showDealAllRents($deal, $perPage = false)
    {
        if ($perPage) {
            
            return new MerchantRentCollection(
                MerchantRent::with(['payments', 'spaceRents'])
                ->whereHasMorph('dealable', [MerchantSpaceDeal::class], function($query) use ($deal) {
                    $query->where('id', $deal);
                })
                ->paginate($perPage)
            );

        }
    }

    public function storeDealNewRent(Request $request, $perPage)
    {
        $request->validate([
            'merchant_space_deal_id' => 'required|exists:merchant_space_deals,id',
            'number_installment' => 'required|numeric|min:1',
            'date_from' => 'required|date|after_or_equal:1970-01-01 00:00:01|before_or_equal:2038-01-19 03:14:07',
            'date_to' => 'required|date|after_or_equal:1970-01-01 00:00:01|before_or_equal:2038-01-19 03:14:07',
            'total_rent' => 'required|numeric',
            'discount' => 'nullable|numeric|between:0,100',
            // 'previous_due' => 'numeric',
            'net_payable' => 'numeric',
            'total_paid_amount' => 'required|numeric|min:1',
            // 'current_due' => 'required|numeric',
        ],

        [
            // 'current_due' => 'Stock quantity is required !',
            'merchant_space_deal_id.*' => 'Merchant deal is required',
            'number_installment' => 'Rent rent number is required',
            'date_from' => 'Rent issueing date is required',
            'date_to' => 'Rent expiring date is required',
            'total_rent' => 'Total rent is invalid',
            'discount' => 'Discount rate should be between 0 to 100',
            // 'previous_due' => 'Previous due is invalid',
            'net_payable' => 'Net payable is invalid',
            'total_paid_amount' => 'Paid amount is invalid',
        ]);

        $merchantDeal = MerchantSpaceDeal::find($request->merchant_space_deal_id);

        $dealNewRent = $merchantDeal->rents()->create([
            'name' => ($merchantDeal->name.'R'.($merchantDeal->rents->count() + 1)),
            'number_installment' => $request->number_installment,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'total_rent' => $request->total_rent,
            'discount' => $request->discount ?? 0,
            'net_payable' => $request->net_payable ?? 0,
            'total_paid_amount' => $request->total_paid_amount ?? 0,
        ]);

        if ($dealNewRent->total_paid_amount > 0) {
            
            $user = $request->user();

            $rentNewPayment = $dealNewRent->payments()->create([
                'invoice_no' => ($dealNewRent->name.'P'.($dealNewRent->payments->count() + 1)),
                // 'previous_due' => 0,    // first payment
                'paid_amount' => $request->total_paid_amount ?? 0,
                'current_due' => ($request->net_payable - $request->total_paid_amount),
                'issuer_type' => get_class($user),
                'issuer_id' => $user->id,
            ]);

        }

        // rent-rents
        foreach ($merchantDeal->containers as $dealContainerIndex => $dealContainer) {
            
            $currentRent = $dealContainer->warehouseContainer->rents->where('rent_period_id', $merchantDeal->rent_period_id)->first();

            $payementNewRent = $dealNewRent->spaceRents()->create([
                'rent' => $currentRent->rent ?? 0,
                'dealt_space_id' => $dealContainer->id
            ]);

        }

        foreach ($merchantDeal->shelves as $dealShelfIndex => $dealShelf) {
            
            $currentRent = $dealShelf->warehouseContainer->shelf->rents->where('rent_period_id', $merchantDeal->rent_period_id)->first();

            $payementNewRent = $dealNewRent->spaceRents()->create([
                'rent' => $currentRent->rent ?? 0,
                'dealt_space_id' => $dealShelf->id
            ]);

        }

        foreach ($merchantDeal->units as $dealUnitIndex => $dealUnit) {
            
            $currentRent = $dealUnit->warehouseContainer->shelf->unit->rents->where('rent_period_id', $merchantDeal->rent_period_id)->first();

            $payementNewRent = $dealNewRent->spaceRents()->create([
                'rent' => $currentRent->rent ?? 0,
                'dealt_space_id' => $dealUnit->id
            ]);

        }

        return $this->showDealAllRents($request->merchant_space_deal_id, $perPage);
    }

    public function updateDealRent(Request $request, $rent, $perPage)
    {
        $request->validate([
            // 'merchant_space_deal_id' => 'required|exists:merchant_space_deals,id',
            'number_installment' => 'required|numeric|min:1',
            'date_from' => 'required|date|after_or_equal:1970-01-01 00:00:01|before_or_equal:2038-01-19 03:14:07',
            'date_to' => 'required|date|after_or_equal:1970-01-01 00:00:01|before_or_equal:2038-01-19 03:14:07',
            'total_rent' => 'required|numeric',
            'discount' => 'nullable|numeric|between:0,100',
            // 'previous_due' => 'numeric',
            'net_payable' => 'numeric',
            'total_paid_amount' => 'required|numeric|min:1',
            // 'current_due' => 'required|numeric',
        ],

        [
            // 'current_due' => 'Stock quantity is required !',
            // 'merchant_space_deal_id.*' => 'Merchant deal is required',
            'number_installment' => 'Rent rent number is required',
            'date_from' => 'Rent issueing date is required',
            'date_to' => 'Rent expiring date is required',
            'total_rent' => 'Total rent is invalid',
            'discount' => 'Discount rate should be between 0 to 100',
            // 'previous_due' => 'Previous due is invalid',
            'net_payable' => 'Net payable is invalid',
            'total_paid_amount' => 'Paid amount is invalid',
        ]);

        $rentToUpdate = MerchantRent::where('id', $rent)
        ->whereHasMorph('dealable', [MerchantSpaceDeal::class], function($query) use ($request) {
            $query->where('id', $request->deal['id']);
        })->firstOrFail();

        $rentToUpdate->update([
            'number_installment' => $request->number_installment,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'total_rent' => $request->total_rent,
            'discount' => $request->discount ?? 0,
            'net_payable' => $request->net_payable ?? 0,
            'total_paid_amount' => $rentToUpdate->payments->count() > 1 ? $rentToUpdate->total_paid_amount : $request->total_paid_amount ?? 0,
        ]);

        // single payment
        if ($rentToUpdate->payments->count() <= 1 && $rentToUpdate->total_paid_amount > 0) {     // payments : 0 / 1
            
            $user = $request->user();

            $rentToUpdate->payments()->updateOrCreate(
                [
                    'merchant_rent_id' => $rentToUpdate->id
                ],
                [
                    'invoice_no' => $rentToUpdate->payments->count() ? $rentToUpdate->payments->first()->invoice_no : ($rentToUpdate->name.'P'.($rentToUpdate->payments->count() + 1)),
                    'paid_amount' => $request->total_paid_amount ?? 0,
                    'current_due' => ($request->net_payable - $request->total_paid_amount),
                    'issuer_type' => $rentToUpdate->payments->count() ? $rentToUpdate->payments->first()->issuer_type : get_class($user),
                    'issuer_id' => $rentToUpdate->payments->count() ? $rentToUpdate->payments->first()->issuer_id : $user->id,
                    'updater_type' => get_class($user),
                    'updater_id' => $user->id,
                ]
            );

        }

        return $this->showDealAllRents($request->deal['id'], $perPage);
    }

    public function deleteDealRent($rent, $perPage)
    {
        $dealRentToDelete = MerchantRent::findOrFail($rent);

        $deal = $dealRentToDelete->deal;

        $dealRentToDelete->spaceRents()->delete();
        $dealRentToDelete->payments()->delete();
        $dealRentToDelete->delete();

        return $this->showDealAllRents($deal->id, $perPage);
    }

    public function searchDealAllRents(Request $request, $perPage)
    {
        $request->validate([
            'merchant_space_deal_id' => 'required|exists:merchant_space_deals,id',
            'search' => 'nullable|required_without_all:dateTo,dateFrom|string', 
            'dateTo' => 'nullable|required_without_all:search,dateFrom|date|after_or_equal:1970-01-01 00:00:01|before_or_equal:2038-01-19 03:14:07',
            'dateFrom' => 'nullable|required_without_all:search,dateTo|date|after_or_equal:1970-01-01 00:00:01|before_or_equal:2038-01-19 03:14:07',
            // 'showPendingRequisitions' => 'nullable|boolean',
            // 'showCancelledRequisitions' => 'nullable|boolean',
            // 'showDispatchedRequisitions' => 'nullable|boolean',
            // 'showProduct' => 'nullable|string', 
        ]);

        $query = MerchantRent::with(['spaceRents', 'payments'])
        ->whereHas('deal', function ($query1) use ($request) {
            $query1->where('merchant_space_deal_id', $request->merchant_space_deal_id);
        });

        if ($request->search) {
            
            $query->where(function($query2) use ($request) {
                $query2->where('name', 'like', "%$request->search%")
                ->orWhere('number_installment', 'like', "%$request->search%")
                ->orWhereDate('date_from', '=', "$request->search")
                ->orWhereDate('date_to', '=', "$request->search")
                ->orWhere('total_rent', 'like', "%$request->search%")
                ->orWhere('discount', 'like', "%$request->search%")
                // ->orWhere('previous_due', 'like', "%$request->search%")
                ->orWhere('net_payable', 'like', "%$request->search%")
                ->orWhere('total_paid_amount', 'like', "%$request->search%")
                // ->orWhere('current_due', 'like', "%$request->search%")
                ->orWhereHas('spaceRents', function ($query3) use ($request) {
                    $query3->where('rent', 'like', "%$request->search%");
                })
                ->orWhereHas('payments', function ($query4) use ($request) {
                    $query4->where('invoice_no', 'like', "%$request->search%")
                    ->orWhere('paid_amount', 'like', "%$request->search%")
                    ->orWhere('current_due', 'like', "%$request->search%");
                });
            });

        }

        if ($request->dateFrom) {
            
            $query->whereDate('created_at', '>=', $request->dateFrom);

        }

        if ($request->dateTo) {
            
            $query->whereDate('created_at', '<=', $request->dateTo);

        }

        return response()->json([
            'all' => new MerchantRentCollection($query->paginate($perPage)),  
        ], 200);
    }

    // Rent-Payments
    public function showRentalAllPayments($rent, $perPage = false)
    {
        if ($perPage) {
            
            return new MerchantPaymentCollection(
                MerchantPayment::where('merchant_rent_id', $rent)->latest('paid_at')->paginate($perPage)
            );

        }
    }

    public function storeRentalNewPayment(Request $request, $perPage)
    {
        $request->validate([
            'merchant_rent_id' => 'required|exists:merchant_rents,id',
            'paid_amount' => 'required|numeric|min:1',
            // 'current_due' => 'required|numeric',
        ],

        [
            // 'current_due' => 'Stock quantity is required !',
            'merchant_rent_id.*' => 'Deal rent is required',
            'paid_amount' => 'Paid amount is invalid',
        ]);

        $dealRent = MerchantRent::find($request->merchant_rent_id);

        if (($dealRent->net_payable - $dealRent->total_paid_amount) < $request->paid_amount) {
            
            return response()->json(['errors'=>["invalidAmount" => "Paid amount is more than due"]], 422);

        }

        $user = $request->user();

        $dealNewPayment = $dealRent->payments()->create([
            'invoice_no' => ($dealRent->name.'P'.($dealRent->payments->count() + 1)),
            'paid_amount' => $request->paid_amount,
            'current_due' => (($dealRent->net_payable - $dealRent->total_paid_amount) - $request->paid_amount),
            'issuer_type' => get_class($user),
            'issuer_id' => $user->id,
        ]);

        $dealRent->update([
            'total_paid_amount' => ($dealRent->total_paid_amount + $request->paid_amount),
        ]);

        return $this->showRentalAllPayments($dealRent->id, $perPage);
    }

    public function updateRentalPayment(Request $request, $payment, $perPage)
    {
        $request->validate([
            'merchant_rent_id' => 'required|exists:merchant_rents,id',
            'paid_amount' => 'required|numeric|min:1',
            // 'current_due' => 'required|numeric',
        ],

        [
            // 'current_due' => 'Stock quantity is required !',
            'merchant_rent_id.*' => 'Deal rent is required',
            'paid_amount' => 'Paid amount is invalid',
        ]);

        $dealRent = MerchantRent::find($request->merchant_rent_id);
        $paymentToUpdate = $dealRent->payments()->where('id', $payment)->firstOrFail();
        $difference = $request->paid_amount - $paymentToUpdate->paid_amount;  // - / +  
        
        // increasing value is more than required
        if ($difference > 0 && ($dealRent->net_payable - $dealRent->total_paid_amount) < $difference) {        
            
            return response()->json(['errors'=>["invalidAmount" => "Paid amount is more than due"]], 422);

        }

        $dealRent->update([
            'total_paid_amount' => $dealRent->total_paid_amount + $difference,
        ]);

        $user = $request->user();

        $paymentToUpdate->update([
            'paid_amount' => $request->paid_amount,
            'current_due' => ($dealRent->net_payable - ($dealRent->payments()->where('id', '<=', $paymentToUpdate->id)->sum('paid_amount') + $difference)),       // as can be + / -
            'updater_type' => get_class($user),
            'updater_id' => $user->id,
        ]);

        $this->adjustSuccessorDues($paymentToUpdate->id, $dealRent, $user);

        return $this->showRentalAllPayments($dealRent->id, $perPage);
    }

    public function deleteRentalPayment($payment, $perPage)
    {
        $dealPaymentToDelete = MerchantPayment::findOrFail($payment);
        $dealRent = $dealPaymentToDelete->rent;

        $dealRent->update([
            'total_paid_amount' => ($dealRent->total_paid_amount - $dealPaymentToDelete->paid_amount),
            // 'current_due' => ($dealRent->current_due + $dealPaymentToDelete->paid_amount),
        ]);

        $deletingPaymentId = $dealPaymentToDelete->id;

        $dealPaymentToDelete->delete();

        $this->adjustSuccessorDues($deletingPaymentId, $dealRent);

        return $this->showRentalAllPayments($dealRent->id, $perPage);
    }

    public function searchRentalAllPayments(Request $request, $perPage)
    {
        $request->validate([
            'merchant_rent_id' => 'required|exists:merchant_rents,id',
            'search' => 'nullable|required_without_all:dateTo,dateFrom|string', 
            'dateTo' => 'nullable|required_without_all:search,dateFrom|date|after_or_equal:1970-01-01 00:00:01|before_or_equal:2038-01-19 03:14:07',
            'dateFrom' => 'nullable|required_without_all:search,dateTo|date|after_or_equal:1970-01-01 00:00:01|before_or_equal:2038-01-19 03:14:07',
            // 'showPendingRequisitions' => 'nullable|boolean',
            // 'showCancelledRequisitions' => 'nullable|boolean',
            // 'showDispatchedRequisitions' => 'nullable|boolean',
            // 'showProduct' => 'nullable|string', 
        ]);

        $query = MerchantPayment::where('merchant_rent_id', $request->merchant_rent_id);

        if ($request->search) {
            
            $query->where(function($query2) use ($request) {
                $query2->where('invoice_no', 'like', "%$request->search%")
                ->orWhere('paid_amount', 'like', "%$request->search%")
                ->orWhere('current_due', 'like', "%$request->search%");
            });

        }

        if ($request->dateFrom) {
            
            $query->whereDate('paid_at', '>=', $request->dateFrom);

        }

        if ($request->dateTo) {
            
            $query->whereDate('paid_at', '<=', $request->dateTo);

        }

        return response()->json([
            'all' => new MerchantPaymentCollection($query->latest('paid_at')->paginate($perPage)),  
        ], 200);
    }

    protected function adjustSuccessorDues($paymentStartingId, MerchantRent $dealRent, $user = NULL)
    {
        // rent next-payments
        foreach (MerchantPayment::where('merchant_rent_id', $dealRent->id)->where('id', '>', $paymentStartingId)->get() as $successorPaymentKey => $successorPayment) {
            
            $successorPayment->update([
                // 'paid_amount' => $request->paid_amount,
                'current_due' => ($dealRent->net_payable - ($dealRent->payments()->where('id', '<=', $successorPayment->id)->sum('paid_amount'))),
                'updater_type' => empty($user) ? NULL : get_class($user),
                'updater_id' => empty($user) ? NULL : $user->id,
            ]);

        }
    }

    protected function retrieveDealtSpaces(MerchantSpaceDeal $deal, MerchantRent $rent)
    {
        // containers
        foreach ($deal->containers as $merchantContainerKey => $merchantContainer) {
               
            // $addedContainer = WarehouseContainerStatus::find($merchantContainer->space_id);
            $addedContainer = $merchantContainer->space;

            if (!empty($addedContainer) && $addedContainer->occupied==0.0) {
                
                $addedContainer->update([
                    'engaged' => 0
                ]);

                $addedContainer->updateContainerStatus(0);

                $sameTypeContainerUsingSameRentPeriod = DealtSpace::where('warehouse_container_id', $addedContainer->warehouse_container_id)->where('id', '!=', $merchantContainer->id)
                ->whereHas('deal', function ($query) use ($merchantContainer) {
                    $query->where('rent_period_id', $merchantContainer->deal->rent_period_id);
                })
                ->exists();

                if (! $sameTypeContainerUsingSameRentPeriod) {
                    
                   Rent::find($addedContainer->warehouseContainer->rents()->where('rent_period_id', $merchantContainer->deal->rent_period_id))->first()->update([
                        'active' => 0
                   ]); 

                }

                // $payment->rents()->where('dealt_space_id', $merchantContainer->id)->delete();

                // $merchantContainer->delete();   // delete dealt space

            }

        }

        // shelves
        if ($deal->shelves->count()) {

            foreach ($deal->shelves as $merchantShelfKey => $merchantShelf) {
               
                // $addedShelf = WarehouseContainerShelfStatus::find($merchantShelf->space_id);
                $addedShelf = $merchantShelf->space;

                if (! empty($addedShelf) && $addedShelf->occupied==0.0) {

                    $addedShelf->update([
                        'engaged' => 0
                    ]);

                    $addedShelf->parentContainer->updateChildUnits($addedShelf, 0);

                    $sameTypeContainerShelfUsingSameRentPeriod = DealtSpace::where('warehouse_container_id', $addedShelf->warehouse_container_id)->where('id', '!=', $merchantShelf->id)
                    ->whereHas('deal', function ($query) use ($merchantShelf) {
                        $query->where('rent_period_id', $merchantShelf->deal->rent_period_id);
                    })
                    ->exists();

                    if (! $sameTypeContainerShelfUsingSameRentPeriod) {
                        
                       Rent::find($addedShelf->warehouseContainer->shelf->rents()->where('rent_period_id', $merchantShelf->deal->rent_period_id))->first()->update([
                            'active' => 0
                       ]); 

                    }

                    // $payment->rents()->where('dealt_space_id', $merchantShelf->id)->delete();

                    // $merchantShelf->delete();

                }

            }

            $addedShelf->parentContainer->updateParentContainer($addedShelf->parentContainer);

        }

        // units
        if ($deal->units->count()) {

            foreach ($deal->units as $merchantUnitfKey => $merchantUnit) {
               
                // $addedUnit = WarehouseContainerShelfUnitStatus::find($merchantUnit->space_id);
                $addedUnit = $merchantUnit->space;

                if (! empty($addedUnit) && $addedUnit->occupied==0.0) {

                    $addedUnit->update([
                        'engaged' => 0
                    ]);

                    $unitOfSameContainerShelfUsingSameRentPeriod = DealtSpace::where('warehouse_container_id', $addedUnit->warehouse_container_id)->where('id', '!=', $merchantUnit->id)
                    ->whereHas('deal', function ($query) use ($merchantUnit) {
                        $query->where('rent_period_id', $merchantUnit->deal->rent_period_id);
                    })
                    ->exists();

                    if (! $unitOfSameContainerShelfUsingSameRentPeriod) {
                        
                       Rent::find($addedUnit->warehouseContainer->shelf->unit->rents()->where('rent_period_id', $merchantUnit->deal->rent_period_id))->first()->update([
                            'active' => 0
                       ]); 

                    }

                    // $payment->rents()->where('dealt_space_id', $merchantUnit->id)->delete();

                    // $merchantUnit->delete();

                }

            }

            // Parent Shelf
            $addedUnit->parentShelf->parentContainer->updateParentShelf($addedUnit->parentShelf);

        }
    }

    protected function resetDealtSpaces(MerchantSpaceDeal $deal, MerchantRent $rent)
    {
        // containers
        foreach ($deal->containers as $merchantContainerKey => $merchantContainer) {
               
            $addedContainer = WarehouseContainerStatus::find($merchantContainer->space_id);

            if (! empty($addedContainer) && $addedContainer->occupied==0.0) {
                
                $addedContainer->update([
                    'engaged' => 0
                ]);

                $addedContainer->updateContainerStatus(0);      // update container-shelves & units

                // if current rent of same type container at same warehouse is using anywhere else
                $sameTypeContainerUsingSameRentPeriod = DealtSpace::where('warehouse_container_id', $addedContainer->warehouse_container_id)
                ->where('id', '!=', $merchantContainer->id)
                ->whereHas('deal', function ($query) use ($merchantContainer) {
                    $query->where('rent_period_id', $merchantContainer->deal->rent_period_id);
                })
                ->exists();

                if (! $sameTypeContainerUsingSameRentPeriod) {
                    
                   // update container rent activness
                   $addedContainer->warehouseContainer->rents->where('rent_period_id', $merchantContainer->deal->rent_period_id)->first()->update([
                        'active' => 0
                   ]); 

                }

                // $rent->rents()->where('dealt_space_id', $merchantContainer->id)->delete();

                $merchantContainer->delete();   // delete dealt space

            }

        }

        // shelves
        if ($deal->shelves->count()) {

            foreach ($deal->shelves as $merchantShelfKey => $merchantShelf) {
               
                $addedShelf = WarehouseContainerShelfStatus::find($merchantShelf->space_id);

                if (! empty($addedShelf) && $addedShelf->occupied==0.0) {

                    $addedShelf->update([
                        'engaged' => 0
                    ]);

                    $addedShelf->parentContainer->updateChildUnits($addedShelf, 0);

                    $sameTypeContainerShelfUsingSameRentPeriod = DealtSpace::where('warehouse_container_id', $addedShelf->warehouse_container_id)->where('id', '!=', $merchantShelf->id)
                    ->whereHas('deal', function ($query) use ($merchantShelf) {
                        $query->where('rent_period_id', $merchantShelf->deal->rent_period_id);
                    })
                    ->exists();

                    if (! $sameTypeContainerShelfUsingSameRentPeriod) {
                        
                       Rent::find($addedShelf->warehouseContainer->shelf->rents->where('rent_period_id', $merchantShelf->deal->rent_period_id))->first()->update([
                            'active' => 0
                       ]); 

                    }

                    // $rent->rents()->where('dealt_space_id', $merchantShelf->id)->delete();

                    // Parent Container
                    $addedShelf->parentContainer->updateParentContainer($addedShelf->parentContainer);

                    $merchantShelf->delete();

                }

            }

        }

        // units
        if ($deal->units->count()) {

            foreach ($deal->units as $merchantUnitfKey => $merchantUnit) {
               
                $addedUnit = WarehouseContainerShelfUnitStatus::find($merchantUnit->space_id);

                if (! empty($addedUnit) && $addedUnit->occupied==0.0) {

                    $addedUnit->update([
                        'engaged' => 0
                    ]);

                    $unitOfSameContainerShelfUsingSameRentPeriod = DealtSpace::where('warehouse_container_id', $addedUnit->warehouse_container_id)->where('id', '!=', $merchantUnit->id)
                    ->whereHas('deal', function ($query) use ($merchantUnit) {
                        $query->where('rent_period_id', $merchantUnit->deal->rent_period_id);
                    })
                    ->exists();

                    if (! $unitOfSameContainerShelfUsingSameRentPeriod) {
                        
                       Rent::find($addedUnit->warehouseContainer->shelf->unit->rents->where('rent_period_id', $merchantUnit->deal->rent_period_id))->first()->update([
                            'active' => 0
                       ]); 

                    }

                    // $rent->rents()->where('dealt_space_id', $merchantUnit->id)->delete();

                    // Parent Shelf
                    $addedUnit->parentShelf->parentContainer->updateParentShelf($addedUnit->parentShelf);

                    $merchantUnit->delete();

                }

            }

        }
    }

    protected function setDealtContainers($containers = [], MerchantSpaceDeal $deal, MerchantRent $rent)
    {
        if (count($containers)) {
            
            foreach ($containers as $warehouseContainerIndex => $warehouseContainer) {
                        
                $expectedContainer = WarehouseContainerStatus::find($warehouseContainer->id);
                
                if (!empty($expectedContainer) && $expectedContainer->engaged == 0.0) {
                    
                    $newSpaces = $expectedContainer->deals()->create([
                        // 'rent_period_id' => $warehouseContainer->selected_rent->rent_period_id,
                        // 'warehouse_id' => $expectedContainer->warehouseContainer->warehouse_id,
                        'warehouse_container_id' => $expectedContainer->warehouse_container_id,
                        'merchant_space_deal_id' => $deal->id,
                    ]);
        
                    $expectedContainer->update([
                        'engaged' => 1
                    ]);
                    
                    $expectedContainer->updateContainerStatus(1); 
                    
                    $containerNewRent = $rent->spaceRents()->create([
                        'rent' => $warehouseContainer->selected_rent->rent,
                        'dealt_space_id' => $newSpaces->id
                    ]);

                    Rent::find($warehouseContainer->selected_rent->id)->update([
                        'active' => 1
                    ]);
    
                }
    
            }

        }

    }

    protected function setDealtShelves($warehouseSpace, MerchantSpaceDeal $deal, MerchantRent $rent)
    {
        if (count($warehouseSpace->container->shelves)) {

            if (count($warehouseSpace->container->shelves) === WarehouseContainerStatus::find($warehouseSpace->container->id)->containerShelfStatuses()->count()) {
                
                $this->setDealtContainers([ $warehouseSpace->container ], $deal, $rent);

            }
            else {
                
                foreach ($warehouseSpace->container->shelves as $warehouseContainerShelfIndex => $warehouseContainerShelf) {
                                
                    $containerExpectedShelf = WarehouseContainerShelfStatus::find($warehouseContainerShelf->id);
                    
                    if (! empty($containerExpectedShelf) && $containerExpectedShelf->engaged==0.0) {
                        
                        $dealtNewShelf = $containerExpectedShelf->deals()->create([
                            'warehouse_container_id' => $containerExpectedShelf->warehouse_container_id,
                            'merchant_space_deal_id' => $deal->id
                        ]);
        
                        $containerExpectedShelf->update([
                            'engaged' => 1
                        ]);
            
                        $containerExpectedShelf->parentContainer->updateChildUnits($containerExpectedShelf, 1);
                        
                        $shelfNewRent = $rent->spaceRents()->create([
                            'rent' => $warehouseSpace->container->selected_rent->rent,
                            'dealt_space_id' => $dealtNewShelf->id
                        ]);
        
                    }
        
                }
        
                $containerExpectedShelf->parentContainer->updateParentContainer($containerExpectedShelf->parentContainer);

                Rent::find($warehouseSpace->container->selected_rent->id)->update([
                    'active' => 1
                ]);

            }

        }

    }

    protected function setDealtUnits($warehouseSpace, MerchantSpaceDeal $deal, MerchantRent $rent) 
    {
        if ($warehouseSpace->container->shelf->units) {

            if (count($warehouseSpace->container->shelf->units) === WarehouseContainerShelfStatus::find($warehouseSpace->container->shelf->id)->containerShelfUnitStatuses()->count()) {
                
                $warehouseSpace->container->{"shelves"} = [ $warehouseSpace->container->shelf ];
                
                $this->setDealtShelves($warehouseSpace, $deal, $rent);

            }
            else {
                
                foreach ($warehouseSpace->container->shelf->units as $containerShelfUnit) {
                    
                    $warehouseContainerShelfExpectedUnit = WarehouseContainerShelfUnitStatus::find($containerShelfUnit->id);

                    if (!empty($warehouseContainerShelfExpectedUnit) && $warehouseContainerShelfExpectedUnit->engaged==0.0) {

                        $dealtNewUnit = $warehouseContainerShelfExpectedUnit->deals()->create([
                            // 'rent_period_id' => $warehouseSpace->container->selected_rent->rent_period_id,
                            // 'warehouse_id' => $warehouseContainerShelfExpectedUnit->warehouseContainer->warehouse_id,
                            'warehouse_container_id' => $warehouseContainerShelfExpectedUnit->warehouse_container_id,
                            'merchant_space_deal_id' => $deal->id
                        ]);

                        $warehouseContainerShelfExpectedUnit->update([
                            'engaged' => 1
                        ]);

                        $unitNewRent = $rent->spaceRents()->create([
                            'rent' => $warehouseSpace->container->selected_rent->rent,
                            'dealt_space_id' => $dealtNewUnit->id
                        ]);

                    }

                }

                // Parent Shelf
                $warehouseContainerShelfExpectedUnit->parentShelf->parentContainer->updateParentShelf($warehouseContainerShelfExpectedUnit->parentShelf);

                Rent::find($warehouseSpace->container->selected_rent->id)->update([
                    'active' => 1
                ]);

            }

        }

    }

}
