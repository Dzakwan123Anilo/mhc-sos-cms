<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."libraries/AdminController.php");
class Rank extends AdminController {  
    function __construct()    
    {
        parent::__construct();    
        $this->_set_action();
        $this->_set_action(array("edit","delete"),"ITEM");
        $this->_set_title( 'Setup Rank' );
        $this->DATA->table="web_rank";
        $this->folder_view = "setting/";
        $this->prefix_view = strtolower($this->_getClass());

        $this->upload_path=cfg('upload_path_rank');
        
        $this->breadcrumb[] = array(
                "title"     => "Rank",
                "url"       => $this->own_link
            );

        $this->cat_search = array(
            ''              => 'All',
            'rank_name'     => 'Name'
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
        
        $this->load->model("mdl_setting","M");
    }

    function _reset(){
        $this->jCfg['search'] = array(
                                'class'     => $this->_getClass(),
                                'date_start'=> '',
                                'date_end'  => '',
                                'status'    => '',
                                'sla'       => 'all',
                                'per_page'  => 20,
                                'order_by'  => 'rank_id',
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
                "offset"    => $this->uri->segment($this->uri_segment),
                "limit"     => $this->per_page,
                "param"     => $this->cat_search
            );

        $this->data_table = $this->M->rank($par_filter);
        $data = $this->_data(array(
                "base_url"  => $this->own_link.'/index'
            ));
        $this->_v($this->folder_view.$this->prefix_view,$data);
    }
    
    
    function add(){ 
        $this->breadcrumb[] = array(
                "title"     => "Add"
            );      
        $this->_v($this->folder_view.$this->prefix_view."_form",array());
    }
    
    function edit(){
        $id = _decrypt($this->input->get('_id'));
        $this->breadcrumb[] = array(
                "title"     => "Edit"
            );

        $id=dbClean(trim($id));
        
        if(trim($id)!=''){
            $this->data_form = $this->DATA->data_id(array(
                    'rank_id'  => $id
                ));
                
            $this->_v($this->folder_view.$this->prefix_view."_form",array());
        }else{
            redirect($this->own_link);
        }
    }
    
    function delete(){
        $id = _decrypt($this->input->get('_id'));   
        if(trim($id) != ''){
            $o = $this->db->delete("web_rank",
                array("rank_id"    => idClean($id))
            );
                        
        }
        redirect($this->own_link."/?msg=".urldecode('Delete data Rank success')."&type_msg=success");
    }

    function save(){
        
        $site_id = $this->input->post('rank_site_id');
        if($this->jCfg['user']['is_all'] != 1){
            $site_id = $this->jCfg['user']['site']['id'];
        }

        $data = array(
            'rank_name'               => $this->input->post('rank_name'),
            'rank_min_post'           => $this->input->post('rank_min_post'),
            'rank_min_likes'          => $this->input->post('rank_min_likes'),
            'rank_min_minutes'        => $this->input->post('rank_min_minutes'),
            'rank_site_id'            => $site_id,
            'rank_status'             => $this->input->post('rank_status')
        );      
        
        $a = $this->_save_master( 
            $data,
            array(
                'rank_id' => dbClean($_POST['rank_id'])
            ),
            dbClean($_POST['rank_id'])         
        );
    
        
        $id = $a['id'];
        $this->_uploaded(
        array(
            'id'        => $id ,
            'input'     => 'rank_icon',
            'param'     => array(
                            'field' => 'rank_icon', 
                            'par'   => array('rank_id' => $id)
                        )
        ));

        redirect($this->own_link."?msg=".urldecode('Save data Rank success')."&type_msg=success");
    }

}

