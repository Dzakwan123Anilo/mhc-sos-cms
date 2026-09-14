<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once(APPPATH.'libraries/FrontController.php');

class Auth extends FrontController
{
	function __construct()
	{
		parent::__construct();
		$this->jCfg['theme'] = 'admin/'.cfg('template_admin');
	}

	function index()
	{
		if (isset($this->jCfg['is_login']) && $this->jCfg['is_login'] == 1) {
			redirect(site_url('meme/me'));
		}

		$this->_issue_captcha();
		$this->_v('login', array('message' => ''));
	}

	function captcha_image()
	{
		$captcha = $this->session->userdata('auth_captcha');
		$refresh = $this->input->get('refresh') === '1';
		if ($refresh || !is_array($captcha) || empty($captcha['code']) || empty($captcha['expires_at']) || $captcha['expires_at'] < time()) {
			$captcha = $this->_issue_captcha();
		}

		$this->output
			->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0')
			->set_header('Pragma: no-cache')
			->set_content_type('image/png');

		$image = imagecreatetruecolor(180, 54);
		$background = imagecolorallocate($image, 246, 248, 250);
		$foreground = imagecolorallocate($image, 32, 45, 58);
		$noise = imagecolorallocate($image, 166, 177, 188);
		imagefilledrectangle($image, 0, 0, 180, 54, $background);

		for ($i = 0; $i < 8; $i++) {
			imageline($image, random_int(0, 179), random_int(0, 53), random_int(0, 179), random_int(0, 53), $noise);
		}

		foreach (str_split($captcha['code']) as $index => $character) {
			imagestring($image, 5, 18 + ($index * 25), random_int(15, 22), $character, $foreground);
		}

		ob_start();
		imagepng($image);
		imagedestroy($image);
		$this->output->set_output(ob_get_clean());
	}

