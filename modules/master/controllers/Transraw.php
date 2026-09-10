<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."libraries/AdminController.php");
class Transraw extends AdminController {  
	function __construct()    
	{
		parent::__construct();    
		$this->_set_action();
		$this->_set_action(array("edit","delete"),"ITEM");
		$this->_set_title( 'Trans. Raw' );
		$this->DATA->table="sos_ttrans_claim";
		$this->folder_view = "master/";
		$this->prefix_view = strtolower($this->_getClass());
		
		$this->breadcrumb[] = array(
				"title"		=> "Trans. Raw",
				"url"		=> $this->own_link
			);

		$this->cat_search = array(
			''					=> 'All',
			'client_name'		=> 'Client',
			'member_cardno'		=> 'No. Kartu',
			'member_name'		=> 'Member Name',
			'relationship_type'	=> 'Tipe Relasi',
			'provider_name'		=> 'Provider',
			'service_type'		=> 'Layanan',
			'room_class'		=> 'Kelas Perawatan',
			'claim_status'		=> 'Status Claim',
			'description'		=> 'Deskripsi',
			'flag_fraud'		=> 'Fraud'
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
		$this->jCfg['search'] = array(
								'class'		=> $this->_getClass(),
								'date_start'=> '',
								'date_end'	=> '',
								'status'	=> '',
								'sla'		=> 'all',
								'per_page'	=> 20,
								'order_by'  => 'date_claim',
								'order_dir' => 'DESC',
								'colum'		=> '',
								'is_done'	=> FALSE,
								'keyword'	=> ''
							);
		$this->_releaseSession();
	}

	function _reset_advance(){
		$this->jCfg['transraw_search'] = array(
								'providerid'	=> '',
								'serviceid'		=> '',
								'name'			=> '',
								'date_start'	=> '',
								'date_end'		=> '',
								'clientid'		=> '',
								'location'		=> '',
								'cardno'		=> '',
								'relationship'	=> ''
							);
		$this->_releaseSession();
	}

	function index(){
		$this->js_file = array(
                'export-data.js',
                'trans-raw.js'
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
				
				if($this->input->post('transraw_providerid') && trim($this->input->post('transraw_providerid'))!="")
					$this->sCfg['transraw_search']['providerid'] = $this->jCfg['transraw_search']['providerid'] = $this->input->post('transraw_providerid');
				else
					$this->sCfg['transraw_search']['providerid'] = $this->jCfg['transraw_search']['providerid'] = "";
										
				if($this->input->post('transraw_serviceid') && trim($this->input->post('transraw_serviceid'))!="")
					$this->sCfg['transraw_search']['serviceid'] = $this->jCfg['transraw_search']['serviceid'] = $this->input->post('transraw_serviceid');
				else
					$this->sCfg['transraw_search']['serviceid'] = $this->jCfg['transraw_search']['serviceid'] = "";
					
				if($this->input->post('transraw_name') && trim($this->input->post('transraw_name'))!="")
					$this->sCfg['transraw_search']['name'] = $this->jCfg['transraw_search']['name'] = $this->input->post('transraw_name');
				else
					$this->sCfg['transraw_search']['name'] = $this->jCfg['transraw_search']['name'] = "";
			
				if($this->input->post('transraw_datestart') && trim($this->input->post('transraw_datestart'))!="")
					$this->sCfg['transraw_search']['date_start'] = $this->jCfg['transraw_search']['date_start'] = $this->input->post('transraw_datestart');

				if($this->input->post('transraw_dateend') && trim($this->input->post('transraw_dateend'))!="")
					$this->sCfg['transraw_search']['date_end'] = $this->jCfg['transraw_search']['date_end'] = $this->input->post('transraw_dateend');
					
				if($this->input->post('transraw_clientid') && trim($this->input->post('transraw_clientid'))!="")
					$this->sCfg['transraw_search']['clientid'] = $this->jCfg['transraw_search']['clientid'] = $this->input->post('transraw_clientid');
				else
					$this->sCfg['transraw_search']['clientid'] = $this->jCfg['transraw_search']['clientid'] = "";
										
				if($this->input->post('transraw_location') && trim($this->input->post('transraw_location'))!="")
					$this->sCfg['transraw_search']['location'] = $this->jCfg['transraw_search']['location'] = $this->input->post('transraw_location');
				else
					$this->sCfg['transraw_search']['location'] = $this->jCfg['transraw_search']['location'] = "";
					
				if($this->input->post('transraw_cardno') && trim($this->input->post('transraw_cardno'))!="")
					$this->sCfg['transraw_search']['cardno'] = $this->jCfg['transraw_search']['cardno'] = $this->input->post('transraw_cardno');
				else
					$this->sCfg['transraw_search']['cardno'] = $this->jCfg['transraw_search']['cardno'] = "";
					
				if($this->input->post('transraw_relationshiptype') && trim($this->input->post('transraw_relationshiptype'))!="")
					$this->sCfg['transraw_search']['relationship'] = $this->jCfg['transraw_search']['relationship'] = $this->input->post('transraw_relationshiptype');
				else
					$this->sCfg['transraw_search']['relationship'] = $this->jCfg['transraw_search']['relationship'] = "";
			}

			$this->_releaseSession();

			$this->per_page = $this->jCfg['search']['per_page'];
	
			$par_filter = array(
					"offset"	=> empty($page)?0:$page,
					"limit"		=> $this->per_page,
					"param"		=> $this->cat_search
				);
	
			$this->data_table = $this->M->transraw($par_filter);
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
	
	function get_service_type(){
		$id = $this->input->post('id');
		
		$data = array(
			"st" => array()
		);
		
		if( trim($id)!="" ){
			
			$this->DATA->table="sos_ref_service_type";
			$this->db->order_by("service_number","ASC");
			$st = $this->DATA->_getall(array('service_clientid' => $id, 'service_status' => 1));
			
			$data['st'] = $st;
		}
		
		die(json_encode($data));
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
		$par_filter = array(
				"offset"	=> $this->input->post('offset'),
				"limit"		=> $this->input->post('limit'),
				"param"		=> $this->cat_search
			);
		$data = $this->M->transraw($par_filter);		
		$return['data']['total'] = $data['total'];

		//start..
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);

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
							 ->setTitle("Office 2007 XLSX Data TransRaw SOS")
							 ->setSubject("Office 2007 XLSX Data TransRaw SOS")
							 ->setDescription("Data TransRaw SOS")
							 ->setKeywords("office 2007 TransRaw SOS")
							 ->setCategory("Data TransRaw SOS");

			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', 'No.')
		            ->setCellValue('B1', 'Client')
		            ->setCellValue('C1', 'No Kartu')
		            ->setCellValue('D1', 'Nama Member')
		            ->setCellValue('E1', 'Tipe Relasi')
		            ->setCellValue('F1', 'Provide')
		            ->setCellValue('G1', 'Layanan')
		            ->setCellValue('H1', 'Kelas Perawatan')
		            ->setCellValue('I1', 'Tgl. Claim')
		            ->setCellValue('J1', 'Deskripsi')
		            ->setCellValue('K1', 'Status Claim')
		            ->setCellValue('L1', 'Fraud');

		    // Rename worksheet
			$objPHPExcel->getActiveSheet()->setTitle('trans-raw');
			$objPHPExcel->setActiveSheetIndex(0);
			$file_name = 'DataTransRaw-'.rand(100,10000)."-".date("dmy-His").'.xlsx';
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
		            ->setCellValue('F'.$start_row, $v->provider_name)
		            ->setCellValue('G'.$start_row, $v->service_type)
		            ->setCellValue('H'.$start_row, $v->room_class)
		            ->setCellValue('I'.$start_row, !empty($v->date_claim)?myDate($v->date_claim,"d M Y",false):'')
		            ->setCellValue('J'.$start_row, $v->description)
		            ->setCellValue('K'.$start_row, $v->claim_status)
		            ->setCellValue('L'.$start_row, $v->flag_fraud);
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
	
	function get_info_raw(){
		$id = $this->input->post('id');
		$data = array(
			"member" => array(),
			"family" => array()
		);
		if( trim($id)!="" ){
			
			//member...
			$this->db->join("sos_client","sos_client.client_id = sos_ttrans_claim.clientid");
			$this->db->join("sos_member","sos_member.member_id = sos_ttrans_claim.memberid");
			$this->db->join("sos_ref_plan_type","sos_ref_plan_type.plan_type = sos_member.member_plantype AND sos_ref_plan_type.plan_clientid = sos_member.member_clientid");
			$tmp_member = $this->DATA->data_id(array(
					'claimid'	=> $id
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

