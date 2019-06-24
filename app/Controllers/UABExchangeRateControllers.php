<?php
	namespace App\Controllers;

	class UABExchangeRateControllers extends Controllers {

		public function getUABExchangeDate() {
			$html = file_get_html('http://www.unitedamarabank.com/');
			$data = array();
			foreach ($html->find('div.ex_body') as $row) {
				$tmp = array();
				foreach ($row->find('li') as $col) {
					$tmp[] = $col->plaintext;
				}
				$data[] = $tmp;
			}
			$this->insertExchangeRate(
				$data,
				$this->bank_id[$this->uab_bank]
			);
			return "";
		}

		private function insertExchangeRate($data, $bank_id) {
			foreach ($data as $key => $row) {
				if($key != 0) {
					$query = "SELECT * FROM exchange_rate WHERE bank_id = " . $bank_id . " AND exchange_currency = '" . $row[0] . "'";
					$result = $this->ci->db->query($query)->fetchall();
					if(count($result) == 1) {
						$query = "UPDATE exchange_rate SET exchange_currency = '" . $row[0] . "', exchange_rate_buy = '" . trim($row[1]) . "', exchange_rate_sell = '" . trim($row[2]) . "' WHERE bank_id = " . $bank_id . " AND exchange_currency = '" . $row[0] . "'";
					} else {
						$query = "INSERT INTO exchange_rate (exchange_currency, exchange_rate_buy, exchange_rate_sell, bank_id) VALUES ('" . $row[0] . "','" . trim($row[1]) . "','" . trim($row[2]) . "'," . $bank_id . ")";
					}
					$this->ci->db->query($query);
				}
			}
		}
	}
?>