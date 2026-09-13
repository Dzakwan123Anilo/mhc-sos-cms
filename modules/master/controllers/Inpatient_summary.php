<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."libraries/AdminController.php");
class Inpatient_summary extends AdminController {  
	function __construct()    
	{
		parent::__construct();
		$this->_set_title( 'In-Patient Summary' );
		$this->DATA->table="sos_inpatient_summary";
		$this->folder_view = "master/";
		$this->prefix_view = strtolower($this->_getClass());
		
		$this->breadcrumb[] = array(
				"title"		=> "In-Patient Summary",
				"url"		=> $this->own_link
			);

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
	}
	
	function index(){			
		$this->data_form = $this->db->get($this->DATA->table)->row();
		
		$this->db->select("member_cardno, member_name, date_claim, room_class, createdDate, provider_name, service_description");
		$this->db->join("sos_member","sos_ttrans_claim.memberId = sos_member.member_id");
		$this->db->join("sos_provider","sos_ttrans_claim.providerId = sos_provider.provider_id");
		$this->db->join("sos_ref_service_type","sos_ttrans_claim.servicetypeId = sos_ref_service_type.service_id AND sos_member.member_clientid = sos_ref_service_type.service_clientid");
		$this->db->where("servicetypeId = 1 and ( date_claim >= '".date('Y-m-d')." 00:00:00' AND date_claim <= '".date('Y-m-d')." 23:59:00' )");
		$this->db->order_by("member_name, date_claim","asc");
		$data = $this->db->get("sos_ttrans_claim")->result();
			
		$this->_v($this->folder_view.$this->prefix_view,array('data' => $data));
	}

	function save(){
		$data = array(
			'inpatient_email'		=> $this->input->post('inpatient_email'),
			'inpatient_title'		=> $this->input->post('inpatient_title'),
			'inpatient_schedule'	=> date('Y-m-d H:i:s', strtotime($this->input->post('inpatient_date').' '.$this->input->post('inpatient_time')))
		);		
		
		$a = $this->_save_master( 
			$data,
			array(
				'inpatient_id' => dbClean($_POST['inpatient_id'])
			),
			dbClean($_POST['inpatient_id'])			
		);

		redirect($this->own_link."?msg=".urldecode('Save data In-Patient Summary success')."&type_msg=success");
	}

}

