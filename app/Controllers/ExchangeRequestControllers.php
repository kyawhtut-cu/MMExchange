<?php
		
	namespace App\Controllers;

	class ExchangeRequestControllers extends Controllers {

		public function kbz($req, $res, $args) {
			$bank = new KBZExchangeRateControllers($this->ci);
			$bankData = $bank->getKBZExchangeDate();
			$this->insertExchangeTime(
				$bankData['time'],
				$this->bank_id[$this->kbz_bank]
			);

            return $res->withStatus(200)->withJson([
					"exchange" => $bankData
				]);
		}
		
		public function aya($req, $res, $args) {
			$bank = new AyaExchangeRateControllers($this->ci);
			$bankData = $bank->getAyaExchangeDate();
			$this->insertExchangeTime(
				$bankData['time'],
				$this->bank_id[$this->aya_bank]
			);

            return $res->withStatus(200)->withJson([
					"exchange" => $bankData
				]);
		}
		
		public function cb($req, $res, $args) {
			$bank = new CBExchangeRateControllers($this->ci);
			$bankData = $bank->getCBExchangeDate();
			$this->insertExchangeTime(
				$bankData['time'],
				$this->bank_id[$this->cb_bank]
			);

            return $res->withStatus(200)->withJson([
					"exchange" => $bankData
				]);
		}
		
		public function uab($req, $res, $args) {
			$bank = new UABExchangeRateControllers($this->ci);
			$bankData = $bank->getUABExchangeDate();
			$this->insertExchangeTime(
				$bankData['time'],
				$this->bank_id[$this->uab_bank]
			);

            return $res->withStatus(200)->withJson([
					"exchange" => $bankData
				]);
		}
		
		public function agd($req, $res, $args) {
			$bank = new AGDExchangeRateControllers($this->ci);
			$bankData = $bank->getAGDExchangeDate();
			$this->insertExchangeTime(
				$bankData['time'],
				$this->bank_id[$this->agd_bank]
			);

            return $res->withStatus(200)->withJson([
					"exchange" => $bankData
				]);
		}
		
		public function mob($req, $res, $args) {
			$bank = new MOBExchangeRateControllers($this->ci);
			$bankData = $bank->getMOBExchangeDate();
			$this->insertExchangeTime(
				$bankData['time'],
				$this->bank_id[$this->mob_bank]
			);

            return $res->withStatus(200)->withJson([
					"exchange" => $bankData
				]);
		}

		public function index($req, $res, $args) {
			return $this->ci->phtml->render($res, 'layouts/exchange.phtml');
		}

		private function insertExchangeTime($exchange_time, $bank_id) {
			$query = "SELECT * FROM exchange_time WHERE bank_id = " . $bank_id;
			$result = $this->ci->db->query($query)->fetchall();
			if(count($result) == 1) {
				$query = "UPDATE exchange_time SET exchange_time = '" . $exchange_time . "' WHERE bank_id = " . $bank_id;
			} else {
				$query = "INSERT INTO exchange_time (exchange_time, bank_id) VALUES('" . $exchange_time . "'," . $bank_id . ")";
			}
			$this->ci->db->query($query);
		}
	}
?>