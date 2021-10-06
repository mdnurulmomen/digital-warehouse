<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\RentPeriod;
use App\Models\MerchantDeal;
use Illuminate\Http\Request;
use App\Models\MerchantPayment;
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
            // 'warehouses.*.spaces.*.containers.*.selected_rent.rent' => 'required_if:warehouses.*.spaces.*.type,containers|numeric',
            
            'warehouses.*.spaces.*.container' => 'required_if:warehouses.*.spaces.*.type,shelves|required_if:warehouses.*.spaces.*.type,units',
            'warehouses.*.spaces.*.container.id' => 'required_if:warehouses.*.spaces.*.type,shelves|required_if:warehouses.*.spaces.*.type,units|exists:warehouse_container_statuses,id',
            'warehouses.*.spaces.*.container.shelves' => 'required_if:warehouses.*.spaces.*.type,shelves|array|min:1',
            'warehouses.*.spaces.*.container.shelves.*.id' => 'required_if:warehouses.*.spaces.*.type,shelves|exists:warehouse_container_shelf_statuses,id',
            'warehouses.*.spaces.*.container.selected_rent' => 'required_if:warehouses.*.spaces.*.type,shelves|required_if:warehouses.*.spaces.*.type,units',
            'warehouses.*.spaces.*.container.selected_rent.id' => 'required_if:warehouses.*.spaces.*.type,shelves|required_if:warehouses.*.spaces.*.type,units|exists:rents,id',
            // 'warehouses.*.spaces.*.container.selected_rent.rent' => 'required_if:warehouses.*.spaces.*.type,containers|numeric',

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
            // 'warehouses.*.spaces.*.containers.*.selected_rent.rent' => 'required_if:warehouses.*.spaces.*.type,containers|numeric',
            
            'warehouses.*.spaces.*.container' => 'required_if:warehouses.*.spaces.*.type,shelves|required_if:warehouses.*.spaces.*.type,units',
            'warehouses.*.spaces.*.container.id' => 'required_if:warehouses.*.spaces.*.type,shelves|required_if:warehouses.*.spaces.*.type,units|exists:warehouse_container_statuses,id',
            'warehouses.*.spaces.*.container.shelves' => 'required_if:warehouses.*.spaces.*.type,shelves|array|min:1',
            'warehouses.*.spaces.*.container.shelves.*.id' => 'required_if:warehouses.*.spaces.*.type,shelves|exists:warehouse_container_shelf_statuses,id',
            'warehouses.*.spaces.*.container.selected_rent' => 'required_if:warehouses.*.spaces.*.type,shelves|required_if:warehouses.*.spaces.*.type,units',
            'warehouses.*.spaces.*.container.selected_rent.id' => 'required_if:warehouses.*.spaces.*.type,shelves|required_if:warehouses.*.spaces.*.type,units|exists:rents,id',
            // 'warehouses.*.spaces.*.container.selected_rent.rent' => 'required_if:warehouses.*.spaces.*.type,containers|numeric',

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
        else if ($dealToDelete->spaces()->where('engaged', '!=', 0.0)->exists()) {
            
           return response()->json(['errors'=>["undeletableDeal" => "Dealt space is engaged"]], 422);
            
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

    public function showDealAllPayments($deal, $perPage = false)
    {
        if ($perPage) {
            
            return new DealPaymentCollection(
                MerchantPayment::with(['rents'])->whereHas('deal', function ($query) use ($deal) {
                    $query->where('merchant_deal_id', $deal);
                })->paginate($perPage)
            );

        }
    }

    public function storeDealNewPayment(Request $request, $perPage)
    {
        $request->validate([
            'merchant_deal_id' => 'required|exists:merchant_deals,id',
            'current_due' => 'required|numeric',
            'discount' => 'required|numeric|between:0,100',
            'net_payable' => 'required|numeric',
            'paid_amount' => 'required|numeric',
            'previous_due' => 'required|numeric',
            'total_rent' => 'required|numeric'
        ],

        [
            // 'payments.*.current_due' => 'Stock quantity is required !',
            'merchant_deal_id.*' => 'Payment deal is required',
            'discount' => 'Discount rate should be between 0 to 100',
            'net_payable' => 'Net payable is invalid',
            'paid_amount' => 'Paid amount is invalid',
            'previous_due' => 'Previous due is invalid',
            'total_rent' => 'Total rent is invalid',
        ]);

        $paymentDeal = MerchantDeal::find($request->merchant_deal_id);

        $dealRecentPayment = $paymentDeal->payments()->has('rents')->latest('id')->first();

        $dealNewPayment = $paymentDeal->payments()->create([
            'invoice_no' => 'inv-'.$paymentDeal->id.'-#-'.($paymentDeal->payments->count() + 1),
            'previous_due' => $dealRecentPayment->current_due,
            'total_rent' => $request->total_rent,
            'discount' => $request->discount,
            'net_payable' => $request->net_payable,
            'paid_amount' => $request->paid_amount,
            'current_due' => ($request->net_payable - $request->paid_amount),
        ]);

        foreach ($paymentDeal->spaces as $dealSpaceIndex => $dealSpace) {
            
            $rentPeriod = RentPeriod::find($dealSpace->rent->rent_period_id);
            $dealRecentPaymentExpectedRent = $dealRecentPayment->rents()->where('dealt_space_id', $dealSpace->id)->first();

            $payementNewRent = $dealNewPayment->rents()->create([
                'issued_from' => $dealRecentPaymentExpectedRent->expired_at,
                'expired_at' => $this->getRentExpiredDate(NULL, $rentPeriod->name, $dealRecentPaymentExpectedRent),
                'rent' => $dealSpace->rent->rent,
                'dealt_space_id' => $dealSpace->id
            ]);

        }

        return $this->showDealAllPayments($request->merchant_deal_id, $perPage);
    }

    public function updateDealPayment(Request $request, $payment, $perPage)
    {
        $request->validate([
            'merchant_deal_id' => 'required|exists:merchant_deals,id',
            'current_due' => 'required|numeric',
            'discount' => 'required|numeric|between:0,100',
            'net_payable' => 'required|numeric',
            'paid_amount' => 'required|numeric',
            'previous_due' => 'required|numeric',
            'total_rent' => 'required|numeric'
        ],

        [
            // 'payments.*.current_due' => 'Stock quantity is required !',
            'merchant_deal_id.*' => 'Payment deal is required',
            'discount' => 'Discount rate should be between 0 to 100',
            'net_payable' => 'Net payable is invalid',
            'paid_amount' => 'Paid amount is invalid',
            'previous_due' => 'Previous due is invalid',
            'total_rent' => 'Total rent is invalid',
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
            'total_rent' => $request->total_rent,
            'discount' => $request->discount,
            'net_payable' => $request->net_payable,
            'paid_amount' => $request->paid_amount,
            'current_due' => ($request->net_payable - $request->paid_amount),
        ]);

        return $this->showDealAllPayments($request->merchant_deal_id, $perPage);
    }

    public function deleteDealPayment($payment, $perPage)
    {
        $dealPaymentToDelete = MerchantPayment::findOrFail($payment);
        $paymentDeal = $dealPaymentToDelete->deal;

        $this->decreaseNextPaymentDues($dealPaymentToDelete->current_due, $dealPaymentToDelete->paid_at);
        $dealPaymentToDelete->rents()->delete();
        $dealPaymentToDelete->delete();

        return $this->showDealAllPayments($paymentDeal->id, $perPage);
    }

    public function searchDealAllPayments($deal, $search, $perPage)
    {
        $query = MerchantPayment::with(['rents'])->whereHas('deal', function ($query1) use ($deal) {
            $query1->where('merchant_deal_id', $deal);
        })
        ->where(function($query2) use ($search) {
            $query2->where('invoice_no', 'like', "%$search%")
            ->orWhere('previous_due', 'like', "%$search%")
            ->orWhere('total_rent', 'like', "%$search%")
            ->orWhere('discount', 'like', "%$search%")
            ->orWhere('net_payable', 'like', "%$search%")
            ->orWhere('paid_amount', 'like', "%$search%")
            ->orWhere('current_due', 'like', "%$search%")
            ->orWhereHas('rents', function ($query3) use ($search) {
                $query3->where('issued_from', 'like', "%$search%")
                ->orWhere('expired_at', 'like', "%$search%")
                ->orWhere('rent', 'like', "%$search%");
            });
        });

        return response()->json([
            'all' => new DealPaymentCollection($query->paginate($perPage)),  
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
                        'rent' => $warehouseContainer->selected_rent->rent,
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
                            'rent' => $warehouseSpace->container->selected_rent->rent,
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
                            'rent' => $warehouseSpace->container->selected_rent->rent,
                            'dealt_space_id' => $dealtNewUnit->id
                        ]);

                    }

                }

                // Parent Shelf
                $warehouseContainerShelfExpectedUnit->parentShelf->parentContainer->updateParentShelf($warehouseContainerShelfExpectedUnit->parentShelf);

            }

        }

    }

    protected function decreaseNextPaymentDues($dueAmount, $date)
    {
        $nextPaymentsToUpdate = MerchantPayment::where('paid_at', '>', $date)->get();

        foreach ($nextPaymentsToUpdate as $merchantPaymentKey => $nextPaymentToUpdate) {
            
            $nextPaymentToUpdate->update([
                'previous_due' => $nextPaymentToUpdate->previous_due - $dueAmount,
                'net_payable' => $nextPaymentToUpdate->net_payable - $dueAmount,
                'current_due' => $nextPaymentToUpdate->current_due - $dueAmount,
            ]);

        }

    }

    protected function increaseNextPaymentDues($dueAmount, $date)
    {
        $nextPaymentsToUpdate = MerchantPayment::where('paid_at', '>', $date)->get();

        foreach ($nextPaymentsToUpdate as $merchantPaymentKey => $nextPaymentToUpdate) {
            
            $nextPaymentToUpdate->update([
                'previous_due' => $nextPaymentToUpdate->previous_due + $dueAmount,
                'net_payable' => $nextPaymentToUpdate->net_payable + $dueAmount,
                'current_due' => $nextPaymentToUpdate->current_due + $dueAmount,
            ]);

        }

    }

    protected function getRentExpiredDate(MerchantDeal $deal = NULL, $rentName, MerchantPaymentDetail $paymentRent = NULL) 
    {   
        $dateToStart = $deal ? Carbon::parse($deal->created_at) : Carbon::parse($paymentRent->expired_at);

        if (strpos($rentName,"daily") !== false) {
            
            return $dateToStart->addDay();
        }
        else if (strpos($rentName,"week") !== false) {
            
            return $dateToStart->addDays(7);
        }
        else if (strpos($rentName,"month") !== false) {
            
            return $dateToStart->addDays(30);
        }
        else if (strpos($rentName,"year") !== false) {
            
            return $dateToStart->addYear();
        }
        else {
            return now();
        }
    }

}
