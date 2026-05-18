<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."libraries/AdminController.php");
class Me extends AdminController {
  var $session;
	var $front_menu = '';
	
	function __construct()
	{
		parent::__construct(); 
		$this->_set_title( 'DASHBOARD' );				
		//$this->output->enable_profiler(TRUE);	
	}
	
	function te(){
		debugCode($this->jCfg['user']);
	}
	
	function update_user(){
		$m = $this->db->query("SELECT * FROM sos_provider ORDER BY provider_id ASC")->result();
		foreach((array)$m as $k=>$v){
			$username = "mhc".$v->provider_code;
			$cek = $this->db->get_where("app_user",array("user_name" => $username))->num_rows();
			if( $cek == 1 ){
				$this->db->update("app_user",array(
					'user_password'	=> password_hash("mhc".$v->provider_code, PASSWORD_ARGON2ID),
					'user_name'		=> $v->provider_code
				),array(
					"user_name" => $username
				));
				echo "Code ".$v->provider_code."<br />";
			}
		}
	}
	function generate_userxxxxx(){
		$m = $this->db->query("SELECT * FROM sos_provider ORDER BY provider_id ASC")->result();
		foreach((array)$m as $k=>$v){
			$username = "mhc".$v->provider_code;
			$password = "123123";
			$cek = $this->db->get_where("app_user",array("user_name" => $username))->num_rows();
			if( $cek == 0 ){
				$data = array(
					'user_fullname'			=> $v->provider_name,
					'user_name'				=> $username,
					'user_email'			=> "mail@mail.com",
					'user_telp'				=> "021-0000000",
					'user_site_id'			=> 1000,
					'user_providerid'		=> $v->provider_id,
					'is_show_all'			=> 0,
				'user_status'			=> 1,
				'user_password'			=> password_hash($password, PASSWORD_ARGON2ID)
				);
				
				$this->db->insert("app_user",$data);
				$user_id = $this->db->insert_id();
				
				$cek1 = $this->db->get_where("app_user_group",array(
						"ug_user_id"	=> $user_id,
						"ug_group_id"	=> 27
					))->num_rows();
					
				if( $cek1 == 0 ){
					$this->db->insert("app_user_group",array(
						"ug_user_id"	=> $user_id,
						"ug_group_id"	=> 27,
						"ug_status"		=> 1
					));
				}
			}
		}
		
	}
	function set_theme(){
		$menu = $this->jCfg['theme_setting']['menu']=="top"?"left":"top";
		$this->sCfg['theme_setting']['menu'] = $menu;
		$this->_releaseSession();
		$go = isset($_GET['next'])?$this->input->get('next'):site_url('meme/me');
		redirect($go);	
	}

	function index_all(){ 
		$this->is_dashboard = false;
		$this->css_file[] = 'daterangepicker/daterangepicker-bs3.css';
		$this->js_file[] = 'page/init.daterangepicker.js';
    $this->own_link = site_url('meme/me');

		$data = array();
		
		$data['guide'] = $this->guide;
		$data['news'] = $this->news;
		$data['activemember'] = $this->activemember;
		$data['swipeclient'] = $this->swipeclient;
		$data['swipeprovider'] = $this->swipeprovider;
		$data['swipemore'] = $this->swipemore;
		$data['hitlogin'] = $this->hitlogin;
		//load js..
		$this->js_plugins = array(
			'plugins/bootstrap/bootstrap-select.js',
			'plugins/bootstrap/bootstrap-datepicker.js',
			'plugins/bootstrap/bootstrap-timepicker.min.js',
			'plugins/moment.min.js',
			'plugins/daterangepicker/daterangepicker.js',
			'plugins/knob/jquery.knob.min.js',
			'plugins/morris/raphael-min.js',
			'plugins/morris/morris.min.js'
		);
		
		if(isset($_POST['btn_show'])){ 			
			$this->DATA->table="sos_member";
			$this->db->select('sos_member.*,sos_client.client_id,sos_client.client_name,sos_ref_plan_type.plan_id,sos_ref_plan_type.plan_description');
			$this->db->join("sos_client","sos_client.client_id = sos_member.member_clientid");
			$this->db->join("sos_ref_plan_type","sos_ref_plan_type.plan_type = sos_member.member_plantype");
			$detail = $this->DATA->data_id(array(
					"member_clientid"	=> $this->input->post('verify_clientid'),
					"member_cardno"		=> $this->input->post('verify_cardno')
				));
				
			$this->data_form = $detail;
			$par_filter = array(
					"sos_member.member_employeeid"	=> $detail->member_employeeid
				);
	
			$this->db->select('sos_member.*,sos_client.client_name,sos_ref_plan_type.plan_description');
			$this->db->join("sos_client","sos_client.client_id = sos_member.member_clientid");
			$this->db->join("sos_ref_plan_type","sos_ref_plan_type.plan_type = sos_member.member_plantype");
			$data['data'] = $this->DATA->_getall($par_filter);
			
			$par_filter = array(
				"memberId" => $detail->member_id
			);
			if( $this->jCfg['user']['is_all'] != 1 ){	
				$par_filter["providerId"] = $this->jCfg['user']['providerid'];			 
			}
			
			$this->DATA->table="sos_ttrans_claim";			
			$this->db->join("sos_client","sos_client.client_id = sos_ttrans_claim.clientid");
			$this->db->join("sos_member","sos_member.member_id = sos_ttrans_claim.memberid");
			$this->db->join("sos_provider","sos_provider.provider_id = sos_ttrans_claim.providerid");
			$this->db->join("sos_ref_service_type","sos_ref_service_type.service_id = sos_ttrans_claim.servicetypeId");
			$this->db->join("sos_ref_plan_type","sos_ref_plan_type.plan_type = sos_member.member_plantype");
			$this->db->order_by("date_claim","DESC");
			$data['history'] =$this->DATA->_getall($par_filter);

		}
		
		$this->_v("index",$data);
	}

