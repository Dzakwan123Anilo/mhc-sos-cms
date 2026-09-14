<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once __DIR__.'/phpmailer/src/Exception.php';
require_once __DIR__.'/phpmailer/src/SMTP.php';
require_once __DIR__.'/phpmailer/src/PHPMailer.php';

class MyPHPMailer extends \PHPMailer\PHPMailer\PHPMailer {
	public function __construct($exceptions = true) {
		parent::__construct($exceptions);
	}
}

/* End of file myphpmailer.php */
