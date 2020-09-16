<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

class modStripedHelper
{
	public static function getAjax ()
	{
		$pinput = file_get_contents('php://input');
		file_put_contents('FCPPAY.LOG', "$pinput\n");
		$rvars = json_decode($pinput);
		file_put_contents('FCPPAY.LOG', print_r($rvars,true)."\n", FILE_APPEND);

		$amount = $rvars->amount;
		$modid = $rvars->modid;
		$module = JModuleHelper::getModuleById($modid);	// warning! module must be enabled on all manu items
		file_put_contents('FCPPAY.LOG', print_r($module,true)."\n", FILE_APPEND);
		$params = new JRegistry($module->params);
		file_put_contents('FCPPAY.LOG', print_r($params,true)."\n", FILE_APPEND);
		$secret = $params['stoken'];

		require_once 'stripe-php/init.php';
		\Stripe\Stripe::setApiKey($secret);
		file_put_contents('FCPPAY.LOG', "Set API key: {$secret}\n", FILE_APPEND);
		file_put_contents('FCPPAY.LOG', print_r($_SERVER,true)."\n", FILE_APPEND);

		// properly build the return url from HTTP_REFERER dealing with ? or trailing /
		$retUrl = $_SERVER['HTTP_REFERER'];
		if (strpos($retUrl,'?')) {
			$prefix = '&';
		} else {
			$prefix = '?';
		}

		$checkout_session = \Stripe\Checkout\Session::create([
			'success_url' => $retUrl . $prefix . 'session_id={CHECKOUT_SESSION_ID}',
			'cancel_url' => $retUrl,
			'payment_method_types' => ['card'],
			'submit_type' => 'donate',
			'mode' => 'payment',
			'line_items' => [[
			  'price_data' => [
				'currency' => 'usd',
				'product_data' => [
				  'name' => 'Donation to Flower City Pickers',
				],
				'unit_amount' => 100 * $amount,
			  ],
			  'quantity' => 1,
			]],
			'metadata' => json_decode($rvars->ccd, true)
		  ]);

		//new JResponseJson(['sessionId' => '6r87td8idd96rd9d9']);
		header('Content-Type: application/json');
		echo json_encode(['sessionId' => $checkout_session['id']]);
		file_put_contents('FCPPAY.LOG', "Sent session id: {$checkout_session['id']}\n", FILE_APPEND);

		jexit();
	}

	public static function attachCCD ($modid, $sessionId)
	{
		$module = JModuleHelper::getModuleById($modid);	// warning! module must be enabled on all manu items
		$params = new JRegistry($module->params);
		$secret = $params['stoken'];

		require_once 'stripe-php/init.php';
		\Stripe\Stripe::setApiKey($secret);
		$session = \Stripe\Checkout\Session::retrieve($sessionId);
		file_put_contents('FCPPAY.LOG', print_r($session,true)."\n", FILE_APPEND);
		$custid = $session['customer'];

		$stripe = new \Stripe\StripeClient($secret);
		$custObj = $stripe->customers->retrieve($custid, []);
		file_put_contents('FCPPAY.LOG', print_r($custObj,true)."\n", FILE_APPEND);

		$ccd = $session['metadata'];
		file_put_contents('FCPPAY.LOG', print_r((array)$ccd,true)."\n", FILE_APPEND);

		$stripe->customers->update($custid, [
			'name' => $ccd->name,
			'phone' => $ccd->phone,
			'address'=> [
				'line1' => $ccd->line1,
				'line2' => $ccd->line2,
				'city' => $ccd->city,
				'state' => $ccd->state,
				'postal_code' => $ccd->postal_code
				]
			]);
		//$stripe->customers->update($custid, ['name' => $name, 'phone' => $phone]);

	}

}