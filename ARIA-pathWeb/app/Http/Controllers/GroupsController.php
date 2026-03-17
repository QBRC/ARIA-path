<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Group;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $res=Permission::where('is_delete', 0)->get();
        $arr1=[];
        $arr2=[];

        foreach ($res as $v){
            isset($arr1[$v->group_id]) ? array_push($arr1[$v->group_id], $v->batch_id) : $arr1[$v->group_id]=[$v->batch_id];
            $arr2[$v->group_id][$v->batch_id]=$v->own;
        }

        return view('groups.index', [
            'groups'=>Group::with('showBatches','users')->get(),
            'batches'=>Batch::where('is_delete', 0)->where('id', '<>', 1)->get(),
            'permission'=>$arr1,
            'provider'=>$arr2,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('groups.create');
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

        Group::create([
            'name'=>$request->input('name'),
            'pi'=>$request->input('pi'),
            'org'=>$request->input('org'),
        ]);

        return redirect('/groups')->with('succeed','New group has been created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        dd('show');

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
        Group::findOrFail($id)->update([
            'name'=>$request->input('name'),
            'pi'=>$request->input('pi'),
            'org'=>$request->input('org'),
        ]);

        // Soft delete all permissions (exclude the own is true) associate with target group
        Permission::where([
            ['group_id', $id],
            ['own', 0]
        ])->update([
            'is_delete'=>1,
            'updated_at'=>now(),
        ]);


        // Update or create permissions according to the post request
        if (!empty($request->batch)){
            foreach ($request->batch as $v) {
                Permission::upsert(
                    ['group_id' => $id, 'batch_id' => $v],
                    ['is_delete'=> 0]
                );
            }
        }

        return back()->with('succeed', 'Updated successfully!');
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
            User::where([['group_id',$id],['role','<>',1]])->update([
                'is_delete'=>1,
                'updated_at'=>now(),
            ]);
        }else{
            User::where('group_id',$id)->update([
                'group_id'=>$request->input('new'),
                'updated_at'=>now(),
            ]);
        }


        Group::findOrFail($id)->update([
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

        return redirect('/groups')->with('succeed','Disable successfully');
    }


    public function restore($id)
    {
        Group::findOrFail($id)->update([
            'is_delete'=>0,
            'updated_at'=>now(),
        ]);

        return redirect('/groups')->with('succeed','Activate successfully');
    }
}
