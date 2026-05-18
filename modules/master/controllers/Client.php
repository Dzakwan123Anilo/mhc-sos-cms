<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."libraries/AdminController.php");
class Client extends AdminController {  
	function __construct()    
	{
		parent::__construct();    
		$this->_set_action();
		$this->_set_action(array("edit","delete"),"ITEM");
		$this->_set_title( 'Master Client' );
		$this->DATA->table="sos_client";
		$this->folder_view = "master/";
		$this->prefix_view = strtolower($this->_getClass());
		
		$this->breadcrumb[] = array(
				"title"		=> "Client",
				"url"		=> $this->own_link
			);

		$this->cat_search = array(
			''					=> 'All',
			'client_code'		=> 'Kode Client',
			'client_name'		=> 'Nama Client',
			'client_phone'		=> 'Telepon'
		); 

		if(!isset($this->jCfg['search']['class']) || $this->jCfg['search']['class'] != $this->_getClass()){
			$this->_reset();
		}

		//load js..
		$this->js_file = array(
                'client.js'
            );
            
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
								'order_by'  => 'client_id',
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

		$this->data_table = $this->M->client($par_filter);
		$data = $this->_data(array(
				"page"		=> empty($page)?0:$page,
				"base_url"	=> $this->own_link.'/index'
			));
			
		$data['load'] = 1;
		$data['switch'] = 1;
		$this->_v($this->folder_view.$this->prefix_view,$data);
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
			$detail = $this->DATA->data_id(array(
					"client_id"	=> $id
				));
				
			$this->data_form = $detail;
			$par_filter = array(
					"offset"	=> (int)$this->uri->segment($this->uri_segment),
					"limit"		=> 5,
					"where"		=> array("provider_clientid" => $detail->client_id)
				);
	
			$this->data_table = $this->M->provider_client($par_filter);
			$data = $this->_data(array(
					"base_url"	=> $this->own_link.'/index'
				));
				
			$this->_v($this->folder_view.$this->prefix_view."_form",$data);
		}else{
			redirect($this->own_link);
		}
	}
	
	function get_data_provider(){
		$id = $this->input->post('id');
		$offset = $this->input->post('offset');
		$data = array(
			"offset"	=> 0,
			"provider"	=> array(),
			"max"	=> 0
		);
		
		if( trim($id)!="" ){
			$par_filter = array(
					"offset"	=> $offset,
					"limit"		=> 5,
					"where"		=> array("provider_clientid" => $id)
				);
			$tmp_provider = $this->M->provider_client($par_filter);
			$new_provider = array();
			foreach((array)$tmp_provider['data'] as $k=>$v ){
				$new_provider[] = $v;
			}
			
			$data['offset']	= $offset;
			$data['provider'] = $new_provider;
			$data['max'] = $tmp_provider['total'];
		}
		
		die(json_encode($data));
	}
	
	function delete($id=''){
		$id=_decrypt(dbClean(trim($this->input->get('_id'))));	
		if(trim($id) != ''){
			$o = $this->db->delete("sos_client",
				array("client_id"	=> idClean($id))
			);						
		}
		redirect($this->own_link."/?msg=".urldecode('Delete data Client success')."&type_msg=success");
	}

	function save(){
		$site_id = $this->input->post('client_site_id');
		if($this->jCfg['user']['is_all'] != 1){
			$site_id = $this->jCfg['user']['site']['id'];
		}
		
		$data = array(
			'client_code'			=> $this->input->post('client_code'),
			'client_name'			=> $this->input->post('client_name'),
			'client_address'		=> $this->input->post('client_address'),
			'client_phone'			=> $this->input->post('client_phone'),
			'client_email'			=> $this->input->post('client_email'),
			'client_region'			=> $this->input->post('client_region'),
			'client_location'		=> $this->input->post('client_location'),
			'client_description'	=> $this->input->post('client_description'),
			'client_status'			=> $this->input->post('client_status'),
			'client_site_id'		=> $site_id
		);		
		
		$a = $this->_save_master( 
			$data,
			array(
				'client_id' => dbClean($_POST['client_id'])
			),
			dbClean($_POST['client_id'])			
		);

		redirect($this->own_link."?msg=".urldecode('Save data Client success')."&type_msg=success");
	}

}

