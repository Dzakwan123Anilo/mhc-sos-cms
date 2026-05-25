<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."libraries/AdminController.php");
class Provider extends AdminController {  
	function __construct()    
	{
		parent::__construct();    
		$this->_set_action();
		$this->_set_action(array("edit","delete"),"ITEM");
		$this->_set_title( 'Master Provider' );
		$this->DATA->table="sos_provider";
		$this->folder_view = "master/";
		$this->prefix_view = strtolower($this->_getClass());
		
		$this->breadcrumb[] = array(
				"title"		=> "Provider",
				"url"		=> $this->own_link
			);

		$this->cat_search = array(
			''					=> 'All',
//			'client_name'		=> 'Client Name',
			'provider_name'		=> 'Nama Provider',
			'provider_name_edc'	=> 'Nama Provider EDC',
			'provider_code'		=> 'Kode Provider',
			'provider_type'		=> 'Tipe Provider',
			'provider_phone'	=> 'Telepon',
			'provider_email'	=> 'Email',
			'provider_location'	=> 'Lokasi'
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
								'order_by'  => 'provider_id',
								'order_dir' => 'DESC',
								'colum'		=> '',
								'is_done'	=> FALSE,
								'keyword'	=> ''
							);
		$this->_releaseSession();
	}

	function _reset_advance(){		
		$this->jCfg['provider_search'] = array(
								'name'		=> '',
								'type'		=> '',
								'region'	=> '',
								'location'	=> ''
							);
		$this->_releaseSession();
	}

	function index(){
		
		$this->js_file = array(
                'export-data.js?r='.rand(1,1000)
            );
            
		$this->breadcrumb[] = array(
				"title"		=> "List"
			);
		$data = array();
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
				
				if($this->input->post('provider_name') && trim($this->input->post('provider_name'))!="")
					$this->sCfg['provider_search']['name'] = $this->jCfg['provider_search']['name'] = $this->input->post('provider_name');
				else
					$this->sCfg['provider_search']['name'] = $this->jCfg['provider_search']['name'] = "";
					
				if($this->input->post('provider_type') && trim($this->input->post('provider_type'))!="")
					$this->sCfg['provider_search']['type'] = $this->jCfg['provider_search']['type'] = $this->input->post('provider_type');
				else
					$this->sCfg['provider_search']['type'] = $this->jCfg['provider_search']['type'] = "";
					
				if($this->input->post('provider_region') && trim($this->input->post('provider_region'))!="")
					$this->sCfg['provider_search']['region'] = $this->jCfg['provider_search']['region'] = $this->input->post('provider_region');
				else
					$this->sCfg['provider_search']['region'] = $this->jCfg['provider_search']['region'] = "";
					
				if($this->input->post('provider_location') && trim($this->input->post('provider_location'))!="")
					$this->sCfg['provider_search']['location'] = $this->jCfg['provider_search']['location'] = $this->input->post('provider_location');
				else
					$this->sCfg['provider_search']['location']= $this->jCfg['provider_search']['location'] = "";
			}

			$this->_releaseSession();
		}

		if($this->input->post('btn_reset')){
			$this->_reset();
			$this->_reset_advance();
			redirect($this->own_link);
		}

		$this->per_page = $this->jCfg['search']['per_page'];

		$par_filter = array(
				"offset"	=> empty($page)?0:$page,
				"limit"		=> $this->per_page,
				"param"		=> $this->cat_search
			);

		$this->data_table = $this->M->provider($par_filter);
		$data = $this->_data(array(
				"page"		=> empty($page)?0:$page,
				"base_url"	=> $this->own_link.'/index'
			));
		$data['load'] = 1;
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
		$par_filter = array(
				"offset"	=> $this->input->post('offset'),
				"limit"		=> $this->input->post('limit'),
				"param"		=> $this->cat_search
			);
		$data = $this->M->provider($par_filter);		
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
							 ->setTitle("Office 2007 XLSX Data Provider SOS")
							 ->setSubject("Office 2007 XLSX Data Provider SOS")
							 ->setDescription("Data Provider SOS")
							 ->setKeywords("office 2007 Provider SOS")
							 ->setCategory("Data Member SOS");

			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', 'No.')
