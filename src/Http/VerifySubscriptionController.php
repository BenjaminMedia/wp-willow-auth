<?php


namespace Bonnier\WP\WillowAuth\Http;

use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use Bonnier\WP\SiteManager\WpSiteManager;
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
        $subscriptionInfo = $this->validateSubscriptionInBmd(
            $request->get_param('subscription_number'),
            $request->get_param('postal_code'),
            LanguageProvider::getCurrentLanguage(),
            $this->getBrandCodes()
        );

        if ($subscriptionInfo) {
            return new WP_REST_Response($subscriptionInfo);
        }
        return new WP_REST_Response(null, 404);
    }

    private function validateSubscriptionInBmd($subscriptionId, $postalCode, $language, $brandCode)
    {
        try {
            $response = $this->client->post('users/verifySubscription', [
                RequestOptions::JSON => [
                    'subscription_number' => $subscriptionId,
                    'postal_code' => $postalCode,
                    'locale' => $language,
                    'brand_code' => $brandCode,
                ],
            ]);
            return json_decode($response->getBody());
        } catch (\Exception $exception) {
            $code = $exception->getCode();
            $message = $exception->getMessage();

            if ($code == 400) {
                $messageSubscriptionNrFormatNotValid = '{"message":"SubscriptionNrFormatNotValid"}';
                if (stripos($message, $messageSubscriptionNrFormatNotValid) !== false) {
                    return json_decode($messageSubscriptionNrFormatNotValid);
                }
                $messageUnknown = '{"message":"Unknown"}';
                if (stripos($message, $messageUnknown) !== false) {
                    return json_decode($messageUnknown);
                }
            }
            
            if ($code == 404) {
                $messageNoSubscriptionFound = '{"message":"No subscription found."}';
                if (stripos($message, $messageNoSubscriptionFound) !== false) {
                    return json_decode($messageNoSubscriptionFound);
                }
            }

            if ($language === 'sv') { // Ugly hack to make Swedish-Finnish customers work, do not remove!
                return $this->validateSubscriptionInBmd($subscriptionId, $postalCode, 'sf', $brandCode);
            }

            return json_decode('{"message":"UndefinedError"}');

        }
    }

    private function getBrandCodes()
    {
        $brand =  WpSiteManager::instance()->settings()->getSite()->brand;
        $primaryBrandCode = $brand->brand_code ?? null;
        return array_merge([$primaryBrandCode], $brand->brand_code_aliases);
    }
}
