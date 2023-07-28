<?php


class finaleInventory{
    private $host = "https://app.finaleinventory.com";
    private $accountName = "Your Account Name";
    private $userName = "Your User Name";
    private $password = "Your Password";

    /**
	 * Default constructor
	 * 
	 */
	public function __construct() {

	}
    
    public function authenticate() {

        // Create curl handle with options used for all requests. Finale API authentication is cookie based, so cookies need to be enabled
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR,"");


		// Login to Finale
		curl_setopt($ch, CURLOPT_URL, $this->host . "/" . $this->accountName . "/api/auth");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode( array( "username" => $this->userName, "password" => $this->password)));
		curl_setopt($ch, CURLOPT_HEADER, 1);

		$response = curl_exec($ch);

		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($status_code != 200) exit("FAIL: authentication error statusCode=$status_code\n");

		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($response, 0, $header_size);
		$body = substr($response, $header_size);

		// Pull out all JSESSIONID cookie headers
		preg_match_all('|Set-Cookie: JSESSIONID=(.*);|U', $header, $cookies);

		// Don't return headers in future http requests to keep them simple (reuse to curl handle for automatic cookie handling)
		curl_setopt($ch, CURLOPT_HEADER, 0);

		return array( "curl_handle" => $ch, "auth_response" => json_decode($body), "host" => $this->host, "session_secret" => array_pop($cookies[1]) );
    }

    /**
	 * getInventories
	 *	Gets all inventories associated with a finale inventory account
	 * @param void
	 *
	 * @return array of inventories objects
	 */
	public function getInventories($auth) {
		$response = $this->finaleInventoryAPIRequest($auth, "inventories");
		return $response;
	}

    /**
	 * getProducts
	 *	Gets all products associated with a finale inventory account
	 * @param void
	 *
	 * @return array of products objects
	 */
	public function getProducts($auth) {
		$response = $this->finaleInventoryAPIRequest($auth, "products");
		return $response;
	}

    public function finaleInventoryAPIRequest($auth, $api){

		$result = null;

		// get URL of api we are sending to
		$URL = "";
		switch($api){
			case "products" : 
                $URL =  $auth['auth_response']->resourceProduct; 
                $errorMessage = "FAIL to get products";
                break;
            case "inventories" : 
                $URL =  $auth['auth_response']->resourceInventoryItem; 
                $errorMessage = "FAIL to get inventories";
                break;
			default : $URL = $auth['auth_response']->resourceProduct;
		}

        try {
            curl_setopt($auth['curl_handle'], CURLOPT_URL, $auth['host'] . $URL);
            curl_setopt($auth['curl_handle'], CURLOPT_HTTPGET, true);
    
            $result = json_decode(curl_exec($auth['curl_handle']), true);
    
            $status_code = curl_getinfo($auth['curl_handle'], CURLINFO_HTTP_CODE);
            if ($status_code != 200) {
                throw new Exception ($status_code . " : " . $errorMessage);
            }

        } catch (Exception $e) {
            throw new Exception ($e->getMessage());
        }
		
		return $result;

	}
}
?>