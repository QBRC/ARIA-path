<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Group;
use App\Models\Image;
use App\Models\Permission;
use App\Models\Mode;
use App\Models\User;
use App\Models\ColorPalette;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Validator;
use Auth;
use Log;


class ImagesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:1')->only('show');
    }

    private $columns = [
        ['data' => 'thumbnail', "title" => ""],
        ['data' => 'name', "title" => "Image Name"],
        ['data' => 'diagnosis', "title" => "Pathologic Diagnosis"],
        ['data' => 'annotations', "title" => "# of Annotations"],
//        ['data'=>'created_at', "title"=> "Created at"],
        ['data' => 'image_id', "title" => "View"],
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $arr = [];
        $batches=Auth::user()->role == 3 ? Auth::user()->group->availBatches : Batch::where('is_delete', 0)->get();

        foreach ($batches as $batch) {
            $node = [
                'id' => $batch->id,
                'name' => $batch->name,
                'nimg' => count(Batch::with('images')->find($batch->id)->images),
                'nanno' => count(Batch::with('annotations')->find($batch->id)->annotations),
            ];

            if (isset($arr[$batch->project])) {
                array_push($arr[$batch->project], $node);
            } else {
                $arr[$batch->project] = [$node];
            }
        }


        $data = [];
        foreach ($arr as $k => $v) {
            array_push($data, ['project' => $k, 'batch' => $v]);
        }

        $annoCount = Mode::where('type', 1)->where('name', 'countAnnos')->first();
        $annoCount = str_replace(config('app.iv.placeholder.image'), '', $annoCount->api);

        return view('images.index', [
            'data' => $data,
            'columns' => $this->columns,
            'annoCountApi' => $annoCount,
        ]);
    }

    public function fetchBatch(Request $request)
    {
        $bid = $request->input('id');
        $images = Image::with('batch', 'annotations')->where('batch_id', $bid)->orderBy('id', 'asc') ->get();
        $data = [];
        $sum_anno = 0;
        foreach ($images as $image) {
            $sum_anno += count($image->annotations);
            $data[] = [
                'thumbnail' => $image->thumbnail(),
                'name' => $image->name,
                'diagnosis' => $image->final_pathologic_diagnosis,
                'annotations' => count($image->annotations),
//                'created_at' => $image->created_at->toDateString(),
                'image_id' => $image->id,
                'image_uuid' => $image->uuid
            ];
        }

        return json_encode([
            "data" => $data,
            "project_name" => Batch::find($bid)->project,
            "batch_name" => Batch::find($bid)->name,
            "created_at" => Batch::find($bid)->created_at->toDateString(),
            "n_img" => count($data),
            "n_anno" => $sum_anno,
        ]);
    }

    public function imgTable()
    {
        $images = Auth::user()->role == 3 ?
            Image::whereIn('id', Auth::user()->imagesID())->with('batch', 'annotations')->get() :
            Image::with('batch', 'annotations')->get();
        $nrow = count($images);
        $table = array();
        foreach ($images as $k => $image) {
            array_push($table, array(
                $image->thumbnail(),
                $image->batch->name,
                $image->name,
                $image->slide,
                $image->pathology,
                count($image->annotations),
                $image->updated_at->toDateString(),
                '<a href="' . url('slides/' . $image->id . '/0') . '" class="text-primary"><i class="fas fa-eye"></i></a>'
            ));
        }

        return response()->json([
            "draw" => 1,
            "recordsTotal" => $nrow,
            "recordsFiltered" => $nrow,
            "data" => $table]);
    }


    public function show($id, $aid)
    {
        $image = Image::with('batch', 'annotations')->find($id);
        $uuid = $image->uuid;
        // Retrieve batch ID for current image
        $batchId = $image->batch->id;

        // Find the previous image within the same batch
        $previousImage = Image::where('batch_id', $batchId)
                                ->where('id', '<', $id)
                                ->orderBy('id', 'desc')
                                ->first();

        // Find the next image within the same batch
        $nextImage = Image::where('batch_id', $batchId)
                            ->where('id', '>', $id)
                            ->orderBy('id')
                            ->first();

        $base=Mode::where('type', 0)->where('name', 'slide')->first();
        $scale=Mode::where('type', 0)->where('name', 'scale')->first();
        $chat=Mode::where('type', 0)->where('name', 'chat')->first();
        $seg=Mode::where('type', 0)->where('name','segment')->first();

        $anno=Mode::where('type', 1)->pluck('api', 'name')->toArray();
        foreach ($anno as $k=>$v){
            $anno[$k]=str_replace(config('app.iv.placeholder.image'), $uuid, $v);
            // $anno[$k]=str_replace(config('app.iv.placeholder.annot'), '', $anno[$k]);
        }

        $color_palette = ColorPalette::where('image_id', $uuid)
                                     ->where('user_id', Auth::user()->id)
                                     ->first();
        // Project and batch name are required
        $path = $image->batch->project . '/' . $image->batch->name . '/';
        // Sub dataset name is optional
        $path .= empty($image->sub_dataset_codename) ? '' : $image->sub_dataset_codename . '/';

        // Check what is the suffix of the image
        $data=[];
        $imagePath = env('IMAGE_PATH');
        $isRemote = !empty($imagePath);
        $apiSlidesDir = config('app.iv.api_slidesDir', 'abc');
        $baseName = pathinfo($image->name, PATHINFO_FILENAME);
        foreach (config('app.iv.folder') as $folder) {
            $data[$folder] = [];

            foreach (config('app.iv.support_format') as $suffix) {
                $filename = $baseName . '.' . $suffix;
                $filePart = "{$folder}/{$path}{$filename}";

                // Build the file reference for the backend service
                // IMAGE_PATH set  => FastAPI reads from URL
                // IMAGE_PATH empty => FastAPI reads from its own local path namespace, e.g. abc/original/...
                if ($isRemote) {
                    $fileUrl = rtrim($imagePath, '/') . "/{$filePart}";
                } else {
                    $fileUrl = "{$apiSlidesDir}/{$filePart}";
                }

                // Build the actual tile URL OpenSeadragon will use
                if ($suffix === 'dzi') {
                    $tileUrl = $fileUrl;
                } else {
                    $tileUrl = str_replace(
                        [config('app.iv.placeholder.file'), config('app.iv.placeholder.image')],
                        [urlencode($fileUrl), $uuid],
                        $base->api
                    );
                }

                // Check availability via the backend-facing URL, not Laravel local filesystem
                try {
                    $response = Http::head($tileUrl);

                    if ($response->successful()) {
                        $exists = true;
                    } else {
                        $response = Http::get($tileUrl);
                        $exists = $response->successful();
                    }
                } catch (\Exception $e) {
                    $exists = false;
                }

                if (!$exists) {
                    continue;
                }

                $data[$folder]['suffix'] = $suffix;
                $data[$folder]['tile'] = $tileUrl;

                if ($suffix === 'dzi') {
                    $data[$folder]['params'] = null;
                    $data[$folder]['chat'] = null;
                    $data[$folder]['seg'] = null;
                } else {
                    $data[$folder]['params'] = str_replace(
                        config('app.iv.placeholder.image'),
                        $uuid,
                        $scale->api
                    );

                    $data[$folder]['chat'] = str_replace(
                        [config('app.iv.placeholder.file'), config('app.iv.placeholder.image')],
                        [urlencode($fileUrl), $uuid],
                        $chat->api
                    );

                    $data[$folder]['seg'] = str_replace(
                        [config('app.iv.placeholder.file'), config('app.iv.placeholder.image')],
                        [urlencode($fileUrl), $uuid],
                        $seg->api
                    );
                }

                // Stop at the first valid format for this folder
                break;
            }
        }

        $imageExist=true;// If there exist image in original

        $hasMask=false;
        if (empty($data['original'])){ // If no image found in original, throw error
            $status=false;
        }else if(empty($data['mask'])){ // image has original but no mask, assign original to mask
            $data['mask']=$data['original'];
        }else{
            $hasMask=true;
        }

        $tree=[];
        if (isset($data['original']['suffix'])){
            if ($data['original']['suffix']!='dzi'){
                foreach (Mode::where('type',9)->where('is_delete',0)->get() as $k=>$v){
                    if($isRemote){
                        $file=env('IMAGE_PATH').'original/'.$path.$image->name.'.'.$data['original']['suffix'];
                    }else{
                        $file="{$apiSlidesDir}/original/".$path.$image->name.'.'.$data['original']['suffix'];;
                    }
                    $url=str_replace(config('app.iv.placeholder.file'), urlencode($file), $v->api);
                    $url=str_replace(config('app.iv.placeholder.image'), $uuid, $url);
                    $urlParts = parse_url($url);
                    $queryString = isset($urlParts['query']) ? $urlParts['query'] : '';
                    parse_str($queryString, $queryParameters);
                    $registryValue = isset($queryParameters['registry']) ? $queryParameters['registry'] : '';
                    $layer=[
                        // 'id'=>strtolower($v->name),
                        'id'=>$registryValue,
//                        'text'=>$v->name.' ('.$v->description.')',
                        'text'=>$v->name,
                        'path'=>$url,
                        'annotator'=>$registryValue,
                    ];
                    array_push($tree, $layer);
                }
            }
        }else{
            $imageExist=false;
        }

        return view('images.show', [
            'status'=>$imageExist,
            'image'=>$image,
            'noteid'=>$aid,
            'data'=>$data,
            'mode'=>1,
            'mask'=>$hasMask,
            'modelBtn'=>$tree,
            'userIdMap'=>$this->fetchAnnotator(),
            'annoAPI'=>$anno,
            'color'=>$color_palette,
            'previousImageId' => $previousImage ? $previousImage->id : null,
            'nextImageId' => $nextImage ? $nextImage->id : null
        ]);
    }

    public function fetchAnnotator(){
        $users=User::where('is_delete', 0)->pluck('name', 'id')->toArray();
        $models=Mode::where('type',9)->where('is_delete',0)->pluck('name', 'api')->toArray();
        $annotators=[];
        foreach ($models as $url=>$name){
            $urlParts = parse_url($url);
            $queryString = isset($urlParts['query']) ? $urlParts['query'] : '';
            parse_str($queryString, $queryParameters);
            $registryValue = isset($queryParameters['registry']) ? $queryParameters['registry'] : '';
            $annotators[$registryValue] = $name;
        }
        foreach ($users as $k=>$v){
            $annotators[$k]=$v;
        }
        return $annotators;
    }

    public function batchAnnotator(Request $request){
        $batch_id=$request->input('batch_id');

        // Fetch all admin and auditor users who have full access to all images
        $users=User::where('is_delete', 0)->where('role', '<', 3)->pluck('name', 'id')->toArray();

        foreach (Batch::with('groups')->find($batch_id)->groups as $grp){
            foreach($grp->users()->pluck('name', 'id')->toArray() as $k=>$v){
                $users[$k]=$v;
            }
        }

        return $users;
    }

    public function historyTable($id)
    {
        $records = [];

        $img = Image::find($id);
        foreach ($img->annotations as $note) {
            array_push($records, array(
                '<input type="checkbox" class="each_checkbox" value="' . $note->id . '">',
                $note->name,
                $note->user->name,
                $note->created_at->format('Y.m.d H:i:s'),
                '<a href="' . url('/slides/' . $id . '/' . $note->id) . '" class="text-primary load-history"><i class="fas fa-eye"></i></a>'

//                '<a href="#" onclick="loadHistory('.$note->id.')" class="text-primary load-history"><i class="fas fa-eye"></i></a>'
            ));
        }

        return response()->json(["data" => $records]);
    }

        # download history annotations
        public function download($id, $annotation_ids)
        {
            $data = [];
            if ($annotation_ids == 0){
                $img = Image::with('annotations')->find($id)->annotations->last();
                array_push($data, array(
                        $img->name,
                        $img->note,
                ));
            }
            else {
                $annotation_ids = explode(",", $annotation_ids);
                $img = Image::with('annotations')->find($id);

                foreach ($img->annotations as $note) {
                    if (in_array(strval($note->id), $annotation_ids)) {
                        array_push($data, array(
                            $note->name,
                            $note->note,
                        ));
                    }
                }
            }

        return response()->json($data);
    }

    public function upload()
    {
        return view('images.upload');
    }
    public function handleUpload(Request $request)
    {
        // Rule 1: csv and < 3Mb
        $validator = Validator::make($request->all(), [
//            'file' => 'required|file|mimes:csv|max:3072', // Max file size: 3MB
            'file' => ['required', 'file', function ($attribute, $value, $fail) {
                $extension = $value->getClientOriginalExtension();
                if ($extension !== 'csv') {
                    $fail('The uploaded file must be a CSV file.');
                }
            }, 'max:3072'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        // status:
        // 1-file not exist or duplicated in DB
        // 2-header not match
        // 3-Invalid file name (only letters, numbers and _ accepted)
        // 9-success, by default
        $res = array('csv' => [], 'status' => 9, 'header' => [],
            'provider' => Group::where('is_delete', 0)->pluck('name', 'id')->toArray());

        // Get header of input template
        $n = 0;
        $standardHeader = [];
        if (($handle = fopen(public_path('input_template.csv'), "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                $data = $this->escapeCSV($data);
                if ($n == 0) {
                    $standardHeader = $data;
                }
            }
        }
        // Process the uploaded file
        $file = $request->file('file');

        $n = 0;
        $header = [];

        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                $data = $this->escapeCSV($data);
                if ($n == 0) {
                    $headers = $data;
                }
                if ($n > 0) {
                    $input = [];
                    $data = array_combine($headers, $data);
                    $data = $this->escapeCSV($data);
//                    $input['name'] = $this->removeSuffixIfExists($data[0]);
                    $input['uuid'] = $data['uuid'];
                    $input['sys_image_file_name'] = $data['sys_image_file_name'];
                    $input['project_codename'] = $data['project_codename'];
                    $input['dataset_codename'] = $data['dataset_codename'];
                    $input['sub_dataset'] = $data['sub_dataset'];
                    $input['associated_dataset'] = $data['associated_dataset'];
                    $input['associated_sub-dataset'] = $data['associated_sub-dataset'];
                    $input['parent_project_codename'] = $data['parent_project_codename'];
                    $input['qbrc_pathology_case_id'] = $data['qbrc_pathology_case_id'];
                    $input['final_pathologic_diagnosis'] = $data['final_pathologic_diagnosis'];
                    $input['stain'] = $data['stain'];
                    $input['stain_marker'] = $data['stain_marker'];
                    $input['block_number'] = $data['block_number'];
                    $input['magnification'] = $data['magnification'];
                    $input['scanner_specs'] = $data['scanner_specs'];
                    $input['width'] = $data['width'];
                    $input['height'] = $data['height'];
                    $input['exist'] = 0;

                    if (preg_match('/[^A-Za-z0-9_.-]/', $this->removeSuffixIfExists($input['sys_image_file_name']))) {
                        $res['status'] = 1;
                        $input['exist'] = 3;
                    } else { // uploaded file name only contains letters, numbers and underline

                        // 0 Not exist, 1 Exist, 2 Duplicated, 3 Invalid file name (only letters, numbers and _ accepted)


                        $input['path'] = $input['project_codename'] . '/' . $input['dataset_codename'] . '/';
                        // Sub dataset name is optional
                        $input['path'] .= empty($input['sub_dataset']) ? '' : $input['sub_dataset'] . '/';
                        // Extract the image name and read the corresponding dzi

                        // Check if there exists same project+dataset+sub_dataset+image
                        // and check if project/dataset/sub_dataset/image physically exists
                        $exist = false;
                        foreach (config('app.iv.support_format') as $ext) {
                            $file = public_path('images') . "/original/" . $input['path'] . $this->removeSuffixIfExists($input['sys_image_file_name']) . "." . $ext;
                            if (file_exists($file)) {
                                $exist = true;
                                if ($ext == 'dzi') {
                                    $xml = json_decode(json_encode(simplexml_load_file($file)), true);

                                    $input['width'] = $xml['Size']['@attributes']['Width'];
                                    $input['height'] = $xml['Size']['@attributes']['Height'];
                                }
                            }
                        }

                        if ($exist) {
                            $input['exist'] = 1;

                            // Check if duplicated
                            $batch = Batch::where('name', $input['dataset_codename'])
                                ->where('project', $input['project_codename'])
                                ->first();

                            if ($batch) {
                                $image = Image::where('name', $this->removeSuffixIfExists($input['sys_image_file_name']))
                                    ->where('batch_id', $batch->id);

                                if (empty($input['sub_dataset'])) {
                                    $image->whereNull('sub_dataset_codename');
                                } else {
                                    $image->where('sub_dataset_codename', $input['sub_dataset']);
                                }

                                if ($image->first()) {
                                    $input['exist'] = 2;
                                    $res['status'] = 1;
                                }
                            }
                        } else {
                            $res['status'] = 1;
                        }
                    }
                    array_push($res['csv'], $input);

                } else {
                    $header = $data;
                    $res['header'] = $header;

//                    if ($header != $standardHeader) {
//                        $res['status'] = 2;
//                        break;
//                    }
                }
                $n++;
            }
            fclose($handle);
        }

        // Rule 2: Column name and required column
        // Add your custom logic here (e.g., storing in storage, processing content)
        return redirect()->back()->with('result', $res);
    }

    public function handleImport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => ['required'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        if (Auth::check()) {
            // Get the authenticated user
            $user = Auth::user();

            // Get the name of the authenticated user
            $name = preg_replace('/\s+/', '_', $user->name);

            Artisan::call('db:backup', [
                'who' => $name,
            ]);
//            $output = Artisan::output();
//            Log::info($output);
        }

        foreach (json_decode($request->input('imageInfo')) as $input) {
            $input = get_object_vars($input);
            //create .db files for each image
            try {
                $response = Http::post($ip=env('API_IP')."/".'annotation/create?image_id='.$input['uuid'], [
                    'Content-Type' => 'application/json'
                ]);
                // Check the response status
                if ($response->failed()) {
                    // Handle a non-OK response
                    throw new \Exception('Network response was not ok');
                }

            } catch (\Exception $e) {
                echo 'There was an error when creating database: ' . $e->getMessage() . "\n";
            }




            // Generate thumbnail
            $source = $input['path'] . $input['sys_image_file_name'];
            $extension = pathinfo($source, PATHINFO_EXTENSION);

            if ($extension == 'svs' or $extension == 'tiff' or $extension == 'tif'){
                Artisan::call('generate:thumbnail', [
                    'input' => public_path('images') . "/original/".$source,
                    'output' => public_path('images') . "/thumbnail/".$input['uuid']. '.jpg',
                ]);
            }
            unset($input['path']);

            // Remove the transitional attributes for importing DB
            $input['name'] = $this->removeSuffixIfExists($input['sys_image_file_name']);
            unset($input['sys_image_file_name']);
            $input['sub_dataset_codename'] = $input['sub_dataset'];
            unset($input['sub_dataset']);
            $input['associated_subdataset'] = $input['associated_sub-dataset'];
            unset($input['associated_sub-dataset']);

            unset($input['exist']);
            $project_codename = $input['project_codename']; // Go to batch table
            unset($input['project_codename']);
            $dataset_codename = $input['dataset_codename']; // The batch name, go to batch table
            unset($input['dataset_codename']);


            // Search the project/batch name and get the batch id from existing/created batch
            $batch = Batch::where('name', $dataset_codename)
                ->where('project', $project_codename)
                ->first();
            $id = '';
            if (!$batch) {
                $id = Batch::create([
                    'group_id' => $request->input('provider'),
                    'name' => $dataset_codename,
                    'project' => $project_codename,
                    'is_delete' => 0,
                    'created_at' => now(),
                ])->id;

                Permission::create([
                    'batch_id' => $id,
                    'group_id' => $request->input('provider'),
                    'own' => 1,
                    'created_at' => now(),
                ]);
            } else {
                $id = $batch->id;
            }

            $input['batch_id'] = $id;
            $input['created_at'] = now();


            // Save the model to the database
            $model = new Image();
            foreach ($input as $k => $v) {
                $model->{$k} = $v;
            }
            $model->save();
        }

        return redirect()->back()->with('succeed', 'Image information is imported successfully.');
    }

    public function backupTable()
    {
        $sql_files = glob(storage_path() . "/app/backup/*.sql");
        $res = [];
        if ($sql_files !== false) {
            foreach ($sql_files as $k => $sql_file) {
                if (file_exists($sql_file)) {
                    $creation_time = date(" Y/m/d/ H:i:s", filectime($sql_file));
                    $filename = basename($sql_file);
                    $arr = explode('_', $filename);
                    array_pop($arr);
                    $creator = implode(" ", $arr);
                    array_push($res, ['creator' => $creator, 'file' => $filename, 'time' => $creation_time]);
                }
            }
        }

        return view('images.backup', [
            'files' => $res,
        ]);
    }

    public function removeSuffixIfExists($fileName)
    {
        // Find the last dot (.) in the file name
        $lastDotPosition = strrpos($fileName, '.');

        // If a dot is found, remove the suffix
        if ($lastDotPosition !== false) {
            $fileName = substr($fileName, 0, $lastDotPosition);
        }

        return $fileName;
    }

    public function escapeCSV($arr)
    {
        $res = [];
        foreach ($arr as $k => $v) {
            $res[$k] = htmlentities(stripslashes(utf8_encode($v)), ENT_QUOTES);
            $res[$k] = preg_replace('/[[:^print:]]/', '', $v);
        }

        return array_map('trim', $res);
    }
}
