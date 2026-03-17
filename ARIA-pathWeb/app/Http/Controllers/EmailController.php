<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\NoticeMail;
use Illuminate\Support\Facades\Mail;
use Auth;

class EmailController extends Controller
{
    public function activateNewUser(){
        if (Auth::check()){
            Mail::to(config('app.iv.email'))->send(new NoticeMail(Auth::user()));
            return redirect('thank-you')->with('succeed','A request email has been sent to Admin successfully!');
        }else{
            return view('login');
        }
    }

    public function noticeAnnotation($toEmail, $user, $img){
        if (Auth::check()){
            Mail::to($toEmail)->send(new NoticeMail(Auth::user()));
            return back()->with('succeed','A notice email has been sent successfully!');
        }else{
            return view('login');
        }
    }
}
