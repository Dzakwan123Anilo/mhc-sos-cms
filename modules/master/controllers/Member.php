<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."libraries/AdminController.php");
class Member extends AdminController {  
	function __construct()    
	{
		parent::__construct();    
		$this->_set_action();
		$this->_set_action(array("edit","delete"),"ITEM");
		$this->_set_title( 'Master Member' );
		$this->DATA->table="sos_member";
		$this->folder_view = "master/";
		$this->prefix_view = strtolower($this->_getClass());

		$this->breadcrumb[] = array(
				"title"		=> "Master Member",
				"url"		=> $this->own_link
			);

		$this->cat_search = array(
			''							=> 'All',
			'member_cardno'				=> 'No. Kartu',
			'member_name'				=> 'Nama Member',
			'member_relationshiptype'	=> 'Tipe Relasi',
			'member_sex'				=> 'Jenis Kelamin',
			'member_birthdate'			=> 'Tgl. Lahir',
			'member_region'				=> 'Provinsi',
			'member_location'			=> 'Kota/Kabupaten',
			'plan_description'			=> 'Kelas Perawatan',
			'member_employmentstatus'	=> 'Status Karyawan',
			'member_statuscard'			=> 'Status Kartu',
			'member_dentallimit'		=> 'Dental Limit'
		); 		
		
		if(!isset($this->jCfg['search']['class']) || $this->jCfg['search']['class'] != $this->_getClass()){
			$this->_reset();
			$this->_reset_advance();
		}

		//load js..
		$this->js_plugins = array(
			'plugins/bootstrap/bootstrap-datepicker.js',
			'plugins/bootstrap/bootstrap-file-input.js',
			'plugins/bootstrap/bootstrap-select.js'
		);
 		
 		$this->load->model("mdl_master","M");
	}

	function _reset(){
		$this->sCfg['search'] = $this->jCfg['search'] = array(
								'class'		=> $this->_getClass(),
								'date_start'=> '',
								'date_end'	=> '',
								'status'	=> '',
								'sla'		=> 'all',
								'per_page'	=> 20,
								'order_by'  => 'member_id',
								'order_dir' => 'DESC',
								'colum'		=> '',
								'is_done'	=> FALSE,
								'keyword'	=> ''
							);
		$this->_releaseSession();
	}

	function _reset_advance(){
		$this->sCfg['member_search'] = $this->jCfg['member_search'] = array(
								'clientid'		=> '',
								'cardno'		=> '',
								'name'			=> '',
								'relationship'	=> '',
								'sex'			=> '',
								'birthdate'		=> '',
								'phone'			=> '',
								'email' 		=> '',
								'region'		=> '',
								'location'		=> '',
								'plantype'		=> '',
								'employment'	=> '',
								'cardstatus'	=> ''
							);
		$this->_releaseSession();
	}

