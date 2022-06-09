<?php

namespace App\Http\Controllers;

use App\Models\ContactQuery;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function index()
    {
    	return view('index');
    }

    public function storeNewMessage(Request $request)
    {
        $request->validate([
            'first_name' => 'required_without:last_name|string|max:50',
            'last_name' => 'required_without:first_name|string|max:50',
            'email' => 'required|email|max:50',
            'contact_no' => 'required|regex:/(01)[0-9]{9}/',
            'subject' => 'required_without:last_name|string|max:255',
            'message' => 'required|string|max:65500'
        ]);

        $newQuery = new ContactQuery();

        $newQuery->first_name = strtolower($request->first_name);
        $newQuery->last_name = strtolower($request->last_name);
        $newQuery->email = strtolower($request->email);
        $newQuery->contact_no = $request->contact_no;
        $newQuery->subject = strtolower($request->subject);
        $newQuery->message = strtolower($request->message);

        $newQuery->save();

        return response()->json([
		    'name' => ucfirst($newQuery->first_name).' '.ucfirst($newQuery->last_name),
		], 200);
    }
}
