<?php

namespace Bonnier\WP\WillowAuth\Http;

use Bonnier\WP\SiteManager\WpSiteManager;
use GuzzleHttp\RequestOptions;
use WP_REST_Response;

class LookupController extends BaseController
{
    public function registerRoutes()
    {
        register_rest_route('app', '/user/lookup', [
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [$this, 'lookup']
        ]);
    }

    /**
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function lookup(\WP_REST_Request $request)
    {
        try {
            $response = $this->client->post('users/lookup', [
                RequestOptions::JSON => [
                    'email' => $request->get_param('email'),
                    'brand' =>  WpSiteManager::instance()->settings()->getSite()->brand->brand_code ?? null,
                ],
            ]);
        } catch (\Exception $exception) {
            return new WP_REST_Response(null, 404);
        }

        return new WP_REST_Response(json_decode($response->getBody()));
    }
}
