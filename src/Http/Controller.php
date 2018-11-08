<?php


namespace Bonnier\WP\WillowAuth\Http;

use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;

class Controller extends \WP_REST_Controller
{
    protected $dbRepository;

    public function __construct()
    {

    }

    public function register_routes()
    {
        register_rest_route('app', '/user/verify-subscription', [
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [$this, 'verifyUserSubscription']
        ]);
    }

    /**
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function verifyUserSubscription(\WP_REST_Request $request)
    {
        $locale = LanguageProvider::getCurrentLanguage();


        return new \WP_REST_Response([

        ]);
    }
}
