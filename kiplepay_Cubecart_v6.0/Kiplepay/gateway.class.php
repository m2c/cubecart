<?php
/**
 * CubeCart v6
 * ========================================
 * CubeCart is a registered trade mark of CubeCart Limited
 * Copyright CubeCart Limited 2014. All rights reserved.
 * UK Private Limited Company No. 5323904
 * ========================================
 * Web:   http://www.cubecart.com
 * Email:  sales@devellion.com
 * License:  GPL-3.0 http://opensource.org/licenses/GPL-3.0
 */
class Gateway
{
    private $_config;
    private $_module;
    private $_basket;
    private $_url;
    private $_encryption = 'AES'; // can be XOR

    public function __construct($module = false, $basket = false)
    {
        $this->_session	=& $GLOBALS['user'];
        $this->_module			= $module;
        $this->_basket			= $basket;
        $this->_sk = $this->_module['sk_live'];
        $this->_pk = $this->_module['pk_live'];

        if ($this->_module['testMode'] == 1) {
            $this->_url = "https://staging.webcash.com.my/wcgatewayinit.php";
        } else {
            $this->_url = "https://webcash.com.my/wcgatewayinit.php";
        }
    }

    private function _ci($strRawText)
    {
        $strAllowableChars = "";
        $blnAllowAccentedChars = false;
        $strCleaned = "";

        $strAllowableChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789 .,'/\\{}@():?-_&�$=%~*+\"\n\r";
        $strCleaned = $this->_ci2($strRawText, $strAllowableChars, true);

        return $strCleaned;
    }

    private function _ci2($strRawText, $strAllowableChars, $blnAllowAccentedChars)
    {
        $iCharPos = 0;
        $chrThisChar = "";
        $strCleanedText = "";


        //Compare each character based on list of acceptable characters
        while ($iCharPos < strlen($strRawText)) {
            // Only include valid characters **
            $chrThisChar = substr($strRawText, $iCharPos, 1);
            if (strpos($strAllowableChars, $chrThisChar) !== false) {
                $strCleanedText = $strCleanedText . $chrThisChar;
            } elseif ($blnAllowAccentedChars == true) {
                // Allow accented characters and most high order bit chars which are harmless **
                if (ord($chrThisChar) >= 191) {
                    $strCleanedText = $strCleanedText . $chrThisChar;
                }
            }

            $iCharPos = $iCharPos + 1;
        }

        return $strCleanedText;
    }

    public function transfer()
    {
        $transfer	= array(
            'action'	=> $this->_url,
            'method'	=> 'post',
            'target'	=> '_self',
            'submit'	=> 'auto',
        );
        return $transfer;
    }

    public function repeatVariables()
    {
        return false;
    }

    public function fixedVariables()
    {
        $amountVal = str_replace(".", "", str_replace(",", "", $this->_basket['total']));
        $order_id = uniqid().'-'.$this->_basket['cart_order_id'];
        $hashvalue =  sha1($this->_pk.$this->_sk. $order_id .$amountVal);

        $hidden	= 	array(
            'ord_mercID' => $this->_sk,
            'ord_mercref' => $order_id,
            'ord_totalamt' => $this->_basket['total'],
            'ord_gstamt' => 0,
            'currency' => 'RM',
            'desc' => '',
            'ord_shipname' => $this->_ci($this->_basket['billing_address']['first_name']." ".$this->_basket['billing_address']['last_name']),
            'address' => $this->_ci($this->_basket['billing_address']['line1']),
            'postcode' => $this->_ci($this->_basket['delivery_address']['postcode']),
            'ord_shipcountry' => $this->_ci($this->_basket['delivery_address']['country_iso']),
            'ord_telephone' => $this->_ci($this->_basket['delivery_address']['postcode']),
            'ord_date' => date('Y-m-d h:i:s'),
            'ord_email' => $this->_ci($this->_basket['billing_address']['email']),
            'merchant_hashvalue' => $hashvalue,
            'ord_returnURL' => $GLOBALS['storeURL'].'/index.php?_g=rm&type=gateway&cmd=process&module=Kiplepay&cart_order_id='.$this->_basket['cart_order_id'],
            'version' => '2.0',
        );
        return $hidden;
    }

    ##################################################

    public function call()
    {
        return false;
    }

    public function process()
    {
        $cart_order_id 		= sanitizeVar($_GET['cart_order_id']); // Used in remote.php $cart_order_id is important for failed orders

        $invalidKey = false;
        $key = $_REQUEST['ord_key'];
        $returncode = $_REQUEST['returncode'];
        $amountVal = str_replace('.', '', $_REQUEST['ord_totalamt']);
        $amountVal = str_replace(',', '', $amountVal);
        $chkOrdKey = sha1($this->_pk.$this->_sk.$_REQUEST['ord_mercref'].$amountVal.$returncode);

        $order				= Order::getInstance();
        $order_summary		= $order->getSummary($cart_order_id);
        $transData['customer_id'] 	= $order_summary["customer_id"];
        $transData['gateway'] 		= "Kiplepay";
        $transData['amount'] 		= $_REQUEST['ord_totalamt'];
        $transData['order_id']		= $cart_order_id;
        $transData['trans_id'] 		= $_REQUEST['wcID'];
        $transData['notes'] = '';

        if ($key == $chkOrdKey) {
            $invalidKey = true;
        } else {
            $invalidKey = false;
        }
        if ($returncode == '100' && $invalidKey == true) {
            $order->orderStatus(Order::ORDER_PROCESS, $cart_order_id);
            $order->paymentStatus(Order::PAYMENT_SUCCESS, $cart_order_id);
            $transData['status'] 		= 'Success';
        } elseif ($returncode == 'E1') {
            $GLOBALS['gui']->setError('Our payment processor has rejected this transaction. Please try using a different payment method.');
            $transData['notes'] = 'Kiple Pay rejected the transaction.';
            $order->orderStatus(Order::ORDER_CANCELLED, $cart_order_id);
            $order->paymentStatus(Order::PAYMENT_CANCEL, $cart_order_id);
            $transData['status'] 		= 'Fail';
            $order->logTransaction($transData);
            httpredir('?_a=checkout');
        } else {
            $transData['notes'] = 'Cancel button clicked by customer on payment form.';
            $order->orderStatus(Order::ORDER_CANCELLED, $cart_order_id);
            $order->paymentStatus(Order::PAYMENT_CANCEL, $cart_order_id);
            $transData['status'] 		= 'Abort';
            $order->logTransaction($transData);
            httpredir('?_a=checkout');
        }

        $order->logTransaction($transData);
        httpredir(currentPage(array('_g', 'type', 'cmd', 'module'), array('_a' => 'complete')));
        return false;
    }

    public function form()
    {
        return false;
    }
}
