<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request): Response
    {
        $payload   = (string) $request->getContent();
        $sigHeader = (string) $request->header('Stripe-Signature', '');
        /** Use config; env() is disallowed outside config files */
        $secret    = (string) config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe webhook signature verification failed.');
            return new Response('Invalid signature', 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                Log::info('💰 Payment succeeded', $event->data->object->toArray());
                break;

            case 'payment_intent.payment_failed':
                Log::info('❌ Payment failed', $event->data->object->toArray());
                break;

            case 'charge.refunded':
                Log::info('💸 Charge refunded', $event->data->object->toArray());
                break;

            default:
                Log::info('Unhandled event type: ' . $event->type);
        }

        return new Response('Webhook handled', 200);
    }
}