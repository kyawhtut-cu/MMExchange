<?php
	namespace App\Controllers;

	use PDO;
	use PDOException;

	class ApiControllers extends Controllers {

		function kbz_bank($req, $res, $args) {
		    $this->ci->logger->addInfo(
				"KBZ Exchange Request"
			);
			return $this->getJsonData($res, $this->bank_id[$this->kbz_bank]);
		}

		function aya_bank($req, $res, $args) {
		    $this->ci->logger->addInfo(
				"AYa Exchange Request"
			);
			return $this->getJsonData($res, $this->bank_id[$this->aya_bank]);
		}

		function cb_bank($req, $res, $args) {
		    $this->ci->logger->addInfo(
				"CB Exchange Request"
			);
			return $this->getJsonData($res, $this->bank_id[$this->cb_bank]);
		}

		function mob_bank($req, $res, $args) {
		    $this->ci->logger->addInfo(
				"MOB Exchange Request"
			);
			return $this->getJsonData($res, $this->bank_id[$this->mob_bank]);
		}

		function agd_bank($req, $res, $args) {
		    $this->ci->logger->addInfo(
				"AGD Exchange Request"
			);
			return $this->getJsonData($res, $this->bank_id[$this->agd_bank]);
		}

		function uab_bank($req, $res, $args) {
		    $this->ci->logger->addInfo(
				"UAB Exchange Request"
			);
			return $this->getJsonData($res, $this->bank_id[$this->uab_bank]);
		}

		private function getJsonData($res, $bank_id) {
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
				//$value['logo_url'] = $this->getFlags($value['exchange_currency']);
				$exchange_data[$key] = $value;
			}

			$query = "SELECT * FROM bank_table WHERE id = " . $bank_id;
			$result = $this->ci->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
			$bank_info = $result[0];
// 			unset($bank_info['id']);

			$data = array();
			$data['bank_info'] = $bank_info;
			$data['exchange_data'] = $exchange_data;
			$data['exchange_time'] = $exchange_time;

			return $res->withStatus(200)->withJson([
					"exchange" => $data
				]);
		}
		
		private function getFlags($name) {
		    $query = "SELECT * FROM country_logo WHERE logo_short_cut='" . $name ."'";
		    $result = $this->ci->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
		    return $result[0]['logo_url'];
		}
	}
?>