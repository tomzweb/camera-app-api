<?php

namespace App\Http\Controllers;

use App\Image;
use Illuminate\Http\Request;
use GuzzleHttp;

class DogController extends Controller
{
    private $request;
    private $client;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->client = new GuzzleHttp\Client(['verify' => false]);
    }

    public function post()
    {
        $image = new Image($this->request);

        try {
            $imageData = $image->getDefaultVisionRequest();
        } catch(\Exception $e)
        {
            return ['status' => false, 'message' => $e->getMessage()];
        }

        try {
            $response = $this->client->request('POST', 'https://vision.googleapis.com/v1/images:annotate?key='.getenv('VISION_API_KEY'), ['json' => $imageData]);
        } catch(\HttpResponseException $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }

        return json_encode(['status' => ($response->getStatusCode() == "200"), 'data' => $response->getBody()->getContents()]);
    }
}
