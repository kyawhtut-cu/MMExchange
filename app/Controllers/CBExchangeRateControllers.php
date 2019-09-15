<?php
	
	namespace App\Controllers;

	class CBExchangeRateControllers extends Controllers {

		public function getCBExchangeDate() {
			$html = file_get_html('https://www.cbbank.com.mm/exchange_rate.aspx');
			$data = array();
			$datetime = "";
			$table = $html->find('table')[0];
			$tr = $table->find('tr');
			$index = 0;
			foreach($tr as $row) {
			    if($index != 0 && $index != 6) {
			        $td = $row->find('td');
			        $tmp = array();
			        foreach($td as $value) {
			            $tmp[] = $value->plaintext;
			        }
			        $data[] = $tmp;
			    } else if($index == 6) {
			        $datetime = $row->plaintext;
			    }
			    $index += 1;
			}
			$this->insertExchangeRate(
				$data,
				$this->bank_id[$this->cb_bank]
			);
			return array(
			    "time" => trim($datetime),
			    "data" => $data
            );
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