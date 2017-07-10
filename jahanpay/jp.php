<?php

session_start();
if (isset($_GET['do'])) {
	include (dirname(__FILE__) . '/../../config/config.inc.php');
	include (dirname(__FILE__) . '/../../header.php');
	include_once (dirname(__FILE__) . '/jahanpay.php');
	$jahanpay = new jahanpay;

	if ($_GET['do'] == 'payment') {

			$jahanpay -> do_payment($cart);

	} else {
		if (isset($_GET['id']) && isset($_GET['amount'])) {
			$orderId = $_GET['id'];
			$amount = $_GET['amount'];
			$au=$_SESSION['au'];
			if (isset($_SESSION['order' . $orderId])) {
				$hash = Configuration::get('jahanpay_HASH');
				$hash = md5($orderId . $amount . $hash);
				if ($hash == $_SESSION['order' . $orderId]) {
					$api = Configuration::get('jahanpay_API');
					
					$client = new SoapClient("http://www.jpws.me/directservice?wsdl");
                    $result = $client->verification($api , $amount/10 , $au , $orderId, $_POST + $_GET );

					if (!empty($result['result']) and $result['result'] == 1) {
						error_reporting(E_ALL);
						
						$jahanpay -> validateOrder($orderId, _PS_OS_PAYMENT_, $amount, $jahanpay -> displayName, "سفارش تایید شده / کد رهگیری {$au}", array(), $cookie -> id_currency);
						$_SESSION['order' . $orderId] = '';
						Tools::redirect('history.php');
					} else {
						echo $jahanpay -> error($jahanpay -> l('There is a problem.') . ' (' . $result['result'] . ')<br/>' . $jahanpay -> l('Authority code') . ' : ' . $au);
					}

				} else {
					echo $jahanpay -> error($jahanpay -> l('There is a problem.'));
				}
			} else {
				echo $jahanpay -> error($jahanpay -> l('There is a problem.'));
			}
		} else {
			echo $jahanpay -> error($jahanpay -> l('There is a problem.'));
		}
	}
	include_once (dirname(__FILE__) . '/../../footer.php');
} else {
	_403();
}
function _403() {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}