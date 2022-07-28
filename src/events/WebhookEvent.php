<?php

namespace owldesign\stripewebhooks\events;

use owldesign\stripewebhooks\records\StripeWebhookCall;
use yii\base\Event;

class WebhookEvent extends Event
{
    /** @var StripeWebhookCall */
    public StripeWebhookCall $model;
}
