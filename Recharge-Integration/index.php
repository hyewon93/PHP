<?php

    require_once "./reCharge.class.php";

    $log = fopen("./logs/testlog.log", "w+");

    $reCharge = new reCharge();

    try {

        /** 
         * Get store ID from ReCharge
        */
        $storeRes = $reCharge->getStore();

        fwrite($log, "\nReCharge Store ID: " . $storeRes["store"]["id"] . "\n\n");

        /** 
         * Get customers
        */
        $customerLimit = 10;    // Limit number of customers (max: 250)
        $custoemrPage = 1;      // current page 

        $customerRes = $reCharge->getCustomers([
            "limit" => $customerLimit,
            "page" => $custoemrPage,
        ]);

        if(!empty($customerRes["customers"])) {

            foreach($customerRes["customers"] as $customer) {
                fwrite($log, "\nCustomer '" . $customer["email"] . "' : \n");

                /** 
                 * Get Payment Methods
                */
                $paymentMethodsRes = $reCharge->getPaymentMethods(array(
                    "customer_id" => $customer["id"]
                ));

                if(!empty($paymentMethodsRes["payment_methods"])) {

                    foreach($paymentMethodsRes["payment_methods"] as $paymentMethod) {
                        fwrite($log, "\n - Payment Method Token: " . $paymentMethod["processor_customer_token"] . " (" . $paymentMethod["processor_name"] . ")\n");
                    }

                } else {
                    fwrite($log, date("Y-m-d H:i:s") . " --- No results (getPaymentMethods) --- \n");
                }

                /** 
                 * Get Subscriptions
                */
                $subscriptionRes = $reCharge->getSubscriptions(array(
                    "customer_id" => $customer["id"]
                ));

                $reChargeSubList = [];
                if(!empty($subscriptionRes["subscriptions"])) {

                    foreach($subscriptionRes["subscriptions"] as $subscription) {
                        fwrite($log, "\n - Subscription# : " . $subscription["id"] . "\n");
                    }

                } else {
                    fwrite($log, date("Y-m-d H:i:s") . " --- No results (getSubscription) --- \n");
                }

                /** 
                 * Get Orders
                */
                $orderRes = $reCharge->getOrders(array(
                    "customer_id" => $customer["id"]
                ));

                if(!empty($orderRes["orders"])) {

                    foreach($orderRes["orders"] as $order) {

                        fwrite($log, "\n - order# : " . $order["id"] . "\n");
                    }

                } else {
                    fwrite($log, date("Y-m-d H:i:s") . " --- No results (getOrders) --- \n");
                }
            }

        } else {
            fwrite($log, date("Y-m-d H:i:s") . " --- No results (getCustomers) --- \n");
        }

    } catch (Exception $e) {
        
        fwrite($log, date("Y-m-d H:i:s") . " --- ERROR --- \n" . $e->getMessage());
    }
    

?>