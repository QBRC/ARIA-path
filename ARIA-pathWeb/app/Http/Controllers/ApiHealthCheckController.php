<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;

class ApiHealthCheckController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 50.0,
        ]);
    }

    public function index()
    {
        // Fetch API group URLs from .env variables
        $apiGroups = [
            [
                'group_name' => 'DeepZoom',
                'api_names' => 'slide, scale',
                'health_check_url' => env('API_IP') . '/deepzoom-health',
                'status' => 'unknown',
                'response_time' => null,
                'status_code' => null,
                'last_response' => null,
                'browser_test' => null,
            ],
            [
                'group_name' => 'Annotation',
                'api_names' => 'createDB, getAnnotator, getLabels, insert, read, update, delete, search, countAnnos',
                'health_check_url' => env('API_IP'). '/annotation-health',
                'status' => 'unknown',
                'response_time' => null,
                'status_code' => null,
                'server_test' => null,
                'browser_test' => null,
            ],
            [
                'group_name' => 'HD-Yolo',
                'api_names' => 'HDYolo-Lung, HDYolo-Colon',
                'health_check_url' => env('API_IP') . '/hdyolo-health',
                'status' => 'unknown',
                'response_time' => null,
                'status_code' => null,
                'server_test' => null,
                'browser_test' => null,
            ],
            [
                'group_name' => 'Copilot',
                'api_names' => 'chat',
                'health_check_url' => env('API_IP') . '/copilot-health',
                'status' => 'unknown',
                'response_time' => null,
                'status_code' => null,
                'server_test' => null,
                'browser_test' => null,
            ],
            [
                'group_name' => 'Segmentation',
                'api_names' =>'segment',
                'health_check_url' => env('API_IP'). '/segment-health',
                'status' => 'unknown',
                'response_time' => null,
                'status_code' => null,
                'server_test' => null,
                'browser_test' => null,
            ],
        ];

        return view('api-health-checks.index', compact('apiGroups'));
    }

    public function checkHealth(Request $request, $groupName)
    {
        // Get the API group URL from .env based on the group name
        $url = $this->getHealthCheckUrl($groupName);
        if (!$url || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid service group name.',
            ], 400);
        }

        try {
            $response = $this->client->get($url, [
                'proxy' => $this->getProxyConfig(),  
                'timeout' => 30,  
            ]);
    
            // Parse the response body
            $responseBody = json_decode($response->getBody()->getContents(), true);
    
            // Ensure the response is the expected structure
            if ($responseBody['health'] === 'OK') {
                $statusCode = $response->getStatusCode();  
                return response()->json([
                    'status' => 'Success',
                    'message' => 'Service is healthy',
                    'environment' => $responseBody['environment'] ?? [],
                    #'network' => $healthData['network'] ?? [],
                    'status_code' => $statusCode,
                    'details' => $responseBody['details'] ?? []
                ]);
            }
    
            return response()->json([
                'status' => 'Failed',
                'message' => 'Health response not ok',
                'details' => $responseBody['details']
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Health check failed!',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function saveUrl(Request $request, $groupName)
    {
        // You can allow the user to change the URL here if needed
        $request->validate([
            'health_check_url' => 'required|url',
        ]);

        // For now, we're not saving to the database but just passing the updated URL to .env
        // This could be implemented later if needed

        return redirect()->route('api-health-checks.index')->with('success', 'URL updated successfully!');
    }

    private function getHealthCheckUrl($groupName)
    {
        // Logic to get URL based on group name
        switch ($groupName) {
            case 'Annotation':
                return env('API_IP') . '/annotation-health';
            case 'DeepZoom':
                return env('API_IP') . '/deepzoom-health';
            case 'HD-Yolo':
                return env('API_IP') . '/hdyolo-health';
            case 'Copilot':
                return env('API_IP') . '/copilot-health';
            case 'Segmentation':
                return env('API_IP') . '/segment-health';
            default:
                return '';
        }
    }

    private function getProxyConfig(): array
    {
        return [
            'http' => env('HTTP_PROXY', ''),
            'https' => env('HTTPS_PROXY', ''),
            'no' => explode(',', env('NO_PROXY', '')),
        ];
    }
}
