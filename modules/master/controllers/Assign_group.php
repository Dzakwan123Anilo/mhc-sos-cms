<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."libraries/AdminController.php");
class Assign_group extends AdminController {  
    function __construct()    
    {
        parent::__construct();    
        $this->_set_action();
        $this->_set_action(array("edit","delete"),"ITEM");
        $this->_set_title( 'Member - Assign Group' );
        $this->DATA->table="sos_member";
        $this->folder_view = "master/";
        $this->prefix_view = strtolower($this->_getClass());

        $this->breadcrumb[] = array(
                "title"     => "Member - Assign Group",
                "url"       => $this->own_link
            );

        $this->cat_search = array(
            ''                  => 'All',
            'member_cardno'     => 'No. Kartu',
            'member_name'       => 'Nama Member',
            'member_email'      => 'Email'
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
                                'class'     => $this->_getClass(),
                                'date_start'=> '',
                                'switch_search' => 1,
                                'date_end'  => '',
                                'status'    => '',
                                'sla'       => 'all',
                                'per_page'  => 20,
                                'order_by'  => 'member_id',
                                'order_dir' => 'DESC',
                                'colum'     => '',
                                'is_done'   => FALSE,
                                'keyword'   => ''
                            );
        
        $this->jCfg['member_search'] = array(
                                'clientid'      => '',
                                'cardno'        => '',
                                'name'          => '',
                                'relationship'  => '',
                                'sex'           => '',
                                'birthdate'     => '',
                                'phone'         => '',
                                'email'         => '',
                                'region'        => '',
                                'location'      => '',
                                'plantype'      => '',
                                'employment'    => '',
                                'cardstatus'    => ''
                            );
        $this->_releaseSession();
    }

    function index(){
        $this->breadcrumb[] = array(
                "title"     => "List"
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
        $this->jCfg['search']['order_by'] = 'sos_member.member_cardno,sos_member.member_name';
        $this->jCfg['search']['order_dir'] = 'ASC';
        $par_filter = array(
				"offset"	=> empty($page)?0:$page,
				"limit"		=> $this->per_page,
				"param"		=> $this->cat_search
			);

        $this->data_table = $this->M->member($par_filter);
        $data = $this->_data(array(
				"page"		=> empty($page)?0:$page,
                "base_url"  => $this->own_link.'/index'
            ));
			
		$data['load'] = 1;
		$data['switch'] = 1;
        $this->_v($this->folder_view.$this->prefix_view,$data);
    }

    
    function edit(){
        $id = _decrypt($this->input->get('_id'));
        $this->breadcrumb[] = array(
                "title"     => "Edit"
            );

        $id=dbClean(trim($id));
        
        if(trim($id)!=''){
            $this->db->select('sos_member.*,sos_client.client_name');
            $this->db->join("sos_client","sos_client.client_id = sos_member.member_clientid");
            $detail = $this->DATA->data_id(array(
                    'member_id' => $id
                ));
                
            $this->data_form = $detail;
            $par_filter = array(
                    "member_employeeid" => $detail->member_employeeid
                );
        
            //group member...
            $this->db->order_by("provider_group","ASC");
            $group = $this->db->get_where("sos_tref_provider_group",array(
                    "status_data"  => 1
                ))->result();

            //group member assign
            $this->db->order_by("memberproviderId","DESC");
            $group_assign_tmp = $this->db->get_where("sos_tmas_member_provider",array(
                    "memberId"  => $id
                ))->result();
            $group_assign = array();
            foreach ((array)$group_assign_tmp as $p => $q) {
                $group_assign[] = $q->providergroupId;
            }

            $this->data_table = $this->M->member_family($par_filter);
            $data = $this->_data(array(
                    "base_url"  => $this->own_link.'/index'
                ));
            $data['group_member'] = $group;
            $data['group_assign'] = $group_assign;
            $this->_v($this->folder_view.$this->prefix_view."_form",$data);
        }else{
            redirect($this->own_link);
        }
    }


    function save(){

        $member_id = $this->input->post('member_id');
        if( trim($member_id)=="" ){
            die('Access Denied');
        }

        $site_id = $this->input->post('member_site_id');
        if($this->jCfg['user']['is_all'] != 1){
            $site_id = $this->jCfg['user']['site']['id'];
        }
        //delete current data..
        $this->db->delete("sos_tmas_member_provider",array(
                "memberId" => $member_id
            ));
        if( isset($_POST['group_assign']) && count($_POST['group_assign'])>0 ){
            foreach ((array)$this->input->post('group_assign') as $key => $value) {
                $this->db->insert("sos_tmas_member_provider",array(
                        "providergroupId"   => $value,
                        "memberId"  		=> $member_id,
                        "createdDate"       => date("Y-m-d H:i:s"),
                        "createdBy"         => $this->jCfg['user']['id']
                    ));
            }
        }    

        redirect($this->own_link."?msg=".urldecode('Save data Member success')."&type_msg=success");
    }

}

