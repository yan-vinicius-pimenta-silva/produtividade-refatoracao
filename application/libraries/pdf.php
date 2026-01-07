<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Pdf {

	public function __construct()
	{

		$CI = & get_instance();
		log_message('Debug', 'mPDF class is loaded.');

	}

	function load()
	{
		require_once './application/third_party/autoload.php';
		$mpdf = new \Mpdf\Mpdf();
		return new $mpdf(['mode' => 'utf-8',
		'format' => 'A4',
		'orientation' => 'P',
		'margin_left'=> 15,
		'margin_right'=> 15,
		'margin_bottom'=> 26,
		'margin_header'=> 9,
		'margin_footer'=> 9,
		'default_font' => 'Helvetica',
		'setAutoTopMargin' => 'stretch']);
	}

}