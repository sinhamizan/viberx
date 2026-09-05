<?php

namespace App\Services;

use App\Models\User;
use Laravel\Cashier\PaymentMethod;
use Stripe\SetupIntent;

class PaymentService
{
    /**
     * Start a SetupIntent so the card can be validated and saved now,
     * without charging it — the actual charge happens later, manually,
     * once a provider approves the order.
     */
    public function createSetupIntent(User $user): SetupIntent
    {
        return $user->createSetupIntent();
    }

    /**
     * Attach the confirmed payment method to the user's Stripe customer
     * and make it their default (creating the Stripe customer if needed).
     */
    public function attachPaymentMethod(User $user, string $paymentMethodId): PaymentMethod
    {
        return $user->updateDefaultPaymentMethod($paymentMethodId);
    }
}