	function index(){ 
		$this->is_dashboard = false;
		$this->css_file[] = 'daterangepicker/daterangepicker-bs3.css';
		$this->js_file[] = 'page/init.daterangepicker.js';
    $this->own_link = site_url('meme/me');

		$data = array();
		//load js..
		$this->js_plugins = array(
			'plugins/bootstrap/bootstrap-select.js',
			'plugins/bootstrap/bootstrap-datepicker.js',
			'plugins/bootstrap/bootstrap-timepicker.min.js',
			'plugins/moment.min.js',
			'plugins/daterangepicker/daterangepicker.js'
		);
		$data['card_no'] = '';
		if(isset($_POST['verify_cardno']) && !empty($_POST['verify_cardno'])){ 	
			$cekNumber = check_number($this->input->post('verify_clientid'),$this->input->post('verify_cardno'));
			if( $cekNumber['status'] == true ){
				$this->DATA->table="sos_member";
				$this->db->select('sos_member.*,sos_client.client_id,sos_client.client_name,sos_ref_plan_type.plan_id,sos_ref_plan_type.plan_description');
				$this->db->join("sos_client","sos_client.client_id = sos_member.member_clientid");
				$this->db->join("sos_ref_plan_type","sos_ref_plan_type.plan_type = sos_member.member_plantype");
				$detail = $this->DATA->data_id(array(
						"member_clientid"	=> $this->input->post('verify_clientid'),
						"member_cardno"		=> $this->input->post('verify_cardno')
					));
				$desc = $detail->member_plantype=='PLAN_FE'?'dental_description':'dental_limit';
				$detail->member_dentallimit = ($detail->member_plantype=='PLAN_FE'?'':'Rp. ').get_name('sos_ref_dental_limit',$desc,array('dental_plantypeid' => $detail->plan_id));
					
				$this->data_form = $detail;
				$member_emp_id = isset($detail->member_employeeid)?$detail->member_employeeid:0;
				$par_filter = array(
						"sos_member.member_employeeid"	=> $member_emp_id
					);
		
				$this->db->select('sos_member.*,sos_client.client_name,sos_ref_plan_type.plan_description,sos_ref_dental_limit.dental_limit');
				$this->db->join("sos_client","sos_client.client_id = sos_member.member_clientid");
				$this->db->join("sos_ref_plan_type","sos_ref_plan_type.plan_type = sos_member.member_plantype");
				$this->db->join("sos_ref_dental_limit","sos_ref_dental_limit.dental_plantypeid = sos_ref_plan_type.plan_id");
				$data['data'] = $this->DATA->_getall($par_filter);
				
				$member_id = isset($detail->member_id)?$detail->member_id:'0';
				$par_filter = array(
					"memberId" => $member_id
				);
				if( $this->jCfg['user']['is_all'] != 1 ){	
					$par_filter["providerId"] = $this->jCfg['user']['providerid'];			 
				}
				
				$this->DATA->table="sos_ttrans_claim";
				$this->db->join("sos_client","sos_client.client_id = sos_ttrans_claim.clientid");
				$this->db->join("sos_member","sos_member.member_id = sos_ttrans_claim.memberid");
				$this->db->join("sos_provider","sos_provider.provider_id = sos_ttrans_claim.providerid");
				$this->db->join("sos_ref_service_type","sos_ref_service_type.service_id = sos_ttrans_claim.servicetypeId");
				$this->db->join("sos_ref_plan_type","sos_ref_plan_type.plan_type = sos_member.member_plantype");
				$this->db->order_by("date_claim","DESC");
				$data['history'] =$this->DATA->_getall($par_filter);
			}else{
				$data['card_no'] = $this->input->post('verify_cardno');
				$data['validate'] = $cekNumber;
			}

		}
		
		$this->_v("index",$data);
	}

	function dashboard(){
		$data = array();
		//load js..
		$this->js_file = array(
                'export-data.js?r='.rand(1,1000)
            );
            
		$this->js_plugins = array(
			'plugins/bootstrap/bootstrap-select.js',
			'plugins/bootstrap/bootstrap-datepicker.js',
			'plugins/bootstrap/bootstrap-timepicker.min.js',
			'plugins/moment.min.js',
			'plugins/daterangepicker/daterangepicker.js',
			'plugins/knob/jquery.knob.min.js',
			'plugins/morris/raphael-min.js',
			'plugins/morris/morris.min.js'
		);
		
//		$data['activemember'] = getNumberActiveMember();
//		$data['swipeclient'] = getNumberSwipeByClient();
//		$data['swipeprovider'] = getNumberSwipeByProvider();
//		$data['swipemore'] = getNumberMemberSwipeMoreThanOnce();
//		$data['hitlogin'] = getNumberUserLogin();
		
		$this->_v("dashboard",$data);
	}
	
