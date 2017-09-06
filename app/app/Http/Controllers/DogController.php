<?php

namespace App\Http\Controllers;

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
        $response = $this->client->request('POST', 'https://vision.googleapis.com/v1/images:annotate?key='.getenv('VISION_API_KEY'));
        echo $response->getStatusCode();
    }
}
