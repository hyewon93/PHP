<?php 

    require_once "./finaleInventory.class.php";

    $finaleInventory = new finaleInventory();

    /** 
     * Authenticate for Finale Inventory
    */
    $auth = $finaleInventory->authenticate();

    /**
     * Get Products from Finale Inventory
     */
    $productRes = $finaleInventory->getProducts($auth);

    $productIDs = array();
    foreach($productRes['productId'] as $idx => $productId) {

        if(strtolower($productRes['category'][$idx]) == "product") {
            array_push($productIDs, $productId);
        }
    }

    /**
     * Get Inventories from Finale Inventory
     */
    $inventoryRes = $finaleInventory->getInventories($auth);

    $productInventories = array();
    foreach($inventoryRes['facilityUrl'] as $idx => $facilityUrl) {
        if(in_array($inventoryRes['productId'][$idx], $productIDs)) {
            if(!isset($productInventories[$inventoryRes['productId'][$idx]])) {
                $productInventories[$inventoryRes['productId'][$idx]] = 0;
            }

            if($inventoryRes['quantityOnHand'][$idx]) {
                $productInventories[$inventoryRes['productId'][$idx]] += $inventoryRes['quantityOnHand'][$idx];
            }

            if($inventoryRes['quantityReserved'][$idx]) {
                $productInventories[$inventoryRes['productId'][$idx]] += $inventoryRes['quantityReserved'][$idx];
            }
        }
    }

    // Current Inventories by product ID
    echo json_encode($productInventories);

?>