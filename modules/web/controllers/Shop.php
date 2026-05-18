<?php
include_once(APPPATH."libraries/FrontController.php");
class Shop extends FrontController {

    var $per_page       = 10;
    var $uri_segment    = 2;
    var $par_lang       = "";
    var $curr_menu      = "";

    function __construct()  
    {   
        parent::__construct();   
        $this->load->model("mdl_site","M");
    }
    
    
    function index(){
        $data = array();
        $this->cur_menu = "";
        $this->meta_title   = 'Marketplace';
        $this->_v('shop/index',$data);
    }

}