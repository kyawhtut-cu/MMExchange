<?php
	
	namespace App\Controllers;

	class CBExchangeRateControllers extends Controllers {

		public function getCBExchangeDate() {
			$html = file_get_html('https://www.cbbank.com.mm/admin/api.xml');
			$data = array();
			$datetime = "";
			foreach ($html->find('cbrate') as $row) {
				$tmp = array();
				foreach ($row->find('date') as $col) {
					$datetime = $col->plaintext . ' ';
				}
				foreach ($row->find('time') as $col) {
					$datetime .= $col->plaintext;
				}
				foreach ($row->find('currency') as $col) {
					$tmp[] = $col->plaintext;
				}
				foreach ($row->find('buy') as $col) {
					$tmp[] = $col->plaintext;
				}
				foreach ($row->find('sell') as $col) {
					$tmp[] = $col->plaintext;
				}
				$data[] = $tmp;
			}
			$this->insertExchangeRate(
				$data,
				$this->bank_id[$this->cb_bank]
			);
			return $datetime;
		}

		private function insertExchangeRate($data, $bank_id) {
			foreach ($data as $row_key => $row) {
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