	function export_data_active_member(){	
		$return = array(
				"status" => 0,
				"message"=> "",
				"data" => array(
						"success" => 0,
						"total"	  => 0,
						"cid"  => $this->input->post('cid'),
						"date"=> $this->input->post('date'),
						"type"=> $this->input->post('type'),
						"offset"  => 0,
						"filename"=> '',
						"url_download" => ''
					)
			);	

		$data = array();	
		$par_filter = array(
				"cid"		=> $this->input->post('cid'),
				"type"		=> $this->input->post('type'),
				"date"		=> $this->input->post('date'),
				"offset"	=> $this->input->post('offset'),
				"limit"		=> $this->input->post('limit')
			);
		
		$data = getExportActiveMember($par_filter);
		$return['data']['total'] = $data['total'];

		//start..
		error_reporting(E_ALL);
//		ini_set('display_errors', TRUE);
//		ini_set('display_startup_errors', TRUE);

		require_once APPPATH.'libraries/PHPExcel.php';
		require_once APPPATH.'libraries/PHPExcel/IOFactory.php';
		
		$start_row = (int)$this->input->post('offset')+2;
		$path_file = './assets/tmp-report/';
		if( $this->input->post('offset') != 0 ){
			$objPHPExcel = PHPExcel_IOFactory::load($path_file.$this->input->post('filename'));
			$file_name = $this->input->post('filename');
		}else{		
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("SOS")
							 ->setLastModifiedBy("SOS")
							 ->setTitle("Office 2007 XLSX Data Member SOS")
							 ->setSubject("Office 2007 XLSX Data Member SOS")
							 ->setDescription("Data Member SOS")
							 ->setKeywords("office 2007 Member SOS")
							 ->setCategory("Data Member SOS");
	
			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', 'No.')
		            ->setCellValue('B1', 'Client')
		            ->setCellValue('C1', 'No Kartu')
		            ->setCellValue('D1', 'Nama Member')
		            ->setCellValue('E1', 'Tipe Relasi')
		            ->setCellValue('F1', 'Jenis Kelamin')
		            ->setCellValue('G1', 'Tgl Lahir')
		            ->setCellValue('H1', 'Location')
		            ->setCellValue('I1', 'Region')
		            ->setCellValue('J1', 'Kelas Perawatan')
		            ->setCellValue('K1', 'Status Karyawan')
		            ->setCellValue('L1', 'Status Kartu')
		            ->setCellValue('M1', 'Dental Limit');
	
		    // Rename worksheet
			$objPHPExcel->getActiveSheet()->setTitle('member');
			$objPHPExcel->setActiveSheetIndex(0);
			$file_name = 'DataActiveMember-'.rand(100,10000)."-".date("dmy-His").'.xlsx';
		}
		$return['data']['filename'] = $file_name;
		$return['data']['offset'] = $this->input->post('offset')+$this->input->post('limit');

		// Miscellaneous glyphs, UTF-8
		$success = 0;
		//debugCode($data);
		foreach ((array)$data['data'] as $k => $v) {
			$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A'.$start_row, $start_row-1)
		            ->setCellValue('B'.$start_row, $v->client_name)
		            ->setCellValue('C'.$start_row, $v->member_cardno)
		            ->setCellValue('D'.$start_row, $v->member_name)
		            ->setCellValue('E'.$start_row, $v->member_relationshiptype)
		            ->setCellValue('F'.$start_row, $v->member_sex=='M'?'Laki-laki':'Perempuan')
		            ->setCellValue('G'.$start_row, myDate($v->member_birthdate,"d M Y",false))
		            ->setCellValue('H'.$start_row, $v->member_region)
		            ->setCellValue('I'.$start_row, $v->member_location)
		            ->setCellValue('J'.$start_row, $v->member_plantype)
		            ->setCellValue('K'.$start_row, $v->member_employmentstatus)
		            ->setCellValue('L'.$start_row, $v->member_statuscard)
		            ->setCellValue('M'.$start_row, $v->member_plantype=='PLAN_FE'?'':get_name('sos_ref_dental_limit','dental_limit',array('dental_plantypeid' => get_name('sos_ref_plan_type','plan_id',array('plan_type' => $v->member_plantype)))));
		    $success++;
		    $start_row++;
		}
		$return['data']['success'] = $success;
		if( count($data['data']) == 0 ){
			$return['data']['url_download'] = base_url()."/assets/tmp-report/".$file_name;
		}

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($path_file.$file_name);

		$return['status'] = 1;
		die(json_encode($return));
	}
	
