<?php

	namespace App\Controllers;

	class KBZExchangeRateControllers extends Controllers {

		public function getKBZExchangeDate() {
		    $data = array();
			$html = file_get_html('https://www.kbzbank.com/en/');
			$time = $html->find('div[class=wp-block-kadence-column inner-column-1 kadence-column_6e8702-29]')[0]->find('p')[1]->plaintext;
			foreach($html->find('div[class=kt-row-column-wrap kt-has-4-columns kt-gutter-skinny kt-v-gutter-default kt-row-valign-top kt-row-layout-equal kt-tab-layout-inherit kt-m-colapse-left-to-right kt-mobile-layout-two-grid]')[0]->find('div[class=wp-block-kadence-column]') as $div) {
			    $tmp = array();
			    foreach($div->find('p') as $p) {
			        $value = explode(" ", $p->plaintext);
			        if(count($value) > 1) {
			            $tmp[] = $this->getAmount($p->plaintext);
			        } else {
			            $tmp[] = $p->plaintext;
			        }
			    }
			    $data[] = $tmp;
			}
            // echo "Time " . $time . "<br>";
            // echo json_encode($data);
			$this->insertExchangeRate(
				$data,
				$this->bank_id[$this->kbz_bank]
			);
			return array(
			    "time" => preg_split('/ /',$time)[2],
			    "data" => $data
            );
		}
		
		private function getAmount($value) {
		    $re = '/[\d]/m';
		    preg_match_all($re, $value, $matches, PREG_SET_ORDER, 0);
		    $tmp = "";
		    foreach($matches as $key => $value) {
		        if($key > 3)
		            $tmp .= $value[0];
		    }
		    return $tmp;
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