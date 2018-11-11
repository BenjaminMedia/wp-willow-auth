<?php


namespace Bonnier\WP\WillowAuth\Http;

use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use Bonnier\WP\SiteManager\WpSiteManager;
use GuzzleHttp\RequestOptions;
use WP_REST_Response;

class SignupController extends BaseController
{
    public function registerRoutes()
    {
        register_rest_route('app', '/user/signup', [
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [$this, 'signup']
        ]);
    }

    /**
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function signup(\WP_REST_Request $request)
    {
        try {
            $response = $this->client->post('users/create', [
                RequestOptions::JSON => [
                    'name' => $request->get_param('firstName'),
                    'family_name' => $request->get_param('lastName'),
                    'email' => $request->get_param('email'),
                    'subscription_number' => (string)$request->get_param('subscriptionNumber'),
                    'postal_code' => (string)$request->get_param('postalCode'),
                    'locale' => LanguageProvider::getCurrentLanguage(),
                    'brand' =>  WpSiteManager::instance()->settings()->getSite()->brand->brand_code ?? null,
                    'password' => $request->get_param('password')
                ],
            ]);
        } catch (\Exception $exception) {
            return new WP_REST_Response(null, 400);
        }

        return new WP_REST_Response(json_decode($response->getBody()));
    }
}
