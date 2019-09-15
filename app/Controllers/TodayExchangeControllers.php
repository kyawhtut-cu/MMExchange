<?php
	namespace App\Controllers;

	use PDO;
	use PDOException;

	/**
	 * 
	 */
	class TodayExchangeControllers extends Controllers
	{
		function saveTodayExchange($req, $res, $args) {
			$response = array(
				"message" => "success",
				"status" => 200
			);
			$data = $req->getParams();
			$exchangeDate = $data['exchange_date'];
			unset($data['exchange_date']);
			$query = "DELETE FROM exchange_daily_rate WHERE exchange_date = '$exchangeDate'";
			$result = $this->ci->db->query($query);
			$query = "";
			foreach ($data as $key => $bankObj) {
				$bankId = $bankObj['bankId'];
				unset($bankObj['bankId']);
				foreach ($bankObj as $key => $exchangeObj) {
					$query = "SELECT * FROM exchange_time where bank_id = $bankId";
					$result = $this->ci->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
					$exchangeTime = "";
					if(count($result) > 0) {
						$exchangeTime = $result[0]['exchange_time'];
					}
					$query = 'INSERT INTO exchange_daily_rate(bank_id,exchange_currency,exchange_buy,exchange_sell,bank_update_time,exchange_date) VALUES (' . $bankId . ',"' . $exchangeObj['exchange_currency'] . '","' . $exchangeObj['exchange_currency_buy'] . '","' . $exchangeObj['exchange_currency_sell'] . '","' . $exchangeTime . '","' . $exchangeDate . '")';
					$result = $this->ci->db->query($query);
				}
			}
			return $res->withStatus($response['status'])->withJson(
				$response
			);
		}
	}
?>