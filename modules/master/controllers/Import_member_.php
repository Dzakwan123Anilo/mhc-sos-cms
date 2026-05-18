<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once(APPPATH."libraries/AdminController.php");
class Import_member extends AdminController {  
    function __construct()    
    {
        parent::__construct();    
        $this->_set_action();
        $this->_set_action(array("edit","delete"),"ITEM");
        $this->_set_title( 'Import Member' );
        $this->DATA->table="sos_import_log";
        $this->folder_view = "master/";
        $this->prefix_view = strtolower($this->_getClass());
        
        $this->breadcrumb[] = array(
                "title"     => "Import Member",
                "url"       => $this->own_link
            );

        $this->upload_path = cfg('upload_path_file')."/";

        $this->cat_search = array(
            ''              => 'All',
            'import_file'   => 'Nama File',
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
                                'switch_search'=> 1,
                                'date_end'  => '',
                                'status'    => '',
                                'sla'       => 'all',
                                'per_page'  => 20,
                                'order_by'  => 'import_id',
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
                "type"      => "member",
                "param"     => $this->cat_search
            );

        $this->data_table = $this->M->import_log($par_filter);
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
        $this->js_file = array(
                'import-member.js?id='.rand(1,1000)
            );
        $data = array();
        $id = _decrypt($this->input->get('_id'));

        $this->breadcrumb[] = array(
                "title"     => "Edit"
            );

        $id=dbClean(trim($id));
        
        if(trim($id)!=''){
            $this->data_form = $this->DATA->data_id(array(
                    'import_id'    => $id
                ));
            $data_row = array();
            if( trim($this->data_form->import_status) == 'UPLOAD'){
                $this->db->order_by('id');
                $data_row = $this->db->get_where("sos_member_tmp",array(
                        "import_id" => $id
                    ))->result_array();
            }
            $data['data'] = $data_row;
            $this->_v($this->folder_view.$this->prefix_view."_form",$data);
        }else{
            redirect($this->own_link);
        }
    }
    
    function delete(){
		$id=_decrypt(dbClean(trim($this->input->get('_id'))));
		if(trim($id) != ''){
			$o = $this->db->delete('sos_import_log', array("import_id" => idClean($id)));
						
		}
		redirect($this->own_link."/?msg=".urldecode('Delete data Import success')."&type_msg=success");
    }

    function read_excel_tmp($file_name=""){
        $path_file = $this->upload_path.$file_name;
        //debugCode($path_file);
        if( !file_exists($path_file) )
            die('file doesnt exist ');

        include APPPATH.'libraries/PHPExcel/IOFactory.php';

        //  Read your Excel workbook
        try {
            $inputFileType = PHPExcel_IOFactory::identify($path_file);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($path_file);
        } catch(Exception $e) {
            die('Error loading file "'.pathinfo($path_file,PATHINFO_BASENAME).'": '.$e->getMessage());
        }

        //  Get worksheet dimensions
        $sheet = $objPHPExcel->getSheet(0); 
        $rowData = array();
        $highestRow = $sheet->getHighestRow(); 
        $highestColumn = 'N';
		
        //  Loop through each row of the worksheet in turn
        for ($row = 2; $row <= $highestRow; $row++){ 
            $birthdate = DateTime::createFromFormat('d-M-Y', $sheet->getCellByColumnAndRow(5,$row)->getFormattedValue());

            $rowData[] = array(
                    'client_id' 	=> $sheet->getCellByColumnAndRow(0,$row)->getValue(),
                    'card_number' 	=> $sheet->getCellByColumnAndRow(1,$row)->getValue(),
                    'name' 			=> $sheet->getCellByColumnAndRow(2,$row)->getValue(),
                    'relationship' 	=> $sheet->getCellByColumnAndRow(3,$row)->getValue(),
                    'gender' 		=> $sheet->getCellByColumnAndRow(4,$row)->getValue(),
                    'birthdate' 	=> $birthdate->format('Y-m-d'),
                    'address' 		=> $sheet->getCellByColumnAndRow(6,$row)->getValue(),
                    'province' 		=> $sheet->getCellByColumnAndRow(7,$row)->getValue(),
                    'code' 			=> $sheet->getCellByColumnAndRow(8,$row)->getValue(),
                    'plan_type' 	=> $sheet->getCellByColumnAndRow(9,$row)->getValue(),
                    'room_class' 	=> $sheet->getCellByColumnAndRow(10,$row)->getValue(),
                    'dental_unit' 	=> $sheet->getCellByColumnAndRow(11,$row)->getValue(),
                    'emp_status' 	=> $sheet->getCellByColumnAndRow(12,$row)->getValue(),
                    'remark' 		=> $sheet->getCellByColumnAndRow(14,$row)->getValue(),
                    'group' 		=> $sheet->getCellByColumnAndRow(13,$row)->getValue()
                );
        }
        return $rowData;
    }

    function upload(){
        
        $site_id = $this->input->post('import_site_id');
        if($this->jCfg['user']['is_all'] != 1){
            $site_id = $this->jCfg['user']['site']['id'];
        }

        $data = array(
            'import_date'       => date("Y-m-d H:i:s"),
            'import_user'       => $this->jCfg['user']['name'],
            'import_status'     => 'UPLOAD',
            'import_site_id'    => $site_id,
        );

        $a = $this->_save_master( 
            $data,
            array(
                'import_id' => dbClean($_POST['import_id'])
            ),
            dbClean($_POST['import_id'])          
        );

        $id = $a['id'];
        $this->upload_types = "files";
        $this->upload_allowed_types = 'xls|xlsx';
        $upload = $this->_uploaded(
        array(
            'id'        => $id ,
            'input'     => 'import_file',
            'param'     => array(
                            'field' => 'import_file', 
                            'par'   => array('import_id' => $id)
                        )
        ));
        //debugCode($upload);
        // insert table temporary...
        $data_excel = $this->read_excel_tmp($upload['file_name']);
        foreach ((array)$data_excel as $key => $v) {
           $data_insert = array(
                "client_id" => isset($v['client_id'])?$v['client_id']:'',
                "card_number" => isset($v['card_number'])?$v['card_number']:'',
                "name" => isset($v['name'])?$v['name']:'',
                "relationship" => isset($v['relationship'])?$v['relationship']:'',
                "gender" => isset($v['gender'])?$v['gender']:'',
                "birthdate" => isset($v['birthdate'])?$v['birthdate']:'',
                "address" => isset($v['address'])?$v['address']:'',
                "province" => isset($v['province'])?$v['province']:'',
                "code" => isset($v['code'])?$v['code']:'',
                "plan_type" => isset($v['plan_type'])?$v['plan_type']:'',
                "room_class" => isset($v['room_class'])?$v['room_class']:'',
                "dental_unit" => isset($v['dental_unit'])?$v['dental_unit']:'',
                "emp_status" => isset($v['emp_status'])?$v['emp_status']:'',
                "remark" => isset($v['remark'])?$v['remark']:'',
                "group" => isset($v['group'])?$v['group']:'',
                "import_id" => $id,
                "status_import" => 0
            );
           $this->db->insert("sos_member_tmp",$data_insert);
        }

        redirect($this->own_link."/edit/?_id="._encrypt($id)."&msg=".urldecode('Upload data member success')."&type_msg=success");

    }

    function save(){

        $site_id = $this->input->post('group_site_id');
        if($this->jCfg['user']['is_all'] != 1){
            $site_id = $this->jCfg['user']['site']['id'];
        }

        redirect($this->own_link."?msg=".urldecode('Save data Import success')."&type_msg=success");
    }

}

