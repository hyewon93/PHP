<?php

class reCharge {

	/**
	 * The public key
	 *
	 * Populated from the service vendor credentials in the database
	 *
	 * @var string
	 */
	private $sk = "%%Your private key%%";

    /**
	 * Details about this instance
	 *
	 * @var string
	 */
	private $url = "https://api.rechargeapps.com";

    /**
	 * Time (in seconds) to wait when we're being throtted.
	 *
	 * @var integer
	 */
	private $throttleWaitTime = 10;

    /**
	 * Default constructor
	 * 
	 */
	public function __construct() {

	}

    /**
	 * getAddresses
	 *		Gets all addresses associated with a reCharge account
	 *
	 *		@param array/object request mixed, 
	 *
	 * 		@return array of addresses objects
	 */
	public function getAddresses($request) {
		$response = $this->reChargeAPIRequest($request, "listAddresses", "GET");
		return $response;
	}

	/**
	 * getCharge
	 *		Gets charge associated with a reCharge account
	 *
	 *		@param array/object request mixed, 
	 *
	 * 		@return array of charge object
	 */
	public function getCharge($request) {
		$response = $this->reChargeAPIRequest($request, "getCharge", "GET");
		return $response;
	}

	/**
	 * getCustomers
	 *		Gets all customers associated with a reCharge account
	 *
	 *		@param array/object request mixed, 
	 *
	 * 		@return array of customers objects
	 */
	public function getCustomers($request) {
		$response = $this->reChargeAPIRequest($request, "listCustomers", "GET");
		return $response;
	}

	/**
	 * getOrders
	 *		Gets orders associated with a reCharge account
	 *
	 *		@param array/object request mixed, 
	 *
	 * 		@return array of store object
	 */
	public function getOrders($request) {
		$response = $this->reChargeAPIRequest($request, "listOrders", "GET");
		return $response;
	}

	/**
	 * getPaymentMethods
	 *		Gets payment methods associated with a reCharge account
	 *
	 *		@param array/object request mixed, 
	 *
	 * 		@return array of store object
	 */
	public function getPaymentMethods($request) {
		$response = $this->reChargeAPIRequest($request, "listPaymentMethods", "GET");
		return $response;
	}

	/**
	 * getProducts
	 *		Gets products associated with a reCharge account
	 *
	 *		@param array/object request mixed, 
	 *
	 * 		@return array of store object
	 */
	public function getProducts() {
		$response = $this->reChargeAPIRequest("", "listProducts", "GET");
		return $response;
	}

	/**
	 * getStore
	 *		Gets store data associated with a reCharge account
	 *
	 *		@param array/object request mixed, 
	 *
	 * 		@return array of store object
	 */
	public function getStore() {
		$response = $this->reChargeAPIRequest("", "getStore", "GET");
		return $response;
	}

	/**
	 * getSusbscription
	 *		Gets subscription data associated with a reCharge account
	 *
	 *		@param array/object request mixed, 
	 *
	 * 		@return array of store object
	 */
	public function getSubscription($request) {
		$response = $this->reChargeAPIRequest($request, "getSubscription", "GET");
		return $response;
	}

	/**
	 * getSubscriptions
	 *		Gets subscriptions associated with a reCharge account
	 *
	 *		@param array/object request mixed, 
	 *
	 * 		@return array of store object
	 */
	public function getSubscriptions($request) {
		$response = $this->reChargeAPIRequest($request, "listSubscriptions", "GET");
		return $response;
	}

	public function getTest($request) {
		$response = $this->reChargeAPIRequest($request, "test", "GET");
		return $response;
	}

