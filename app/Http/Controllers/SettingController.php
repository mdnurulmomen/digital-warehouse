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
    	$request->validate([
            'official_bank_name' => 'required|string|max:50',
            'official_bank_account_name' => 'required|string|max:50',
            'official_bank_account_number' => 'required|string|max:50',
            'official_merchant_name' => 'required|string|max:50',
            'official_merchant_account_number' => 'required|string|max:50',
            'vat_percentage' => 'required|numeric|min:0|max:100',
        ]);

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

    public function updateContactSetting(Request $request)
    {
        $request->validate([
            'official_customer_care_number' => 'required|string|max:50',
            'official_mail_address' => 'required|string|max:50',
            'official_contact_address' => 'required|string|max:250',
        ]);

        $adminSettings = ApplicationSetting::firstOrCreate([]);

        $adminSettings->official_customer_care_number = $request->official_customer_care_number;
        $adminSettings->official_mail_address = $request->official_mail_address;
        $adminSettings->official_contact_address = $request->official_contact_address;

        $adminSettings->save();

        return $this->showApplicationSetting();
    }
}
