<?php 
class ModelPaymentPPStandard extends \Core\Model {
	public function getMethod($address, $total) {
		$this->load->language('payment/pp_standard');

		$status = true;
		if (0 > $total) {
			$status = false;
		}
			

		$currencies = array(
			'AUD',
			'CAD',
			'EUR',
			'GBP',
			'JPY',
			'USD',
			'NZD',
			'CHF',
			'HKD',
			'SGD',
			'SEK',
			'DKK',
			'PLN',
			'NOK',
			'HUF',
			'CZK',
			'ILS',
			'MXN',
			'MYR',
			'BRL',
			'PHP',
			'TWD',
			'THB',
			'TRY'
		);

		if (!in_array(strtoupper($this->currency->getCode()), $currencies)) {
			$status = false;
		}			

		$method_data = array();

		if ($status) {  
			$method_data = array(
				'code'       => 'pp_standard',
				'title'      => $this->language->get('text_title'),
				'sort_order' => $this->config->get('pp_standard_sort_order')
			);
		}

		return $method_data;
	}
}
?>