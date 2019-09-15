<?php
	
	namespace App\Controllers;

	class MABExchangeRateControllers extends Controllers {

		public function getMABExchangeDate() {
		    $context = stream_context_create(
                array(
                    'http' => array(
                        'header' => "User-agent: chrome",
                        'ignore_errors' => true,
                        'follow_location' => true
                    )
                )
            );
			$html = file_get_html('https://www.mabbank.com/foreign-exchange-rate/', false, $context);
			$data = array();
			echo $html;
			die();
// 			foreach($html->find('table.tablepress-id-1') as $article) {
// 				foreach ($article->find('tr') as $value) {
// 					$tmp = array();
// 					foreach($value->find('td') as $row){
// 						$tmp[] = $row->plaintext;
// 					}
// 					$data[] = $tmp;
// 				}
// 			}
// 			$rmindex = count($data) - 1;
// 			unset($data[$rmindex]);
// 			$this->insertExchangeRate(
// 				$data,
// 				$this->bank_id[$this->aya_bank]
// 			);
// 			return $data[0][0];
            return "";
		}

		private function insertExchangeRate($data, $bank_id) {
			foreach ($data as $key => $row) {
				if($key != 0) {
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
	}
?>