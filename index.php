<?php
	require 'vendor/autoload.php';

	use Slim\Http\Request;
	use Slim\Http\Response;
	use App\Controllers\ExchangeRequestControllers;
	use App\Controllers\ApiControllers;
	use App\Controllers\HomeControllers;
	
	    $request_application = $_SERVER['HTTP_USER_AGENT'];
	    $request_client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    $request_client_host_ip = $_SERVER['REMOTE_ADDR'];

	$app = new Slim\App([
		"settings" => [
			"displayErrorDetails" => true,
		]
	]);

	$container = $app->getContainer();

	$container['logger'] = function($container) {
		$logger = new \Monolog\Logger(" mm exchange ");
		$file_handler = new \Monolog\Handler\StreamHandler(__DIR__ . '/logs/' . date("Y_m_d") . '/' . 'error.log');
		$logger->pushHandler($file_handler);
		return $logger;
	};

	$container['phtml'] = new \Slim\Views\PhpRenderer(__DIR__ . '/resources/views');

	$container['notFoundHandler'] = function ($container) {
		return function ($req, $res) use($container) {
		    $request_info = array(
    	        "Request application " => $req->getServerParam('HTTP_USER_AGENT'),
    	        "Request client ip " => $req->getServerParam('HTTP_X_FORWARDED_FOR'),
    	        "Request client host ip " => $req->getServerParam('REMOTE_ADDR')
    	    );
			$data = [
				"path" => $req->getServerParams()['REQUEST_URI']
			];
			$container->logger->addError(
				"Unknow url " . $req->getServerParams()['REQUEST_URI'],
				$request_info
			);
			$container->phtml->render($res, '/errors/404.phtml',$data);
			return $res->withStatus(404);
		};
	};

	$container['db'] = function () {
		$db = new PDO('mysql:host=localhost;dbname=kyawhtut_mmcurrency;charset=utf8', 'kyawhtut_alldb', 'kyawhtuk@alldb');
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $db;
	};

	$app->get('/api', HomeControllers::class . ':api');
	$app->get('/', HomeControllers::class . ':home');

	$app->get('/exchange', ExchangeRequestControllers::class . ':index');

	$app->get('/agd-exchange', ExchangeRequestControllers::class . ':agd');

	$app->post('/agd-date', ExchangeRequestControllers::class . ':agd_date');
	$app->post('/agd-data', ExchangeRequestControllers::class . ':agd_data');

	$app->group('/api/v1', function() {
		$this->get('/kbz_bank', ApiControllers::class . ':kbz_bank');
		$this->get('/aya_bank', ApiControllers::class . ':aya_bank');
		$this->get('/cb_bank', ApiControllers::class . ':cb_bank');
		$this->get('/mob_bank', ApiControllers::class . ':mob_bank');
		$this->get('/agd_bank', ApiControllers::class . ':agd_bank');
		$this->get('/uab_bank', ApiControllers::class . ':uab_bank');
	});

	$app->run();
?>