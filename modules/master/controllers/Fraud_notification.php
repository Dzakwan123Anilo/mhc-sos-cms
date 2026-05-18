<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."libraries/AdminController.php");
class Fraud_notification extends AdminController {  
	function __construct()    
	{
		parent::__construct();    
//		$this->_set_action();
//		$this->_set_action(array("edit","delete"),"ITEM");
		$this->_set_title( 'Fraud Notification' );
		$this->DATA->table="sos_fraud_notification";
		$this->folder_view = "master/";
		$this->prefix_view = strtolower($this->_getClass());
		
		$this->breadcrumb[] = array(
				"title"		=> "Fraud Notification",
				"url"		=> $this->own_link
			);

		/*$this->cat_search = array(
			''					=> 'All',
			'fraud_email'		=> 'Email',
			'fraud_title'		=> 'Title',
			'fraud_description'	=> 'Content'
		); 

		if(!isset($this->jCfg['search']['class']) || $this->jCfg['search']['class'] != $this->_getClass()){
			$this->_reset();
		}*/

		//load js..
		$this->js_file = array('input-tags.js');
		$this->js_plugins = array(
			'plugins/bootstrap/bootstrap-timepicker.min.js',
			'plugins/bootstrap/bootstrap-datepicker.js',
			'plugins/bootstrap/bootstrap-file-input.js',
			'plugins/bootstrap/bootstrap-select.js',
			'plugins/tagsinput/jquery.tagsinput.js',
			'ckeditor/ckeditor.js',
			'ckeditor/adapters/jquery.js'
		);
 		
// 		$this->load->model("mdl_master","M");
	}

	/*function _reset(){
		$this->jCfg['search'] = array(
								'class'		=> $this->_getClass(),
								'date_start'=> '',
								'date_end'	=> '',
								'status'	=> '',
								'sla'		=> 'all',
								'per_page'	=> 20,
								'order_by'  => 'fraud_id',
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

		$this->data_table = $this->M->fraud($par_filter);
		$data = $this->_data(array(
				"page"		=> empty($page)?0:$page,
				"base_url"	=> $this->own_link.'/index'
			));
			
		$data['load'] = 1;
		$data['switch'] = 1;
		$this->_v($this->folder_view.$this->prefix_view,$data);
	}*/
	
	function index(){			
		$this->data_form = $this->db->get($this->DATA->table)->row();
			
		$this->_v($this->folder_view.$this->prefix_view,array());
	}
	
	/*function add(){	
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
					'fraud_id'	=> $id
				));
				
			$this->_v($this->folder_view.$this->prefix_view."_form",array());
		}else{
			redirect($this->own_link);
		}
	}
	
	function delete($id=''){
		$id=_decrypt(dbClean(trim($this->input->get('_id'))));		
		if(trim($id) != ''){
			$o = $this->db->delete('sos_fraud_notification', array("fraud_id" => idClean($id))
			);
						
		}
		redirect($this->own_link."/?msg=".urldecode('Delete data Fraud Notification success')."&type_msg=success");
	}*/

	function save(){
		$data = array(
			'fraud_email'		=> $this->input->post('fraud_email'),
			'fraud_title'		=> $this->input->post('fraud_title'),
			'fraud_description'	=> $this->input->post('fraud_description')
		);		
		
		$a = $this->_save_master( 
			$data,
			array(
				'fraud_id' => dbClean($_POST['fraud_id'])
			),
			dbClean($_POST['fraud_id'])			
		);

		redirect($this->own_link."?msg=".urldecode('Save data Fraud Notification success')."&type_msg=success");
	}

}

