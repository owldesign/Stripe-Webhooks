<?php

namespace owldesign\stripewebhooks\exceptions;

use Exception;
use owldesign\stripewebhooks\records\StripeWebhookCall;

class WebhookFailed extends Exception
{
    public static function missingSignature(): static
    {
        return new static('The request did not contain a header named `Stripe-Signature`.');
    }

    public static function invalidSignature($signature): static
    {
        return new static("The signature `{$signature}` found in the header named `Stripe-Signature` is invalid. Make sure that the `services.stripe.webhook_signing_secret` config key is set to the value you found on the Stripe dashboard. If you are caching your config try running `php artisan cache:clear` to resolve the problem.");
    }

    public static function signingSecretNotSet(): static
    {
        return new static('The Stripe webhook signing secret is not set. Make sure that the `services.stripe.webhook_signing_secret` config key is set to the value you found on the Stripe dashboard.');
    }

    public static function jobClassDoesNotExist(string $jobClass, StripeWebhookCall $webhookCall): static
    {
        return new static("Could not process webhook id `{$webhookCall->id}` of type `{$webhookCall->type} because the configured jobclass `$jobClass` does not exist.");
    }

    public static function missingType(StripeWebhookCall $webhookCall): static
    {
        return new static("Webhook call id `{$webhookCall->id}` did not contain a type. Valid Stripe webhook calls should always contain a type.");
    }

    public function render($request)
    {
        return response(['error' => $this->getMessage()], 400);
    }
}
