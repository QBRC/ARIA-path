<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\User;
use App\Models\Image;
use App\Models\Annotation;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home', [
            'count'=>[
                'groups'=>Group::where('is_delete',0)->count(),
                'users'=>User::where('is_delete',0)->count(),
                'images'=>Image::where('is_delete',0)->count(),
                'notes'=>Annotation::where('is_delete',0)->count(),
            ],

        ]);
    }
}
