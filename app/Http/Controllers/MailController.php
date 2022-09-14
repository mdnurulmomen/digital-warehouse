<?php

namespace App\Http\Controllers;

use Mail;
use App\Models\Admin;
use App\Models\Merchant;
use App\Models\Manager;
use App\Models\Warehouse;
use App\Models\AppMail;
use App\Mail\GeneralMail;
use Illuminate\Http\Request;
use App\Models\WarehouseOwner;
use App\Models\ApplicationSetting;
use App\Http\Resources\Web\MailCollection;

class MailController extends Controller
{
	public function __construct()
    {
        $this->middleware("permission:view-mail-index")->only(['showAllMails', 'searchAllMails']);
        $this->middleware("permission:delete-mail")->only(['deleteMail']);
    }

    // Mails
    public function showAllMails($perPage=false)
    {
    	if ($perPage) {
            
            return response()->json([

        		'manual' => new MailCollection(
        			AppMail::whereNotNull('sender_type')
        			->whereHas('recipients', function ($query) {
					    $query->where('status', 1);
					})
        			->latest('id')->paginate($perPage)
				),
        		
        		'automated' => new MailCollection(
        			AppMail::whereNull('sender_type')
        			->whereHas('recipients', function ($query) {
					    $query->where('status', 1);
					})
        			->latest('id')->paginate($perPage)
        		),

        		'failed' => new MailCollection(
        			AppMail::whereHas('recipients', function ($query) {
					    $query->where('status', 0);
					})
        			->latest('id')->paginate($perPage)
        		),

        	], 200);

        }

        // return AppMail::latest('id')->get();
    }

	public function sendDynamicMail(Request $request, $perPage = false) {

		$request->validate([
			'recipients' => 'required|array|min:1',
			'recipients.*' => 'required|email',
			'subject' => 'required|string|max:255',
			'body' => 'required|string|max:65000',
		]);

		$generalSettings = ApplicationSetting::firstOrCreate([
            'id' => 1
        ]);

        $sender = \Auth::guard('admin')->user() ?? \Auth::guard('manager')->user();

		$newMail = new AppMail();
		$newMail->subject = $request->subject;
		$newMail->body = $request->body;
		$newMail->sender_type = get_class($sender);
		$newMail->sender_id = $sender->id;
		$newMail->created_at = now();
		$newMail->save();

		foreach ($request->recipients as $recipient) {
			
			try{
			   	//code to send the mail
			   	Mail::to($recipient)->send(new GeneralMail($request->subject, $request->body, $generalSettings));

			   	$newMailRecipient = $newMail->recipients()->create([
			   		'recipient' => $recipient,
			   		'status' => 1, // success
			   	]);	

			}catch(\Swift_TransportException $transportExp){
			  	//$transportExp->getMessage();
			  	
				$newMailRecipient = $newMail->recipients()->create([
			   		'recipient' => $recipient,
			   		'status' => 0, // success
			   	]);
			}
		    
		}

		return $this->showAllMails($perPage);

	}

	public function deleteMail($mail, $perPage)
	{	
		$mail = AppMail::findOrFail($mail);
		
		$mail->recipients()->delete();
		$mail->delete();

		return $this->showAllMails($perPage);
	}

	public function searchAllMails($search, $perPage)
	{	
		$query = AppMail::where('subject', 'like', "%$search%")
		->orWhereHas('recipients', function ($query) use ($search) {
		    $query->where('recipient', 'like', "%$search%");
		})
		->orWhereHasMorph(
		    'sender',
		    [Admin::class, Manager::class, Merchant::class, Warehouse::class, WarehouseOwner::class],
		    function ($query) use ($search) {
		        $query->where('user_name', 'like', "%$search%")
			    ->orWhere('email', 'like', "%$search%")
			    ->orWhere('mobile', 'like', "%$search%");
		    }
		);
		
		return response()->json([
            'all' => new MailCollection($query->paginate($perPage)),    
        ], 200);
	}

	public function html_email() {
		$data = array('name'=>"Virat Gandhi");
		Mail::send('mail', $data, function($message) {
			$message->to('abc@gmail.com', 'Tutorials Point')->subject
			('Laravel HTML Testing Mail');
			$message->from('xyz@gmail.com','Virat Gandhi');
		});
		echo "HTML Email Sent. Check your inbox.";
	}

	public function attachment_email() {
		$data = array('name'=>"Virat Gandhi");
		Mail::send('mail', $data, function($message) {
			$message->to('abc@gmail.com', 'Tutorials Point')->subject
			('Laravel Testing Mail with Attachment');
			$message->attach('C:\laravel-master\laravel\public\uploads\image.png');
			$message->attach('C:\laravel-master\laravel\public\uploads\test.txt');
			$message->from('xyz@gmail.com','Virat Gandhi');
		});
		echo "Email Sent with attachment. Check your inbox.";
	}
}
