<?php
class FrontController extends My_Controller{ 
  public $benchmark;

  public $hooks;
  public $config;

  public $log;

  public $utf8;
  public $uri;
  public $router;
  public $output;
  public $security;
  public $input;
  public $lang;
  public $load;
  public $db;
  public $email;
  public $session;
	var $meta_keyword 		= "";
	var $meta_title 		= "Home";
	var $meta_desc 			= "";
	var $current_page		= "";
	var $site_id			= "";
	var $img_socialmedia	= "";
	var $meta_image 		= "";
	
	function __construct(){  
		parent::__construct(); 		  

		$this->domain = cfg('domain');
   		$this->site_info = get_site_info($this->domain);
   		$this->site_id = $this->site_info['site']->site_id;
   		if( isset($this->site_info['template']->template_id) ){
	   		$this->jCfg['theme'] = 'web/'.$this->site_info['template']->template_name;
	   	}
		$this->load->helper("app");
		$this->load->helper("web");
		$this->img_socialmedia = "";
	}
	
	function _v($file,$data=array(),$single=true){

		$data["meta_desc"]		= $this->meta_desc;
		$data["meta_title"]		= $this->meta_title;
		$data["meta_keyword"] 	= $this->meta_keyword;
		$data["meta_image"] 	= $this->meta_image;
		
		if(!$single)
			$this->load->view($this->jCfg['theme'].'/header',$data);
		
		$this->load->view($this->jCfg['theme'].'/'.$file,$data);
		
		if(!$single)
			$this->load->view($this->jCfg['theme'].'/footer',$data);
	}
	
}