<?php
	namespace App\Controllers;

	class UABExchangeRateControllers extends Controllers {

		public function getUABExchangeDate() {
		    $context = stream_context_create(
                array(
                    'http' => array(
                        'header' => "User-agent: chrome",
                        'ignore_errors' => true,
                        'follow_location' => true
                    )
                )
            );
			$html = file_get_html('https://www.uab.com.mm/', false, $context);
			$time = "";
			$div = $html->find('div[class=zone-exchange-wrapper]')[0]->find('ul')[0]->find('li');
			$data = array();
			foreach($div as $key => $value) {
			    
			    if($key == 0) {
			        $time = $value->plaintext;
			    } else {
			        $tmp = array();
			        $row = explode(":", $value->plaintext);
			        $tmp[] = trim(str_replace(chr( 194 ) . chr( 160 ), "", $row[0]));
			        $row = explode(",", $row[1]);
			        $buy = explode(" ", $row[0]);
			        $sell = explode(" ", $row[1]);
			        $tmp[] = trim($buy[count($buy)-1]);
			        $tmp[] = trim($sell[count($sell)-1]);
			        $data[] = $tmp;
			    }
			}
			$this->insertExchangeRate(
				$data,
				$this->bank_id[$this->uab_bank]
			);
			return array(
			    "time" => explode("-", $time)[1],
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