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

namespace owldesign\stripewebhooks\models;

use craft\base\Model;
use owldesign\stripewebhooks\records\StripeWebhookCall;

/**
 * @author    Vadim Goncharov
 *
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /** @var string */
    public string $signingSecret = '';

    /** @var array */
    public array $jobs = [];

    /** @var string */
    public string $model = StripeWebhookCall::class;

    /** @var string */
    public string $endpoint = 'stripe-webhooks';

    // Public Methods
    // =========================================================================

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['signingSecret', 'string'],
            ['model', 'string'],
            ['endpoint', 'string'],
        ];
    }
}
