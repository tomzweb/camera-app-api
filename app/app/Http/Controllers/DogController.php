<?php

namespace App\Http\Controllers;

use App\Image;
use Illuminate\Http\Request;
use GuzzleHttp;
use App\Dog;

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

        $data = json_decode($response->getBody()->getContents());
        $results = collect($data->responses[0]->labelAnnotations);

        $dogs = Dog::get();
        $found = $results->filter(function($item) use ($dogs) {
            return in_array($item->description, $dogs);
        });

        print_r($found->count());

        return $this->respond($results);
    }
}
