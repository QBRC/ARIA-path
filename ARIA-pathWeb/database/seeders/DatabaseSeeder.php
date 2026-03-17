<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Image;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Add random data records
//        $fs=new Filesystem();
//        $db=$fs->get(base_path('database/seeders').'/'.'datarecord.sql');
//        DB::connection()->getPdo()->exec($db);

        // Seeders
        $this->call([
            ModelSeeder::class,
        ]);
        // Add admin user
        $data = [
            [
                'name' => 'Admin',
                'email' => 'admin@email.com',
                'password' => Hash::make('admin'),
                'group_id' => 1,
                'role' => 1,
                'is_delete' => false,
                'created_at' => now(),
            ],

            [
                'name' => 'User1',
                'email' => 'user1@email.com',
                'password' => Hash::make('helloworld'),
                'group_id' => 1,
                'role' => 3,
                'is_delete' => false,
                'created_at' => now(),
            ],      
        ];

        DB::table('users')->insert($data);

        // Groups
        $data = [
            [
                'name' => 'DataCenter',
                'pi' => 'Test PI',
                'org' => 'UT Southwestern',
                'created_at' => now(),
            ],
        ];

        DB::table('groups')->insert($data);


        // Batches
        $data = [
            [
                'group_id' => 1,
                'name' => 'default',
                'project' => 'default',
                'created_at' => now(),
            ],
        ];
        DB::table('batches')->insert($data);

        // Permission
        $data = [
            [
                'batch_id' => 1,
                'group_id' => 1,
                'own' => 1,
                'created_at' => now(),
            ],
        ];

        DB::table('permissions')->insert($data);

        // Images
        $data = [];
        $ip=env('API_IP');
        for ($i = 1; $i <= 10; $i++) {
            $temp = [
                'name' => 'example' . $i,
                'uuid' => 'example_uuid_'.$i,
                'batch_id' => 1,
                'created_at' => now(),
            ];

            Artisan::call('generate:thumbnail', [
                'input' => public_path('images') . "/original/default/default/example".$i.".svs",
                'output' => public_path('images') . "/thumbnail/example_uuid_".$i. '.jpg',
            ]);

            //make .db files for each image data
            try {
                $response = Http::post($ip.'/annotation/create?image_id='.'example_uuid_'.$i, [
                    'Content-Type' => 'application/json'
                ]);
                // Check the response status
                if ($response->failed()) {
                    // Handle a non-OK response
                    throw new \Exception('Network response was not ok');
                }
                // Log the success message (optional)
                Log::info('Successfully created database');
                     
            } catch (\Exception $e) {
                // Log the error
                Log::error('There was an error when creating database: ' . $e->getMessage());
                echo 'There was an error when creating database: ' . $e->getMessage() . "\n";
            }

            array_push($data, $temp);
        }

        DB::table('images')->insert($data);

    }

}