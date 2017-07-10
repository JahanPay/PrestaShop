<?php
@session_start();
if (!defined('_PS_VERSION_'))
	exit ;
class jahanpay extends PaymentModule {

	private $_html = '';
	private $_postErrors = array();

	public function __construct() {

		$this->name = 'jahanpay';
		$this->tab = 'payments_gateways';
		$this->version = '1.1';
		$this->author = 'JahanPay';
		$this->currencies = true;
		$this->currencies_mode = 'radio';
		parent::__construct();
		$this->displayName = $this->l('jahanpay Payment Modlue');
		$this->description = $this->l('Online Payment With jahanpay');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details?');
		if (!sizeof(Currency::checkPaymentCurrencies($this->id)))
			$this->warning = $this->l('No currency has been set for this module');
		$config = Configuration::getMultiple(array('jahanpay_API'));
		if (!isset($config['jahanpay_API']))
			$this->warning = $this->l('You have to enter your jahanpay merchant key to use jahanpay for your online payments.');

	}

	public function install() {
		if (!parent::install() || !Configuration::updateValue('jahanpay_API', '') || !Configuration::updateValue('jahanpay_LOGO', '') || !Configuration::updateValue('jahanpay_HASH_KEY', $this->hash_key()) || !$this->registerHook('payment') || !$this->registerHook('paymentReturn'))
			return false;
		else
			return true;
	}

	public function uninstall() {
		if (!Configuration::deleteByName('jahanpay_API') || !Configuration::deleteByName('jahanpay_LOGO') || !Configuration::deleteByName('jahanpay_HASH_KEY') || !parent::uninstall())
			return false;
		else
			return true;
	}

	public function hash_key() {
		$en = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
		$one = rand(1, 26);
		$two = rand(1, 26);
		$three = rand(1, 26);
		return $hash = $en[$one] . rand(0, 9) . rand(0, 9) . $en[$two] . $en[$tree] . rand(0, 9) . rand(10, 99);
	}

	public function getContent() {

		if (Tools::isSubmit('jahanpay_setting')) {

			Configuration::updateValue('jahanpay_API', $_POST['jp_API']);
			$this->_html .= '<div class="conf confirm">' . $this->l('Settings updated') . '</div>';
		}

		$this->_generateForm();
		return $this->_html;
	}

	private function _generateForm() {
		$this->_html .= '<div align="center"><form action="' . $_SERVER['REQUEST_URI'] . '" method="post">';
		$this->_html .= $this->l('Enter your pin :') . '<br/><br/>';
		$this->_html .= '<input type="text" name="jp_API" value="' . Configuration::get('jahanpay_API') . '" ><br/><br/>';
		$this->_html .= '<input type="submit" name="jahanpay_setting"';
		$this->_html .= 'value="' . $this->l('Save it!') . '" class="button" />';
		$this->_html .= '</form><br/></div>';
	}

	public function do_payment($cart) {
		
		
        $client = new SoapClient("http://www.jpws.me/directservice?wsdl");
		
		$jahanpayPin = Configuration::get('jahanpay_API');
		$amount = floatval(number_format($cart ->getOrderTotal(true, 3), 2, '.', ''));
		$callbackUrl = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'modules/jahanpay/jp.php?do=call_back&id=' . $cart ->id . '&amount=' . $amount;
		$orderId = $cart ->id;
		$res = $client->requestpayment($jahanpayPin , $amount/10 , $callbackUrl , $orderId );
		
		$hash = Configuration::get('jahanpay_HASH');
		$_SESSION['order' . $orderId] = md5($orderId . $amount . $hash);
		if ($res['result'] ==1 ) {
		    $_SESSION['au']=$res['au'];
		    echo $this->success($this->l('Redirecting...'));
			echo ('<div style="display:none;">'.$res['form'].'</div><script>document.forms["jahanpay"].submit();</script>');
			} else {
			echo $this->error($this->l('There is a problem.') . ' (' . $res['result'] . ')');
		    }
		
	}

	public function error($str) {
		return '<div class="alert error">' . $str . '</div>';
	}

	public function success($str) {
		echo '<div class="conf confirm">' . $str . '</div>';
	}

	public function hookPayment($params) {
		global $smarty;
		$smarty ->assign('jahanpay_logo', Configuration::get('jahanpay_LOGO'));
		if ($this->active)
			return $this->display(__FILE__, 'jppayment.tpl');
	}

	public function hookPaymentReturn($params) {
		if ($this->active)
			return $this->display(__FILE__, 'jpconfirmation.tpl');
	}

}


?>