	function act_auth()
	{
		if (!$this->input->post('login')) {
			show_404();
			return;
		}

		$username = trim((string) $this->input->post('username'));
		$password = (string) $this->input->post('password');
		$captcha = trim((string) $this->input->post('captcha'));
		$client_ip = $this->input->ip_address();

		$retry_after = $this->_rate_limit_retry_after($client_ip, $username);
		if ($retry_after > 0) {
			$this->_issue_captcha();
			$this->_json_response(0, 'Terlalu banyak percobaan login. Silakan coba lagi dalam '.ceil($retry_after / 60).' menit.', array(
				'retry_after' => $retry_after,
				'captcha_url' => $this->_captcha_url()
			));
		}

		if ($username === '' || $password === '' || strlen($username) > 128 || strlen($password) > 128) {
			$this->_login_failure($client_ip, $username, 'Username, password, atau kode verifikasi tidak valid.');
		}

		if (!$this->_verify_captcha($captcha)) {
			$this->_login_failure($client_ip, $username, 'Kode verifikasi tidak valid atau sudah kedaluwarsa.');
		}

		$user = $this->db->get_where('app_user', array(
			'user_name' => $username,
			'user_status' => 1,
			'is_trash' => 0
		))->row();

		$password_valid = false;
		if ($user) {
			if (password_verify($password, $user->user_password)) {
				$password_valid = true;
				if (password_needs_rehash($user->user_password, PASSWORD_ARGON2ID)) {
					$this->db->update('app_user', array(
						'user_password' => password_hash($password, PASSWORD_ARGON2ID)
					), array('user_id' => $user->user_id));
				}
			} elseif (preg_match('/^[a-f0-9]{32}$/i', (string) $user->user_password)
				&& hash_equals(strtolower((string) $user->user_password), md5($password))) {
				$password_valid = true;
				$this->db->update('app_user', array(
					'user_password' => password_hash($password, PASSWORD_ARGON2ID)
				), array('user_id' => $user->user_id));
			}
		} else {
			// Keep the password-verification cost comparable for unknown usernames.
			password_verify(
				$password,
				'$argon2id$v=19$m=65536,t=4,p=1$UlZ4VE93YnFabDVaV2V2ag$ho5XPhyFofcEoS6shMEutknvXoie/DvQxUOezsXrBh4'
			);
		}

		if (!$user || !$password_valid) {
			$this->_login_failure($client_ip, $username, 'Username, password, atau kode verifikasi tidak valid.');
		}

		$this->_rate_limit_clear($client_ip, $username);
		$this->session->unset_userdata('auth_captcha');
		$this->session->sess_regenerate(true);

		$groups = $this->db->get_where('app_user_group', array('ug_user_id' => $user->user_id))->result();
		$roles = array();
		foreach ((array) $groups as $group) {
			$roles[] = $group->ug_group_id;
		}

		$site = $this->db->get_where('app_site', array('site_id' => $user->user_site_id))->row();
		if (!$site) {
			$this->_json_response(0, 'Konfigurasi situs untuk akun ini tidak valid.', array('captcha_url' => $this->_captcha_url()));
		}

		$this->sCfg['is_login'] = 1;
		$this->sCfg['user']['id'] = $user->user_id;
		$this->sCfg['user']['site'] = array(
			'id' => $site->site_id,
			'name' => $site->site_name,
			'type' => $site->site_type,
			'parent' => $site->site_parent
		);
		$this->sCfg['user']['name'] = $user->user_name;
		$this->sCfg['user']['image'] = get_image(base_url().'assets/collections/user/'.$user->user_photo);
		$this->sCfg['user']['fullname'] = $user->user_fullname;
		$this->sCfg['user']['is_all'] = $user->is_show_all;
		$this->sCfg['user']['bg'] = $user->user_background;
		$this->sCfg['user']['color'] = $user->user_themes;
		$this->sCfg['user']['providerid'] = $user->user_providerid;

		$provider_groups = $this->db->get_where('sos_tref_member_provider_group', array(
			'providerId' => $user->user_providerid
		))->result();
		$provider_group_ids = array();
		foreach ((array) $provider_groups as $provider_group) {
			$provider_group_ids[] = $provider_group->providergroupId;
		}
		$this->sCfg['user']['providergroupid'] = $provider_group_ids;

		$provider = $this->db->get_where('sos_provider', array('provider_id' => $user->user_providerid))->row();
		if (isset($provider->provider_clientid)) {
			$this->sCfg['user']['client'] = $provider->provider_clientid;
		}

		$this->sCfg['logo'] = get_new_image(array(
			'url' => cfg('upload_path_photo').'/'.$this->site_info['site']->site_logo,
			'folder' => 'site'
		), true);
		$this->sCfg['user']['role'] = $roles;
		$this->_releaseSession();

		$this->db->update('app_user', array('user_logindate' => date('Y-m-d H:i:s')), array('user_id' => $user->user_id));

		$this->_json_response(1, 'Login Success, Please Wait..', array(
			'go_to' => site_url('meme/me/welcome')
		));
	}

	function out()
	{
		$this->session->sess_destroy();
		redirect(site_url());
	}

	private function _issue_captcha()
	{
		$alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
		$code = '';
		for ($i = 0; $i < 6; $i++) {
			$code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
		}

		$captcha = array(
			'code' => $code,
			'expires_at' => time() + (int) cfg('auth_captcha_ttl')
		);
		$this->session->set_userdata('auth_captcha', $captcha);
		return $captcha;
	}

	private function _verify_captcha($submitted)
	{
		$captcha = $this->session->userdata('auth_captcha');
		$this->session->unset_userdata('auth_captcha');

		return is_array($captcha)
			&& !empty($captcha['code'])
			&& !empty($captcha['expires_at'])
			&& $captcha['expires_at'] >= time()
			&& hash_equals($captcha['code'], strtoupper($submitted));
	}

	private function _captcha_url()
	{
		return site_url('auth/captcha_image').'?refresh=1&t='.rawurlencode((string) microtime(true));
	}

