<?php
	namespace App\Controllers;

	use PDO;
	use PDOException;

	class RestApiControllers extends Controllers {

		function getData($req, $res, $args) {
			$tableName = $args['tableName'];
			if($tableName == "exchange_daily_rate" && $req->getParams()['api_key'] != "~reo?~Lpz!~") {

				return $res->withStatus(401)->withJson([
					"status" => 401,
					"message" => "You have no permission for this api. Please contact to developer.kyawhtut@gmail.com"
				]);
			}
			$param = $req->getParams();
			return $res->withStatus(200)->withJson([
					$tableName => $this->getTableData($req->getParams(), $args)
				]);
		}

		function getTableData($param, $args) {
			$tableName = $args['tableName'];
			$w = "";
			$order = "";
			$page = "";
			$columns = "*";
			$join = "";
			if(isset($args['id'])) {
				$id = explode(',', $args['id']);
				$w = " where id in ('" . $args['id'] . "')";
			}
			if(isset($param['filter'])) {
				$w = " where " . $this->getFilter($param['filter']);
			}
			if(isset($param['columns'])) {
				$columns = $param['columns'];
			}
			if(isset($param['order'])) {
				$order = $this->getOrder($param['order']);
			}
			if(isset($param['page'])) {
				$page = $this->getPage($param['page']);
			}
			if(isset($param['join'])) {
				$join = $this->getJoin($param['join']);
			}
			$query = "select $columns from `$tableName`" . $join . $w . $order . $page;
			$result = $this->ci->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
			return $result;
		}

		function getPage($params) {
			$page = explode(',', $params);
			return " limit " . $page[1] . ' offset ' . $page[1] * $page[0];
		}

		function getOrder($params) {
			$order = explode(',', $params);
			return " order by " . $order[0] . ' ' . $order[1];
		}

		function getJoin($params) {
			$join = explode('&', $params);
			$joinQuery = "";
			foreach ($join as $key => $joinValue) {
				$table = explode(',', $joinValue);
				
				$joinQuery .= ' ' . $table[0] . ' join ' . $table[1] . ' on ' . $table[2];
				
				if($table[3] == "eq") {
					$joinQuery .= " = ";
				} else if($table[3] == 'neq') {
					$joinQuery .= " <> ";
				} else if($table[3] == 'in') {
					$joinQuery .= ' in (';
				} else if($table[3] == 'nin') {
					$joinQuery .= ' not in (';
				}

				$joinQuery .= $table[4];

				if(($key+1) != count($join)) {
					$joinQuery .= " ";
				}
			}
			return $joinQuery;
		}

		function getFilter($params) {
			$operator = '-1';
			$filter = explode(',or,', $params);
			if(count($filter) == 1) {
				$filter = explode(',and,', $params);
				if(count($filter) >= 2) {
					$operator = ' and ';
				}
			} else if(count($filter) >= 2) {
				$operator = ' or ';
			}
			$w = "";
			for($index = 0; $index < count($filter); $index++) {
				$tmp = explode(',', $filter[$index]);
				if(strpos($filter[$index], '[') !== false) {
					$tmp = explode(',[', $filter[$index]);
					$array = explode(',', $tmp[0]);
					$tmp = array(
						$array[0],
						$array[1],
						str_replace(']', ')', $tmp[1])
					);
				}
				$w .= "$tmp[0]";
				if($tmp[1] == "eq") {
					$w .= " = ";
				} else if($tmp[1] == 'neq') {
					$w .= " <> ";
				} else if($tmp[1] == 'in') {
					$w .= ' in (';
				} else if($tmp[1] == 'nin') {
					$w .= ' not in (';
				}
				$w .= "'$tmp[2]'";
				if(($index+1) != count($filter)) {
					$w .= " $operator ";
				}
			}
			return $w;
		}
	}
?>