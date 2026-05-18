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
		$data = array(
			'message'=>''
		);
		
		//debugCode($this->site_info);
		$this->_v('login',$data);
	}
	
	function act_auth(){
		if(isset($_POST['login'])){
			$u = $this->input->post('username');
			$p = $this->input->post('password');
			if( trim($u) == '' || trim($p) == '' ){
				$status = array(
						"status"	=> 0,
						"data"		=> array(),
						"message"	=> 'Please input your username and password'
					);
				die(json_encode($status));
			}else{
				$d = $this->db->get_where("app_user",array(
						"user_name"		=> $u,
						"user_status"	=> 1,
						"is_trash"		=> 0
					))->row();

				if($d){
					$password_valid = false;
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
					$this->_releaseSession();

          // $sessionAAA = $this->session->get_userdata();
          // echo print_r($_SESSION);
          // exit;

					$this->db->update("app_user",array(
						'user_logindate' => date("Y-m-d H:i:s")
					),array(
						'user_id' => $d->user_id
					));
					
					$go_to = site_url('meme/me/welcome');				

					$status = array(
						"status"	=> 1,
						"data"		=> array(
								"go_to"	=> $go_to
							),
						"message"	=> 'Login Success, Please Wait..'
					);
					die(json_encode($status));

				}else{
					$status = array(
						"status"	=> 0,
						"data"		=> array(),
						"message"	=> 'Please check Username and password...'
					);
					die(json_encode($status));
				}
			}		
		}	
	}

	function out(){
		$this->session->sess_destroy();
		redirect(site_url());
	}
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */