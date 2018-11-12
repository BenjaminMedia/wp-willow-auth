<?php


namespace Bonnier\WP\WillowAuth\Http;

use GuzzleHttp\Client;

abstract class BaseController extends \WP_REST_Controller
{
    protected $client;

    /**
     * BaseControllerController constructor.
     *
     * @param \GuzzleHttp\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        add_action('rest_api_init', function () {
            $this->registerRoutes();
        });
    }
}
