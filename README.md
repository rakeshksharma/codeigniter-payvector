codeigniter-payvector
=====================

i m serving a payvector payment gateway library for codeigniter

step to implement:-

1. download packege.
2. add libraries files and Thepaymentgateway dir  to your site application/libraries/
   so your structure will be:-
   application/libraries/payvector.php
   application/libraries/ThePaymentGateway/PaymentSystem.php
   application/libraries/ThePaymentGateway/Common.php
   application/libraries/ThePaymentGateway/ISOCountries.php
   application/libraries/ThePaymentGateway/SOAP.php
3. add below code to your controller where you want to send payment. you can use this function code in any your file 
where you want to add code. send your card from functions varible
function stand for sending required values to payvector and getting response.

function paytest(){
		//Load library
		
		$this->load->library('payvector');
		// Define blank arrays.
		$address = array();
		$card = array();
		$customer = array();
		$order = array();
		$merchant = array();
		
		// Sending merchant info.
		// Will need to set these variables to valid a MerchantID and Password
		$merchant['username'] = "rakesh-391"; // merchant account username.
		$merchant['password'] = "AV46789O80"; // merchant account password.
		
		//Sending card details.
		$card['exp_month'] = 12; //An int value of month.
		$card['exp_year'] = 15; //An int value of year.
		$card['start_month'] = ''; // optional
		$card['start_year'] = ''; // optional
		$card['holder'] = 'Rakesh'; // card holder name
		$card['number'] = '4976000000003436'; // card number
		$card['issue_number'] = '10'; // optional two digit number
		$card['cvv'] = '452';
		
		// Sending address details.
		$address['country_code'] = 826; // For UK you can change values according to ISOCounteries.php
		$address['address1'] = '32 Edward Street'; // optional
		$address['address2'] = 'Camborne'; // optional
		$address['address3'] = ''; // optional
		$address['address4'] = ''; // optional
		$address['city'] = 'Cornwall'; // optional
		$address['state'] = 'PA'; // optional
		$address['postcode'] = 'TR14 8PA'; // optional
		
		// Sending customer details.
		// Will need to put a valid path here for where the payment pages reside 
	    // e.g. https://www.yoursitename.com/Pages/ 
	    // NOTE: This path MUST include the trailing "/" 
		$customer['site_url'] = base_url(); 
		// This is the domain (minus any host header or port number for your payment processor
	    // e.g. for "https://gwX.paymentprocessor.net:4430/", this should just be "paymentprocessor.net
		$customer['entry_point'] = "payvector.net";
		$customer['email'] = 'test@test.com'; 
		$customer['phone'] = '123456789'; 
		
		// Sending order details.
		$order['amount'] = 1000; //will be in thousands eg :- for 10GBP need to send 10*100 
		$order['currency'] = 826; // For UK you can change values according to ISOCounteries.php
		$order['order_id'] = 'Order-5555'; // will be random number
		$order['order_desc'] = 'Test Order'; // Your any order description 
		$payment_reponse = $this->payvector->pay($address, $card, $order, $customer, $merchant); //for success response will be 0
		if(isset($payment_reponse) && $payment_reponse == '0') {
		 // do your stuff here
		}
		
	}
	
	For support contact to sharmarakesh395@gmail.com