	function export_data_swipe_by_number(){	
		$return = array(
				"status" => 0,
				"message"=> "",
				"data" => array(
						"success"	=> 0,
						"total"		=> 0,
						"cid"		=> $this->input->post('cid'),
						"date"		=> $this->input->post('date'),
						"today"		=> $this->input->post('today'),
						"type"		=> $this->input->post('type'),
						"offset"	=> 0,
						"filename"	=> '',
						"url_download" => ''
					)
			);	

		$data = array();	
		$par_filter = array(
				"cid"		=> $this->input->post('cid'),
				"date"		=> $this->input->post('date'),
				"today"		=> $this->input->post('today'),
				"type"		=> $this->input->post('type'),
				"offset"	=> $this->input->post('offset'),
				"limit"		=> $this->input->post('limit')
			);
		
		$data = getExportSwipeByClient($par_filter);
		$return['data']['total'] = $data['total'];
    // 

		//start..
		error_reporting(E_ALL);
//		ini_set('display_errors', TRUE);
//		ini_set('display_startup_errors', TRUE);

		require_once APPPATH.'libraries/PHPExcel.php';
		require_once APPPATH.'libraries/PHPExcel/IOFactory.php';
		
		$start_row = (int)$this->input->post('offset')+2;
		$path_file = './assets/tmp-report/';
		if( $this->input->post('offset') != 0 ){
			$objPHPExcel = PHPExcel_IOFactory::load($path_file.$this->input->post('filename'));
			$file_name = $this->input->post('filename');
		}else{
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("SOS")
							 ->setLastModifiedBy("SOS")
							 ->setTitle("Office 2007 XLSX Data Member SOS")
							 ->setSubject("Office 2007 XLSX Data Member SOS")
							 ->setDescription("Data Member SOS")
							 ->setKeywords("office 2007 Member SOS")
							 ->setCategory("Data Member SOS");
	
			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', 'No.')
		            ->setCellValue('B1', 'Client')
		            ->setCellValue('C1', 'Provider')
		            ->setCellValue('D1', 'No Kartu')
		            ->setCellValue('E1', 'Nama Member')
		            ->setCellValue('F1', 'Tipe Layanan')
		            ->setCellValue('G1', 'Tanggal Klaim')
		            ->setCellValue('H1', 'Kelas Kamar')
		            ->setCellValue('I1', 'Status Klaim');
	
		    // Rename worksheet
			$objPHPExcel->getActiveSheet()->setTitle('member');
			$objPHPExcel->setActiveSheetIndex(0);
			$file_name = 'DataTransactionByClient-'.rand(100,10000)."-".date("dmy-His").'.xlsx';
		}
		$return['data']['filename'] = $file_name;
		$return['data']['offset'] = $this->input->post('offset')+$this->input->post('limit');

		// Miscellaneous glyphs, UTF-8
		$success = 0;
		//debugCode($data);
		foreach ((array)$data['data'] as $k => $v) {
			$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A'.$start_row, $start_row-1)
		            ->setCellValue('B'.$start_row, $v->client_name)
		            ->setCellValue('C'.$start_row, $v->provider_name)
		            ->setCellValue('D'.$start_row, $v->member_cardno)
		            ->setCellValue('E'.$start_row, $v->member_name)
		            ->setCellValue('F'.$start_row, $v->service_type)
		            ->setCellValue('G'.$start_row, myDate($v->date_claim,"d M Y",false))
		            ->setCellValue('H'.$start_row, $v->room_class)
		            ->setCellValue('I'.$start_row, $v->claim_status);
		    $success++;
		    $start_row++;
		}
		$return['data']['success'] = $success;
		if( count($data['data']) == 0 ){
			$return['data']['url_download'] = base_url()."/assets/tmp-report/".$file_name;
		}

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($path_file.$file_name);

		$return['status'] = 1;
		die(json_encode($return));
	}
	
	function export_data_swipe_more(){	
		$return = array(
				"status" => 0,
				"message"=> "",
				"data" => array(
						"success" => 0,
						"total"	  => 0,
						"cid"	=> $this->input->post('cid'),
						"date"	=> $this->input->post('date'),
						"today"	=> $this->input->post('today'),
						"type"	=> $this->input->post('type'),
						"offset"  => 0,
						"filename"=> '',
						"url_download" => ''
					)
			);	

		$data = array();	
		$par_filter = array(
				"cid"		=> $this->input->post('cid'),
				"date"		=> $this->input->post('date'),
				"today"		=> $this->input->post('today'),
				"type"		=> $this->input->post('type'),
				"offset"	=> $this->input->post('offset'),
				"limit"		=> $this->input->post('limit')
			);
		
		$data = getExportSwipeMore($par_filter);
		$return['data']['total'] = $data['total'];
    // 

		//start..
		error_reporting(E_ALL);
//		ini_set('display_errors', TRUE);
//		ini_set('display_startup_errors', TRUE);

		require_once APPPATH.'libraries/PHPExcel.php';
		require_once APPPATH.'libraries/PHPExcel/IOFactory.php';
		
		$start_row = (int)$this->input->post('offset')+2;
		$path_file = './assets/tmp-report/';
		if( $this->input->post('offset') != 0 ){
			$objPHPExcel = PHPExcel_IOFactory::load($path_file.$this->input->post('filename'));
			$file_name = $this->input->post('filename');
		}else{		
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("SOS")
							 ->setLastModifiedBy("SOS")
							 ->setTitle("Office 2007 XLSX Data Member SOS")
							 ->setSubject("Office 2007 XLSX Data Member SOS")
							 ->setDescription("Data Member SOS")
							 ->setKeywords("office 2007 Member SOS")
							 ->setCategory("Data Member SOS");
	
			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', 'No.')
		            ->setCellValue('B1', 'Client')
		            ->setCellValue('C1', 'Provider')
		            ->setCellValue('D1', 'No Kartu')
		            ->setCellValue('E1', 'Nama Member')
		            ->setCellValue('F1', 'Tipe Layanan')
		            ->setCellValue('G1', 'Tanggal Klaim')
		            ->setCellValue('H1', 'Kelas Kamar')
		            ->setCellValue('I1', 'Status Klaim');
	
		    // Rename worksheet
			$objPHPExcel->getActiveSheet()->setTitle('member');
			$objPHPExcel->setActiveSheetIndex(0);
			$file_name = 'DataMemberSwipeMoreThanOnce-'.rand(100,10000)."-".date("dmy-His").'.xlsx';
		}
		$return['data']['filename'] = $file_name;
		$return['data']['offset'] = $this->input->post('offset')+$this->input->post('limit');

		// Miscellaneous glyphs, UTF-8
		$success = 0;
		//debugCode($data);
		foreach ((array)$data['data'] as $k => $v) {
			$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A'.$start_row, $start_row-1)
		            ->setCellValue('B'.$start_row, $v->client_name)
		            ->setCellValue('C'.$start_row, $v->provider_name)
		            ->setCellValue('D'.$start_row, $v->member_cardno)
		            ->setCellValue('E'.$start_row, $v->member_name)
		            ->setCellValue('F'.$start_row, $v->service_type)
		            ->setCellValue('G'.$start_row, myDate($v->date_claim,"d M Y",false))
		            ->setCellValue('H'.$start_row, $v->room_class)
		            ->setCellValue('I'.$start_row, $v->claim_status);
		    $success++;
		    $start_row++;
		}
		$return['data']['success'] = $success;
		if( count($data['data']) == 0 ){
			$return['data']['url_download'] = base_url()."/assets/tmp-report/".$file_name;
		}

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($path_file.$file_name);

		$return['status'] = 1;
		die(json_encode($return));
	}
	
