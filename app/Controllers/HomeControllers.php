<?php

	namespace App\Controllers;

	use PDOException;
	use PDO;

	class HomeControllers extends Controllers {
		public function api($req, $res, $args) {
		    $this->ci->logger->addInfo(
				"API Home url request",$req->getParams()
			);
			return $this->ci->phtml->render(
				$res,
				'api-home.phtml'
			);
		}

		public function home($req, $res, $args) {
		    $this->ci->logger->addInfo(
				"Home page request",$req->getParams()
			);
			return $this->ci->phtml->render(
				$res,
				'home.phtml',
				[
					"exchange" => $this->getExchangeRateForAllBank()
				]
			);
		}

		private function getExchangeRateForAllBank() {
			$kbz = $this->getJsonData($this->bank_id[$this->kbz_bank]);
			$aya = $this->getJsonData($this->bank_id[$this->aya_bank]);
			$cb = $this->getJsonData($this->bank_id[$this->cb_bank]);
			$mob = $this->getJsonData($this->bank_id[$this->mob_bank]);
			$agd = $this->getJsonData($this->bank_id[$this->agd_bank]);
			$uab = $this->getJsonData($this->bank_id[$this->uab_bank]);
			$data = array(
				$kbz,
				$aya,
				$cb,
				$mob,
				$agd,
				$uab
			);
			return $data;
		}

		private function getJsonData($bank_id) {
			$query = "SELECT * FROM exchange_time WHERE bank_id = " . $bank_id;
			$result = $this->ci->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
			$exchange_time = $result[0]['exchange_time'];
			$exchange_time = str_replace("\r\n", "", $exchange_time);

			$query = "SELECT * FROM exchange_rate WHERE bank_id = " . $bank_id;
			$result = $this->ci->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
			$exchange_data = array();
			foreach ($result as $key => $value) {
				unset($value['bank_id']);
				unset($value['id']);
				$value['exchange_currency'] = trim($value['exchange_currency']);
				$value['logo_url'] = $this->getFlags($value['exchange_currency']);
				$exchange_data[$key] = $value;
			}

			$query = "SELECT * FROM bank_table WHERE id = " . $bank_id;
			$result = $this->ci->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
			$bank_info = $result[0];
			unset($bank_info['id']);

			$data = array();
			$data['bank_info'] = $bank_info;
			$data['exchange_data'] = $exchange_data;
			$data['exchange_time'] = $exchange_time;

			return $data;
		}
		
		private function getFlags($name) {
		    $query = "SELECT * FROM country_logo WHERE logo_short_cut='" . $name ."'";
		    $result = $this->ci->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
		    return $result[0]['logo_url'];
		}
	}
?>