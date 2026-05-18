<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."libraries/AdminController.php");
class Dashboard extends AdminController {  
	function __construct()    
	{
		parent::__construct();    
		$this->_set_action();
		$this->_set_action(array("edit","delete"),"ITEM");
		$this->_set_title( 'Trans. Claim' );
		$this->DATA->table="sos_ttrans_claim";
		$this->folder_view = "master/";
		$this->prefix_view = strtolower($this->_getClass());
		
		$this->breadcrumb[] = array(
				"title"		=> "Trans. Claim",
				"url"		=> $this->own_link
			);

		$this->cat_search = array(
			''							=> 'All',
			'client_name'				=> 'Client',
			'member_cardno'				=> 'No. Kartu',
			'member_employeeid'			=> 'ID. Karyawan',
			'member_name'				=> 'Nama Member',
			'member_relationshiptype'	=> 'Tipe Relasi',
			'member_region'				=> 'Provinsi',
			'member_location'			=> 'Kota/Kabupaten'
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
    redirect('meme/me/dashboard');
	}
}

