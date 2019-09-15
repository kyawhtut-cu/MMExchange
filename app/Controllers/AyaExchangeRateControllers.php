<?php
	
	namespace App\Controllers;

	class AyaExchangeRateControllers extends Controllers {

		public function getAyaExchangeDate() {
			$html = file_get_html('https://www.ayabank.com/en_US/');
			$data = array();
			foreach($html->find('table.tablepress-id-104') as $article) {
				foreach ($article->find('tr') as $value) {
					$tmp = array();
					foreach($value->find('td') as $row){
						$tmp[] = $row->plaintext;
					}
					$data[] = $tmp;
				}
			}
			$this->insertExchangeRate(
				$data,
				$this->bank_id[$this->aya_bank]
			);
			$time = $data[0][0];
			unset($data[0]);
			return array(
			    "time" => $time,
			    "data" => $data
            );
		}

		private function insertExchangeRate($data, $bank_id) {
		    $currency = array("USD","EURO","SGD");
			foreach ($data as $key => $row) {
				if($key != 0) {
					$query = "SELECT * FROM exchange_rate WHERE bank_id = " . $bank_id . " AND exchange_currency = '" . $currency[$key - 1] . "'";
					$result = $this->ci->db->query($query)->fetchall();
					if(count($result) == 1) {
						$query = "UPDATE exchange_rate SET exchange_currency = '" . $currency[$key - 1] . "', exchange_rate_buy = '" . $row[2] . "', exchange_rate_sell = '" . $row[3] . "' WHERE bank_id = " . $bank_id . " AND exchange_currency = '" . $currency[$key - 1] . "'";
					} else {
						$query = "INSERT INTO exchange_rate (exchange_currency, exchange_rate_buy, exchange_rate_sell, bank_id) VALUES ('" . $currency[$key - 1] . "','" . $row[2] . "','" . $row[3] . "'," . $bank_id . ")";
					}
					$this->ci->db->query($query);
				}
			}
		}
	}
?>