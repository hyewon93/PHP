<?php 

require_once __DIR__ . '/vendor/autoload.php';

class paymentGateway {

    private $apiKey;
    public $next_action;
    public $client_secret;
    public $paymentMethodType;
    public $paymentIntentID;

    public function __construct() {

        $this->apiKey = "sk_test_51NW1czAgQo6v5Vl6qoxlaakhMbyZ2Ny11JKPZOV1YXRSP7EN4N3wj3bVKAfQCrg6aDryZJu3NRgQrKkqYBwY5NOI00UnbbIcHb";
        
    }

    public function addCard($formdata) {

        try {
            
            $stripe = new \Stripe\StripeClient($this->apiKey);

            $stripe->paymentMethods->attach(
                $formdata['paymentMethodID'],
                ['customer' => $formdata['stripeCustomerID']]
            );

            $stripe->paymentIntents->update(
                $this->paymentIntentID,
                [
                    'payment_method' => $formdata['paymentMethodID']
                ]
            );

        } catch(Exception $e) {
            throw $e;
        }
        
        return $this;
    }

    public function createCustomer($formdata) {

        try {

            $stripe = new \Stripe\StripeClient($this->apiKey);

            $customer = $stripe->customers->create([
                "description" => $formdata['name'],
                "email" => $formdata['email'],
            ]);

        } catch(Exception $e) {
            throw $e;
        }

        return $customer;
    }

    public function createPaymentIntent($formdata) {

        try {

            $stripe = new \Stripe\StripeClient($this->apiKey);
            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => (intval($formdata['amount']) * pow(10, 2)),
                'currency' => 'cad',
                'customer' => $formdata['stripeCustomerID'],
                'payment_method_types' => ['card'],
                'confirmation_method' => 'manual',
                'description' => 'Payment for online order.',
                'statement_descriptor_suffix' => "Hyewons Dev Site"
            ]);

            $this->paymentIntentID = $paymentIntent->id;
            $this->client_secret = $paymentIntent->client_secret;

        } catch(Exception $e) {
            throw $e;
        }

        return $this;
    }

    public function takePayment($formdata) {

        try {
            
            $stripe = new \Stripe\StripeClient($this->apiKey);

            // Retrieve Payment Intent 
            $paymentIntent = $stripe->paymentIntents->retrieve(
                $formdata['paymentIntentID'],
                []
            );

            // Confirm charge
            if(isset($formdata['off_session']) && $formdata['off_session']) {
                $charge = $stripe->paymentIntents->confirm(
                    $paymentIntent->id,
                    [
                        'off_session' => true
                    ]
                );
            } else {
                $charge = $stripe->paymentIntents->confirm(
                    $paymentIntent->id
                );
            }

            if(!empty($charge->next_action)) {
                $this->next_action = $charge->next_action;

            }

        } catch(Exception $e) {
            throw $e;
        }
        
        return $this;
    }
    
    public function updatePaymentIntent($formdata) {

        try {

            $stripe = new \Stripe\StripeClient($this->apiKey);
            $stripe->paymentIntents->update(
                $formdata['paymentIntentID'],
                [
                    'setup_future_usage' => 'off_session'
                ]
            );

        } catch(Exception $e) {
            throw $e;
        }

        return $this;
    }
}
?>