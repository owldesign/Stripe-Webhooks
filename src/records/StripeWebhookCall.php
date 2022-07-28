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

namespace owldesign\stripewebhooks\records;

use Craft;
use craft\db\ActiveRecord;
use Exception;
use owldesign\stripewebhooks\events\WebhookEvent;
use owldesign\stripewebhooks\exceptions\WebhookFailed;
use owldesign\stripewebhooks\StripeWebhooks;

/**
 * @author    Vadim Goncharov
 *
 * @since     1.0.0
 */
class StripeWebhookCall extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%stripewebhooks_calls}}';
    }

    public function getPayload()
    {
        return json_decode($this->payload);
    }

    public function getException()
    {
        return json_decode($this->exception);
    }

    /**
     * @throws WebhookFailed
     */
    public function process()
    {
        $this->clearException();

        if ($this->type === '') {
            throw WebhookFailed::missingType($this);
        }

        $this->trigger("stripe-webhooks::{$this->type}", new WebhookEvent(['model' => $this]));

        $jobClass = $this->determineJobClass($this->type);
        if ($jobClass === '') return;

        if (!class_exists($jobClass)) {
            throw WebhookFailed::jobClassDoesNotExist($jobClass, $this);
        }

        Craft::$app->getQueue()->push(new $jobClass(['model' => $this]));
    }

    public function saveException(Exception $exception): static
    {
        $this->exception = json_encode([
            'code'    => $exception->getCode(),
            'message' => $exception->getMessage(),
            'trace'   => $exception->getTraceAsString(),
        ]);

        $this->save();

        return $this;
    }

    protected function determineJobClass(string $eventType): string
    {
        $jobConfigKey = str_replace('.', '_', $eventType);

        return StripeWebhooks::$plugin->settings->jobs[$jobConfigKey] ?? '';
    }

    protected function clearException(): static
    {
        $this->exception = null;
        $this->save();

        return $this;
    }
}