	private function _login_failure($client_ip, $username, $message)
	{
		$this->_rate_limit_record($client_ip, $username);
		$this->_issue_captcha();
		$this->_json_response(0, $message, array('captcha_url' => $this->_captcha_url()));
	}

	private function _rate_limit_retry_after($client_ip, $username)
	{
		$retry_after = $this->_rate_limit_state('ip:'.$client_ip)['retry_after'];
		if ($username !== '') {
			$retry_after = max($retry_after, $this->_rate_limit_state('user:'.strtolower($username))['retry_after']);
		}
		return $retry_after;
	}

	private function _rate_limit_record($client_ip, $username)
	{
		$this->_rate_limit_state('ip:'.$client_ip, 'record');
		if ($username !== '') {
			$this->_rate_limit_state('user:'.strtolower($username), 'record');
		}
	}

	private function _rate_limit_clear($client_ip, $username)
	{
		$this->_rate_limit_state('ip:'.$client_ip, 'clear');
		if ($username !== '') {
			$this->_rate_limit_state('user:'.strtolower($username), 'clear');
		}
	}

	private function _rate_limit_state($subject, $action = 'read')
	{
		$directory = rtrim((string) $this->config->item('sess_save_path'), '/').'/auth-rate-limit';
		if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) {
			log_message('error', 'Unable to create authentication rate-limit directory.');
			return array('retry_after' => (int) cfg('auth_rate_limit_lockout'));
		}

		$file = $directory.'/'.hash('sha256', $subject).'.json';
		$handle = fopen($file, 'c+');
		if ($handle === false || !flock($handle, LOCK_EX)) {
			if (is_resource($handle)) {
				fclose($handle);
			}
			log_message('error', 'Unable to lock authentication rate-limit state.');
			return array('retry_after' => (int) cfg('auth_rate_limit_lockout'));
		}

		$contents = stream_get_contents($handle);
		$state = json_decode($contents ?: '', true);
		$now = time();
		$window = (int) cfg('auth_rate_limit_window');
		$lockout = (int) cfg('auth_rate_limit_lockout');
		$max_attempts = (int) cfg('auth_rate_limit_attempts');

		if (!is_array($state)) {
			$state = array('attempts' => 0, 'window_ends_at' => $now + $window, 'blocked_until' => 0);
		} else {
			$state['attempts'] = isset($state['attempts']) ? max(0, (int) $state['attempts']) : 0;
			$state['window_ends_at'] = isset($state['window_ends_at']) ? (int) $state['window_ends_at'] : 0;
			$state['blocked_until'] = isset($state['blocked_until']) ? (int) $state['blocked_until'] : 0;

			// An active lockout must survive the expiry of the counting window.
			if ($state['window_ends_at'] <= $now && $state['blocked_until'] <= $now) {
				$state = array('attempts' => 0, 'window_ends_at' => $now + $window, 'blocked_until' => 0);
			}
		}

		if ($action === 'clear') {
			$state = array('attempts' => 0, 'window_ends_at' => $now + $window, 'blocked_until' => 0);
		} elseif ($action === 'record' && $state['blocked_until'] <= $now) {
			$state['attempts']++;
			if ($state['attempts'] >= $max_attempts) {
				$state['blocked_until'] = $now + $lockout;
			}
		}

		$retry_after = max(0, (int) $state['blocked_until'] - $now);
		rewind($handle);
		ftruncate($handle, 0);
		fwrite($handle, json_encode($state));
		fflush($handle);
		flock($handle, LOCK_UN);
		fclose($handle);
		@chmod($file, 0600);

		return array('retry_after' => $retry_after);
	}

	private function _json_response($status, $message, $data = array())
	{
		$this->output
			->set_header('Cache-Control: no-store')
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode(array(
				'status' => $status,
				'data' => $data,
				'message' => $message
			)));
		$this->output->_display();
		exit;
	}
}
