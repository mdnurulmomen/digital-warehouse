<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApplicationSetting;

class SettingController extends Controller
{
    public function __construct()
    {   
        $this->middleware("permission:view-application-setting-index")->only('showApplicationSetting');
        $this->middleware("permission:update-application-setting")->only(['updatePaymentSetting', 'updateContactSetting', 'updateWarehouseSetting', 'updateSystemSetting']);
    }

    // Application Setting
    public function showApplicationSetting()
    {
    	return response(ApplicationSetting::first(), 200);
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
            'official_currency_name' => 'required|string|max:50',
        ]);

        $adminSettings = ApplicationSetting::firstOrCreate([]);

    	$adminSettings->official_bank_name = strtolower($request->official_bank_name);
    	$adminSettings->official_merchant_name = strtolower($request->official_merchant_name);
    	$adminSettings->official_bank_account_name = strtolower($request->official_bank_account_name);
    	$adminSettings->official_bank_account_number = $request->official_bank_account_number;
    	$adminSettings->official_merchant_account_number = $request->official_merchant_account_number;
    	$adminSettings->vat_percentage = $request->vat_percentage;
        $adminSettings->official_currency_name = $request->official_currency_name;

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
        $adminSettings->official_mail_address = strtolower($request->official_mail_address);
        $adminSettings->official_contact_address = strtolower($request->official_contact_address);

        $adminSettings->save();

        return $this->showApplicationSetting();
    }

    public function updateWarehouseSetting(Request $request)
    {
        $request->validate([
            // 'default_selling_price' => 'required|numeric|min:0',
            // 'default_storing_price' => 'required|numeric|min:0',
            'default_length' => 'required|numeric|min:0',
            'default_width' => 'required|numeric|min:0',
            'default_height' => 'required|numeric|min:0',
            'default_measure_unit' => 'string|max:100',
        ]);

        $adminSettings = ApplicationSetting::firstOrCreate([]);

        // $adminSettings->default_selling_price = $request->default_selling_price;
        // $adminSettings->default_storing_price = $request->default_storing_price;
        $adminSettings->default_length = $request->default_length;
        $adminSettings->default_width = $request->default_width;
        $adminSettings->default_height = $request->default_height;
        $adminSettings->default_measure_unit = strtolower($request->default_measure_unit);

        $adminSettings->save();

        return $this->showApplicationSetting();
    }

    public function updateSystemSetting(Request $request)
    {
        $adminSettings = ApplicationSetting::firstOrCreate([]);

        $adminSettings->application_logo = $request->application_logo;
        $adminSettings->application_favicon = $request->application_favicon;

        $adminSettings->save();

        return $this->showApplicationSetting();
    }
}