	function index(){
		
		$this->js_file = array(
                'export-data.js?r='.rand(1,1000),
                'member.js?r='.rand(1,1000)
            );
            
		$this->breadcrumb[] = array(
				"title"		=> "List"
			);
		$data = array();
		$data['data'] = array();
		$switch = $this->input->post('switch_search')?$this->input->post('switch_search'):1;
		$load = $this->input->post('load');
		$page = $this->input->post('page');

		if($this->input->post('btn_search') || $load == 1){
			if($switch==1) {				
				$this->_reset_advance();
			
				if($this->input->post('date_start') && trim($this->input->post('date_start'))!="")
					$this->sCfg['search']['date_start'] = $this->jCfg['search']['date_start'] = $this->input->post('date_start');
	
				if($this->input->post('date_end') && trim($this->input->post('date_end'))!="")
					$this->sCfg['search']['date_end'] = $this->jCfg['search']['date_end'] = $this->input->post('date_end');
	
				if($this->input->post('colum') && trim($this->input->post('colum'))!="")
					$this->sCfg['search']['colum'] = $this->jCfg['search']['colum'] = $this->input->post('colum');
				else
					$this->sCfg['search']['colum'] = $this->jCfg['search']['colum'] = "";	
	
				if($this->input->post('keyword') && trim($this->input->post('keyword'))!="")
					$this->sCfg['search']['keyword'] = $this->jCfg['search']['keyword'] = $this->input->post('keyword');
				else
					$this->sCfg['search']['keyword'] = $this->jCfg['search']['keyword'] = "";
			}
			
			if($switch==2) { 
				$this->_reset();
					
				if($this->input->post('member_clientid') && trim($this->input->post('member_clientid'))!="")
					$this->sCfg['member_search']['clientid'] = $this->jCfg['member_search']['clientid'] = $this->input->post('member_clientid');
				else
					$this->sCfg['member_search']['clientid'] = $this->jCfg['member_search']['clientid'] = "";
					
				if($this->input->post('member_cardno') && trim($this->input->post('member_cardno'))!="")
					$this->sCfg['member_search']['cardno'] = $this->jCfg['member_search']['cardno'] = $this->input->post('member_cardno');
				else
					$this->sCfg['member_search']['cardno'] = $this->jCfg['member_search']['cardno'] = "";
					
				if($this->input->post('member_name') && trim($this->input->post('member_name'))!="")
					$this->sCfg['member_search']['name']= $this->jCfg['member_search']['name'] = $this->input->post('member_name');
				else
					$this->sCfg['member_search']['name'] = $this->jCfg['member_search']['name'] = "";
					
				if($this->input->post('member_relationshiptype') && trim($this->input->post('member_relationshiptype'))!="")
					$this->sCfg['member_search']['relationship']= $this->jCfg['member_search']['relationship'] = $this->input->post('member_relationshiptype');
				else
					$this->sCfg['member_search']['relationship']= $this->jCfg['member_search']['relationship'] = "";
					
				if($this->input->post('member_sex') && trim($this->input->post('member_sex'))!="")
					$this->sCfg['member_search']['sex'] = $this->jCfg['member_search']['sex'] = $this->input->post('member_sex');
				else
					$this->sCfg['member_search']['sex'] = $this->jCfg['member_search']['sex'] = "";
					
				if($this->input->post('member_birthdate') && trim($this->input->post('member_birthdate'))!="")
					$this->sCfg['member_search']['birthdate']= $this->jCfg['member_search']['birthdate'] = $this->input->post('member_birthdate');
				else
					$this->sCfg['member_search']['birthdate']= $this->jCfg['member_search']['birthdate'] = "";
					
				if($this->input->post('member_phone') && trim($this->input->post('member_phone'))!="")
					$this->sCfg['member_search']['phone'] = $this->jCfg['member_search']['phone'] = $this->input->post('member_phone');
				else
					$this->sCfg['member_search']['phone'] = $this->jCfg['member_search']['phone'] = "";
					
				if($this->input->post('member_email') && trim($this->input->post('member_email'))!="")
					$this->sCfg['member_search']['email'] = $this->jCfg['member_search']['email'] = $this->input->post('member_email');
				else
					$this->sCfg['member_search']['email'] = $this->jCfg['member_search']['email'] = "";
					
				if($this->input->post('member_region') && trim($this->input->post('member_region'))!="")
					$this->sCfg['member_search']['region'] = $this->jCfg['member_search']['region'] = $this->input->post('member_region');
				else
					$this->sCfg['member_search']['region'] = $this->jCfg['member_search']['region'] = "";
					
				if($this->input->post('member_location') && trim($this->input->post('member_location'))!="")
					$this->sCfg['member_search']['location'] = $this->jCfg['member_search']['location'] = $this->input->post('member_location');
				else
					$this->sCfg['member_search']['location'] = $this->jCfg['member_search']['location'] = "";
					
				if($this->input->post('member_plantype') && trim($this->input->post('member_plantype'))!="")
					$this->sCfg['member_search']['plantype'] = $this->jCfg['member_search']['plantype'] = $this->input->post('member_plantype');
				else
					$this->sCfg['member_search']['plantype'] = $this->jCfg['member_search']['plantype'] = "";
					
				if($this->input->post('member_employmentstatus') && trim($this->input->post('member_employmentstatus'))!="")
					$this->sCfg['member_search']['employment'] = $this->jCfg['member_search']['employment'] = $this->input->post('member_employmentstatus');
				else
					$this->sCfg['member_search']['employment'] = $this->jCfg['member_search']['employment'] = "";
					
				if($this->input->post('member_statuscard') && trim($this->input->post('member_statuscard'))!="")
					$this->sCfg['member_search']['cardstatus'] = $this->jCfg['member_search']['cardstatus'] = $this->input->post('member_statuscard');
				else
					$this->sCfg['member_search']['cardstatus'] = $this->jCfg['member_search']['cardstatus'] = "";
			}
			
			$this->_releaseSession();
			
			$this->per_page = $this->jCfg['search']['per_page'];
			$this->jCfg['search']['order_by'] = 'sos_member.member_cardno,sos_member.member_name';
			$this->jCfg['search']['order_dir'] = 'ASC';
			$par_filter = array(
					"offset"	=> empty($page)?0:$page,
					"limit"		=> $this->per_page,
					"param"		=> $this->cat_search
				);
	
			$this->data_table = $this->M->member($par_filter);
			$data = $this->_data(array(
					"page"		=> empty($page)?0:$page,
					"base_url"	=> $this->own_link.'/index'
				));
			$data['load'] = 1;
		}

		if($this->input->post('btn_reset')){
			$this->_reset();
			$this->_reset_advance();
			redirect($this->own_link);
		}

		$data['switch'] = $switch;
		$this->_v($this->folder_view.$this->prefix_view,$data);
	}
	
