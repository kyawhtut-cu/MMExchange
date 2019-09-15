<?php
	
	namespace App\Controllers;

	class ConverterController extends Controllers {

		function convert($req, $res, $args) {
			$amount = $req->getParams()['a'];
			$from = $req->getParams()['f'];
			$to = $req->getParams()['t'];
			$bank = $req->getParams()['b'];
			return $this->ci->phtml->render(
				$res,
				'convert.phtml'
			);
		}

		function exchange($req, $res, $args) {
			if(isset($req->getParams()['amount']) && isset($req->getParams()['type']) && isset($req->getParams()['exchangeCurrencyType']) && isset($req->getParams()['bank'])) {
				$amount = $req->getParams()['amount'];
				$type = $req->getParams()['type'];
				$exchangeType = $req->getParams()['exchangeCurrencyType'];
				$bank = $req->getParams()['bank'];
				$typeName = array(
					"1" => "Buy",
					"2" => "Sell"
				);

				if($type == 1 ||  $type ==2) {
					$restApi = new RestApiControllers($this->ci);
				
					$tables = array(
						'tableName' => 'bank_table'
					);
					$param = array(
						'filter' => 'id,eq,' . $bank
					);

					$result = $restApi->getTableData($param, $tables);
					if(count($result) == 0) {
						return $res->withStatus(200)->withJson([
							"message" => "Invalid Bank Id."
						]);
					}
					$bankName = $result[0]['bank_name'];

					$tables['tableName'] = 'exchange_rate';
					$param['filter'] = 'bank_id,eq,' . $bank . ',and,exchange_currency,eq,' . $exchangeType;
					$result = $restApi->getTableData($param, $tables);
					if(count($result) == 0) {
						return $res->withStatus(200)->withJson([
							"message" => "Invalid Exchnage Currency Type."
						]);
					}
					$exchangeAmount = 0;

					if($type == 1) {
						$exchangeAmount = $amount * $result[0]['exchange_rate_sell'];
					} else {
						$exchangeAmount = $amount * $result[0]['exchange_rate_buy'];
					}

					$tables['tableName'] = 'exchange_time';
					$param['filter'] = 'bank_id,eq,' . $bank;
					$result = $restApi->getTableData($param, $tables)[0];
					return $res->withStatus(200)->withJson([
						"bankName" => $bankName,
						"exchangeType" => $typeName[$type],
						"exchangeCurrency" => $exchangeType,
						"amount" => $amount,
						"exchangeAmount" => $exchangeAmount
					]);
				} else {
					return $res->withStatus(200)->withJson([
							"message" => "Invalid Exchange Type."
						]);
				}
			} else {
				return $res->withStatus(400)->withJson([
						"message" => "Bad Request."
					]);
			}
		}

		//euro, eru = EUR Dollar
		//usd = Unite State Dollar
		//sgd = Singapore Dollar
		//thb = Thai Bhat
		//myr = Malaysian Ringgit

		function convertAmount($req, $res, $args) {
			$amount = $req->getParams()['amount'];
			$type = $req->getParams()['type'];
			$exchangeType = $req->getParams()['exchangeType'];

			$message1 = "Myanmar Kyat";
			$message2 = "Myanmar Kyat";

			if($exchangeType == 'USD') {
				$message1 = "United State Dollar";
			} else if($exchangeType == 'EURO' || $exchangeType == 'EUR') {
				$message1 = 'EUR Dollar';
			} else if($exchangeType == 'SGD') {
				$message1 = 'Singapore Dollar';
			} else if($exchangeType == 'THB') {
				$message1 = 'Thai Bhat';
			} else if($exchangeType == 'MYR'){
				$message1 = 'Malaysian Ringgit';
			}

			$bank = $req->getParams()['bank'];

			$restApi = new RestApiControllers($this->ci);
			$tables = array(
				'tableName' => 'bank_table'
			);
			$param = array(
				'filter' => 'id,eq,' . $bank
			);
			$result = $restApi->getTableData($param, $tables)[0];
			$bankImage = $result['bank_logo'];

			$class = array(
				"KBZ Bank" => "kbz",
				"AYA Bank" => "aya",
				"CB Bank" => "cb",
				"MOB Bank" => "mob",
				"AGD Bank" => "agd",
				"UAB Bank" => "uab"
			);
			$bankName = $class[$result['bank_name']];

			$tables['tableName'] = 'exchange_rate';
			$param['filter'] = 'bank_id,eq,' . $bank . ',and,exchange_currency,eq,' . $exchangeType;
			$result = $restApi->getTableData($param, $tables)[0];

			$exchangeAmount = 0;

			if($type == 1) {
				$exchangeAmount = $amount * $result['exchange_rate_sell'];
			} else {
				$exchangeAmount = $amount * $result['exchange_rate_buy'];
			}

			$tables['tableName'] = 'exchange_time';
			$param['filter'] = 'bank_id,eq,' . $bank;
			$result = $restApi->getTableData($param, $tables)[0];

			echo '<div class="card mt-3 ' . $bankName . '">
					<div class="card-header bg-transparent ' . $bankName . '">
						<img src="' . $bankImage . '" class="card-img-top">
					</div>
					<div class="card-body">
						<div class="vk_sh vk_gy">
							<span class="DFlfde eNFL1">' . number_format($amount,2) . '</span> 
							<span class="vLqKYe" data-mid="/m/09nqf" data-name="' . $message1 . '">
								' . $message1 . '
							</span> equals</div>
							<div class="dDoNo vk_bk gsrt">
								<span class="DFlfde SwHCTb" data-precision="2" data-value="' . number_format($exchangeAmount,2) . '">
									' . number_format($exchangeAmount,2) . '
								</span> 
								<span class="MWvIVe" data-mid="/m/04r7gc" data-name="' . $message2 . '">
									' . $message2 . '
								</span>
							</div>
						<div class="hqAUc">
							<span>' . $result['exchange_time'] . ' · </span> <a href="./api">Exchange Api</a>
						</div>
					</div>
				</div>';
		}

		function getExchangeCurrency($req, $res, $args) {
			$bank = $req->getParams()['bank'];
			$restApi = new RestApiControllers($this->ci);
			$tables = array(
				'tableName' => 'exchange_rate'
			);
			$param = array(
				'filter' => 'bank_id,eq,' . $bank
			);
			$exchange = array();
			foreach($restApi->getTableData($param, $tables) as $key => $value) {
				$exchange[] = $value['exchange_currency'];
			}
			return $res->withStatus(200)->withJson($exchange);
		}

	}