	/**
	 * reChargeAPIRequest
	 * 		send HTTP request to end point specified in the $api argument
	 * 
	 *	@param array/object request mixed,
	 *	@param api string, can be one of the following:
	 *					- listCustomers: [GET]
	 *	@param httpRequestMethod string, this api accepts GET, DELETE, and POST request to most endpoints, see above for outliers.
	 */
	private function reChargeAPIRequest($request = array(), $api, $httpRequestMethod = "GET"){

		$URL = "";
		$header = "";
		$limit = (!empty($request['limit'])) ? $request['limit'] : 250;
		$page = (!empty($request['page'])) ? $request['page'] : 1;

		switch($api){
			case "listAddresses": 	
				$URL = $this->url . "/addresses?customer_id=" . $request['customer_id']; 
				$header = array(
					"X-Recharge-Version: 2021-11",
					"X-Recharge-Access-Token: " . $this->sk
				);
				break;
			case "listCustomers": 	
				$URL = $this->url . "/customers?limit=" . $limit . "&page=" . $page; 
				$header = array(
					"Accept: application/json",
					"X-Recharge-Access-Token: " . $this->sk
				);
				break;
			case "listOrders":
				$URL = $this->url . "/orders?customer_id=" . $request['customer_id']; 
				$header = array(
					"Accept: application/json",
					"X-Recharge-Access-Token: " . $this->sk
				);
				break;
			case "listPaymentMethods": 	
				$URL = $this->url . "/payment_methods?customer_id=" . $request['customer_id']; 
				$header = array(
					"Accept: application/json",
					"X-Recharge-Access-Token: " . $this->sk
				);
				break;
			case "listProducts":
				$URL = $this->url . "/products"; 
				$header = array(
					"Accept: application/json",
					"X-Recharge-Access-Token: " . $this->sk
				);
				break;
			case "listSubscriptions":
				$URL = $this->url . "/subscriptions?customer_id=" . $request['customer_id']; 
				$header = array(
					"Accept: application/json",
					"X-Recharge-Access-Token: " . $this->sk
				);
				break;
			case "getCharge":
				$URL = $this->url . "/charges?external_order_id=" . $request['external_order_id']; 
				$header = array(
					"X-Recharge-Version: 2021-11",
					"X-Recharge-Access-Token: " . $this->sk
				);
				break;
			case "getStore":
				$URL = $this->url . "/store"; 
				$header = array(
					"X-Recharge-Version: 2021-11",
					"X-Recharge-Access-Token: " . $this->sk
				);
				break;
			case "getSubscription":
				$URL = $this->url . "/subscriptions/" . $request['subscription_id']; 
				$header = array(
					"X-Recharge-Version: 2021-11",
					"X-Recharge-Access-Token: " . $this->sk
				);
				break;
			case "test":
				$URL = $request['testURL'];
				$header = array(
					"X-Recharge-Version: 2021-11",
					"X-Recharge-Access-Token: " . $this->sk
				);
				break;
			default : 
				$URL = $this->url . "customers?limit=250&page=1";
				$header = array(
					"Accept: application/json",
					"X-Recharge-Access-Token: " . $this->sk
				);
		}

		$responseCode = "";
		$connectionAttempts = 0;

		while($responseCode != 200 && $connectionAttempts < 10) {
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => $URL,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => $httpRequestMethod,
				CURLOPT_HTTPHEADER => $header,
			));

			$response = curl_exec($curl);

			// Check if any error occurred
			if (!curl_errno($curl)) {
				$info = curl_getinfo($curl);
				$response = json_decode($response, true);
				$responseCode = ((isset($info['http_code'])) ? $info['http_code'] : "");

				// Init the time we're going to wait after.
				$timeToWait = 0;

				$reChargeTime = (
					(!empty($info['total_time'])) 
					? 
						$info['total_time'] - (
							(!empty($info['connect_time']))
							? $info['connect_time']
							: 0
						)
					: (
						(!empty($info['connect_time']))
						? $info['connect_time']
						: 0
					)
				); 

				// If klaviyo is taking longer than 1 second, we're probably being throttled.
				if($reChargeTime > 1) {
					// Let's wait a bit when we're done so we can stop the throttle
					$timeToWait = $this->throttleWaitTime;
				}

				if($responseCode != 200) {
					// ERROR
				}

				if(!empty($timeToWait)) {
					// Time to take a rest.
					sleep($timeToWait);
				}

				$connectionAttempts++;

			}

			curl_close($curl);
		}

		return ((isset($response)) ? $response : []);
		
	}
}
?>