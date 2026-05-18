<?php
include_once(APPPATH."libraries/FrontController.php");
class Site extends FrontController {

    var $per_page       = 10;
    var $uri_segment    = 2;
    var $par_lang       = "";
    var $curr_menu      = "";

    function __construct()  
    {   
        parent::__construct();   
        $this->load->model("mdl_site","M");
        //$this->output->cache(60);
    }
	
	function migrasi_user(){
		
		
		
	}
    
    /*function rt_xxxx($limit=1500,$offset=0){
	    $start = 4777;
	    $end = 4896;
	    
	    $this->db->limit($limit,$offset);
	    $m = $this->db->get("sos_ttrans_claim")->result();
	    $hitung=0;
	    foreach($m as $k=>$v){
	    	$ndc1 = myDate($v->date_claim,"m",false);
	    	$ncd1 = myDate($v->createdDate,"m",false);
	    	$ncd = $v->createdDate;
	    	$ndc = $v->date_claim;
	    	
	    	if($ncd1=='05'){
		    	$ncd = myDate($v->createdDate,"Y-",false)."07-".myDate($v->createdDate,"d H:i:s",false);
	    	}
	    	if($ncd1=='06'){
		    	$ncd = myDate($v->createdDate,"Y-",false)."08-".myDate($v->createdDate,"d H:i:s",false);
	    	}
	    	
	    	if($ndc1=='05'){
		    	$ndc = myDate($v->date_claim,"Y-",false)."07-".myDate($v->date_claim,"d H:i:s",false);
	    	}
	    	if($ndc1=='06'){
		    	$ndc = myDate($v->date_claim,"Y-",false)."08-".myDate($v->date_claim,"d H:i:s",false);
	    	}
	    	
		    $this->db->update("sos_ttrans_claim",array(
		    	"memberId" => rand($start,$end),
		    	"relationship_type" => "Employee",
		    	"room_class" => "VIP",
		    	"date_claim" => $ndc,
		    	"createdDate" => $ncd
		    ),array(
		    	"claimId" => $v->claimId
		    ));
		    
		    $hitung++;
	    }
	    
	    die($hitung);
    }*/
    
    function index(){
        $data = array();
        $this->cur_menu = "";
        $this->meta_title   = 'Home';
        $this->_v('index',$data);
    }

    function contact(){
        $data = array();
        $this->cur_menu = "contact";
        $this->_v('contact',$data);
    }

    function about(){
        $data = array();
        $this->cur_menu = "about";
        $this->_v('about',$data);
    }

    function term(){
        $data = array();
        $this->cur_menu = "term";
        $this->_v('term',$data);
    }

    function history(){
        $data = array();
        $this->cur_menu = "history";
        $this->_v('history',$data);
    }

    function events(){
        $data = array();
        $this->cur_menu = "events";
        $this->_v('events/index',$data);
    }

    function articles(){
        $data = array();
        $this->cur_menu = "articles";
        $this->_v('articles',$data);
    }

    function login(){
        $data = array();
        $this->cur_menu = "login";
        $this->_v('login',$data);
    }

    function register(){
        $data = array();
        $this->cur_menu = "register";
        $this->_v('register',$data);
    }

}