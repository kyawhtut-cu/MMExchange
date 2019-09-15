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
			$date = "";
			$today = date("Y-m-d");
			if(!empty($args['date'])) {
				$date = $args['date'];
			}
			if($date == $today) {
				$date = "";
			}
		    $this->ci->logger->addInfo(
				"Home page request",$req->getParams()
			);
			return $this->ci->phtml->render(
				$res,
				'home.phtml',
				[
					"exchangeView" => $this->getExchangeView($args, $date),
					"selectedDate" => $args['date']
				]
			);
		}

		public function editBranch($req, $res, $args) {
			$branchId = 0;
			$restapi = new RestApiControllers($this->ci);
			$args['tableName'] = "available_bank_location";
			if(!empty($args['id'])) {
				$branchId = $args['id'];
			}
			$args['id'] = $branchId;
			$branchData = $restapi->getTableData($req->getParams(), $args)[0];
			$bankId = 0;
			$bankType = 0;
			$bankLat = 21.9162;
			$bankLong = 95.9560;
			$cityId = 0;
			$name = "";
			$description = "";
			$image = "";
			$phNo = "";
			$townshipId = 0;
			if($branchId != 0) {
				$branchId = $branchData['id'];
				$bankId = $branchData['bank_id'];
				$bankType = $branchData['bank_type'];
				$name = $branchData['bank_branch_name'];
				$description = $branchData['bank_description'];
				$image = $branchData['bank_branch_image'];
				$phNo = $branchData['bank_branch_ph_no'];
				$bankLat = floatval($branchData['bank_lat']);
				$bankLong = floatval($branchData['bank_long']);
				$cityId = $branchData['city_id'];
				$townshipId = $branchData['township_id'];
			}
			return $this->ci->phtml->render(
				$res,
				'bank-edit.phtml',
				[
					"branchId" => $branchId,
					"bankId" => $bankId,
					"bankType" => $bankType,
					"bank_branch_name" => $name,
					"bank_description" => $description,
					"bank_branch_image" => $image,
					"bank_branch_ph_no" => $phNo,
					"bankLat" => $bankLat,
					"bankLong" => $bankLong,
					"cityId" => $cityId,
					"townshipId" => $townshipId
				]
			);
		}

		public function branchInput($req, $res, $args) {
			return $this->ci->phtml->render(
				$res,
				'bank-home.phtml'
			);
		}

		public function dailyExchange($req, $res, $args) {
			return $this->ci->phtml->render(
				$res,
				'exchange-daily.phtml'
			);
		}

		public function dailyExchangeAdd($req, $res, $args) {
			return $this->ci->phtml->render(
				$res,
				'daily-exchange-add.phtml'
			);
		}

		private function getExchangeView($args, $date) {
			$params = array();
			$restApi = new RestApiControllers($this->ci);
			$data = array();
			foreach ($this->bank_id as $key => $bankId) {
				//Select exchange time information

				$args['tableName'] = 'exchange_time';
				$params['filter'] = 'bank_id,eq,' . $bankId;
				$exchangeTime = $restApi->getTableData($params, $args)[0]['exchange_time'];

				//Select Bank Information
				$args['tableName'] = 'bank_table';
				$params['filter'] = 'id,eq,' . $bankId;
				$bankInfo = $restApi->getTableData($params, $args)[0];

				//Select Exchange rate
				$args['tableName'] = 'exchange_rate';
				$params['filter'] = 'bank_id,eq,' . $bankId;
				if(!empty($date)) {
					$args['tableName'] = 'exchange_daily_rate';
					$params['filter'] = 'bank_id,eq,' . $bankId . ',and,exchange_date,eq,' . $date;
				}
				$exchangeInfo = $restApi->getTableData($params, $args);
				foreach ($exchangeInfo as $key => $exchangeValue) {
					if(empty($date)) {
						$exchangeInfo[$key]['exchange_buy'] = $exchangeValue['exchange_rate_buy'];
						$exchangeInfo[$key]['exchange_sell'] = $exchangeValue['exchange_rate_sell'];
						unset($exchangeInfo[$key]['exchange_rate_buy']);
						unset($exchangeInfo[$key]['exchange_rate_sell']);
					} else {
						$exchangeTime = $exchangeValue['bank_update_time'];
					}
				}
				$data[] = array(
					'bank_info' => $bankInfo,
					"exchange_data" => $exchangeInfo,
					'exchange_time' => $exchangeTime
				);
			}
			return $this->getView($data);
		}

		function str_replace_first($from, $to, $content) {
			$from = '/'.preg_quote($from, '/').'/';

			return preg_replace($from, $to, $content, 1);
		}

		private function getView($data) {
			$class = array(
				"KBZ Bank" => "kbz",
				"AYA Bank" => "aya",
				"CB Bank" => "cb",
				"MOB Bank" => "mob",
				"AGD Bank" => "agd",
				"UAB Bank" => "uab"
			);
			$view = "";
			foreach ($data as $row_key => $row) {
				$bank_name = $row['bank_info']['bank_name'];
				$bank_logo = $row['bank_info']['bank_logo'];
				$exchange_rate = $row['exchange_data'];
				$exchange_time = $row['exchange_time'];
				$view .= '
					<div class="card ' . $class[$bank_name] . '">
						<div class="card-header bg-transparent ' . $class[$bank_name] . '">
							<img src="' . $bank_logo . '" class="card-img-top">
						</div>
						<div class="card-body table-responsive" style="padding: 0px;">
							<table class="table table-hover" style="margin-bottom: 0px;">
								<thead>
									<tr>
										<th class="currency">Currency</th>
										<th class="buy">Buy</th>
										<th class="sell">Sell</th>
									</tr>
								</thead>
								<tbody>';
				foreach ($exchange_rate as $col_key => $col_value) {
					$exchange_currency = $col_value['exchange_currency'];
					$exchange_rate_buy = $col_value['exchange_buy'];
					$exchange_rate_sell = $col_value['exchange_sell'];
					$logo = $col_value['logo_url'];

					$view .= '
									<tr>
										<td align="left">
											' . $exchange_currency . '
										</td>
										<td style="vertical-align: middle">
											' . $exchange_rate_buy . '
										</td>
										<td style="vertical-align: middle">
											' . $exchange_rate_sell . '
										</td>
									</tr>';
				}
				$view .= '
								</tbody>
							</table>
						</div>
						<div class="card-footer text-right bg-transparent ' . $class[$bank_name] . '">
							<span>' . $exchange_time . '</span>
						</div>
					</div>';
			}
			return $view;
		}
	}
?>