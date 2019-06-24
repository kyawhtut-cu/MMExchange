<?php
		
	namespace App\Controllers;

	class ExchangeRequestControllers extends Controllers {

		public function index($req, $res, $args) {
			$aya = new AyaExchangeRateControllers($this->ci);
			$aya_date = $aya->getAyaExchangeDate();
			$this->insertExchangeTime(
				$aya_date,
				$this->bank_id[$this->aya_bank]
			);

			$cb = new CBExchangeRateControllers($this->ci);
			$cb_date = $cb->getCBExchangeDate();
			$this->insertExchangeTime(
				$cb_date,
				$this->bank_id[$this->cb_bank]
			);

			$kbz = new KBZExchangeRateControllers($this->ci);
			$kbz_date = $kbz->getKBZExchangeDate();
			$this->insertExchangeTime(
				$kbz_date,
				$this->bank_id[$this->kbz_bank]
			);
			
			$uab = new UABExchangeRateControllers($this->ci);
			$uab_date = $uab->getUABExchangeDate();
			$this->insertExchangeTime(
				$uab_date,
				$this->bank_id[$this->uab_bank]
			);
			
			$agd = new AGDExchangeRateControllers($this->ci);
			$agd_date = $agd->getAGDExchangeDate();
			$this->insertExchangeTime(
				$agd_date,
				$this->bank_id[$this->agd_bank]
			);

			$mob = new MOBExchangeRateControllers($this->ci);
			$mob_date = $mob->getMOBExchangeDate();
			$this->insertExchangeTime(
				$mob_date,
				$this->bank_id[$this->mob_bank]
			);
			
			return $this->ci->phtml->render($res, 'layouts/exchange.phtml');
		}

		public function agd($req, $res, $args) {
			return $this->ci->phtml->render(
				$res,
				'layouts/agd.phtml'
			);
		}

		public function agd_date($req, $res, $args) {
			$this->insertExchangeTime(
				$req->getParam('agd-date'),
				$this->bank_id[$this->agd_bank]
			);
			echo "success";
		}

		public function agd_data($req, $res, $args) {
			$uab = new AGDExchangeRateControllers($this->ci);
			$uab_date = $uab->insertAGDdata($req->getParam('agd-data'));
			echo "success";
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