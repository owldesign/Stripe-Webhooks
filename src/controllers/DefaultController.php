<?php
/**
 * Stripe Webhooks plugin for Craft CMS 3.x.
 *
 * Handle Stripe webhooks in a CraftCMS application
 *
 * @link      https://owl-design.net
 *
 * @copyright Copyright (c) 2022 Vadim Goncharov
 */

namespace owldesign\stripewebhooks\controllers;

use Craft;
use craft\web\Controller;
use owldesign\stripewebhooks\exceptions\WebhookFailed;
use owldesign\stripewebhooks\StripeWebhooks;
use Exception;
use Stripe\Webhook;

/**
 * @author    Vadim Goncharov
 *
 * @since     1.0.0
 */
class DefaultController extends Controller
{
    // Protected Properties
    // =========================================================================

    /**
     * @var bool|array Allows anonymous access to this controller's actions.
     *                 The actions must be in 'kebab-case'
     */
    protected int|bool|array $allowAnonymous = ['index'];

    // Public Methods
    // =========================================================================
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config = []);
        $this->enableCsrfValidation = false;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function actionIndex(): mixed
    {
        $this->requirePostRequest();
        $this->verifySignature();

        $eventPayload = json_decode(Craft::$app->getRequest()->getRawBody());
        $modelClass = StripeWebhooks::$plugin->settings->model;

        $stripeWebhookCall = new $modelClass([
            'siteId'  => Craft::$app->getSites()->getCurrentSite()->id,
            'type'    => $eventPayload->type ?? '',
            'payload' => json_encode($eventPayload),
        ]);
        $stripeWebhookCall->save(false);

        try {
            $stripeWebhookCall->process();
        } catch (Exception $exception) {
            $stripeWebhookCall->saveException($exception);

            throw $exception;
        }
    }

    /**
     * Verify stripe signature
     *
     * @throws WebhookFailed
     */
    protected function verifySignature(): bool
    {
        $signature = Craft::$app->getRequest()->getHeaders()->get('Stripe-Signature');
        $secret = StripeWebhooks::$plugin->getSettings()->signingSecret;
        $payload = Craft::$app->getRequest()->getRawBody();

        if (!$signature) {
            throw WebhookFailed::missingSignature();
        }

        try {
            Webhook::constructEvent($payload, $signature, $secret);
        } catch (Exception $exception) {
            throw WebhookFailed::invalidSignature($signature);
        }

        if (empty($secret)) {
            throw WebhookFailed::signingSecretNotSet();
        }

        return true;
    }
}
