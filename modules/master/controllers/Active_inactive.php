<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."libraries/AdminController.php");
class Active_inactive extends AdminController {  
	function __construct()    
	{
		parent::__construct();    
		$this->_set_action();
		$this->_set_title( 'Set Member Active Inactive' );
		$this->DATA->table="sos_member";
		$this->folder_view = "master/";
		$this->prefix_view = strtolower($this->_getClass());

		$this->breadcrumb[] = array(
				"title"		=> "Set Member Active Inactive",
				"url"		=> $this->own_link
			);

		//load js..
		$this->js_plugins = array(
			'plugins/bootstrap/bootstrap-select.js'
		);
 		
 		$this->load->model("mdl_master","M");
	}

	function index(){
		$this->breadcrumb[] = array(
				"title"		=> "Set Member Active Inactive"
			);
		$data = array();
		
		$data['card_no'] = '';
		if(isset($_POST['activation_cardno']) && !empty($_POST['activation_cardno'])){ 	
			$cekNumber = check_number($this->input->post('activation_clientid'),$this->input->post('activation_cardno'),'ai');
			if( $cekNumber['status'] == true ){ 				
				$this->DATA->table="sos_member";
				$this->db->select('sos_member.*,sos_client.client_name,sos_ref_plan_type.plan_description');
				$this->db->join("sos_client","sos_client.client_id = sos_member.member_clientid");
				$this->db->join("sos_ref_plan_type","sos_ref_plan_type.plan_type = sos_member.member_plantype");
				$detail = $this->DATA->data_id(array(
						"member_clientid"	=> $this->input->post('activation_clientid'),
						"member_cardno"		=> $this->input->post('activation_cardno')
					));
					
				$this->data_form = $detail;
				$par_filter = array(
						"sos_member.member_employeeid"	=> $detail->member_employeeid
					);
		
				$this->db->select('sos_member.*,sos_client.client_name,sos_ref_plan_type.plan_description');
				$this->db->join("sos_client","sos_client.client_id = sos_member.member_clientid");
				$this->db->join("sos_ref_plan_type","sos_ref_plan_type.plan_type = sos_member.member_plantype");
				$data['data'] = $this->DATA->_getall($par_filter);
			}else{
				$data['card_no'] = $this->input->post('activation_cardno');
				$data['validate'] = $cekNumber;
			}
		}
		
		$this->_v($this->folder_view.$this->prefix_view,$data);
	}

	function activated(){

		$data = array(
			'member_activationdate'	=> date('Y-m-d'),
			'member_statuscard'		=> $this->input->post('member_statuscard')=='Active'?'InActive':'Active'
		);		

		$a = $this->_save_master( 
			$data,
			array(
				'member_clientid'	=> $this->input->post('activation_clientid'),
				'member_cardno'		=> $this->input->post('activation_cardno')
			)			
		);
		
		$this->DATA->table="sos_log_activate";
		$this->_save_master( 
			array(
				'log_cardno'	=> $this->input->post('activation_cardno'),
				'log_action'	=> $this->input->post('member_statuscard')=='Active'?'InActive':'Active',
				'log_ip'		=> $_SERVER['REMOTE_ADDR'] 
			), array()			
		);

		redirect($this->own_link."?msg=".urldecode('Set Member '.($this->input->post('member_statuscard')=='Active'?'InActive':'Active').' is success')."&type_msg=success");
	}
}

