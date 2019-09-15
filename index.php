<?php
	require 'vendor/autoload.php';

	use Slim\Http\Request;
	use Slim\Http\Response;
	use App\Controllers\ExchangeRequestControllers;
	use App\Controllers\ApiControllers;
	use App\Controllers\HomeControllers;
	use App\Controllers\RestApiControllers;
	use App\Controllers\PhotoUploadControllers;
	use App\Controllers\SaveBankControllers;
	use App\Controllers\TodayExchangeControllers;
	use App\Controllers\ConverterController;
	
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

	$container['phtml'] = new \Slim\Views\PhpRenderer(__DIR__ . '/resources/views',[
		'baseUrl' => "/mmexchange"
	]);

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
		/*Local*/
		$db = new PDO('mysql:host=localhost;dbname=kyawhtut_exchange;charset=utf8', 'root', 'root');
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		return $db;
	};

	$app->group('/convert', function() {
		$this->get('', ConverterController::class . ':convert');
		$this->get('/exchangeCurrency', ConverterController::class . ':getExchangeCurrency');
		$this->get('/convert', ConverterController::class . ':convertAmount');
	});

	$app->get('/api', HomeControllers::class . ':api');
	$app->group('/', function() {
		$this->get('', HomeControllers::class . ':home');
		$this->get('daily-exchange', HomeControllers::class . ':dailyExchange');
		$this->get('daily-exchange/add', HomeControllers::class . ':dailyExchangeAdd');
		$this->get('branch-input', HomeControllers::class . ':branchInput');
		$this->get('exchange', ExchangeRequestControllers::class . ':index');
		$this->get('exchange-kbz', ExchangeRequestControllers::class . ':kbz');
		$this->get('exchange-daily/kbz', ExchangeRequestControllers::class . ':kbz');
		$this->get('exchange-daily/aya', ExchangeRequestControllers::class . ':aya');
		$this->get('exchange-daily/cb', ExchangeRequestControllers::class . ':cb');
		$this->get('exchange-daily/mob', ExchangeRequestControllers::class . ':mob');
		$this->get('exchange-daily/agd', ExchangeRequestControllers::class . ':agd');
		$this->get('exchange-daily/uab', ExchangeRequestControllers::class . ':uab');
		$this->get('{date}', HomeControllers::class . ':home');
	});

	$app->group('/branch-input', function() {
		$this->get('/add', HomeControllers::class . ':editBranch');
		$this->get('/edit/{id}', HomeControllers::class . ':editBranch');
	});

	$app->group('/api/v1', function() {
		$this->get('/kbz_bank', ApiControllers::class . ':kbz_bank');
		$this->get('/aya_bank', ApiControllers::class . ':aya_bank');
		$this->get('/cb_bank', ApiControllers::class . ':cb_bank');
		$this->get('/mob_bank', ApiControllers::class . ':mob_bank');
		$this->get('/agd_bank', ApiControllers::class . ':agd_bank');
		$this->get('/uab_bank', ApiControllers::class . ':uab_bank');
	});

	$app->group('/api/v2', function() {
		$this->get('/exchange', ConverterController::class . ':exchange');
		$this->get('/{tableName}', RestApiControllers::class . ':getData');
		$this->get('/{tableName}/{id}', RestApiControllers::class . ':getData');
	});
	$app->group('/api', function() {
		$this->post('/saveBank', SaveBankControllers::class . ':saveBank');
		$this->delete('/deleteBank', SaveBankControllers::class . ':deleteBank');
		$this->post('/upload', PhotoUploadControllers::class . ':upload');
		$this->post('/delete', PhotoUploadControllers::class . ':delete');
		$this->post('/saveTodayExchange', TodayExchangeControllers::class . ':saveTodayExchange');
	});

	$app->run();
?>