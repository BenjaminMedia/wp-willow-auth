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
        $signupData = [
            'name' => $request->get_param('first_name'),
            'family_name' => $request->get_param('last_name'),
            'email' => $request->get_param('email'),
            'subscription_number' => (string)$request->get_param('subscription_number'),
            'postal_code' => (string)$request->get_param('postal_code'),
            'password' => $request->get_param('password'),
            'locale' => LanguageProvider::getCurrentLanguage(),
            'brand' =>  WpSiteManager::instance()->settings()->getSite()->brand->brand_code ?? null,
        ];

        $isSubscriber = $request->get_param('is_subscriber');
        if ($isSubscriber !== null && in_array($isSubscriber, ["0","1"])){
            $signupData ['is_subscriber'] =$isSubscriber;
        }

        if ($createdBy = $request->get_param('created_by')){
            $signupData ['created_by'] =$createdBy;
        }

        try {
            $response = $this->client->post('users/create', [
                RequestOptions::JSON => $signupData,
            ]);
        } catch (\Exception $exception) {
            if (str_contains($exception->getMessage(), 'UsernameExistsException')) {
                return new WP_REST_Response(['message' => 'A user already exists with that email'], 409);
            }
            return new WP_REST_Response(null, 400);
        }

        return new WP_REST_Response(json_decode($response->getBody()));
    }
}
