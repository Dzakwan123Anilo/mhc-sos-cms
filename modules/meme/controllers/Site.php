<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."libraries/AdminController.php");
class Site extends AdminController {   
	function __construct()    
	{
		parent::__construct();    
		$this->_set_action();
		$this->_set_action(array("edit","delete"),"ITEM");
		$this->_set_title( 'Master Daftar Klub' );
		$this->DATA->table="app_site";
		$this->folder_view = "meme/";
		$this->prefix_view = strtolower($this->_getClass());
		$this->breadcrumb[] = array(
				"title"		=> "Site",
				"url"		=> $this->own_link
			);

		$this->cat_search = array(
			''				=> 'All',
			'site_name'		=> 'Nama Klub',
			'site_slogan'	=> 'Slogan Klub',
			'site_url'		=> 'Alamat URL Klub',
			'site_cp_name'	=> 'Contact Person Klub'
		); 

		if(!isset($this->jCfg['search']['class']) || $this->jCfg['search']['class'] != $this->_getClass()){
			$this->_reset();
		}

		//load js..
		$this->js_plugins = array(
			'plugins/bootstrap/bootstrap-datepicker.js',
			'plugins/bootstrap/bootstrap-file-input.js',
			'plugins/bootstrap/bootstrap-select.js',
			'plugins/bootstrap/bootstrap-colorpicker.js'
		);
 		
 		$this->load->model("mdl_meme","M");
	}

	function _reset(){
		$this->jCfg['search'] = array(
								'class'		=> $this->_getClass(),
								'date_start'=> '',
								'date_end'	=> '',
								'status'	=> '',
								'sla'		=> 'all',
								'per_page'	=> 20,
								'order_by'  => 'site_id',
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

		if($this->input->post('btn_search')){
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
		}

		$this->per_page = $this->jCfg['search']['per_page'];

		$par_filter = array(
				"offset"	=> $this->uri->segment($this->uri_segment),
				"limit"		=> $this->per_page,
				"param"		=> $this->cat_search
			);

		$this->data_table = $this->M->site($par_filter);
		$data = $this->_data(array(
				"base_url"	=> $this->own_link.'/index'
			));
		$this->_v($this->folder_view.$this->prefix_view,$data);
	}
	
	
	function add(){	
		$this->breadcrumb[] = array(
				"title"		=> "Add"
			);		
		$this->_v($this->folder_view.$this->prefix_view."_form",array());
	}
	
	function edit(){
		$id = _decrypt($this->input->get('_id'));
		$this->breadcrumb[] = array(
				"title"		=> "Edit"
			);		
		if(trim($id)!=''){
			$this->data_form = $this->DATA->data_id(array(
					'site_id'	=> $id
				));
				
			$this->_v($this->folder_view.$this->prefix_view."_form",array(
					"val2" => $this->db->get_where("app_site_template",array(
							"template_site_id"	=> $this->data_form->site_id
						))->row()
				));
		}else{
			redirect($this->own_link);
		}
	}
	
	function delete(){
		$id = _decrypt($this->input->get('_id'));		
		if(trim($id) != ''){
			$this->db->delete('app_site', array('site_id' => idClean($id))); 		
			$this->db->delete('app_site_template', array('template_site_id' => idClean($id))); 			
		}
		redirect($this->own_link."/?msg=".urldecode('Delete data Data Site success')."&type_msg=success");
	}

	function save(){

		
		$data = array(
			'site_name'				=> $this->input->post('site_name'),
			'site_slogan'			=> $this->input->post('site_slogan'),
			'site_url'				=> $this->input->post('site_url'),
			'site_cp_name'			=> $this->input->post('site_cp_name'),
			'site_cp_phone'			=> $this->input->post('site_cp_phone'),
			'site_start_date'		=> $this->input->post('site_start_date'),
			'site_end_date'			=> $this->input->post('site_end_date'),
			'site_parent'			=> $this->input->post('site_parent'),
			'site_type'				=> $this->input->post('site_type'),
			'site_status'			=> $this->input->post('site_status')
		);	

		$data2 = array(
			'template_bg_head_show'		=> isset($_POST['template_bg_head_show'])?1:0,
			'template_bg_show'			=> isset($_POST['template_bg_show'])?1:0,
			'template_name'				=> $this->input->post('template_name'),
			'template_color'			=> $this->input->post('template_color')
		);		
		
		$a = $this->_save_master( 
			$data,
			array(
				'site_id' => $this->input->post('site_id')
			),
			$this->input->post('site_id')
		);

		
		$this->upload_path = cfg('upload_path_klub');
		$id = $a['id'];
		$data2['template_site_id'] = $id;
		$this->_uploaded(
		array(
			'id'		=> $id ,
			'input'		=> 'site_logo',
			'param'		=> array(
							'field' => 'site_logo', 
							'par'	=> array('site_id' => $id)
						)
		));

		$this->DATA->table="app_site_template";
		$a2 = $this->_save_master( 
			$data2,
			array(
				'template_id' => $this->input->post('template_id')
			),
			$this->input->post('template_id')			
		);
		

		$this->upload_path=cfg('upload_path_template');
		$id2 = $a2['id'];
		$this->_uploaded(
		array(
			'id'		=> $id2,
			'input'		=> 'template_bg_head',
			'param'		=> array(
							'field' => 'template_bg_head', 
							'par'	=> array('template_id' => $id2)
						)
		));
		$this->_uploaded(
		array(
			'id'		=> $id2,
			'input'		=> 'template_bg',
			'param'		=> array(
							'field' => 'template_bg', 
							'par'	=> array('template_id' => $id2)
						)
		));

		redirect($this->own_link."?msg=".urldecode('Save data Data Site success')."&type_msg=success");
	}

}

