<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApplicationSetting;

class SettingController extends Controller
{
    public function showApplicationSetting()
    {
    	return ApplicationSetting::first();
    }

    public function updatePaymentSetting(Request $request)
    {
    	$adminSettings = ApplicationSetting::firstOrCreate([]);

    	$adminSettings->official_bank_name = $request->official_bank_name;
    	$adminSettings->official_merchant_name = $request->official_merchant_name;
    	$adminSettings->official_bank_account_name = $request->official_bank_account_name;
    	$adminSettings->official_bank_account_number = $request->official_bank_account_number;
    	$adminSettings->official_merchant_account_number = $request->official_merchant_account_number;
    	$adminSettings->vat_percentage = $request->vat_percentage;

    	$adminSettings->save();

    	return $this->showApplicationSetting();
    }
}
