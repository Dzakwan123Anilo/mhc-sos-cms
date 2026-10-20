<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('phpmailer5/class.phpmailer.php');

class MyPHPMailer extends PHPMailer {

	function __construct() {
		parent::__construct();
	}
}

/* End of file myphpmailer.php */