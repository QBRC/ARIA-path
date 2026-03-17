<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Group;
use App\Models\Image;
use App\Models\Permission;
use Illuminate\Http\Request;

class BatchesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('batches.index', [
            'batches'=>Batch::with('groups','images','providerName')->get(),
            'groups'=>Group::where('is_delete',0)->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('batches.create', [
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
            'name' => 'required|unique:groups,name|max:255',
        ]);

        Batch::create([
            'name'=>$request->input('name'),
            'group_id'=>$request->input('group'),
        ]);

        return redirect('/batches')->with('succeed','New batch has been created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        Batch::findOrFail($id)->update([
            'name'=>$request->input('name'),
            'group_id'=>$request->input('group'),
            'updated_at'=>now(),
        ]);

        Permission::where([['batch_id', $id], ['own', 1]])->update([
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

    public function delete($id, Request $request)
    {
        if ($request->input('new') == 0){
            Image::where('batch_id',$id)->update([
                'is_delete'=>1,
                'updated_at'=>now(),
            ]);
        }else{
            Image::where('batch_id',$id)->update([
                'batch_id'=>$request->input('new'),
                'updated_at'=>now(),
            ]);
        }


        Batch::findOrFail($id)->update([
            'is_delete'=>1,
            'updated_at'=>now(),
        ]);

//        activity('Data record')
//            ->causedBy(Auth::user()->id)
//            ->tap(function(Activity $activity) use ($old,$new,$num)  {
//                if ($num>1){
//                    $activity->show_changes=$num." records from ".$old." are moved to ".$new;
//                }else{
//                    $activity->show_changes=$num." record from ".$old." is moved to ".$new;
//                }
//            })
//            ->log('batch-move');

        return redirect('/batches')->with('succeed','Disable successfully');
    }


    public function restore($id)
    {
        Batch::findOrFail($id)->update([
            'is_delete'=>0,
            'updated_at'=>now(),
        ]);

        return redirect('/batches')->with('succeed','Activate successfully');
    }
}
