<?php
	
	namespace App\Controllers;

	class MOBExchangeRateControllers extends Controllers {

		public function getMOBExchangeDate() {
		    $context = stream_context_create(
                array(
                    'http' => array(
                        'header' => "User-agent: chrome",
                        'ignore_errors' => true,
                        'follow_location' => true
                    )
                )
            );
			$html = file_get_html('https://www.mobmyanmar.com/location/mob-ahlon-branch/', false, $context);
			$exchangeWidget = $html->find('div[class=exchange_widget]');
			$date = $exchangeWidget[0]->find('h1')[0]->plaintext;
			$mobRate = $exchangeWidget[0]->find('div[class=mob-rate]');
			
			$data = array();
			foreach($mobRate as $key => $value) {
			    $rateObj = array();
			    $rateObj[] = explode(" ",$value->find('span')[0]->plaintext)[0];
			    $rate = explode(" ",$value->find('p')[0]->plaintext);
			    $rateObj[] = $rate[1];//Buy
			    $rateObj[] = $rate[count($rate)-1];//Sell
			    $data[] = $rateObj;
			}
			$this->insertExchangeRate(
				$data,
				$this->bank_id[$this->mob_bank]
			);
            return $date;
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