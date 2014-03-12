<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
* Payvector CodeIgniter library
*
* This CodeIgniter library handles payments via payvector.
*
* @package codeigniter-payvector
* @author Rakesh Sharma, Too many tabs <sharmarakesh395[at]gmail.com>
* @copyright Copyright (c) 2014, Rakesh Sharma
* @link https://github.com/rakeshksharma/codeigniter-payvector/
*/



  class Payvector {
  
	var $debug  = false; // seted for debug mode on set it true for debug
	
	function __construct() {
		
	}

	/**
	 * this is a generic function to capture instant payment from card
	 */
	
	public function pay($address='', $card ='', $order ='', $customer='', $merchant=''){
			
			$SiteSecureBaseURL = $customer['site_url'];
		    
			$PaymentProcessorDomain = $customer['entry_point'];
			//$PaymentProcessorDomain = "payvector.net";
			// This is the port that the gateway communicates on
			$PaymentProcessorPort = 443;
		
			if ($PaymentProcessorPort == 443)
			{
				$PaymentProcessorFullDomain = $PaymentProcessorDomain."/";
			}
			else
			{
				$PaymentProcessorFullDomain = $PaymentProcessorDomain.":".$PaymentProcessorPort."/";
			}
			
			require_once ("ThePaymentGateway/PaymentSystem.php");
			
			$rgeplRequestGatewayEntryPointList = new RequestGatewayEntryPointList();
			
			// you need to put the correct gateway entry point urls in here
			// contact support to get the correct urls
		
			// The actual values to use for the entry points can be established in a number of ways
			// 1) By periodically issuing a call to GetGatewayEntryPoints
			// 2) By storing the values for the entry points returned with each transaction
			// 3) Speculatively firing transactions at https://gw1.xxx followed by gw2, gw3, gw4....
			// The lower the metric (2nd parameter) means that entry point will be attempted first,
			// EXCEPT if it is -1 - in this case that entry point will be skipped
			// NOTE: You do NOT have to add the entry points in any particular order - the list is sorted
			// by metric value before the transaction sumbitting process begins
			// The 3rd parameter is a retry attempt, so it is possible to try that entry point that number of times
			// before failing over onto the next entry point in the list
			$rgeplRequestGatewayEntryPointList->add("https://gw1.".$PaymentProcessorFullDomain, 100, 2);
			$rgeplRequestGatewayEntryPointList->add("https://gw2.".$PaymentProcessorFullDomain, 200, 2);
			$rgeplRequestGatewayEntryPointList->add("https://gw3.".$PaymentProcessorFullDomain, 300, 2);
		
			$mdMerchantDetails = new MerchantDetails($merchant['username'], $merchant['password']);
		
			$ttTransactionType = new NullableTRANSACTION_TYPE(TRANSACTION_TYPE::SALE);
			$mdMessageDetails = new MessageDetails($ttTransactionType);
		
			$boEchoCardType = new NullableBool(true);
			$boEchoAmountReceived = new NullableBool(true);
			$boEchoAVSCheckResult = new NullableBool(true);
			$boEchoCV2CheckResult = new NullableBool(true);
			$boThreeDSecureOverridePolicy = new NullableBool(true);
			$nDuplicateDelay = new NullableInt(60);
			$tcTransactionControl = new TransactionControl($boEchoCardType, $boEchoAVSCheckResult, $boEchoCV2CheckResult, $boEchoAmountReceived, $nDuplicateDelay, "",  "", $boThreeDSecureOverridePolicy,  "",  null, null);
		
			$nAmount = new NullableInt($order['amount']); // will be in thousand if you want GBP 10 send in 1000
			$nCurrencyCode = new NullableInt($order['currency']); //will be int values
			$nDeviceCategory = new NullableInt(0);
			$tdsbdThreeDSecureBrowserDetails = new ThreeDSecureBrowserDetails($nDeviceCategory, "*/*",  $_SERVER["HTTP_USER_AGENT"]);
			$tdTransactionDetails = new TransactionDetails($mdMessageDetails, $nAmount, $nCurrencyCode, $order['order_id'], $order['order_desc'], $tcTransactionControl, $tdsbdThreeDSecureBrowserDetails);
		
			$nExpiryDateMonth = new NullableInt($card['exp_month']);
			
			$nExpiryDateYear = new NullableInt($card['exp_year']);
			
			$ccdExpiryDate = new CreditCardDate($nExpiryDateMonth, $nExpiryDateYear);
			$nStartDateMonth = new NullableInt($card['start_month']);
			
			$nStartDateYear = new NullableInt($card['start_year']);
			
			$ccdStartDate = new CreditCardDate($nStartDateMonth, $nStartDateYear);
			$cdCardDetails = new CardDetails($card['holder'], $card['number'], $ccdExpiryDate, $ccdStartDate, $card['issue_number'], $card['cvv']);
		
			$nCountryCode = new NullableInt($address['country_code']);
			
			$adBillingAddress = new AddressDetails($address['address1'], $address['address2'], $address['address3'], $address['address4'], $address['city'], $address['state'], $address['postcode'], $nCountryCode);
			$cdCustomerDetails = new CustomerDetails($adBillingAddress, $customer['email'], $customer['phone'], $_SERVER["REMOTE_ADDR"]);
		
			$cdtCardDetailsTransaction = new CardDetailsTransaction($rgeplRequestGatewayEntryPointList, 1, null, $mdMerchantDetails, $tdTransactionDetails, $cdCardDetails, $cdCustomerDetails, "Some data to be passed out");
			
			$boTransactionProcessed = $cdtCardDetailsTransaction->processTransaction($goGatewayOutput, $tomTransactionOutputMessage);			
			
			if ($boTransactionProcessed == false)
			{
				// could not communicate with the payment gateway 
				$NextFormMode = "PAYMENT_FORM";
				$Message = "Couldn't communicate with payment gateway";
			}
			else
			{
				// status code of 0 - means transaction successful 
				// status code of 3 - means 3D Secure authentication required 
				// status code of 5 - means transaction declined 
				// status code of 20 - means duplicate transaction 
				// status code of 30 - means an error occurred 
				// remain unhandled status code				
				$return = array(
					"StatusCode"=> $goGatewayOutput->getStatusCode(),
					"Message"=> $goGatewayOutput->getMessage()
					);
				if($this->debug){
					echo '<pre>';print_r($return); die('DEBUGE IS ACTIVE');
				}else{
					return $goGatewayOutput->getStatusCode();
				}
				
			}
		}
	}
?>