	function export_data(){	
		$return = array(
				"status" => 0,
				"message"=> "",
				"data" => array(
						"success" => 0,
						"total"	  => 0,
						"offset"  => 0,
						"filename"=> '',
						"url_download" => ''
					)
			);	

		$data = array();	
		$this->jCfg['search']['order_by'] = 'sos_member.member_cardno,sos_member.member_name';
		$this->jCfg['search']['order_dir'] = 'ASC';

		$par_filter = array(
				"offset"	=> $this->input->post('offset'),
				"limit"		=> $this->input->post('limit'),
				"param"		=> $this->cat_search
			);
		
		$data = $this->M->member($par_filter);
		$return['data']['total'] = $data['total'];

		//start..

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
			$file_name = 'DataMember-'.rand(100,10000)."-".date("dmy-His").'.xlsx';
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
	
	function get_info_member(){
		$id = $this->input->post('id');
		$data = array(
			"member" => array(),
			"family" => array()
		);
		if( trim($id)!="" ){
			
			//member...
			$this->DATA->table="sos_member";
			$this->db->select('sos_member.*,sos_client.client_name,sos_ref_plan_type.plan_id,sos_ref_plan_type.plan_description');
			$this->db->join("sos_client","sos_client.client_id = sos_member.member_clientid");
			$this->db->join("sos_ref_plan_type","sos_ref_plan_type.plan_type = sos_member.member_plantype AND sos_ref_plan_type.plan_clientid = sos_member.member_clientid");
			$tmp_member = $this->DATA->data_id(array(
					'member_id'	=> $id
				));
			$tmp_member->member_sex = jenkel($tmp_member->member_sex);
			$tmp_member->member_birthdate = myDate($tmp_member->member_birthdate,"d M Y",false);
			$tmp_member->member_activationdate = !empty($tmp_member->member_activationdate)&&$tmp_member->member_activationdate!='0000-00-00'?myDate($tmp_member->member_activationdate,"d M Y",false):'';
			
			$desc = $tmp_member->member_plantype=='PLAN_FE'?'dental_description':'dental_limit';
			$tmp_member->member_dentallimit = ($tmp_member->member_plantype=='PLAN_FE'?'':'Rp. ').get_name('sos_ref_dental_limit',$desc,array('dental_plantypeid' => $tmp_member->plan_id));
			$data['member'] = $tmp_member;
							
			// family...
			$emp_id = isset($data['member']->member_employeeid)?$data['member']->member_employeeid:'0';
			$par_filter = array(
					"sos_member.member_employeeid"	=> $emp_id
				);
	
			$this->DATA->table="sos_member";
			$this->db->select('sos_member.*,sos_client.client_name,sos_ref_plan_type.plan_description,sos_ref_dental_limit.dental_limit');
			$this->db->join("sos_client","sos_client.client_id = sos_member.member_clientid");
			$this->db->join("sos_ref_plan_type","sos_ref_plan_type.plan_type = sos_member.member_plantype AND sos_ref_plan_type.plan_clientid = sos_member.member_clientid");
			$this->db->join("sos_ref_dental_limit","sos_ref_dental_limit.dental_plantypeid = sos_ref_plan_type.plan_id");
			$tmp_family = $this->DATA->_getall($par_filter);	
			$new_family = array();
			foreach((array)$tmp_family as $k=>$v ){
				$v->member_birthdate = myDate($v->member_birthdate,"d M Y",false);
				$v->member_sex = jenkel($v->member_sex);
				$v->member_dentallimit = $v->member_plantype=='PLAN_FE'?'':$v->dental_limit;
				$new_family[] = $v;
			}
			
			$data['family'] = $new_family;

		}
		
		die(json_encode($data));
	}

}