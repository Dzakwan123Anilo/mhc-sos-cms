<?php
include_once(APPPATH."libraries/FrontController.php");
class Auth extends FrontController {

	function __construct()
	{
		parent::__construct();
		$this->jCfg['theme'] = 'admin/'.cfg('template_admin');
	}

	function index()
	{
		if(isset($this->jCfg['is_login']) && $this->jCfg['is_login']==1){
			redirect(site_url("meme/me"));
		}

		$this->_prepareLoginChallenge();

		$data = array(
			'message'		=> '',
			'login_token'	=> $this->jCfg['login_token']
		);

		//debugCode($this->site_info);
		$this->_v('login',$data);
	}

	function act_auth(){
		if(!isset($_POST['login'])){
			exit;
		}

		$client_ip = $this->input->ip_address();
		$u = trim($this->input->post('username'));
		$p = trim($this->input->post('password'));
		$posted_token = (string)$this->input->post('login_token');

		if( $u == '' || $p == '' ){
			$this->_auth_fail('Please input your username and password', true);
		}

		$this->_cleanupThrottle();

		/* CSRF scoped token: valid only for this session */
		$sess_token = isset($this->jCfg['login_token']) ? (string)$this->jCfg['login_token'] : '';
		if( $sess_token == '' || $posted_token == '' || !hash_equals($sess_token, $posted_token) ){
			$this->_auth_fail('Sesi login tidak valid. Muat ulang halaman.', true);
		}

		/* brute-force throttle: per-IP (10/15 menit) */
		if( $this->_isBlocked('ip', $this->_throttleKey('ip', $client_ip)) ){
			$this->_auth_fail('Terlalu banyak percobaan login. Silakan coba lagi dalam 15 menit.', false);
		}

		/* brute-force throttle: per-username (5/15 menit) */
		$username_key = $this->_throttleKey('username', $u);
		if( $this->_isBlocked('username', $username_key) ){
			$this->_auth_fail('Terlalu banyak percobaan login untuk username ini. Silakan coba lagi dalam 15 menit.', false);
		}

		/* captcha: one-time, server-side session state */
		$captcha_input = strtoupper(trim((string)$this->input->post('captcha')));
		$captcha_code  = isset($this->jCfg['captcha']['code']) ? (string)$this->jCfg['captcha']['code'] : '';
		$this->jCfg['captcha'] = array(); /* consume sekali pakai, apapun hasilnya */
		$this->sCfg['captcha'] = array();
		if( $captcha_code == '' || $captcha_input == '' || !hash_equals($captcha_code, $captcha_input) ){
			$this->_auth_fail('Captcha salah.', true);
		}
		$this->_releaseSession();

		$d = $this->db->get_where("app_user",array(
				"user_name"		=> $u,
				"user_status"	=> 1,
				"is_trash"		=> 0
			))->row();

		$password_valid = false;
		if($d){
			if(password_verify($p, $d->user_password)){
				$password_valid = true;
			}elseif($d->user_password === md5($p)){
				$password_valid = true;
				$this->db->update("app_user",array(
					'user_password' => password_hash($p, PASSWORD_ARGON2ID)
				),array(
					'user_id' => $d->user_id
				));
			}
		}

		if($d && $password_valid){
			/* reset throttle counter untuk ip dan username ini */
			$this->db->delete("app_login_throttle",array(
				"scope_type" => "ip",
				"scope_key"  => $this->_throttleKey('ip', $client_ip)
			));
			$this->db->delete("app_login_throttle",array(
				"scope_type" => "username",
				"scope_key"  => $username_key
			));

			/*set session*/

			$group = $this->db->get_where("app_user_group",array(
					"ug_user_id" => $d->user_id
				))->result();
				
			$arr_group = array();
			foreach ((array)$group as $p => $q) {
				$arr_group[] = $q->ug_group_id;
			}

			// site ..
			$site = $this->db->get_where("app_site",array(
					"site_id" => $d->user_site_id
				))->row();

			$this->sCfg['is_login'] 		= 1;
			$this->sCfg['user']['id'] 		= $d->user_id;
			$this->sCfg['user']['site'] 	= array(
										"id"		=> $site->site_id,
										"name"		=> $site->site_name,
										"type"  	=> $site->site_type,
										"parent"	=> $site->site_parent
									);
			$this->sCfg['user']['name']		= $d->user_name;
			$this->sCfg['user']['image']	= get_image(base_url()."assets/collections/user/".$d->user_photo);
			$this->sCfg['user']['fullname'] = $d->user_fullname;
			$this->sCfg['user']['is_all']	= $d->is_show_all;	
			$this->sCfg['user']['bg']		= $d->user_background;
			$this->sCfg['user']['color']	= $d->user_themes;
			$this->sCfg['user']['providerid']	= $d->user_providerid;
			
			/*start session provider group*/
			$pgroup = $this->db->get_where("sos_tref_member_provider_group",array(
					"providerId" => $d->user_providerid
				))->result();
			$arr_groupid = array();
			foreach ((array)$pgroup as $p => $q) {
				$arr_groupid[] = $q->providergroupId;
			}
			
			$provider_tmp = $this->db->get_where("sos_provider",array(
					"provider_id"	=> $d->user_providerid
			))->row();
			$this->sCfg['user']['providergroupid']	= $arr_groupid;
			
			if( isset($provider_tmp->provider_clientid) ){
				$this->sCfg['user']['client']	= $provider_tmp->provider_clientid;
			}
			/*end session provider group*/

			$image_logo = get_new_image(array(
                              "url"     => cfg('upload_path_photo')."/".$this->site_info['site']->site_logo,
                              "folder"  => "site"
                            ),true);

			$this->sCfg['logo'] = $image_logo;
			$this->sCfg['user']['role'] 	= $arr_group;

			/* new session ID: hilangkan potensi session fixation */
			$this->session->sess_regenerate(TRUE);
			$this->_releaseSession();

			$this->db->update("app_user",array(
				'user_logindate' => date("Y-m-d H:i:s")
			),array(
				'user_id' => $d->user_id
			));
			
			$go_to = '/meme/me/welcome';

			$status = array(
				"status"	=> 1,
				"data"		=> array(
						"go_to"	=> $go_to
					),
				"message"	=> 'Login Success, Please Wait..'
			);
			die(json_encode($status));

		}else{
			/* catat gagal: per-IP dan per-username */
			$this->_recordFailure('ip', $this->_throttleKey('ip', $client_ip), 10);
			$this->_recordFailure('username', $username_key, 5);
			$this->_auth_fail('Please check Username and password...', true);
		}
	}

