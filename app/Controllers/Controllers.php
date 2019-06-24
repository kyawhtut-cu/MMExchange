<?php
	
	namespace App\Controllers;
	include_once('simplehtmldom/simple_html_dom.php');

	use Interop\Container\ContainerInterface;

	abstract class Controllers {

		protected $ci;
		
		protected $kbz_bank = "kbz_bank";
		protected $aya_bank = "aya_bank";
		protected $cb_bank = "cb_bank";
		protected $mob_bank = "mob_bank";
		protected $agd_bank = "agd_bank";
		protected $uab_bank = "uab_bank";

		protected $bank_id = array(
			"kbz_bank" => 1,
			"aya_bank" => 2,
			"cb_bank" => 3,
			"mob_bank" => 4,
			"agd_bank" => 5,
			"uab_bank" => 6
		);

		public function __construct(ContainerInterface $ci) {
			$this->ci = $ci;
		}
	}
?>