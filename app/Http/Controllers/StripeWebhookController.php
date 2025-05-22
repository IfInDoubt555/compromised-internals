<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Stripe\Webhook;
use Stripe\Stripe;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe webhook signature verification failed.');
            return response('Invalid signature', 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                // Handle successful payment
                Log::info('💰 Payment succeeded', $event->data->object->toArray());
                break;

            case 'payment_intent.payment_failed':
                // Handle failed payment
                Log::info('❌ Payment failed', $event->data->object->toArray());
                break;

            case 'charge.refunded':
                Log::info('💸 Charge refunded', $event->data->object->toArray());
                break;

            // Add more cases as needed...
            default:
                Log::info('Unhandled event type: ' . $event->type);
        }

        return new Response('Webhook handled', 200);
    }
}