	function captcha()
	{
		$code = isset($this->jCfg['captcha']['code']) ? (string)$this->jCfg['captcha']['code'] : '';
		if( $code == '' ){
			$this->_prepareLoginChallenge(true);
			$code = (string)$this->jCfg['captcha']['code'];
		}

		$path_bg   = FCPATH."assets/captcha/captchabg.jpg";
		$path_font = FCPATH."assets/captcha/roboto-condensed-v16-latin-700.ttf";

		if( !is_file($path_bg) || !is_file($path_font) || !function_exists('imagecreatefromjpeg') ){
			header($_SERVER['SERVER_PROTOCOL'].' 500 Internal Server Error', true, 500);
			exit;
		}

		$im = imagecreatefromjpeg($path_bg);
		$text_color = imagecolorallocate($im, 77, 90, 112);
		$font = $path_font;

		$len = strlen($code);
		$x = 12;
		for ($i = 0; $i < $len; $i++) {
			$angle = random_int(-10, 10);
			imagettftext($im, 18, $angle, $x, 32, $text_color, $font, $code[$i]);
			$x += random_int(12, 16);
		}

		header('Content-type: image/jpeg');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Pragma: no-cache');
		imagejpeg($im, null, 90);
		imagedestroy($im);
		exit;
	}

	function refresh()
	{
		if( !$this->input->is_ajax_request() ){
			redirect(site_url());
			exit;
		}

		$this->_prepareLoginChallenge(true);

		header('Content-Type: application/json');
		echo json_encode(array(
			"status"	=> 1,
			"data"		=> array(
				"login_token"	=> $this->jCfg['login_token'],
				"captcha_url"	=> "/auth/captcha?t=".time()
			)
		));
		exit;
	}

