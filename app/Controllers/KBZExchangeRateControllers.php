<?php

	namespace App\Controllers;

	class KBZExchangeRateControllers extends Controllers {

		public function getKBZExchangeDate() {
			$time = "Date - 23.06.2019";
			$html = file_get_html('https://www.kbzbank.com/en/');
			$data = array();
			$index = 0;
			$table = $html->find('div[class=example]')[0]->find('table')[0];
			$usdData = array();
			$euroData = array();
			$sgdData = array();
			$thbData = array();
			$trIndex = 0;
            foreach ($table->find('tr') as $value) {
                $tdIndex = 0;
                foreach($value->find('td') as $td) {
                    if($trIndex == 1) {
                        //todo exchange time
                        $time = $td->plaintext;
                    } else if($trIndex == 2) {
                        //todo usd and euro title
                        if($tdIndex == 0) {
                            //todo usd title
                            $usdData[] = $td->plaintext;
                        } else {
                            $euroData[] = $td->plaintext;
                        }
                    } else if($trIndex == 5) {
                        //todo sgd and thb title
                        if($tdIndex == 0) {
                            $sgdData[] = $td->plaintext;
                        } else {
                            $thbData[] = $td->plaintext;
                        }
                    } else if($trIndex == 3 || $trIndex == 4) {
                        //todo exchange value buy and sell for usd and euro
                        if($tdIndex == 0) {
                            $value = str_replace("Buy - ","",$td->plaintext);
                            $value = str_replace("Sell - ","",$value);
                            $usdData[] = $value;
                        } else {
                            $value = str_replace("Buy - ","",$td->plaintext);
                            $value = str_replace("Sell - ","",$value);
                            $euroData[] = $value;
                        }
                    } else if($trIndex == 6 || $trIndex == 7) {
                        //todo exchange value buy and sell for sgd and thb
                        if($tdIndex == 0) {
                            $value = str_replace("Buy - ","",$td->plaintext);
                            $value = str_replace("Sell - ","",$value);
                            $sgdData[] = $value;
                        } else {
                            $value = str_replace("Buy - ","",$td->plaintext);
                            $value = str_replace("Sell - ","",$value);
                            $thbData[] = $value;
                        }
                    }
                    $tdIndex += 1;
                }
                $trIndex += 1;
			}
			$data[] = $usdData;
			$data[] = $euroData;
			$data[] = $sgdData;
			$data[] = $thbData;
			$this->insertExchangeRate(
				$data,
				$this->bank_id[$this->kbz_bank]
			);
			return str_replace("Date - ","",$time);
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