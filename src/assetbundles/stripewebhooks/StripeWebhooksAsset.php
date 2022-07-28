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

namespace owldesign\stripewebhooks\assetbundles\stripewebhooks;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Vadim Goncharov
 *
 * @since     1.0.0
 */
class StripeWebhooksAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->sourcePath = '@owldesign/stripewebhooks/assetbundles/stripewebhooks/dist';

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/StripeWebhooks.js',
        ];

        $this->css = [
            'css/StripeWebhooks.css',
        ];

        parent::init();
    }
}
