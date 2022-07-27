<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Rent;
use App\Models\DealtSpace;
use App\Models\RentPeriod;
use App\Models\MerchantDeal;
use Illuminate\Http\Request;
use App\Models\MerchantPayment;
use Illuminate\Validation\Rule;
use App\Models\MerchantPaymentDetail;
use App\Models\WarehouseContainerStatus;
use App\Http\Resources\Web\DealCollection;
use App\Models\WarehouseContainerShelfStatus;
use App\Http\Resources\Web\DealPaymentCollection;
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

        // Deal-Payment
        $this->middleware("permission:view-merchant-payment-index")->only(['showDealAllPayments', 'searchDealAllPayments']);
        $this->middleware("permission:create-merchant-payment")->only('storeDealNewPayment');
        $this->middleware("permission:update-merchant-payment")->only('updateDealPayment');
        $this->middleware("permission:delete-merchant-payment")->only('deleteDealPayment');       
    }

    // Merchant-Deal
    public function showMerchantAllDeals($merchant, $perPage)
    {
        return new DealCollection(MerchantDeal::where('merchant_id', $merchant)->with(['spaces', 'payments'])->with(['rentPeriod' => function($query) {
                    $query->withTrashed();
        }])
        ->latest()->paginate($perPage));
    }

    public function storeMerchantDeal(Request $request, $perPage)
    {
        $request->validate([

            'active' => 'required|boolean',
            'auto_renewal' => 'required|boolean',
            'e_commerce_fulfillment' => 'required|boolean',
            'merchant_id' => 'required|exists:merchants,id',
            'sale_percentage' => 'required_if:e_commerce_fulfillment,1|min:0|max:100',
            'rent_period_id' => 'required|exists:rent_periods,id|exists:rents,rent_period_id',
            
            'payments' => 'required|array|min:1',
            'payments.*.number_installment' => 'required|numeric',
            'payments.*.date_from' => 'required|date|after_or_equal:1970-01-01 00:00:01|before_or_equal:2038-01-19 03:14:07',
            'payments.*.date_to' => 'required|date|after_or_equal:1970-01-01 00:00:01|before_or_equal:2038-01-19 03:14:07',
            'payments.*.total_rent' => 'required|numeric',
            'payments.*.discount' => 'required|numeric|between:0,100',
            'payments.*.previous_due' => 'numeric|min:0',
            'payments.*.net_payable' => 'required|numeric',
            'payments.*.paid_amount' => 'required|numeric|min:0',
            // 'payments.*.current_due' => 'required|numeric',

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
            'payments' => 'Payment is required !',
            // 'payments.*.current_due' => 'Stock quantity is required !',
            'payments.*.number_installment' => 'Number installment is required',
            'payments.*.date_from' => 'Rent counting date is required',
            'payments.*.date_to' => 'Rent ending date is required',
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

        $newDeal = MerchantDeal::create([
            'active' => $request->active,
            'e_commerce_fulfillment' => $request->e_commerce_fulfillment,
            'auto_renewal' => $request->auto_renewal,
            'sale_percentage' => $request->e_commerce_fulfillment ? $request->sale_percentage : NULL,
            'rent_period_id' => $request->rent_period_id,
            'merchant_id' => $request->merchant_id,
            'created_at' => now()
        ]);

        $request['payments'] = json_decode(json_encode($request->payments)); 

        $dealNewPayment = $newDeal->payments()->create([
            'invoice_no' => $newDeal->name.'P1',
            'number_installment' => $request->payments[0]->number_installment,
            'date_from' => $request->payments[0]->date_from,
            'date_to' => $request->payments[0]->date_to,
            'total_rent' => $request->payments[0]->total_rent,
            'discount' => $request->payments[0]->discount,
            'previous_due' => 0,
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
            'rent_period_id' => 'required|exists:rent_periods,id|exists:rents,rent_period_id',
            
            'payments' => 'required|array|min:1',
            'payments.*.number_installment' => 'required|numeric',
            'payments.*.date_from' => 'required|date|after_or_equal:1970-01-01 00:00:01|before_or_equal:2038-01-19 03:14:07',
            'payments.*.date_to' => 'required|date|after_or_equal:1970-01-01 00:00:01|before_or_equal:2038-01-19 03:14:07',
            'payments.*.total_rent' => 'required|numeric',
            'payments.*.discount' => 'required|numeric|between:0,100',
            'payments.*.previous_due' => 'required|numeric',
            'payments.*.net_payable' => 'required|numeric',
            'payments.*.paid_amount' => 'required|numeric|min:0',
            // 'payments.*.current_due' => 'required|numeric',

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
            'payments' => 'Payment is required !',
            // 'payments.*.current_due' => 'Stock quantity is required !',
            'payments.*.number_installment' => 'Number installment is required',
            'payments.*.date_from' => 'Rent counting date is required',
            'payments.*.date_to' => 'Rent ending date is required',
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

        $dealToUpdate = MerchantDeal::findOrFail($deal);
        $dealRecentPayment = $dealToUpdate->payments()->latest('paid_at')->first();

        $dealToUpdate->update([
            'active' => $request->active,
            'e_commerce_fulfillment' => $request->e_commerce_fulfillment,
            'auto_renewal' => $request->active ? $request->auto_renewal : false,
            'sale_percentage' => $request->e_commerce_fulfillment ? $request->sale_percentage : NULL,
            'rent_period_id' => $request->rent_period_id,
            'merchant_id' => $request->merchant_id,
        ]);

        if ($dealToUpdate->payments->count() < 2 && $dealToUpdate->active) {

            $request['payments'] = json_decode(json_encode($request->payments)); 

            $dealRecentPaymentUpdated = $dealRecentPayment->update([
                'number_installment' => $request->payments[0]->number_installment,
                'date_from' => $request->payments[0]->date_from,
                'date_to' => $request->payments[0]->date_to,
                'total_rent' => $request->payments[0]->total_rent,
                'discount' => $request->payments[0]->discount,
                'previous_due' => 0,
                'net_payable' => $request->payments[0]->net_payable,
                'paid_amount' => $request->payments[0]->paid_amount,
                'current_due' => ($request->payments[0]->net_payable - $request->payments[0]->paid_amount),
            ]);
            
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

        if ($dealToDelete->payments->count() > 1) {
            
           return response()->json(['errors'=>["undeletableDeal" => "Deal has multiple payments"]], 422);
            
        }
        else if ($dealToDelete->spaces->count() && $dealToDelete->spaces()->whereHasMorph('space',[ WarehouseContainerStatus::class, WarehouseContainerShelfStatus::class, WarehouseContainerShelfUnitStatus::class ],function ($query1) {$query1->where('occupied', '!=', 0.0);})->exists()) {

           return response()->json(['errors'=>["undeletableDeal" => "Dealt space is occupied"]], 422);
            
        }
        else {

            $merchantId = $dealToDelete->merchant_id;
            
            $dealRecentPayment = $dealToDelete->payments()->first();

            if ($dealRecentPayment) {
               
                $this->resetDealtSpaces($dealToDelete, $dealRecentPayment);
                $dealRecentPayment->rents()->delete();
                $dealRecentPayment->delete();

            }

            $dealToDelete->delete();

        }

        return $this->showMerchantAllDeals($merchantId, $perPage);
    }

    public function searchMerchantAllDeals(Request $request, $perPage)
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


        $query = MerchantDeal::with(['spaces', 'payments'])->where('merchant_id', $request->merchant_id);

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
                ->orWhereHas('payments', function ($query4) use ($request) {
                    $query4->where('invoice_no', 'like', "%$request->search%")
                    ->orWhere('number_installment', 'like', "%$request->search%")
                    ->orWhere('date_from', 'like', "%$request->search%")
                    ->orWhere('date_to', 'like', "%$request->search%")
                    ->orWhere('total_rent', 'like', "%$request->search%")
                    ->orWhere('discount', 'like', "%$request->search%")
                    ->orWhere('previous_due', 'like', "%$request->search%")
                    ->orWhere('net_payable', 'like', "%$request->search%")
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
            'all' => new DealCollection($query->latest()->paginate($perPage)),  
        ], 200);
    }

    public function showDealAllPayments($deal, $perPage = false)
    {
        if ($perPage) {
            
            return new DealPaymentCollection(
                MerchantPayment::with(['rents'])->whereHas('deal', function ($query) use ($deal) {
                    $query->where('merchant_deal_id', $deal);
                })->latest('paid_at')->paginate($perPage)
            );

        }
    }

    public function storeDealNewPayment(Request $request, $perPage)
    {
        $request->validate([
            'merchant_deal_id' => 'required|exists:merchant_deals,id',
            'number_installment' => 'required|numeric|min:1',
            'date_from' => 'required|date|after_or_equal:1970-01-01 00:00:01|before_or_equal:2038-01-19 03:14:07',
            'date_to' => 'required|date|after_or_equal:1970-01-01 00:00:01|before_or_equal:2038-01-19 03:14:07',
            'total_rent' => 'required|numeric',
            'discount' => 'required|numeric|between:0,100',
            'previous_due' => 'numeric',
            'net_payable' => 'numeric',
            'paid_amount' => 'required|numeric|min:0',
            // 'current_due' => 'required|numeric',
        ],

        [
            // 'current_due' => 'Stock quantity is required !',
            'merchant_deal_id.*' => 'Payment deal is required',
            'number_installment' => 'Rent installment number is required',
            'date_from' => 'Rent issueing date is required',
            'date_to' => 'Rent expiring date is required',
            'total_rent' => 'Total rent is invalid',
            'discount' => 'Discount rate should be between 0 to 100',
            'previous_due' => 'Previous due is invalid',
            'net_payable' => 'Net payable is invalid',
            'paid_amount' => 'Paid amount is invalid',
        ]);

        $paymentDeal = MerchantDeal::find($request->merchant_deal_id);

        $dealRecentPayment = $paymentDeal->payments()->has('rents')->latest('id')->first();

        $dealNewPayment = $paymentDeal->payments()->create([
            'invoice_no' => $paymentDeal->name.'P'.($paymentDeal->payments->count() + 1),
            'number_installment' => $request->number_installment,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'total_rent' => $request->total_rent,
            'discount' => $request->discount,
            'previous_due' => $dealRecentPayment->current_due ?? 0,
            'net_payable' => $request->net_payable ?? 0,
            'paid_amount' => $request->paid_amount ?? 0,
            'current_due' => ($request->net_payable - $request->paid_amount),
        ]);

        foreach ($paymentDeal->containers as $dealContainerIndex => $dealContainer) {
            
            $currentRent = $dealContainer->warehouseContainer->rents->where('rent_period_id', $paymentDeal->rent_period_id)->first();

            $payementNewRent = $dealNewPayment->rents()->create([
                'rent' => $currentRent->rent ?? 0,
                'dealt_space_id' => $dealContainer->id
            ]);

        }

        foreach ($paymentDeal->shelves as $dealShelfIndex => $dealShelf) {
            
            $currentRent = $dealShelf->warehouseContainer->shelf->rents->where('rent_period_id', $paymentDeal->rent_period_id)->first();

            $payementNewRent = $dealNewPayment->rents()->create([
                'rent' => $currentRent->rent ?? 0,
                'dealt_space_id' => $dealShelf->id
            ]);

        }


        foreach ($paymentDeal->units as $dealUnitIndex => $dealUnit) {
            
            $currentRent = $dealUnit->warehouseContainer->shelf->unit->rents->where('rent_period_id', $paymentDeal->rent_period_id)->first();

            $payementNewRent = $dealNewPayment->rents()->create([
                'rent' => $currentRent->rent ?? 0,
                'dealt_space_id' => $dealUnit->id
            ]);

        }

        return $this->showDealAllPayments($request->merchant_deal_id, $perPage);
    }

    public function updateDealPayment(Request $request, $payment, $perPage)
    {
        $request->validate([
            'merchant_deal_id' => 'required|exists:merchant_deals,id',
            'number_installment' => 'required|numeric|min:1',
            'date_from' => 'required|date|after_or_equal:1970-01-01 00:00:01|before_or_equal:2038-01-19 03:14:07',
            'date_to' => 'required|date|after_or_equal:1970-01-01 00:00:01|before_or_equal:2038-01-19 03:14:07',
            'total_rent' => 'required|numeric',
            'discount' => 'required|numeric|between:0,100',
            'previous_due' => 'numeric',
            'net_payable' => 'numeric',
            'paid_amount' => 'required|numeric|min:0',
            // 'current_due' => 'required|numeric',
        ],

        [
            // 'current_due' => 'Stock quantity is required !',
            'merchant_deal_id.*' => 'Payment deal is required',
            'number_installment' => 'Rent installment number is required',
            'date_from' => 'Rent issueing date is required',
            'date_to' => 'Rent expiring date is required',
            'total_rent' => 'Total rent is invalid',
            'discount' => 'Discount rate should be between 0 to 100',
            'previous_due' => 'Previous due is invalid',
            'net_payable' => 'Net payable is invalid',
            'paid_amount' => 'Paid amount is invalid',
        ]);

        $paymentDeal = MerchantDeal::find($request->merchant_deal_id);
        $paymentToUpdate = $paymentDeal->payments()->where('id', $payment)->firstOrFail();

        if (($paymentToUpdate->discount < $request->discount) || ($paymentToUpdate->net_payable > $request->net_payable) || ($paymentToUpdate->paid_amount < $request->paid_amount)) {
            
            $this->decreaseNextPaymentDues($paymentToUpdate->current_due - ($request->net_payable - $request->paid_amount), $paymentToUpdate->paid_at);

        }
        else if (($paymentToUpdate->discount > $request->discount) || ($paymentToUpdate->net_payable < $request->net_payable) || ($paymentToUpdate->paid_amount > $request->paid_amount)) {
            
            $this->increaseNextPaymentDues(($request->net_payable - $request->paid_amount) - $paymentToUpdate->current_due, $paymentToUpdate->paid_at);

        }

        $dealNewPayment = $paymentToUpdate->update([
            // 'invoice_no' => $paymentDeal->name.'-PY-'.($paymentDeal->payments->count() + 1),
            'number_installment' => $request->number_installment,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'total_rent' => $request->total_rent,
            'discount' => $request->discount,
            // 'previous_due' => $dealRecentPayment->current_due,
            'net_payable' => $request->net_payable ?? 0,
            'paid_amount' => $request->paid_amount ?? 0,
            'current_due' => ($request->net_payable - $request->paid_amount),
        ]);

        return $this->showDealAllPayments($request->merchant_deal_id, $perPage);
    }

    public function deleteDealPayment($payment, $perPage)
    {
        $dealPaymentToDelete = MerchantPayment::findOrFail($payment);
        $paymentDeal = $dealPaymentToDelete->deal;

        if ($paymentDeal->payments->count() < 2) {
            
            return response()->json(['errors'=>["undeletableDeal" => "One payment is required"]], 422);

        }

        $this->decreaseNextPaymentDues($dealPaymentToDelete->current_due, $dealPaymentToDelete->paid_at);
        $dealPaymentToDelete->rents()->delete();
        $dealPaymentToDelete->delete();

        return $this->showDealAllPayments($paymentDeal->id, $perPage);
    }

    public function searchDealAllPayments(Request $request, $perPage)
    {
        $request->validate([
            'merchant_deal_id' => 'required|exists:merchant_deals,id',
            'search' => 'nullable|required_without_all:dateTo,dateFrom|string', 
            'dateTo' => 'nullable|required_without_all:search,dateFrom|date|after_or_equal:1970-01-01 00:00:01|before_or_equal:2038-01-19 03:14:07',
            'dateFrom' => 'nullable|required_without_all:search,dateTo|date|after_or_equal:1970-01-01 00:00:01|before_or_equal:2038-01-19 03:14:07',
            // 'showPendingRequisitions' => 'nullable|boolean',
            // 'showCancelledRequisitions' => 'nullable|boolean',
            // 'showDispatchedRequisitions' => 'nullable|boolean',
            // 'showProduct' => 'nullable|string', 
        ]);

        $query = MerchantPayment::with(['rents'])->whereHas('deal', function ($query1) use ($request) {
            $query1->where('merchant_deal_id', $request->merchant_deal_id);
        });

        if ($request->search) {
            
            $query->where(function($query2) use ($request) {
                $query2->where('invoice_no', 'like', "%$request->search%")
                ->orWhere('number_installment', 'like', "%$request->search%")
                ->orWhere('date_from', 'like', "%$request->search%")
                ->orWhere('date_to', 'like', "%$request->search%")
                ->orWhere('total_rent', 'like', "%$request->search%")
                ->orWhere('discount', 'like', "%$request->search%")
                ->orWhere('previous_due', 'like', "%$request->search%")
                ->orWhere('net_payable', 'like', "%$request->search%")
                ->orWhere('paid_amount', 'like', "%$request->search%")
                ->orWhere('current_due', 'like', "%$request->search%")
                ->orWhereHas('rents', function ($query3) use ($request) {
                    $query3->where('rent', 'like', "%$request->search%");
                });
            });

        }

        if ($request->dateFrom) {
            
            $query->whereDate('paid_at', '>=', $request->dateFrom);

        }

        if ($request->dateTo) {
            
            $query->whereDate('paid_at', '<=', $request->dateTo);

        }

        return response()->json([
            'all' => new DealPaymentCollection($query->latest('paid_at')->paginate($perPage)),  
        ], 200);
    }

    protected function retrieveDealtSpaces(MerchantDeal $deal, MerchantPayment $payment)
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

    protected function resetDealtSpaces(MerchantDeal $deal, MerchantPayment $payment)
    {
        // containers
        foreach ($deal->containers as $merchantContainerKey => $merchantContainer) {
               
            $addedContainer = WarehouseContainerStatus::find($merchantContainer->space_id);

            if (! empty($addedContainer) && $addedContainer->occupied==0.0) {
                
                $addedContainer->update([
                    'engaged' => 0
                ]);

                $addedContainer->updateContainerStatus(0);

                // if current rent of same type container at same warehouse is using anywhere else
                $sameTypeContainerUsingSameRentPeriod = DealtSpace::where('warehouse_container_id', $addedContainer->warehouse_container_id)
                ->where('id', '!=', $merchantContainer->id)
                ->whereHas('deal', function ($query) use ($merchantContainer) {
                    $query->where('rent_period_id', $merchantContainer->deal->rent_period_id);
                })
                ->exists();

                if (! $sameTypeContainerUsingSameRentPeriod) {
                    
                   $addedContainer->warehouseContainer->rents->where('rent_period_id', $merchantContainer->deal->rent_period_id)->first()->update([
                        'active' => 0
                   ]); 

                }

                $payment->rents()->where('dealt_space_id', $merchantContainer->id)->delete();

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

                    $payment->rents()->where('dealt_space_id', $merchantShelf->id)->delete();

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

                    $payment->rents()->where('dealt_space_id', $merchantUnit->id)->delete();

                    // Parent Shelf
                    $addedUnit->parentShelf->parentContainer->updateParentShelf($addedUnit->parentShelf);

                    $merchantUnit->delete();

                }

            }

        }
    }

    protected function setDealtContainers($containers = [], MerchantDeal $deal, MerchantPayment $payment)
    {
        if (count($containers)) {
            
            foreach ($containers as $warehouseContainerIndex => $warehouseContainer) {
                        
                $expectedContainer = WarehouseContainerStatus::find($warehouseContainer->id);
                
                if (!empty($expectedContainer) && $expectedContainer->engaged==0.0) {
                    
                    $newSpaces = $expectedContainer->deals()->create([
                        // 'rent_period_id' => $warehouseContainer->selected_rent->rent_period_id,
                        // 'warehouse_id' => $expectedContainer->warehouseContainer->warehouse_id,
                        'warehouse_container_id' => $expectedContainer->warehouse_container_id,
                        'merchant_deal_id' => $deal->id,
                    ]);
        
                    $expectedContainer->update([
                        'engaged' => 1
                    ]);
                    
                    $expectedContainer->updateContainerStatus(1); 

                    $rentPeriod = RentPeriod::find($warehouseContainer->selected_rent->rent_period_id);
                    // $lastPayment = $payment->rents()->where('dealt_space_id', )->latest()->first();
                    
                    $containerNewRent = $payment->rents()->create([
                        // 'issued_from' => $deal->created_at,
                        // 'expired_at' => $deal->created_at->addDays($rentPeriod->number_days),
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
                            // 'rent_period_id' => $warehouseSpace->container->selected_rent->rent_period_id,
                            // 'warehouse_id' => $containerExpectedShelf->warehouseContainer->warehouse_id,
                            'warehouse_container_id' => $containerExpectedShelf->warehouse_container_id,
                            'merchant_deal_id' => $deal->id
                        ]);
        
                        $containerExpectedShelf->update([
                            'engaged' => 1
                        ]);
            
                        $containerExpectedShelf->parentContainer->updateChildUnits($containerExpectedShelf, 1);
                        
                        $shelfNewRent = $payment->rents()->create([
                            // 'issued_from' => $deal->created_at,
                            // 'expired_at' => $deal->created_at->addDays($rentPeriod->number_days),
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
                            // 'rent_period_id' => $warehouseSpace->container->selected_rent->rent_period_id,
                            // 'warehouse_id' => $warehouseContainerShelfExpectedUnit->warehouseContainer->warehouse_id,
                            'warehouse_container_id' => $warehouseContainerShelfExpectedUnit->warehouse_container_id,
                            'merchant_deal_id' => $deal->id
                        ]);

                        $warehouseContainerShelfExpectedUnit->update([
                            'engaged' => 1
                        ]);

                        $unitNewRent = $payment->rents()->create([
                            // 'issued_from' => $deal->created_at,
                            // 'expired_at' => $deal->created_at->addDays($rentPeriod->number_days),
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

    protected function decreaseNextPaymentDues($amountToDeduct, $date)
    {
        $nextPaymentsToUpdate = MerchantPayment::where('paid_at', '>', $date)->get();

        foreach ($nextPaymentsToUpdate as $merchantPaymentKey => $nextPaymentToUpdate) {
            
            $nextPaymentToUpdate->update([
                'previous_due' => $nextPaymentToUpdate->previous_due - $amountToDeduct,
                'net_payable' => $nextPaymentToUpdate->net_payable - $amountToDeduct,
                'current_due' => $nextPaymentToUpdate->current_due - $amountToDeduct
            ]);

        }

    }

    protected function increaseNextPaymentDues($amountToAdd, $date)
    {
        $nextPaymentsToUpdate = MerchantPayment::where('paid_at', '>', $date)->get();

        foreach ($nextPaymentsToUpdate as $merchantPaymentKey => $nextPaymentToUpdate) {
            
            $nextPaymentToUpdate->update([
                'previous_due' => $nextPaymentToUpdate->previous_due + $amountToAdd,
                'net_payable' => $nextPaymentToUpdate->net_payable + $amountToAdd,
                'current_due' => $nextPaymentToUpdate->current_due + $amountToAdd,
            ]);

        }

    }

}
