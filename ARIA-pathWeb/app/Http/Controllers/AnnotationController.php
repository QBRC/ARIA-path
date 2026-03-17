<?php

namespace App\Http\Controllers;

use App\Models\Annotation;
use App\Models\Image;
use Illuminate\Http\Request;
use Auth;
use Validator;

use Illuminate\Support\Facades\Log;

class AnnotationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:2')->only('save');
    }

    public function fetch(Request $request)
    {
        if ($request->input('noteid') == 0){
            $record=Annotation::where('image_id', $request->input('id'))->latest()->first();
        }else{
            $record=Annotation::find($request->input('noteid'));
        }

        if (is_null($record)){
            return response()->json([
                'author'=>null,
                'name'=>null,
                'date'=>null,
                'time'=>null,
                'note' => null,
            ]);
        }else{
            return response()->json([
                'author'=>$record->user->name,
                'name'=>$record->name,
                'date'=>$record->created_at->format("d-m-Y"),
                'time'=>$record->created_at->format("H:i:s"),
                'note' => $record->note,
            ]);
        }
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
        ]);

        if($validator->fails()){
            return response()->json(['status'=>400, 'error'=>$validator->errors()]);
        }else {
            $date = Annotation::create([
                'user_id' => Auth::user()->id,
                'image_id' => $request->input('id'),
                'name' => $request->input('name'),
                'note' => $request->input('note'),
            ])->created_at;

            Image::findOrFail($request->input('id'))->update([
                'updated_at'=>now(),
            ]);

            return response()->json([
                'status'=>200,
                'author'=>Auth::user()->name,
                'name'=>$request->input('name'),
                'date'=>$date->format("d-m-Y"),
                'time'=>$date->format("H:i:s"),
            ]);
        }


        /** implement annotation overwriting */
//        Annotation::updateOrCreate(
//            ['user_id' => Auth::user()->id, 'image_id' => $img->id],
//            ['note' => json_encode($request->input('note'))]
//        );

    }



    public function loadHistory(Request $request){

        $record=Annotation::find($request->input('id'));

        return response()->json([
            'author'=>$record->user->name,
            'name'=>$record->name,
            'date'=>$record->created_at->format("d-m-Y"),
            'time'=>$record->created_at->format("H:i:s"),
            'note' => $record->note,
        ]);
    }

}
