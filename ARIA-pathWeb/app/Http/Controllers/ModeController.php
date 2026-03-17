<?php

namespace App\Http\Controllers;

use App\Models\Mode;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\AssignOp\Mod;
use Log;

class ModeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('model', [
            'models'=>Mode::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'name' => 'required|unique:modes,name|max:255',
            'api' => 'required|max:255',
            'description' => 'required|max:255',
        ]);

        Mode::create([
            'name'=>$request->input('name'),
            'api'=>$request->input('api'),
            'description'=>$request->input('description'),
            'type'=>$request->input('type'),
        ]);

        return redirect('/models')->with('succeed','New model has been created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Mode  $mode
     * @return \Illuminate\Http\Response
     */
    public function show(Mode $mode)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Mode  $mode
     * @return \Illuminate\Http\Response
     */
    public function edit(Mode $mode)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Mode  $mode
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255|unique:modes,name,' . $id,
            'api' => 'required|max:255',
            'description' => 'required|max:255',
        ]);

        Mode::findOrFail($id)->update([
            'name'=>$request->input('name'),
            'api'=>$request->input('api'),
            'description'=>$request->input('description'),
            'type'=>$request->input('type'),
        ]);

        return back()->with('succeed', 'Updated successfully!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Mode  $mode
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mode $mode)
    {
        //
    }

    public function delete($id)
    {
        Mode::findOrFail($id)->update([
            'is_delete'=>1,
            'updated_at'=>now(),
        ]);


        return redirect('/models')->with('succeed','Disable successfully');
    }


    public function restore($id)
    {
        Mode::findOrFail($id)->update([
            'is_delete'=>0,
            'updated_at'=>now(),
        ]);

        return redirect('/models')->with('succeed','Activate successfully');
    }
}