	function export_data_swipe_by_provider(){	
		$return = array(
				"status" => 0,
				"message"=> "",
				"data" => array(
						"success" => 0,
						"total"	  => 0,
						"pid"	=> $this->input->post('pid'),
						"date"	=> $this->input->post('date'),
						"today"	=> $this->input->post('today'),
						"type"	=> $this->input->post('type'),
						"offset"  => 0,
						"filename"=> '',
						"url_download" => ''
					)
			);	

		$data = array();	
		$par_filter = array(
				"pid"	=> $this->input->post('pid'),
				"date"	=> $this->input->post('date'),
				"today"	=> $this->input->post('today'),
				"type"	=> $this->input->post('type'),
				"offset"	=> $this->input->post('offset'),
				"limit"		=> $this->input->post('limit')
			);
		
		$data = getExportSwipeByProvider($par_filter);
		$return['data']['total'] = $data['total'];

		//start..
		error_reporting(E_ALL);
//		ini_set('display_errors', TRUE);
//		ini_set('display_startup_errors', TRUE);

		require_once APPPATH.'libraries/PHPExcel.php';
		require_once APPPATH.'libraries/PHPExcel/IOFactory.php';
		
		$start_row = (int)$this->input->post('offset')+2;
		$path_file = './assets/tmp-report/';
		if( $this->input->post('offset') != 0 ){
			$objPHPExcel = PHPExcel_IOFactory::load($path_file.$this->input->post('filename'));
			$file_name = $this->input->post('filename');
		}else{		
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("SOS")
							 ->setLastModifiedBy("SOS")
							 ->setTitle("Office 2007 XLSX Data Member SOS")
							 ->setSubject("Office 2007 XLSX Data Member SOS")
							 ->setDescription("Data Member SOS")
							 ->setKeywords("office 2007 Member SOS")
							 ->setCategory("Data Member SOS");
	
			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', 'No.')
		            ->setCellValue('B1', 'Client')
		            ->setCellValue('C1', 'Provider')
		            ->setCellValue('D1', 'No Kartu')
		            ->setCellValue('E1', 'Nama Member')
		            ->setCellValue('F1', 'Tipe Layanan')
		            ->setCellValue('G1', 'Tanggal Klaim')
		            ->setCellValue('H1', 'Kelas Kamar')
		            ->setCellValue('I1', 'Status Klaim');
	
		    // Rename worksheet
			$objPHPExcel->getActiveSheet()->setTitle('member');
			$objPHPExcel->setActiveSheetIndex(0);
			$file_name = 'DataTransactionByProvider-'.rand(100,10000)."-".date("dmy-His").'.xlsx';
		}
		$return['data']['filename'] = $file_name;
		$return['data']['offset'] = $this->input->post('offset')+$this->input->post('limit');

		// Miscellaneous glyphs, UTF-8
		$success = 0;
		//debugCode($data);
		foreach ((array)$data['data'] as $k => $v) {
			$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A'.$start_row, $start_row-1)
		            ->setCellValue('B'.$start_row, $v->client_name)
		            ->setCellValue('C'.$start_row, $v->provider_name)
		            ->setCellValue('D'.$start_row, $v->member_cardno)
		            ->setCellValue('E'.$start_row, $v->member_name)
		            ->setCellValue('F'.$start_row, $v->service_type)
		            ->setCellValue('G'.$start_row, myDate($v->date_claim,"d M Y",false))
		            ->setCellValue('H'.$start_row, $v->room_class)
		            ->setCellValue('I'.$start_row, $v->claim_status);
		    $success++;
		    $start_row++;
		}
		$return['data']['success'] = $success;
		if( count($data['data']) == 0 ){
			$return['data']['url_download'] = base_url()."/assets/tmp-report/".$file_name;
		}

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($path_file.$file_name);

		$return['status'] = 1;
		die(json_encode($return));
	}
	
