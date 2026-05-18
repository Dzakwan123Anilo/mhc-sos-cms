<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."libraries/AdminController.php");
class Panduan extends AdminController {  
	function __construct()    
	{
		parent::__construct();    
		$this->_set_action();
		$this->_set_action(array("edit","delete"),"ITEM");
		$this->_set_title( 'Master Panduan' );
		$this->DATA->table="sos_panduan";
		$this->folder_view = "master/";
		$this->prefix_view = strtolower($this->_getClass());
		$this->upload_path = cfg('upload_path_file');
		$this->upload_allowed_types = 'pdf';
		
		$this->breadcrumb[] = array(
				"title"		=> "Panduan",
				"url"		=> $this->own_link
			);

		$this->cat_search = array(
			''						=> 'All',
			'panduan_name'			=> 'Judul Panduan',
			'panduan_description'	=> 'Deskripsi',
			'panduan_filename'	=> 'File'
		); 

		if(!isset($this->jCfg['search']['class']) || $this->jCfg['search']['class'] != $this->_getClass()){
			$this->_reset();
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
								'order_by'  => 'panduan_id',
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

		$this->data_table = $this->M->panduan($par_filter);
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
			$this->data_form = $this->DATA->data_id(array(
					'panduan_id'	=> $id
				));
				
			$this->_v($this->folder_view.$this->prefix_view."_form",array());
		}else{
			redirect($this->own_link);
		}
	}
	
	function delete($id=''){
		$id=_decrypt(dbClean(trim($this->input->get('_id'))));	
		if(trim($id) != ''){
			$o = $this->db->delete('sos_panduan', array("panduan_id" => idClean($id)));
						
		}
		redirect($this->own_link."/?msg=".urldecode('Hapus data Panduan sukses')."&type_msg=success");
	}

	function save(){
		$site_id = $this->input->post('panduan_site_id');
		if($this->jCfg['user']['is_all'] != 1){
			$site_id = $this->jCfg['user']['site']['id'];
		}
		
		$data = array(
			'panduan_name'			=> $this->input->post('panduan_name'),
			'panduan_description'	=> $this->input->post('panduan_description'),
			'panduan_status'		=> $this->input->post('panduan_status'),
			'panduan_site_id'		=> $site_id
		);		
		
		$a = $this->_save_master( 
			$data,
			array(
				'panduan_id' => dbClean($_POST['panduan_id'])
			),
			dbClean($_POST['panduan_id'])			
		);

		$id = $a['id'];		
		$this->upload_allowed_types = 'pdf';
		$this->upload_max_size = 1024*10;
		$this->upload_types = 'pdf';
		$pdf = $this->_uploaded(
			array(
				'id'		=> $id ,
				'input'		=> 'panduan_filename',
				'param'		=> array(
								'field' => 'panduan_filename', 
								'par'	=> array('panduan_id' => $id)
							)
		));
		
		$msg = !empty($pdf)? 'Pembuatan data Panduan sukses' : '';
		$msgtype = !empty($pdf)?'warning':'success';

		redirect($this->own_link."?msg=".urldecode($msg)."&type_msg=".$msgtype);
	}

}

