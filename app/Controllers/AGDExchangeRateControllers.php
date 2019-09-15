<?php
	namespace App\Controllers;

	class AGDExchangeRateControllers extends Controllers {
	    
	    public function getAGDExchangeDate() {
	        $html = file_get_html('https://www.agdbank.com/');
	        $date = $html->find('div.date-update')[0]->plaintext;
			$html = file_get_html('https://ccapi.agdbank.com:8080/ExchangeRate/index?callback=?')->plaintext;
			$html = str_replace("?(", "", $html);
			$html = str_replace(");", "", $html);
			$html =  json_decode($html, true)['ExchangeRates'];
			$data = array(
				array(
					"USD"
				),
				array(
					"EURO"
				),
				array(
					"SGD"
				),
				array(
					"THB"
				),
			);
			foreach ($html as $key => $value) {
				if($value['From'] == 'THB' && $value['To'] == 'KYT') {
					$data[3][1] = $value['Rate'];
				}
				if($value['From'] == 'KYT' && $value['To'] == 'THB') {
					$data[3][2] = $value['Rate'];
				}
				if($value['From'] == 'SGD' && $value['To'] == 'KYT') {
					$data[2][1] = $value['Rate'];
				}
				if($value['From'] == 'KYT' && $value['To'] == 'SGD') {
					$data[2][2] = $value['Rate'];
				}
				if($value['From'] == 'EURO' && $value['To'] == 'KYT') {
					$data[1][1] = $value['Rate'];
				}
				if($value['From'] == 'KYT' && $value['To'] == 'EURO') {
					$data[1][2] = $value['Rate'];
				}
				if($value['From'] == 'USD' && $value['To'] == 'KYT') {
					$data[0][1] = $value['Rate'];
				}
				if($value['From'] == 'KYT' && $value['To'] == 'USD') {
					$data[0][2] = $value['Rate'];
				}
			}
			$this->insertExchangeRate($data, $this->bank_id[$this->agd_bank]);
			return array(
			    "time" => $date,
			    "data" => $data
			);
		}
		
		private function insertExchangeRate($data, $bank_id) {
			foreach ($data as $key => $row) {
				$query = "SELECT * FROM exchange_rate WHERE bank_id = " . $bank_id . " AND exchange_currency = '" . $row[0] . "'";
				$result = $this->ci->db->query($query)->fetchall();
				if(count($result) == 1) {
					$query = "UPDATE exchange_rate SET exchange_currency = '" . $row[0] . "', exchange_rate_buy = '" . $row[1] . "', exchange_rate_sell = '" . $row[2] . "' WHERE bank_id = " . $bank_id . " AND exchange_currency = '" . $row[0] . "'";
				} else {
					$query = "INSERT INTO exchange_rate (exchange_currency, exchange_rate_buy, exchange_rate_sell, bank_id) VALUES ('" . $row[0] . "','" . $row[1] . "','" . $row[2] . "'," . $bank_id . ")";
				}
				$this->ci->db->query($query);
			}
		}
	}
?>