	function export_data_user_login(){	
		$return = array(
				"status" => 0,
				"message"=> "",
				"data" => array(
						"success" => 0,
						"total"	  => 0,
						"pid"	=> $this->input->post('pid'),
						"type"	=> $this->input->post('type'),
						"offset"  => 0,
						"filename"=> '',
						"url_download" => ''
					)
			);	

		$data = array();	
		$par_filter = array(
				"pid"	=> $this->input->post('pid'),
				"type"	=> $this->input->post('type'),
				"offset"	=> $this->input->post('offset'),
				"limit"		=> $this->input->post('limit')
			);
		
		$data = getExportUserLogin($par_filter);
		$return['data']['total'] = $data['total'];

		//start..
		error_reporting(E_ALL);
//		ini_set('display_errors', TRUE);
//		ini_set('display_startup_errors', TRUE);

		require_once APPPATH.'libraries/PHPExcel.php';
		require_once APPPATH.'libraries/PHPExcel/IOFactory.php';
		
		$start_row = (int)$this->input->post('offset')+2;
		$path_file = './assets/tmp-report/';
		if( $this->input->post('offset') != 0 ){
			$objPHPExcel = PHPExcel_IOFactory::load($path_file.$this->input->post('filename'));
			$file_name = $this->input->post('filename');
		}else{
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("SOS")
							 ->setLastModifiedBy("SOS")
							 ->setTitle("Office 2007 XLSX Data Member SOS")
							 ->setSubject("Office 2007 XLSX Data Member SOS")
							 ->setDescription("Data Member SOS")
							 ->setKeywords("office 2007 Member SOS")
							 ->setCategory("Data Member SOS");
	
			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', 'No.')
		            ->setCellValue('B1', 'Provider')
		            ->setCellValue('C1', 'Tanggal Login');
	
		    // Rename worksheet
			$objPHPExcel->getActiveSheet()->setTitle('member');
			$objPHPExcel->setActiveSheetIndex(0);
			$file_name = 'DataTransactionByProvider-'.rand(100,10000)."-".date("dmy-His").'.xlsx';
		}
		$return['data']['filename'] = $file_name;
		$return['data']['offset'] = $this->input->post('offset')+$this->input->post('limit');

		// Miscellaneous glyphs, UTF-8
		$success = 0;
		//debugCode($data);
		foreach ((array)$data['data'] as $k => $v) {
			$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A'.$start_row, $start_row-1)
		            ->setCellValue('B'.$start_row, $v->provider_name)
		            ->setCellValue('C'.$start_row, myDate($v->log_date,"d M Y H:i:s",false));
		    $success++;
		    $start_row++;
		}
		$return['data']['success'] = $success;
		if( count($data['data']) == 0 ){
			$return['data']['url_download'] = base_url()."/assets/tmp-report/".$file_name;
		}

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($path_file.$file_name);

		$return['status'] = 1;
		die(json_encode($return));
	}

	function guide(){
		$data = array();
		
		$this->DATA->table="sos_panduan";
		$data['guide'] = $this->DATA->_getall(array('panduan_status' => 1));
		
		$this->_v("guide",$data);
	}

	function news(){
		$data = array();
			
		$this->DATA->table="sos_news";
		$data['news'] = $this->DATA->_getall(array('news_status' => 1));
		
		$this->_v("news",$data);
	}