//		            ->setCellValue('B1', 'Client')
		            ->setCellValue('B1', 'Nama Provider')
		            ->setCellValue('C1', 'Nama Provider EDC')
		            ->setCellValue('D1', 'Kode Provider')
		            ->setCellValue('E1', 'Tipe Provider')
		            ->setCellValue('F1', 'Telepon')
		            ->setCellValue('G1', 'Email')
		            ->setCellValue('H1', 'Provinsi')
		            ->setCellValue('I1', 'Kota/Kabupaten');

		    // Rename worksheet
			$objPHPExcel->getActiveSheet()->setTitle('trans-raw');
			$objPHPExcel->setActiveSheetIndex(0);
			$file_name = 'DataProvider-'.rand(100,10000)."-".date("dmy-His").'.xlsx';
		}
		$return['data']['filename'] = $file_name;
		$return['data']['offset'] = $this->input->post('offset')+$this->input->post('limit');

		// Miscellaneous glyphs, UTF-8
		$success = 0;
		//debugCode($data);
		foreach ((array)$data['data'] as $k => $v) {
			$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A'.$start_row, $start_row-1)
//		            ->setCellValue('B'.$start_row, $v->client_name)
		            ->setCellValue('B'.$start_row, $v->provider_name)
		            ->setCellValue('C'.$start_row, $v->provider_name_edc)
		            ->setCellValue('D'.$start_row, $v->provider_code)
		            ->setCellValue('E'.$start_row, $v->provider_type)
		            ->setCellValue('F'.$start_row, $v->provider_phone)
		            ->setCellValue('G'.$start_row, $v->provider_email)
		            ->setCellValue('H'.$start_row, $v->provider_region)
		            ->setCellValue('I'.$start_row, $v->provider_location);
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
	
	function add(){	
		$this->breadcrumb[] = array(
				"title"		=> "Add"
			);		
		$this->_v($this->folder_view.$this->prefix_view."_form",array());
	}
	
	function edit($id=''){

		$this->breadcrumb[] = array(
				"title"		=> "Edit"
			);

		$id = _decrypt($this->input->get('_id'));
		
		if(trim($id)!=''){
			$this->data_form = $this->DATA->data_id(array(
					'provider_id'	=> $id
				));
				
			$this->_v($this->folder_view.$this->prefix_view."_form",array());
			//group member assign
            /*$this->db->order_by("memberprovidergroupId","DESC");
            $group_assign_tmp = $this->db->get_where("sos_tref_member_provider_group",array(
                    "providerId"  => $id
                ))->result();
            $group_assign = array();
            foreach ((array)$group_assign_tmp as $p => $q) {
                $group_assign[] = $q->providergroupId;
            }
            
			$this->_v($this->folder_view.$this->prefix_view."_form",array(
				"group_assign" => $group_assign
			));*/
		}else{
			redirect($this->own_link);
		}
	}
	
	function delete($id=''){
		$id=_decrypt(dbClean(trim($this->input->get('_id'))));
		if(trim($id) != ''){
			$o = $this->db->delete("sos_provider",
				array("provider_id"	=> idClean($id))
			);						
		}
		redirect($this->own_link."/?msg=".urldecode('Delete data Provider success')."&type_msg=success");
	}

	function save(){
		/*$site_id = $this->input->post('provider_site_id');
		if($this->jCfg['user']['is_all'] != 1){
			$site_id = $this->jCfg['user']['site']['id'];
		}*/
		
		$data = array(
//			'provider_clientid'		=> $this->input->post('provider_clientid'),
			'provider_code'			=> $this->input->post('provider_code'),
			'provider_name'			=> $this->input->post('provider_name'),
			'provider_name_edc'		=> $this->input->post('provider_name_edc'),
			'provider_type'			=> $this->input->post('provider_type'),
			'provider_address'		=> $this->input->post('provider_address'),
			'provider_phone'		=> $this->input->post('provider_phone'),
			'provider_email'		=> $this->input->post('provider_email'),
			'provider_region'		=> $this->input->post('provider_region'),
			'provider_location'		=> $this->input->post('provider_location'),
			'provider_description'	=> $this->input->post('provider_description'),
			//'providergroupId'		=> $this->input->post('providergroupId'),
			'provider_status'		=> $this->input->post('provider_status')
//			,'provider_site_id'		=> $site_id
		);		
		
		$a = $this->_save_master( 
			$data,
			array(
				'provider_id' => dbClean($_POST['provider_id'])
			),
			dbClean($_POST['provider_id'])			
		);
		
		//delete current data..
		/*$provider_id = $a['id'];
        $this->db->delete("sos_tref_member_provider_group",array(
                "providerId" => $provider_id
            ));
        if( isset($_POST['providergroupId']) && count($_POST['providergroupId'])>0 ){
            foreach ((array)$this->input->post('providergroupId') as $key => $value) {
                $this->db->insert("sos_tref_member_provider_group",array(
                        "providergroupId"   => $value,
                        "providerId"  		=> $provider_id,
                        "createdDate"       => date("Y-m-d H:i:s"),
                        "createdBy"         => $this->jCfg['user']['id'],
                        "status_data"		=> 1
                    ));
            }
        }*/  
        
		redirect($this->own_link."?msg=".urldecode('Save data Provider success')."&type_msg=success");
	}

}

