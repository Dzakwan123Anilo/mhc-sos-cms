<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."libraries/AdminController.php");
class Benefit extends AdminController {  
	function __construct()    
	{
		parent::__construct();    
		$this->_set_action();
		$this->_set_action(array("edit","delete"),"ITEM");
		$this->_set_title( 'Referensi Benefit' );
		$this->DATA->table="sos_ref_benefit";
		$this->folder_view = "reference/";
		$this->prefix_view = strtolower($this->_getClass());
		
		$this->breadcrumb[] = array(
				"title"		=> "Benefit",
				"url"		=> $this->own_link
			);

		$this->cat_search = array(
			''						=> 'All',
			'client_name'			=> 'Client Name',
			'service_type'			=> 'Kelas Perawatan',
			'plan_description'		=> 'Jenis Pelayanan',
			'benefit_description'	=> 'Benefit'
		); 

		if(!isset($this->jCfg['search']['class']) || $this->jCfg['search']['class'] != $this->_getClass()){
			$this->_reset();
		}

		//load js..
		$this->js_file = array('benefit.js');
		$this->js_plugins = array(
			'plugins/bootstrap/bootstrap-timepicker.min.js',
			'plugins/bootstrap/bootstrap-datepicker.js',
			'plugins/bootstrap/bootstrap-file-input.js',
			'plugins/bootstrap/bootstrap-select.js',
			'plugins/tagsinput/jquery.tagsinput.min.js',
			'ckeditor/ckeditor.js',
			'ckeditor/adapters/jquery.js'
		);
 		
 		$this->load->model("mdl_reference","M");
	}

	function _reset(){
		$this->jCfg['search'] = array(
								'class'		=> $this->_getClass(),
								'date_start'=> '',
								'date_end'	=> '',
								'status'	=> '',
								'sla'		=> 'all',
								'per_page'	=> 20,
								'order_by'  => 'benefit_id',
								'order_dir' => 'DESC',
								'colum'		=> '',
								'is_done'	=> FALSE,
								'keyword'	=> ''
							);
		$this->_releaseSession();
	}

	function index(){
		$this->breadcrumb[] = array(
				"title"		=> "List"
			);
		$data = array();
		$load = $this->input->post('load');
		$page = $this->input->post('page');

		if($this->input->post('btn_search') || $load == 1){
			if($this->input->post('date_start') && trim($this->input->post('date_start'))!="")
				$this->jCfg['search']['date_start'] = $this->input->post('date_start');

			if($this->input->post('date_end') && trim($this->input->post('date_end'))!="")
				$this->jCfg['search']['date_end'] = $this->input->post('date_end');

			if($this->input->post('colum') && trim($this->input->post('colum'))!="")
				$this->jCfg['search']['colum'] = $this->input->post('colum');
			else
				$this->jCfg['search']['colum'] = "";	

			if($this->input->post('keyword') && trim($this->input->post('keyword'))!="")
				$this->jCfg['search']['keyword'] = $this->input->post('keyword');
			else
				$this->jCfg['search']['keyword'] = "";

			$this->_releaseSession();
		}

		if($this->input->post('btn_reset')){
			$this->_reset();
			redirect($this->own_link);
		}

		$this->per_page = $this->jCfg['search']['per_page'];

		$par_filter = array(
				"offset"	=> empty($page)?0:$page,
				"limit"		=> $this->per_page,
				"param"		=> $this->cat_search
			);

		$this->data_table = $this->M->benefit($par_filter);
		$data = $this->_data(array(
				"page"		=> empty($page)?0:$page,
				"base_url"	=> $this->own_link.'/index'
			));
			
		$data['load'] = 1;
		$data['switch'] = 1;
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
	
	function get_plant_type(){
		$id = $this->input->post('id');
		
		$data = array(
			"pt" => array()
		);
		
		if( trim($id)!="" ){
			
			$this->DATA->table="sos_ref_plan_type";
			$this->db->order_by("plan_type","ASC");
			$pt = $this->DATA->_getall(array('plan_clientid' => $id, 'plan_status' => 1));
			
			$data['pt'] = $pt;
		}
		
		die(json_encode($data));
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
					'benefit_id'	=> $id
				));
				
			$this->_v($this->folder_view.$this->prefix_view."_form",array());
		}else{
			redirect($this->own_link);
		}
	}
	
	function delete($id=''){
		$id=_decrypt(dbClean(trim($this->input->get('_id'))));		
		if(trim($id) != ''){
			$o = $this->db->delete('sos_ref_benefit', array("benefit_id" => idClean($id))
			);
						
		}
		redirect($this->own_link."/?msg=".urldecode('Delete data Benefit success')."&type_msg=success");
	}

	function save(){
		$data = array(
			'benefit_clientid'		=> $this->input->post('benefit_clientid'),
			'benefit_serviceid'		=> $this->input->post('benefit_serviceid'),
			'benefit_planid'		=> $this->input->post('benefit_planid'),
			'benefit_description'	=> $this->input->post('benefit_description'),
			'benefit_status'		=> $this->input->post('benefit_status')
		);		
		
		$a = $this->_save_master( 
			$data,
			array(
				'benefit_id' => dbClean($_POST['benefit_id'])
			),
			dbClean($_POST['benefit_id'])			
		);

		redirect($this->own_link."?msg=".urldecode('Save data Benefit success')."&type_msg=success");
	}

}

