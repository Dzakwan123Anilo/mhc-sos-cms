<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."libraries/AdminController.php");
class Group_member extends AdminController {  
    function __construct()    
    {
        parent::__construct();    
        $this->_set_action();
        $this->_set_action(array("edit","delete"),"ITEM");
        $this->_set_title( 'Master Group Member' );
        $this->DATA->table="sos_group_member";
        $this->folder_view = "master/";
        $this->prefix_view = strtolower($this->_getClass());
        
        $this->breadcrumb[] = array(
                "title"     => "Group",
                "url"       => $this->own_link
            );

        $this->cat_search = array(
            ''              => 'All',
            'group_name'    => 'Nama Group',
            'site_name'     => 'Nama Site'
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
                                'date_end'  => '',
                                'status'    => '',
                                'sla'       => 'all',
                                'per_page'  => 20,
                                'order_by'  => 'group_id',
                                'order_dir' => 'DESC',
                                'colum'     => '',
                                'is_done'   => FALSE,
                                'keyword'   => ''
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

        $par_filter = array(
				"offset"	=> empty($page)?0:$page,
				"limit"		=> $this->per_page,
				"param"		=> $this->cat_search
			);

        $this->data_table = $this->M->member_group($par_filter);
        $data = $this->_data(array(
                "base_url"  => $this->own_link.'/index'
            ));
			
		$data['load'] = 1;
		$data['switch'] = 1;
        $this->_v($this->folder_view.$this->prefix_view,$data);
    }
    
    
    function add(){ 
        $this->breadcrumb[] = array(
                "title"     => "Add"
            );      
        $this->_v($this->folder_view.$this->prefix_view."_form",array());
    }
    
    function edit(){
        $data = array();
        $id = _decrypt($this->input->get('_id'));

        $this->breadcrumb[] = array(
                "title"     => "Edit"
            );

        $id=dbClean(trim($id));
        
        if(trim($id)!=''){
            $this->data_form = $this->DATA->data_id(array(
                    'group_id'    => $id
                ));
            
            //provider ....
            $this->db->order_by("provider_name","ASC");
            $provider = $this->db->get_where("sos_provider",array(
                    "provider_status"  => 1
                ))->result();

            //provider ......
            $this->db->order_by("time_add","ASC");
            $provider_assign_tmp = $this->db->get_where("sos_group_provider",array(
                    "gp_group_id"  => $id
                ))->result();
            $provider_assign = array();
            foreach ((array)$provider_assign_tmp as $p => $q) {
                $provider_assign[] = $q->gp_provider_id;
            }

            $data['provider_data'] = $provider;
            $data['provider_assigns'] = $provider_assign;
            $this->_v($this->folder_view.$this->prefix_view."_form",$data);
        }else{
            redirect($this->own_link);
        }
    }
    
    function delete(){
        $id = _decrypt($this->input->get('_id'));

        if(trim($id) != ''){
            $this->db->delete('sos_group_member', array('group_id' => idClean($id)));         
        }
        redirect($this->own_link."/?msg=".urldecode('Delete data Group success')."&type_msg=success");
    }

    function save(){

        $site_id = $this->input->post('group_site_id');
        if($this->jCfg['user']['is_all'] != 1){
            $site_id = $this->jCfg['user']['site']['id'];
        }

        $data = array(
            'group_name'              => $this->input->post('group_name'),
            'group_desc'              => $this->input->post('group_desc'),
            'group_site_id'           => $site_id,
            'group_status'            => $this->input->post('group_status')

        );      
        
        $a = $this->_save_master( 
            $data,
            array(
                'group_id' => dbClean($_POST['group_id'])
            ),
            dbClean($_POST['group_id'])           
        );

        //save provder assign..
        $this->db->delete("sos_group_provider",array(
                "gp_group_id" => $a['id']
            ));
        if( isset($_POST['provider_assigns']) && count($_POST['provider_assigns'])>0 ){
            foreach ((array)$this->input->post('provider_assigns') as $key => $value) {
                $this->db->insert("sos_group_provider",array(
                        "gp_provider_id"    => $value,
                        "gp_group_id"       => $a['id'],
                        "gp_site_id"        => $site_id,
                        "time_add"          => date("Y-m-d H:i:s"),
                        "user_add"          => $this->jCfg['user']['id']
                    ));
            }
        }    
    
        redirect($this->own_link."?msg=".urldecode('Save data group code success')."&type_msg=success");
    }

}