	function save_claim(){
		
		$return  = array(
			"status" => 0,
			"message" => urldecode('Verification claim is failed')
		);
		
		$memberid = $this->input->post('claim_memberid');
		$clientid = $this->input->post('claim_clientid');
		$servicetypeid = $this->input->post('claim_servicetype');
		$date = $this->input->post('claim_date');
		$desc_claim = $this->input->post('claim_desc');
		
		$this->DATA->table="sos_ttrans_claim";
		$this->db->where("date_format(date_claim,'%y%m%d% H%i') BETWEEN date_format(date_add('".$date."',INTERVAL -".cfg('fraud_prevention')." MINUTE),'%y%m%d% H%i') AND date_format('".$date."','%y%m%d% H%i')");
		$check = $this->DATA->data_id(array("memberId" => $memberid, "servicetypeId" => $servicetypeid));
		if(empty($check)){
			$this->DATA->table="sos_member";
			$member = $this->DATA->data_id(array('member_id' => $memberid));//get_name('sos_member','member_cardno',array('member_id' => $memberid));
			$client_name = get_name('sos_client','client_name',array('client_id' => $clientid));
			$room = $this->input->post('claim_roomclass');
			$provider_name = get_name('sos_provider','provider_name',array('provider_id' => $this->jCfg['user']['providerid']));
			$service_type = get_name('sos_ref_service_type','service_type',array('service_id' => $servicetypeid));
			
			$status = 'success';
			$fraud = 0;
			$this->DATA->table="sos_ttrans_claim";
			$this->db->where("date_format(date_claim,'%y%m%d') = date_format('$date','%y%m%d')");
			$check = $this->DATA->_cek(array("memberId" => $memberid, "servicetypeId" => $servicetypeid));
			if($check > 2){
//				$status = 'fail';
				$fraud = 1;
				$this->db->where("date_format(date_claim,'%y%m%d') = date_format('$date','%y%m%d')");
				$this->db->update(
					'sos_ttrans_claim',
					array("flag_fraud" => $fraud), 
					array("memberId" => $memberid, "servicetypeId" => $servicetypeid)
				);
				
				$this->DATA->table="sos_ttrans_claim";
				$this->db->join("sos_member","sos_member.member_id = sos_ttrans_claim.memberid");
				$this->db->join("sos_provider","sos_provider.provider_id = sos_ttrans_claim.providerid");
				$this->db->join("sos_ref_service_type","sos_ref_service_type.service_id = sos_ttrans_claim.servicetypeId");
				$this->db->order_by("date_claim","DESC");
				$this->db->where("date_format(date_claim,'%y%m%d') = date_format('$date','%y%m%d')");
				$tmp_fraud = $this->DATA->_getall(array("memberId" => $memberid, "servicetypeId" => $servicetypeid));
			
				$html = '<html><head><style type="text/css">
						table { background:#4E4E4E; color:#000; }
						table tr th { padding:5px 10px; background-color:#D5D7FF; font-weight:bold; }
						table tr td { padding:5px 10px; background-color:#FFFFFF;}
	        		</style></head><body>';
				$html .= 'This is to report you that member uses the following card for more than 1 time transaction for the same type of services.<br><br>';
				$html .= 'The details as below: <br>';
				$html .= '<table><tr><th>Member ID</th><th>Member Name</th><th>DOB</th><th>Benefit Entitlement</th><th>Date and Time</th><th>Provider Name</th><th>Type of Service</th></tr>';
				$html .= '<tr><td>'.$member->member_cardno.'</td><td>'.$member->member_name.'</td><td>'.myDate($member->member_birthdate,"d M Y",false).'</td><td>'.$room.'</td><td>'.myDate($date,"d M Y H:i:s",false).'</td><td>'.$provider_name.'</td><td>'.$service_type.'</td></tr>';
				foreach((array)$tmp_fraud as $k => $v ){
					$html .= '<tr><td>'.$v->member_cardno.'</td><td>'.$v->member_name.'</td><td>'.myDate($v->member_birthdate,"d M Y",false).'</td><td>'.$v->room_class.'</td><td>'.myDate($v->date_claim,"d M Y H:i:s",false).'</td><td>'.$v->provider_name.'</td><td>'.$v->service_type.'</td></tr>';
				}
				$html .= '</table>';
				$html .= '<br>***<i>Take note that this is an unmanned email account and cannot be reply</i>**';
				$html .= '<br>Important Notice: This communication (including any attachments) is intended for the use of the intended recipient(s) only and may contain information that is confidential, privileged or legally protected. Any unauthorized use or dissemination of this communication is strictly prohibited. If you have received this communication in error, please immediately notify the sender by return e-mail message and delete all copies of the original communication. Thank you for your cooperation.';
				$html .= '</body></html>';
				
				$this->send_email(array(
						'from' => cfg('email_sender'),
						'title' => cfg('email_sender_name'),
	//					'to' => 'DL.JKT.AC.MHC_Demo@internationalsos.com',
	//					'to' => 'iqbal.unpak@gmail.com',
						'to' => cfg('email_reciever'),
						'totitle' => cfg('email_reciever_name'),
						'cc' => cfg('email_cc'),
						'cctitle' => cfg('email_cc_name'),
						'subject' => '[MHC] Suspected Fraud Detection for Member# '.$member->member_cardno.' ('.$client_name.')',
						'message' => $html
				));
			}		
			$data = array(
				'memberId'			=> $memberid,
				'clientId'			=> $clientid,
				'providerId'		=> $this->jCfg['user']['providerid'],
				'servicetypeId'		=> $servicetypeid,
				'relationship_type'	=> $this->input->post('claim_relationshiptype'),
				'date_claim'		=> $date,
				//'description'		=> 'Transaction '.get_name('sos_ref_service_type','service_type',array('service_id' => $this->input->post('claim_servicetype'))).' by portal',
				'description'		=> $desc_claim,
				'room_class'		=> $room,
				'claim_status'		=> $status,
				'createdBy'			=> $this->jCfg['user']['id'],
				'createdDate'		=> $date,
				'flag_fraud'		=> $fraud
			);		
			
			$m = $this->db->insert(
				'sos_ttrans_claim',
				$data
			);
			
			if( $m ){
				$return['status'] = 1;
				$return['message'] = urldecode('Verification claim is succes');
				
				if($servicetypeid == 1) {
					$html = '<html><head><style type="text/css">
							table { background:#4E4E4E; color:#000; }
							table tr th { padding:5px 10px; background-color:#D5D7FF; font-weight:bold; }
							table tr td { padding:5px 10px; background-color:#FFFFFF;}
		        		</style></head><body>';
					$html .= 'This is to report you that member uses the following card for In-Patient services.<br><br>';
					$html .= 'The details as below: <br>';
					$html .= '<table><tr><th>Member ID</th><th>Member Name</th><th>DOB</th><th>Benefit Entitlement</th><th>Date and Time</th><th>Provider Name</th><th>Type of Service</th></tr>';
					$html .= '<tr><td>'.$member->member_cardno.'</td><td>'.$member->member_name.'</td><td>'.myDate($member->member_birthdate,"d M Y",false).'</td><td>'.$room.'</td><td>'.myDate($date,"d M Y H:i:s",false).'</td><td>'.$provider_name.'</td><td>'.$service_type.'</td></tr></table>';
					$html .= '<br>***<i>Take note that this is an unmanned email account and cannot be reply</i>**';
					$html .= '</body></html>';
					
					$this->send_email(array(
							'from' => cfg('email_sender'),
							'title' => cfg('email_sender_name'),
	//						'to' => 'DL.JKT.AC.MHC_Demo@internationalsos.com',
	//						'to' => 'iqbal.unpak@gmail.com',
							'to' => cfg('email_reciever'),
							'totitle' => cfg('email_reciever_name'),
							'cc' => cfg('email_cc'),
							'cctitle' => cfg('email_cc_name'),
							'subject' => '[MHC][DEMO]In-Patient Admisson Detection for Member# '.$member->member_cardno.' ('.$client_name.')',
							'message' => $html
					));
				}
			}
		}
		
		die(json_encode($return));
	}
	
	function locked(){
		$data = array();
		$this->is_dashboard = TRUE;
		$this->_v("lockscreen",$data,false);
	}

	function daftar(){ 
		$data = array();
		$this->is_dashboard = TRUE;

		$this->_set_title( 'PENDAFTARAN KARTU ANGGOTA' );
		$this->_v("daftar",$data);
	}

	function kasir(){
		$data = array();

		$this->_set_title( 'KASIR PENDAFTARAN EVENT' );
		$this->_v("kasir",$data);
	}

	function user_guide(){

		$this->_set_title( 'User Guide' );
		$this->_v("user_guide",array());

	}

	function background(){
		$this->is_dashboard = TRUE;
		$this->_set_title( 'Change Background & Color' );

		if( isset($_POST['simpan']) ){
			$color = '';
			$bg = isset($_POST['opt_bg'])?$_POST['opt_bg']:'bg12.png';

			$this->db->update("app_user",array(
					'user_background' 	=> $bg,
					'user_themes'		=> $color
				),array(
					'user_id'		=> $this->jCfg['user']['id']
				));

			//set sesstion...
			$this->sCfg['user']['bg']		= $bg;
			$this->sCfg['user']['color']	= $color;
			$this->_releaseSession();
		
			redirect($this->own_link."/background?msg=".urldecode('Update background & color succes')."&type_msg=success");

		}

		$this->_v("background",array());
	}
	function profile(){
		$this->is_dashboard = TRUE;
		$this->_set_title('Profile of ( You ) '.$this->jCfg['user']['fullname']);
		$this->breadcrumb[] = array(
				"title"		=> "Profile"
			);
		$this->_v("view-profile",array(
			"data"	=> $this->db->get_where("app_user",array(
					"user_id"	=> $this->jCfg['user']['id']
				))->row()
		));

	}

	function edit_profile(){

		$this->is_dashboard = TRUE;
		$this->_set_title('Update Profile For ( You ) '.$this->jCfg['user']['fullname']);
		$this->breadcrumb[] = array(
				"title"		=> "Profile",
				"url"		=> $this->own_link
			);

		$this->breadcrumb[] = array(
				"title"		=> "Update Profile"
			);

		if( isset($_POST['update']) ){
			$this->DATA->table = "app_user";
			$data = array(
				'user_fullname'		=> dbClean($_POST['user_fullname']),
				'user_email'		=> dbClean($_POST['user_email']),
				'user_telp'			=> dbClean($_POST['user_telp']),
				'is_share'			=> dbClean($_POST['is_share']),
			);		
			$a = $this->_save_master( 
				$data,
				array(
					'user_id' => $this->jCfg['user']['id']
				),
				$this->jCfg['user']['id']		
			);

			$this->upload_path="./assets/collections/photo/";
			$id = $this->jCfg['user']['id'];
			$this->upload_allowed_types = 'jpeg|jpg|png';
			$this->upload_max_size = 500;
			$this->upload_max_width = 1024;
			$this->upload_max_height = 768;
			$img = $this->_uploaded(
				array(
					'id'		=> $id ,
					'input'		=> 'user_photo',
					'param'		=> array(
									'field' => 'user_photo', 
									'par'	=> array('user_id' => $id)
								)
			));
		
			$msg = !empty($img)?$img:'Save data Berita success';
			$msgtype = !empty($img)?'warning':'success';
	
			redirect($this->own_link."/edit_profile?msg=".urldecode($msg)."&type_msg=".$msgtype);
		}

		$this->_v("edit_profile",array(
			"val"	=> $this->db->get_where("app_user",array(
					"user_id"	=> $this->jCfg['user']['id']
				))->row()
		));

	}

	function change_password(){
		$this->breadcrumb[] = array(
				"title"		=> "Change Password",
				"url"		=> $this->own_link
			);

		$this->is_dashboard = TRUE;
		$pesan="";
		if(isset($_POST['btn_simpan'])){
			$old_pass = dbClean($_POST['old_pass']);
			$this->DATA->table="app_user";
			$m1 = $this->DATA->_getall(array(
				"user_name"		=> $this->jCfg['user']['name']
			));

			$password_valid = false;
			if(count($m1)>0){
				$user = $m1[0];
				if(password_verify($old_pass, $user->user_password)){
					$password_valid = true;
				}elseif($user->user_password === md5($old_pass)){
					$password_valid = true;
				}
			}

			if($password_valid){
				$pass_baru = password_hash(dbClean($_POST['new_pass']), PASSWORD_ARGON2ID);
				$mx = $this->DATA->_update(
					array(
						"user_name"		=> $this->jCfg['user']['name']
					),array(
						"user_password" => $pass_baru
					)

				);
				$pesan = ($mx)?"Success update your password":"Success update your password";
				$mtype = ($mx)?"success":"error";
			}else{
				$pesan ="Your old password is not correctly!";
				$mtype = "error";
			}

			redirect($this->own_link."/change_password?msg=".urldecode($pesan)."&type_msg=".$mtype);
		}

		
		$this->_set_title('Change Password For ( You ) '.$this->jCfg['user']['fullname']);
		$this->_v("change-password",array(
			"pesan"	=> $pesan
		));
	}

	function bug(){
		if(isset($_POST['btn_simpan'])){
			$pesan = dbClean($_POST['pesan']);
			$url   = isset($_GET['url'])?$_GET['url']:'';
			$by    = $this->jCfg['user']['name'];
			$tgl   = date("Y-m-d H:i:s");
			$msg   = "Telah Terjadi Error Pada ".$tgl." Dilaporkan Oleh : ".$by."\n";
			$msg  .= "Error Pada ".$url." \n Pesan : ".$pesan."\n";

			$this->sendEmail(array(
				'from'		=> 'web@'.$this->domain,
				'to'		=> array(getCfgApp('bug_email')),
				'subject'	=> 'Bolanews Bug',
				'priority'	=> 1,
				'message'	=> $msg
			));

			echo "<script>parent.location.reload(true);</script>";
		}

		$this->_v("report_bug",array(
			"url"	=> isset($_GET['url'])?$_GET['url']:''
		),FALSE); 
	}	


	function help(){
		if( isset($_GET['page']) && trim($_GET['page'])!="" ){
			$this->_v("help/".$_GET['page'],array());
		}else{
			$this->_v("help/index",array());
		}
	}

  function welcome() {
    $this->_v("welcome", array());
  }
}



