<?php 

    require_once "./public_html/include/paymentGateWay.class.php";

    $output = array();

    if(file_get_contents('php://input')) {
        $_REQUEST = json_decode(file_get_contents('php://input'), true);
    }

    if(!empty($_REQUEST['f'])) {
        if($_REQUEST['f'] == "submitForm") {

            $data = [];
            $errorMessage = "";

            try {
                $paymentGateway = new paymentGateway();

                // Create customer
                $stripeCustomer = $paymentGateway->createCustomer($_REQUEST);
                $_REQUEST['stripeCustomerID'] = $stripeCustomer->id;
                $data['stripeCustomerID'] = $stripeCustomer->id;

                // Create Payment Intent
                $paymentGateway->createPaymentIntent($_REQUEST);
                $_REQUEST['paymentIntentID'] = $paymentGateway->paymentIntentID;
                $data['next_action'] = $paymentGateway->paymentIntentID;

                // Add Payment Method
                $paymentGateway->addCard($_REQUEST);

                // Take a payment
                $paymentGateway->takePayment($_REQUEST);
                $data['next_action'] = $paymentGateway->next_action;
            

            } catch(Exception $e) {
                $errorMessage = $e->getMessage();
            }
            
            //$paymentGateway->takePayment($_REQUEST);

            $output = array(
                "status" => (empty($errorMessage)) ? "OK" : "Error",
                "messages" => $errorMessage,
                "data" => $data
            );
        } 
    } 

    echo json_encode($output)
?>