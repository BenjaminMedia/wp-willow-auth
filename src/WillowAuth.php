<?php


namespace Bonnier\WP\WillowAuth;

use Bonnier\WP\WillowAuth\Http\LookupController;
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
        if ($authEndpoint = $this->getAuthEndpoint()) {
            $client = new Client([
                'base_uri' => $authEndpoint
            ]);
            new VerifySubscriptionController($client);
            new SignupController($client);
            new LookupController($client);
        }
    }

    private function getAuthEndpoint()
    {
        if (! $authEndpoint = env('WILLOW_AUTH_ENDPOINT')) {
            add_action('admin_notices', function () {
                echo sprintf(
                    '<div class="error notice"><p>%s</p></div>',
                    "WILLOW_AUTH_ENDPOINT not present in the .env file!"
                );
            });
        }
        return $authEndpoint;
    }
}
