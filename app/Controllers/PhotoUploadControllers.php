<?php
	namespace App\Controllers;

	use PDO;
	use PDOException;

	class PhotoUploadControllers extends Controllers {

		function upload($req, $res, $args) {
			$data = array(
				"message" => "",
				"files" => array()
			);
			if(!empty($_FILES["file"])){
				$name = $_FILES["file"]['name'];
				$fileInfo = pathinfo($name);
				$ext = $fileInfo['extension'];
				session_start();
				if(($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png')) {
				    $location = './resources/img/bank-image/' . md5($_FILES["file"]["name"]). '.' . $ext;
				    // Upload with original size
				    // move_uploaded_file($_FILES["file"]["tmp_name"], $location);
				    // Compress image
				    $image = $this->compressImage($_FILES["file"]["tmp_name"], $location, 80);
				    move_uploaded_file($image, $location);
				    $data["message"] = "success";
				    $data["files"][0] = $location; 
				} else {
				    $data["message"] = empty($_FILES["file"]);
				}
			}else{
				$data["message"] = empty($_FILES["file"]);
			}
			return $res->withStatus(200)->withJson($data);
		}

		function delete($req, $res, $args) {
			$data = array(
				"message" => "Image Can not Delete"
			);
			if(!empty($req->getParams()['file'])){
				$data = $this->deleteFile($req->getParams()['file']);
			}
			return $res->withStatus(200)->withJson($data);
		}

		function deleteFile($url) {
			$data = array(
				"message" => ""
			);
			if(file_exists($url)) {
				unlink($url);
				$data["message"] = "success";
			} else {
				$data["message"] = "Image not Found";	
			}
			return $data;
		}

		private function compressImage($sourceUrl, $detination, $quality) {
			$info = getimagesize($sourceUrl);
			if ($info['mime'] == 'image/jpeg')
				$image = imagecreatefromjpeg($sourceUrl);
			else if ($info['mime'] == 'image/gif')
				$image = imagecreatefromgif($sourceUrl);
			else if ($info['mime'] == 'image/png')
				$image = imagecreatefrompng($sourceUrl);
			return imagejpeg($image, $detination, $quality);;
		}
	}
?>