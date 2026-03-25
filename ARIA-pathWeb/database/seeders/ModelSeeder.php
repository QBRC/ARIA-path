<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ModelSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $apiHost = trim((string) env('API_IP', ''));
        $apiScheme = strtolower((string) env('API_SCHEME', 'http'));
        $wsScheme = strtolower((string) env('API_WS_SCHEME', $apiScheme === 'https' ? 'wss' : 'ws'));
        $useServicePorts = filter_var(env('API_USE_SERVICE_PORTS', false), FILTER_VALIDATE_BOOLEAN);

        if (!in_array($apiScheme, ['http', 'https'], true)) {
            $apiScheme = 'http';
        }

        if (!in_array($wsScheme, ['ws', 'wss'], true)) {
            $wsScheme = $apiScheme === 'https' ? 'wss' : 'ws';
        }

        $getPort = function (string $key, ?int $fallback = null): ?int {
            $value = env($key, $fallback);
            if ($value === null || $value === '') {
                return $fallback;
            }

            $port = (int) $value;
            return $port > 0 ? $port : $fallback;
        };

        $apiHostOnly = parse_url(
            preg_match('#^https?://#i', $apiHost) === 1 ? $apiHost : $apiScheme.'://'.$apiHost,
            PHP_URL_HOST
        );

        if (empty($apiHostOnly)) {
            $apiHostOnly = explode('/', $apiHost, 2)[0] ?? '';
        }

        $buildBase = function (string $scheme, string $host, ?int $port = null): string {
            $portPart = $port !== null ? ':'.$port : '';
            return rtrim($scheme.'://'.$host.$portPart, '/');
        };

        if ($useServicePorts && !empty($apiHostOnly)) {
            // Direct mode: use one host/IP and route each service with its own port.
            $deepzoomPort = $getPort('API_PORT_DEEPZOOM');
            $annotationPort = $getPort('API_PORT_ANNOTATION');
            $nucleiPort = $getPort('API_PORT_NUCLEI');
            $copilotPort = $getPort('API_PORT_COPILOT');
            $segmentPort = $getPort('API_PORT_SEGMENT');
            $streamPort = $getPort('API_PORT_STREAM', $annotationPort);

            $deepzoomBase = $buildBase($apiScheme, $apiHostOnly, $deepzoomPort);
            $annotationBase = $buildBase($apiScheme, $apiHostOnly, $annotationPort);
            $nucleiBase = $buildBase($apiScheme, $apiHostOnly, $nucleiPort);
            $copilotBase = $buildBase($apiScheme, $apiHostOnly, $copilotPort);
            $segmentBase = $buildBase($apiScheme, $apiHostOnly, $segmentPort);
            $streamBase = $buildBase($wsScheme, $apiHostOnly, $streamPort).'/';
        } else {
            // Proxy mode: one base URL handles all API paths.
            if (preg_match('#^https?://#i', $apiHost) === 1) {
                $base = rtrim($apiHost, '/');
            } else {
                $base = rtrim($apiScheme.'://'.$apiHost, '/');
            }

            $deepzoomBase = $base;
            $annotationBase = $base;
            $nucleiBase = $base;
            $copilotBase = $base;
            $segmentBase = $base;
            $streamBase = preg_replace('#^https?://#i', $wsScheme.'://', $base).'/';
        }

        $data = [
            [
                'name' => 'slide',
                'api' => $deepzoomBase.'/deepzoom/dummy.dzi?image_id=IMAGE_UUID&file=FILE_PATH&registry=slide',
                'description' => 'Generate tiles with non-transparent background (jpg)',
                'type' => 0,
                'created_at' => now(),
            ],
            [
                'name' => 'scale',
                'api' => $deepzoomBase.'/deepzoom/params?image_id=IMAGE_UUID',
                'description' => 'Extract scale information',
                'type' => 0,
                'created_at' => now(),
            ],
            [
                'name' => 'createDB',
                'api' => $annotationBase.'/annotation/create?image_id=IMAGE_UUID',
                'description' => 'Create a sqlite db file as per image ID',
                'type' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'getAnnotator',
                'api' => $annotationBase.'/annotation/annotators?image_id=IMAGE_UUID',
                'description' => 'Get all annotators as per image ID',
                'type' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'getLabels',
                'api' => $annotationBase.'/annotation/labels?image_id=IMAGE_UUID',
                'description' => 'Get all labels as per image ID',
                'type' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'insert',
                'api' => $annotationBase.'/annotation/insert?image_id=IMAGE_UUID',
                'description' => 'Create a new annotation by image ID',
                'type' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'read',
                'api' => $annotationBase.'/annotation/read?image_id=IMAGE_UUID&item_id=',
                'description' => 'Read an annotation by image ID and item ID',
                'type' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'update',
                'api' => $annotationBase.'/annotation/update?image_id=IMAGE_UUID&item_id=',
                'description' => 'Update an annotation by image ID and item ID',
                'type' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'delete',
                'api' => $annotationBase.'/annotation/delete?image_id=IMAGE_UUID&item_id=',
                'description' => 'Delete an annotation by image ID and item ID',
                'type' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'search',
                'api' => $annotationBase.'/annotation/search?image_id=IMAGE_UUID',
                'description' => 'Fetch all annotation as per image ID',
                'type' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'stream',
                'api' => $streamBase.'stream?image_id=IMAGE_UUID',
                'description' => 'Fetch all annotation as per image ID',
                'type' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'countAnnos',
                'api' => $annotationBase.'/annotation/count?image_id=IMAGE_UUID',
                'description' => 'Count all annotation as per image ID',
                'type' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'HDYolo-Lung',
                'api' => $nucleiBase.'/nuclei/dummy.dzi?image_id=IMAGE_UUID&file=FILE_PATH&registry=yolov8-lung',
                'description' => 'Generate tiles predicted by HD-Yolo lung cancer model',
                'type' => 9,
                'created_at' => now(),
            ],
            [
                'name' => 'HDYolo-Colon',
                'api' => $nucleiBase.'/nuclei/dummy.dzi?image_id=IMAGE_UUID&file=FILE_PATH&registry=yolov8-colon',
                'description' => 'Generate tiles predicted by HD-Yolo colon cancer model',
                'type' => 9,
                'created_at' => now(),
            ],
            [
                'name' => 'chat',
                'api' => $copilotBase.'/copilot?image_id=IMAGE_UUID&file=FILE_PATH&caption=gpt-4o&rag=gpt-4o',
                'description' => 'Generate chat response based on user inputs by GEMMA',
                'type' => 0,
                'created_at' => now(),
            ],
            [
                'name' => 'segment',
                'api' => $segmentBase.'/segment?image_id=IMAGE_UUID&file=FILE_PATH&registry=sam2-b',
                'description' => 'Use the SAM2 model to segment both large areas and individual cells',
                'type' => 0,
                'created_at' => now(),
            ],
        ];

        DB::table('modes')->insert($data);

    }

}