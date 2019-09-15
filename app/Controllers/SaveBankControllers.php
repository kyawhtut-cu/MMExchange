<?php
	namespace App\Controllers;

	use PDO;
	use PDOException;

	class SaveBankControllers extends Controllers {

		function saveBank($req, $res, $args) {
			$data = $req->getParams();
			$response = array(
				"message" => "Empty data",
				"status" => 204
			);
			if(!empty($data)) {
				$response["message"] = "success";
				$response["status"] = 200;
				$id = $data['id'];
				if($id != 0 && $this->idHasTable("available_bank_location",$id) == 1) {
					$this->update($data, $id);
				} else {
					$this->insert($data);
				}
			}
			return $res->withStatus($response['status'])->withJson(
				$response
			);
		}

		function insert($data) {
			$index = 0;
			unset($data['id']);
			$columns = "";
			$values = "";
			foreach ($data as $key => $value) {
				$columns .= $key;
				$values .= ":" . $key;
				if($index+1 != count($data)) {
					$columns .= ',';
					$values .= ',';
				}
				$index++;
			}
			$query = "insert into available_bank_location($columns) values ($values)";
			$result = $this->ci->db->prepare($query);
			$result->execute($data);
		}

		function update($data, $id) {
			$index = 0;
			$set = "";
			foreach ($data as $key => $value) {
				$set .= $key . "=:" . $key;
				if($index+1 != count($data)) {
					$set .= ',';
				}
				$index++;
			}
			$query = "update available_bank_location set $set where id = $id";
			$result = $this->ci->db->prepare($query);
			$result->execute($data);
		}

		function idHasTable($tableName, $id) {
			$query = "select * from $tableName where id = '$id'";
			if(count($this->ci->db->query($query)->fetchAll(PDO::FETCH_ASSOC)) == 0)
				return 0;
			return 1;
		}

		function deleteBank($req, $res, $args) {
			$response = array(
				"message" => "Empty data",
				"status" => 204
			);
			if(!empty($req->getParams()['id'])) {
				$restapi = new RestApiControllers($this->ci);
				$args['tableName'] = 'available_bank_location';
				$args['id'] = $req->getParams()['id'];
				$branch = $restapi->getTableData($req->getParams(), $args);
				if(!empty($branch)) {
					$response['message'] = "success";
					$response['status'] = 200;
					$photoControllers = new PhotoUploadControllers($this->ci);
					$photoControllers->deleteFile($branch[0]['bank_branch_image']);
					$query = "delete from available_bank_location where id = :id";
					$result = $this->ci->db->prepare($query);
					$result->execute($req->getParams());
				} else {
					$response['message'] = "Invalid Id.";
					$response['status'] = 204;
				}
			}
			return $res->withStatus(200)->withJson(
				$response
			);
		}

	}
?>