<?php


namespace Bonnier\WP\WillowAuth;

use Bonnier\WP\WillowAuth\Http\SignupController;
use Bonnier\WP\WillowAuth\Http\VerifySubscriptionController;
use GuzzleHttp\Client;

class WillowAuth
{
    protected static $instance;

    /**
     * Returns the instance of this class.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __construct()
    {
        $client = new Client([
            'base_uri' => getenv('WILLOW_AUTH_ENDPOINT')
        ]);

        new VerifySubscriptionController($client);
        new SignupController($client);
    }
}
