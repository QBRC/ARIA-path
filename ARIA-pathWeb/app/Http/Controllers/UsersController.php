<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use Illuminate\Support\Facades\Auth;
use Validator;


class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('users.index', [
            'users'=>User::with('group')->get(),
            'groups'=> Group::where('is_delete',0)->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create', [
//            'title'=>'Add new user',
            'groups'=>Group::where('is_delete', 0)->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:users,name|max:255',
            'email' => 'required|unique:users,email|max:255',
        ]);

        User::create([
            'name'=>$request->input('name'),
            'email'=>$request->input('email'),
            'password'=>Hash::make($request->input('password')),
            'role'=>$request->input('role'),
            'group_id'=>$request->input('group'),
            'is_delete'=>0,
        ]);


        return redirect('users')->with('succeed','New user has been created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(Auth::user()->role == 1 || Auth::user()->role == 2){
            return view('users.show', [
                'user'=> User::find($id),
            ]);
        }else{
            return redirect()->back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $authority=array();
//        foreach (Permission::where('user_id',$id)->get() as $v){
//            $authority[$v->project_id]=$v->permission_id;
//        }

        return view('users.edit', [
            'user'=> User::find($id),
            'groups'=> Group::where('is_delete',0)->get(),
//            'authority'=> $authority,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        User::findOrFail($id)->update([
            'role'=>$request->input('role'),
            'group_id'=>$request->input('group'),
            'updated_at'=>now(),
        ]);

        return back()->with('succeed','Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function profile()
    {
        return view('users.profile', [
            'user'=> Auth::user(),
        ]);
    }

    public function changPasswordForm(){
        return view('auth.passwords.change');
    }

    public function changPassword(Request $request){
        $validator = Validator::make($request->all(), [ // <---
            'oldpassword' => 'required',
            'newpassword' => 'required|string|min:6|confirmed',
        ])->validate();


        if (!(Hash::check($request->input('oldpassword'), Auth::user()->password))){
            $validator->getMessageBag()->add('oldpassword', 'Your current password does not match with what you provided.');
            return back()->withErrors($validator)->withInput();
        }
        if (strcmp($request->input('oldpassword'),$request->input('newpassword'))==0){
            $validator->getMessageBag()->add('newpassword', 'Your new password cannot be same with the current password.');
            return back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        $user->password = Hash::make($request->get('newpassword'));
        $user->save();

        return back()->with('succeed','Password changed successfully');

    }

    /**
     * Soft delete and restore
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        User::findOrFail($id)->update([
            'is_delete'=>1,
            'updated_at'=>now(),
        ]);

        return back()->with('succeed','Deactivated successfully');
    }

    public function restore($id)
    {
        User::findOrFail($id)->update([
            'is_delete'=>0,
            'updated_at'=>now(),
        ]);

        return back()->with('succeed','Activated successfully');
    }

    /**
     * Authorization
     */

    public function updateAuthorization(Request $request)
    {
        if ($request->user_role == 4){
            User::findOrFail($request->user_id)->update([
                'role'=>3,
                'updated_at'=>now(),
            ]);
        }

        Permission::updateOrCreate(
            ['user_id' => $request->user_id, 'project_id' => $request->project_id],
            ['permission_id' => $request->permission, 'updated_at'=>now()]
        );

        return back()->with('succeed', 'Updated successfully!');
    }

}
