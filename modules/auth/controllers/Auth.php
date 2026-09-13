<?php
include_once(APPPATH."libraries/FrontController.php");
class Auth extends FrontController {
	private function json_response($status, $message, $data = array(), $http_status = 200){
		http_response_code($http_status);
		header('Content-Type: application/json; charset=UTF-8');
		header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
		die(json_encode(array(
			'status' => $status,
			'data' => $data,
			'message' => $message
		)));
	}

	private function captcha_url(){
		return site_url('auth/captcha').'?v='.rawurlencode(bin2hex(random_bytes(8)));
	}

	private function normalize_username($username){
		return trim((string)$username);
	}

	private function login_attempts($username, $client_ip){
		$minutes = max(1, (int)cfg('login_lockout_minutes'));
		$window = date('Y-m-d H:i:s', strtotime('-'.$minutes.' minutes'));

		$by_ip = $this->db
			->where('log_ip', $client_ip)
			->where('log_class', 'auth')
			->where('log_function', 'act_auth_failed')
			->where('log_date >=', $window)
			->count_all_results('app_log');

		$by_account = 0;
		if($username !== ''){
			$by_account = $this->db
				->where('log_user_name', $username)
				->where('log_class', 'auth')
				->where('log_function', 'act_auth_failed')
				->where('log_date >=', $window)
				->like('log_var_post', '"reason":"invalid_credentials"')
				->count_all_results('app_log');
		}

		return array('ip' => $by_ip, 'account' => $by_account);
	}

	private function is_login_locked($username, $client_ip){
		$attempts = $this->login_attempts($username, $client_ip);
		$account_limit = max(1, (int)cfg('login_max_attempts_account'));
		$ip_limit = max($account_limit, (int)cfg('login_max_attempts_ip'));

		return $attempts['account'] >= $account_limit || $attempts['ip'] >= $ip_limit;
	}

	private function record_login_failure($username, $client_ip, $reason){
		$this->db->insert('app_log', array(
			'log_date' => date('Y-m-d H:i:s'),
			'log_class' => 'auth',
			'log_function' => 'act_auth_failed',
			'log_url' => substr((string)(($_SERVER['HTTP_HOST'] ?? '').($_SERVER['REQUEST_URI'] ?? '')), 0, 255),
			'log_user_id' => 0,
			'log_ip' => $client_ip,
			'log_role' => '',
			'log_user_agent' => substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
			'log_user_name' => substr($username, 0, 100),
			'log_var_get' => '',
			'log_var_post' => json_encode(array(
				'username' => substr($username, 0, 100),
				'reason' => $reason
			)),
			'app_id' => (int)($this->jCfg['app_id'] ?? 1)
		));
	}

	private function valid_captcha($answer){
		$expected_hash = (string)$this->session->userdata('login_captcha_hash');
		$expires = (int)$this->session->userdata('login_captcha_expires');
		$this->session->unset_userdata('login_captcha_hash');
		$this->session->unset_userdata('login_captcha_expires');

		if($expected_hash === '' || $expires < time()){
			return false;
		}

		$key = (string)cfg('encryption_key');
		$actual_hash = hash_hmac('sha256', strtolower(trim((string)$answer)), $key);
		return hash_equals($expected_hash, $actual_hash);
	}

	function captcha(){
		$alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
		$code = '';
		for($i = 0; $i < 6; $i++){
			$code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
		}

		$key = (string)cfg('encryption_key');
		$ttl = max(60, (int)cfg('login_captcha_ttl'));
		$this->session->set_userdata(array(
			'login_captcha_hash' => hash_hmac('sha256', strtolower($code), $key),
			'login_captcha_expires' => time() + $ttl
		));

		if(!function_exists('imagecreatetruecolor')){
			show_error('Captcha image support is unavailable.', 503);
			return;
		}

		$image = imagecreatetruecolor(180, 56);
		$background = imagecolorallocate($image, 245, 247, 250);
		$text_color = imagecolorallocate($image, 32, 48, 64);
		imagefilledrectangle($image, 0, 0, 180, 56, $background);

		for($i = 0; $i < 10; $i++){
			$noise_color = imagecolorallocate(
				$image,
				random_int(120, 210),
				random_int(120, 210),
				random_int(120, 210)
			);
			imageline(
				$image,
				random_int(0, 179),
				random_int(0, 55),
				random_int(0, 179),
				random_int(0, 55),
				$noise_color
			);
		}

		for($i = 0; $i < strlen($code); $i++){
			imagestring($image, 5, 20 + ($i * 24), random_int(18, 25), $code[$i], $text_color);
		}

		if(ob_get_level() > 0){
			ob_clean();
		}
		header('Content-Type: image/png');
		header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
		header('Pragma: no-cache');
		imagepng($image);
		imagedestroy($image);
		exit;
	}

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
		$data = array(
			'message'=>''
		);
		
		//debugCode($this->site_info);
		$this->_v('login',$data);
	}
	
	function act_auth(){
		if(isset($_POST['login'])){
			$client_ip = $this->input->ip_address();
			$u = $this->normalize_username($this->input->post('username', false));
			$p = (string)$this->input->post('password', false);

			if($this->is_login_locked($u, $client_ip)){
				$minutes = max(1, (int)cfg('login_lockout_minutes'));
				$this->json_response(0, 'Terlalu banyak percobaan login. Silakan coba lagi dalam '.$minutes.' menit.', array(
					'captcha_url' => $this->captcha_url()
				), 429);
			}

			if(!$this->valid_captcha($this->input->post('captcha', false))){
				$this->record_login_failure($u, $client_ip, 'invalid_captcha');
				$this->json_response(0, 'Kode verifikasi tidak valid atau sudah kedaluwarsa.', array(
					'captcha_url' => $this->captcha_url()
				), 422);
			}

			if($u === '' || $p === ''){
				$this->record_login_failure($u, $client_ip, 'missing_credentials');
				$this->json_response(0, 'Please input your username and password', array(
					'captcha_url' => $this->captcha_url()
				), 422);
			}else{
				$d = $this->db->get_where("app_user",array(
						"user_name"		=> $u,
						"user_status"	=> 1,
						"is_trash"		=> 0
					))->row();

				if($d){
					$password_valid = false;
					$must_change_password = false;
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
					if($password_valid && validate_password_strength($p) !== true){
						$must_change_password = true;
					}
				}

					if($d && $password_valid){
						$this->session->sess_regenerate(TRUE);
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
					$this->sCfg['user']['must_change_password'] = $must_change_password ? 1 : 0;
					
					
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
					$this->_releaseSession();

          // $sessionAAA = $this->session->get_userdata();
          // echo print_r($_SESSION);
          // exit;

					$this->db->update("app_user",array(
						'user_logindate' => date("Y-m-d H:i:s")
					),array(
						'user_id' => $d->user_id
					));
					
					$go_to = $must_change_password
						? site_url('meme/me/change_password')
						: site_url('meme/me/welcome');

						$this->json_response(1, 'Login Success, Please Wait..', array(
							'go_to' => $go_to
						));

					}else{
						$this->record_login_failure($u, $client_ip, 'invalid_credentials');
						$this->json_response(0, 'Please check Username and password...', array(
							'captcha_url' => $this->captcha_url()
						), 401);
					}
				}
			}

		$this->json_response(0, 'Invalid authentication request.', array(
			'captcha_url' => $this->captcha_url()
		), 400);
	}

	function out(){
		$this->session->sess_destroy();
		redirect(site_url());
	}
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
