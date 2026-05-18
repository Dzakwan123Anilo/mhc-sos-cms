<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."libraries/AdminController.php");
class GeneralSetting extends AdminController {   
    function __construct()    
    {
        parent::__construct();    
        $this->_set_action();
        $this->_set_action(array("edit"),"ITEM");
        $this->_set_title( 'General Setting Community' );
        $this->DATA->table="app_site";
        $this->folder_view = "setting/";
        $this->prefix_view = strtolower($this->_getClass());
        $this->breadcrumb[] = array(
                "title"     => "Site",
                "url"       => $this->own_link
            );

        $this->cat_search = array(); 

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
        
        $this->load->model("Mdl_setting","M");
        $this->is_dashboard = false;
    }

    function _reset(){
        $this->jCfg['search'] = array(
                                'class'     => $this->_getClass(),
                                'date_start'=> '',
                                'date_end'  => '',
                                'status'    => '',
                                'sla'       => 'all',
                                'per_page'  => 20,
                                'order_by'  => 'site_id',
                                'order_dir' => 'DESC',
                                'colum'     => '',
                                'is_done'   => FALSE,
                                'keyword'   => ''
                            );
        $this->_releaseSession();
    }

    function index(){
        $this->breadcrumb[] = array(
                "title"     => "Detail Community"
            );
        $data = array();

        $id = $this->jCfg['user']['site']['id'];
        if(trim($id)!=''){
            $this->data_form = $this->DATA->data_id(array(
                    'site_id'   => $id
                ));
                
            $this->db->order_by("sos_name","ASC");
            $sosmedx = $this->db->get_where("app_site_socialmedia",array(
                            "sos_site_id"  => $this->data_form->site_id
                        ))->result();
            $sosmed = array();
            foreach ((array)$sosmedx as $x => $y) {
                $sosmed[$y->sos_type] = $y->sos_url;
            }

            $template = $this->db->get_where("app_site_template",array(
                            "template_site_id"  => $this->data_form->site_id
                        ))->row();
            
            $this->_v($this->folder_view.$this->prefix_view,array(
                    "template"  => $template,
                    "sosmed"    => $sosmed
                    
                ));
        }else{
            die('Access Denied');
        }
    }
    
    function edit(){
        $this->breadcrumb[] = array(
                "title"     => "Edit"
            );    
        $id = $id = $this->jCfg['user']['site']['id'];  
        if(trim($id)!=''){
            $this->data_form = $this->DATA->data_id(array(
                    'site_id'   => $id
                ));
                
            $this->db->order_by("sos_name","ASC");
            $sosmedx = $this->db->get_where("app_site_socialmedia",array(
                            "sos_site_id"  => $this->data_form->site_id
                        ))->result();
            $sosmed = array();
            foreach ((array)$sosmedx as $x => $y) {
                $sosmed[$y->sos_type] = $y->sos_url;
            }

            $template = $this->db->get_where("app_site_template",array(
                            "template_site_id"  => $this->data_form->site_id
                        ))->row();
            
            $this->_v($this->folder_view.$this->prefix_view."_form",array(
                    "template"  => $template,
                    "sosmed"    => $sosmed
                    
                ));
        }else{
            redirect($this->own_link);
        }
    }
    

    function save(){

        $data = array(
            'site_name'                 => $this->input->post('site_name'),
            'site_slogan'               => $this->input->post('site_slogan'),
            'site_description'          => $this->input->post('site_description'),
            'site_cp_address'           => $this->input->post('site_cp_address'),
            'site_cp_name'              => $this->input->post('site_cp_name'),
            'site_cp_phone'             => $this->input->post('site_cp_phone'),
            'site_cp_email'             => $this->input->post('site_cp_email'),
            'site_meta_title'           => $this->input->post('site_meta_title'),
            'site_meta_keyword'         => $this->input->post('site_meta_keyword'),
            'site_meta_description'     => $this->input->post('site_meta_description'),
            'site_ga'                   => $this->input->post('site_ga')
        );  

        $data2 = array(
            'template_bg_head_show'     => isset($_POST['template_bg_head_show'])?1:0,
            'template_bg_show'          => isset($_POST['template_bg_show'])?1:0,
            'template_name'             => $this->input->post('template_name'),
            'template_color'            => $this->input->post('template_color')
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
            'id'        => $id ,
            'input'     => 'site_logo',
            'param'     => array(
                            'field' => 'site_logo', 
                            'par'   => array('site_id' => $id)
                        )
        ));

        $this->_uploaded(
        array(
            'id'        => $id ,
            'input'     => 'site_maskot',
            'param'     => array(
                            'field' => 'site_maskot', 
                            'par'   => array('site_id' => $id)
                        )
        ));

        foreach ((array)$this->input->post('sosmed_item') as $si => $sv) {
            $cek = $this->db->get_where("app_site_socialmedia",array(
                    "sos_type" => $si,
                    "sos_site_id" => $id
                ))->num_rows();
            if( $cek == 0 ){
                $this->db->insert("app_site_socialmedia",array(
                        "sos_type" => $si,
                        "sos_url"  => $sv,
                        "time_update" => date("Y-m-d H:i:s"),
                        "user_update" => $this->jCfg['user']['id'],
                        "sos_site_id" => $id,
                    ));
            }else{
                $this->db->update("app_site_socialmedia",array(
                        "sos_url"  => $sv
                    ),array(
                        "sos_type" => $si,
                        "time_update" => date("Y-m-d H:i:s"),
                        "user_update" => $this->jCfg['user']['id'],
                        "sos_site_id" => $id,
                    ));
            }         
        }

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
            'id'        => $id2,
            'input'     => 'template_bg_head',
            'param'     => array(
                            'field' => 'template_bg_head', 
                            'par'   => array('template_id' => $id2)
                        )
        ));
        $this->_uploaded(
        array(
            'id'        => $id2,
            'input'     => 'template_bg',
            'param'     => array(
                            'field' => 'template_bg', 
                            'par'   => array('template_id' => $id2)
                        )
        ));

        redirect($this->own_link."?msg=".urldecode('Save data Data Site success')."&type_msg=success");
    }

}

