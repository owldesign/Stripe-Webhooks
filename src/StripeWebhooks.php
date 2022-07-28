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

namespace owldesign\stripewebhooks;

use craft\base\Plugin;
use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use owldesign\stripewebhooks\models\Settings;
use yii\base\Event;

/**
 * Class StripeWebhooks.
 *
 * @author    Vadim Goncharov
 *
 * @since     1.0.0
 */
class StripeWebhooks extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var StripeWebhooks
     */
    public static StripeWebhooks $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public string $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules[$this->settings->endpoint] = 'stripe-webhooks/default';
            }
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * {@inheritdoc}
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }
}
