<?php

namespace App\Http\Controllers;

use Mail;
use App\Mail\GeneralMail;
use Illuminate\Http\Request;

class MailController extends Controller
{
	public function sendDynamicMail(Request $request, $perPage = false) {

		$request->validate([
			'recipients' => 'required|array|min:1',
			'recipients.*' => 'required|email',
			'subject' => 'required|string|max:255',
			'body' => 'required|string|max:255',
		]);

		foreach ($request->recipients as $recipient) {
		    Mail::to($recipient)->send(new GeneralMail($request->subject));
		}

		return "Email is sent successfully";
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
