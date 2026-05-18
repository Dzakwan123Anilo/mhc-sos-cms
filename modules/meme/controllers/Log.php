<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."libraries/AdminController.php");
class Log extends AdminController {  
	function __construct()   
	{
		parent::__construct();   

		$this->_set_action();
		$this->_set_action(array("edit","delete"),"ITEM");
		$this->_set_title('User Log');

		$this->cat_search = array(
			''					=> 'All',
			'log_user_name'		=> 'Username'
		); 

		$this->DATA->table="app_log";
		$this->folder_view = "meme/";
		$this->prefix_view = strtolower($this->_getClass());

		$this->breadcrumb[] = array(
				"title"		=> "Log",
				"url"		=> $this->own_link
			);
			
		if(!isset($this->jCfg['search']['class']) || $this->jCfg['search']['class'] != $this->_getClass()){
			$this->_reset();
		}

		$this->load->model("mdl_meme","M");
		$this->js_plugins = array(
			'plugins/bootstrap/bootstrap-datepicker.js',
			'plugins/bootstrap/bootstrap-file-input.js',
			'plugins/bootstrap/bootstrap-select.js'
		);
		
	}
	function _reset(){
		$this->sCfg['search'] = $this->jCfg['search'] = array(
			'class'		=> $this->_getClass(),
			'name'		=> 'log',
			'date_start'=> '',
			'date_end'	=> '',
			'status'	=> '',
			'order_by'  => 'log_id',
			'order_dir' => 'DESC',
			'colum'		=> '',
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

		$this->per_page = 20;//$this->jCfg['search']['per_page'];

		$par_filter = array(
				"offset"	=> empty($page)?0:$page,
				"limit"		=> $this->per_page,
				"param"		=> $this->cat_search
			);

		$this->data_table = $this->M->data_log($par_filter);
		$data = $this->_data(array(
				"page"		=> empty($page)?0:$page,
				"base_url"	=> $this->own_link.'/index'
			));
			
		$data['load'] = 1;
		$data['switch'] = 1;
		$this->_v($this->folder_view.$this->prefix_view,$data);
	
	}
	
}