	function out(){
		$this->session->sess_destroy();
		redirect(site_url());
	}

	function _auth_fail($message, $renew_challenge=false)
	{
		if($renew_challenge){
			$this->_prepareLoginChallenge(true);
		}

		$status = array(
			"status"	=> 0,
			"data"		=> array(
				"login_token"	=> $this->jCfg['login_token'],
				"captcha_url"	=> $renew_challenge ? "/auth/captcha?t=".time() : ''
			),
			"message"	=> $message
		);
		die(json_encode($status));
	}

	function _prepareLoginChallenge($new_captcha=true)
	{
		if( empty($this->jCfg['login_token']) ){
			$this->jCfg['login_token'] = bin2hex(random_bytes(32));
		}
		if($new_captcha){
			$chars = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';
			$code = '';
			for($i=0; $i<5; $i++){
				$code .= $chars[random_int(0, strlen($chars)-1)];
			}
			$this->jCfg['captcha'] = array('code' => $code);
		}
		/* _releaseSession hanya merge sCfg — mirror challenge ke sCfg agar persist */
		$this->sCfg['login_token']	= $this->jCfg['login_token'];
		$this->sCfg['captcha']		= $this->jCfg['captcha'];
		$this->_releaseSession();
	}

	function _throttleKey($scope_type, $raw)
	{
		return hash('sha256', $scope_type."|".strtolower(trim($raw)));
	}

	function _isBlocked($scope_type, $scope_key)
	{
		$row = $this->db->select("blocked_until")
			->from("app_login_throttle")
			->where("scope_type", $scope_type)
			->where("scope_key", $scope_key)
			->get()->row();

		if( $row && !empty($row->blocked_until) && $row->blocked_until > date("Y-m-d H:i:s") ){
			return true;
		}
		return false;
	}

	function _recordFailure($scope_type, $scope_key, $max)
	{
		$now = date("Y-m-d H:i:s");
		$window_start = date("Y-m-d H:i:s", strtotime("-15 minutes"));

		$row = $this->db->select("failure_count, window_started_at")
			->from("app_login_throttle")
			->where("scope_type", $scope_type)
			->where("scope_key", $scope_key)
			->get()->row();

		if( $row && $row->window_started_at >= $window_start ){
			$count = (int)$row->failure_count + 1;
			$this->db->where("scope_type", $scope_type)
				->where("scope_key", $scope_key)
				->update("app_login_throttle", array(
					"failure_count"		=> $count,
					"last_attempt_at"	=> $now
				));
		}elseif( $row ){
			/* window 15 menit sudah lewat: reset counter */
			$count = 1;
			$this->db->where("scope_type", $scope_type)
				->where("scope_key", $scope_key)
				->update("app_login_throttle", array(
					"failure_count"		=> $count,
					"window_started_at"	=> $now,
					"last_attempt_at"	=> $now,
					"blocked_until"		=> NULL
				));
		}else{
			$count = 1;
			$this->db->insert("app_login_throttle", array(
				"scope_type"		=> $scope_type,
				"scope_key"			=> $scope_key,
				"failure_count"		=> $count,
				"window_started_at"	=> $now,
				"last_attempt_at"	=> $now
			));
		}

		if( $count >= $max ){
			$this->db->where("scope_type", $scope_type)
				->where("scope_key", $scope_key)
				->update("app_login_throttle", array(
					"blocked_until"	=> date("Y-m-d H:i:s", strtotime("+15 minutes"))
				));
		}
	}

	/* housekeeping: baris throttle lama (>1 hari) */
	function _cleanupThrottle()
	{
		$cutoff = date("Y-m-d H:i:s", strtotime("-1 day"));
		$this->db->query(
			"DELETE FROM app_login_throttle WHERE last_attempt_at < ? AND (blocked_until IS NULL OR blocked_until < ?)",
			array($cutoff, $cutoff)
		);
	}

}

/* End of file Auth.php */
/* Location: ./modules/auth/controllers/Auth.php */
