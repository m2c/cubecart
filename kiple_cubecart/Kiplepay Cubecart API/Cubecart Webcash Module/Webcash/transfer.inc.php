<?php
/*
+--------------------------------------------------------------------------
|   CubeCart v3.0.17
|   ========================================
|   by Alistair Brookbanks
|	CubeCart is a Trade Mark of Devellion Limited
|   Copyright Devellion Limited 2005 - 2006. All rights reserved.
|   Devellion Limited,
|   5 Bridge Street,
|   Bishops Stortford,
|   HERTFORDSHIRE.
|   CM23 2JU
|   UNITED KINGDOM
|   http://www.devellion.com
|	UK Private Limited Company No. 5323904
|   ========================================
|   Web: http://www.cubecart.com
|   Date: Tuesday, 17th July 2007
|   Email: sales (at) cubecart (dot) com
|	License Type: CubeCart is NOT Open Source Software and Limitations Apply 
|   Licence Info: http://www.cubecart.com/site/faq/license.php
+--------------------------------------------------------------------------
|	transfer.php
|   ========================================
|	Core functions for the Webcash Gateway	
+--------------------------------------------------------------------------
*/
/*
//////////////////////////
// WEBCASH GATEWAY
//////////////////////////
// L@@K AT AL THE LOVELY 
// VARIABLES WE HAVE TO
// PLAY WITH!!
//////

//////////////////////////
// IN THE REPEATED REGION
//////
$orderInv['productId']						- product id as an integer
$orderInv['name']							- product name as a varchar
$orderInv['price']							- price of each product (inc options)
$orderInv['quantity']						- quantity of products as an integer
$orderInv['product_options']				- products attributes as test
$orderInv['productCode']					- product code as a varchar
$i											- This is the current incremented integer starting at 0

/////////////////////////
// FIXED VARS
///////
$cart_order_id							- cart order id as a varchar
$ccUserData[0]['email']						- Customers email address
$ccUserData[0]['title']						- Customers title (Mr Miss etc...)
$ccUserData[0]['firstName']					- Customers first name
$ccUserData[0]['lastName']					- Customers last name 
$ccUserData[0]['add_1']						- Invoice Address line 1
$ccUserData[0]['add_2']						- Invoice Address line 1
$ccUserData[0]['town']						- Invoice Town or city
$ccUserData[0]['county']					- Invoice County or state
$ccUserData[0]['postcode']					- Invoice Post/Zip Code
$ccUserData[0]['country']					- Invoice country Id we can look up the country name like this
										countryName($ccUserData[0]['country']);
$ccUserData[0]['phone']						- Contact phone no
$ccUserData[0]['mobile']					- Mobile/Cell phone number

$basket['delInf']['title']				- Delivery title (Mr Miss etc...)
$basket['delInf']['firstName']			- Delivery customers first name
$basket['delInf']['lastName']			- Delivery customers last name 
$basket['delInf']['add_1']				- Delivery Address line 1
$basket['delInf']['add_2']				- Delivery Address line 1
$basket['delInf']['town']				- Delivery Town or city
$basket['delInf']['county']				- Delivery County or state
$basket['delInf']['postcode']			- Delivery Post/Zip Code
$basket['delInf']['country']			- Delivery  country Id we can look up the country name like this	
									countryName($basket['delInf']['country']);


$basket['subTotal'] 					- Order Subtotal (exTax and Shipping)
$basket['grandTotal']					- Basket total which has to be paid (inc Tax and Shipping).
$basket['tax']							- Total tax to pay
$basket['shipCost']						- Shipping price
////////////////////////////////////////////////////////
*/

$module = fetchDbConfig("Webcash");

function repeatVars(){

		global $i, $orderInv;

		// product specific fields are not required.
		$hiddenVars = "";

		return $hiddenVars;
}

function fixedVars(){
	
	global $module, $basket, $ccUserData, $cart_order_id, $config;
	
	// get language for payment page
	switch( trim($ccUserData[0]['lang']) )
	{
		case 'nl':
			$webcashLang = 'NL';
			break;
		case 'es':
			$webcashLang = 'ES';
			break;
		default:
			$webcashLang = 'EN';
			break;
	}
			
	$cus_name = $ccUserData[0]['firstName']." ".$ccUserData[0]['lastName'];
	$hiddenVars = "<input type='hidden' name='ord_mercID' value='80000155".$module['product_ID']."' />
			<input type='hidden' name='product_name' value='".$module['productName']."' />
			<input type='hidden' name='ord_totalamt' value='".$basket['grandTotal']."' />
			<input type='hidden' name='currency' value='RM' />
			<input type='hidden' name='ord_mercref' value='".$cart_order_id."' />
			<input type='hidden' name='ord_shipname' value='".$cus_name."' />
			<input type='hidden' name='ord_shipcountry' value='MY' />
			<input type='hidden' name='ord_date' value='".$cart_order_id."' />
            <input type='hidden' name='ord_returnURL' value='http://XXXXXXXXX/confirmed.php' />
			<input type='hidden' name='ord_email' value='".$ccUserData[0]['email']."' />
			<input type='hidden' name='ord_telephone' value='".$ccUserData[0]['mobile']."' />
			<input type='hidden' name='bill_desc' value='Product Purchase From Rocky101Marketing' />";

			return $hiddenVars;
	
}

function successFirst(){
	global $basket, $db, $glob, $lang, $config;
	
	$cart_order_id = trim( $_POST['cs1'] );

	// check order status
	$order_sum = $db->select( "SELECT status FROM ".$glob['dbprefix']."Webcash_order_sum WHERE cart_order_id = ".$db->mySQLsafe($cart_order_id) );			

	if( $order_sum[0]['status']=='2' )	// processing status
	{
		// set order status
		include("../../../includes/orderSuccess.inc.php");
		return true;
	}
	else
		return false;
}

function success()
{
	global $basket;
	if( $_REQUEST['returncode']=='E1' || $_REQUEST['returncode']=='E2')
			return false;
	else
		return true;
}





///////////////////////////
// Other Vars
////////
$formAction = "https://webcash.com.my/wcgatewayinit.php";
$formMethod = "post";
$formTarget = "_self";
$transfer = "auto";
$stateUpdate = TRUE;

$emailText = "Questions regarding your order should be directed to:\r\n\r\n";
$emailText .= $config['storeName']."\r\n";
$emailText .= $GLOBALS['storeURL']."\r\n";
$emailText .= $config['masterEmail'];
?>