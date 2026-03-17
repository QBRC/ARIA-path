<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Image;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        /***********************
         *** Get the parameters from command
         **********************/

        $grpid=env('TEAM_ID', 1);
        $csv=public_path('images').'/csv/'.env('CSV_FILE', 1).'.csv';



        /***********************
         *** Parse the csv file of clinical information
         **********************/

        // Read the csv file to get the attributes and read the dzi to get the image size
        $n = 0;
        $header = [];
        if (($handle = fopen($csv, "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                $data = $this->escapeCSV($data);
                if ($n == 0) {
                    $headers = $data;
                }
                if ($n>0) {
                    $input = [];
                    $data = array_combine($headers, $data);
                    $data = $this->escapeCSV($data);
//                    $input['name'] = $this->removeSuffixIfExists($data[0]);
                    $input['uuid'] = $data['uuid'];
                    $input['sys_image_file_name'] = $data['sys_image_file_name'];
                    $input['project_codename'] = $data['project_codename'];
                    $input['dataset_codename'] = $data['dataset_codename'];
                    $input['sub_dataset_codename'] = $data['sub_dataset'];
                    $input['associated_dataset'] = $data['associated_dataset'];
                    $input['associated_subdataset'] = $data['associated_sub-dataset'];
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


                    
                    $project_codename=$input['project_codename'] ; // Go to batch table
                    $dataset_codename=$input['dataset_codename']; // The batch name, go to batch table

                    // Search the project/batch name and get the batch id from existing/created batch
                    $batch = Batch::where('name', $dataset_codename)
                        ->where('project', $project_codename)
                        ->first();
                    $id='';
                    if (!$batch) {
                        $id = Batch::create([
                            'group_id'=>$grpid,
                            'name'=>$dataset_codename,
                            'project'=>$project_codename,
                            'is_delete'=>0,
                            'created_at'=>now(),
                        ])->id;

                        Permission::create([
                            'batch_id'=>$id,
                            'group_id'=>$grpid,
                            'own'=>1,
                            'created_at'=>now(),
                        ]);
                    }else{
                        $id = $batch->id;
                    }



                    $input['path'] = $input['project_codename'] . '/' . $input['dataset_codename'] . '/';
                    // Sub dataset name is optional
                    $input['path'] .= empty($input['sub_dataset_codename']) ? '' : $input['sub_dataset_codename'] . '/';
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

                    // Check if there exists same project+dataset+sub_dataset+image
                    // and check if project/dataset/sub_dataset/image physically exists
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
                    $input['name'] = $this->removeSuffixIfExists($input['sys_image_file_name']);       
                    unset($input['sys_image_file_name']);     
                    unset($input['path']);
                    unset($input['project_codename']);
                    unset($input['dataset_codename']);
                    $input['batch_id'] = $id;
                    $input['created_at'] = now();
                     // Save the model to the database
                    $model = new Image();
                    foreach ($input as $k => $v) {
                        $model->{$k} = $v;
                    }
                    $model->save();
                }
                $n++;
            }
            fclose($handle);
        }
    }


    public function escapeCSV($arr){
        $res=[];
        foreach ($arr as $k=>$v){
            $v=htmlentities(stripslashes(utf8_encode($v)), ENT_QUOTES);
            $v=htmlentities(stripslashes(utf8_encode($v)), ENT_QUOTES);
            $res[$k]=trim($v);// remove any non-printable characters
        }
        return $res;
    }

    public function getFileExtension($filename) {
        $pathinfo = pathinfo($filename);
        if (isset($pathinfo['extension'])) {
            return $pathinfo['extension'];
        } else {
            dd('No extension found: '.$filename);
        }
    }
    public function removeSuffixIfExists($fileName) {
        // Find the last dot (.) in the file name
        $lastDotPosition = strrpos($fileName, '.');

        // If a dot is found, remove the suffix
        if ($lastDotPosition !== false) {
            $fileName = substr($fileName, 0, $lastDotPosition);
        }

        return $fileName;
    }
}
