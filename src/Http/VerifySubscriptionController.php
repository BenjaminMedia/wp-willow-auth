<?php


namespace Bonnier\WP\WillowAuth\Http;

use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use Bonnier\WP\SiteManager\WpSiteManager;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use WP_REST_Response;

class VerifySubscriptionController extends BaseController
{
    protected function registerRoutes()
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
        try {
            $response = $this->client->post('users/verifySubscription', [
                RequestOptions::JSON => [
                    'subscription_number' => $request->get_param('subscriptionNumber'),
                    'postal_code' => $request->get_param('postalCode'),
                    'locale' => LanguageProvider::getCurrentLanguage(),
                    'brand_code' => WpSiteManager::instance()->settings()->getSite()->brand->brand_code ?? null,
                ],
            ]);
        } catch (\Exception $exception) {
            return new WP_REST_Response(null, 404);
        }

        return new WP_REST_Response(json_decode($response->getBody()));
    }
}
