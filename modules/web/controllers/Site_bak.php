<?php
include_once(APPPATH."libraries/FrontController.php");
class Site extends FrontController {

	var $per_page 		= 10;
	var $uri_segment 	= 2;
	var $par_lang 		= "";
	var $curr_menu 		= "";

	function __construct()  
	{	
		parent::__construct(); 	 
		$this->load->model("mdl_site","M");
		//$this->output->cache(60);
	}
	
	
	function index(){
		$data = array();
		$this->cur_menu = "";
		$this->meta_title 	= 'Home';
		$this->_v('index',$data);
	}

	function comingsoon(){
		$data = array();
		$this->jCfg['theme'] = 'web/comingsoon';
		$this->_v('index',$data);
	}
	
	function comingsoon_ahc(){
		$data = array();
		$this->jCfg['theme'] = 'web/comingsoon-ahc';
		$this->_v('index',$data);
	}

	
	function cal(){
		$data = array(
			'year' => $this->uri->segment(2),
			'month' => $this->uri->segment(3)
		);

		// Creating template for table
		$prefs['template'] = '
		{table_open}<table cellpadding="1" cellspacing="2">{/table_open}
		{heading_row_start}<tr>{/heading_row_start}
		{heading_previous_cell}<th class="prev_sign"><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
		{heading_title_cell}<th colspan="{colspan}">{heading}</th>{/heading_title_cell}
		{heading_next_cell}<th class="next_sign"><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}
		{heading_row_end}</tr>{/heading_row_end}
		//Deciding where to week row start
		{week_row_start}<tr class="week_name" >{/week_row_start}
		//Deciding  week day cell and  week days
		{week_day_cell}<td >{week_day}</td>{/week_day_cell}
		//week row end
		{week_row_end}</tr>{/week_row_end}
		{cal_row_start}<tr>{/cal_row_start}
		{cal_cell_start}<td>{/cal_cell_start}
		{cal_cell_content}<a href="{content}">{day}</a>{/cal_cell_content}
		{cal_cell_content_today}<div class="highlight"><a href="{content}">{day}</a></div>{/cal_cell_content_today}
		{cal_cell_no_content}{day}{/cal_cell_no_content}
		{cal_cell_no_content_today}<div class="highlight">{day}</div>{/cal_cell_no_content_today}
		{cal_cell_blank}&nbsp;{/cal_cell_blank}
		{cal_cell_end}</td>{/cal_cell_end}
		{cal_row_end}</tr>{/cal_row_end}
		{table_close}</table>{/table_close}
		';

		$prefs['day_type'] 		 = 'short';
		$prefs['show_next_prev'] = true;
		$prefs['next_prev_url']  = site_url('cal');

		$this->load->library('calendar', $prefs);
		$this->_v('cal',$data);
	}
	function  rss($domain=""){
		$data = array();
		$this->load->helper('xml');
		$this->load->helper('text');
		
		$par_filter = array(
				"offset"	=> 0,
				"limit"		=> 20
			);
		
		if( trim($domain)!= "" ){
			$par_filter['domain'] = $domain;
		}
		
		$data_content = $this->M->content($par_filter);

		$data['feed_name'] = $this->domain;
		$data['encoding'] = 'utf-8';
        $data['feed_url'] = site_url('rss');
        $data['page_description'] = 'Honda Community';
        $data['page_language'] = 'en-en';
        $data['creator_email'] = 'humaedi@aplika.co.id';
        $data['posts'] = $data_content['data'];  
        header("Content-Type: application/rss+xml");

		$this->_v('rss',$data);
	}

	function news(){
		$data = array();
		$this->meta_title = "Berita Honda Community";
		$this->meta_desc = "Berita dari para komunitas honda di indonesia";
		$this->meta_keyword = "berita, komunitas honda, informasi honda, informasi komunitas";
		
		$this->per_page = 16;

		$par_filter = array(
				"offset"	=> (int)$this->uri->segment($this->uri_segment),
				"limit"		=> $this->per_page,
				"site_id"	=> $this->site_id
			);

		$this->data_table = $this->M->content($par_filter);
		$data = $this->_data_web(array(
				"base_url"	=> site_url('news')
			));
		$this->_v('news',$data);
	}
	

	function static_content($type=""){
		$data = array();
		$type = trim($type)==""?"xx":$type;
		$this->cur_menu = $type;
		$data['item'] = $this->db->get_where("static_content",array(
					"content_site_id"	=> $this->site_info['site']['site_id'],
					"content_status"	=> 1,
					"content_type"		=> $type
				))->row_array();
		$data['judul_top'] = strtoupper($type);
		
		$judul_item = isset($data['item']['judul'])?$data['item']['judul']:'';
		$this->meta_title = ucwords($type)." ".$judul_item;
		$this->meta_desc = ucwords($type)." ".$judul_item;
		$this->meta_keyword = "berita, komunitas honda, informasi honda, informasi komunitas,".str_replace(" ",",",$judul_item);
		
		$this->_v('static_content',$data);
	}

	function read($cat="",$content_id="",$cat_id=""){
		$this->news_details($content_id,$cat_id);
	}

	function news_details($content_id="",$cat_id=""){
		$this->cur_menu = "news";
		if( trim($content_id)!="" && trim($cat_id)!="" ){
			$detail = $this->M->content(array(
					"id"	=> $content_id
				));

			if( count($detail['data']) > 0){
				$data['m'] = $detail['data'][0];
				
				$this->img_socialmedia = get_new_image(array(
					"url" 		=> cfg('upload_path_content')."/".$data['m']->content_image,
					"url_klub" 	=> cfg('upload_path_content')."/".$data['m']->content_image,
					"folder"	=> "content",
					"width"		=> 650,
					"height"	=> 366,
					"site_id"	=> $data['m']->content_site_id
				),true);
				
				$this->db->query("
						UPDATE 
							web_content 
						SET content_read = content_read + 1
						WHERE 
						content_id = '".$content_id."'
					");
				$this->_v('details',$data);
			}
		}
	}

	function anggota_klub_detail($angg_id=""){
		$this->cur_menu = "news";
		if( trim($angg_id)!="" ){
			$detail = $this->M->anggota_klub(array(
					"angg_id"	=> $angg_id,
				));
			
			if( count($detail['data']) > 0){
				$data['klub'] = $detail['data'][0];
				$this->_v('about_klub',$data);
			}
		}
	}

	function category($slug=""){
		$data = array();
		$this->curr_menu = $slug;
		$this->meta_title = "category ".$slug;

		$this->per_page = 5;
		$this->uri_segment = 3;

		$par_filter = array(
				"offset"	=> $this->uri->segment($this->uri_segment),
				"limit"		=> $this->per_page,
				"cat_slug"	=> $slug
			);

		$this->data_table = $this->M->content($par_filter);
		$data = $this->_data(array(
				"base_url"	=> site_url().'/category/'.$slug
			));
		$data['cat_slug'] = $slug;
		$this->_v('kanal',$data);
	}

	function search(){
		$data = array();
		$this->cur_menu = "search";
		$this->per_page = 16;
		$q = $this->input->post("keyword");

		if(!empty($q)){
			$this->session->set_userdata('keyword', $q);
			$sess_keyword = $this->session->userdata('keyword');
			$keyword = $sess_keyword;
			$data["page"] = "search";
			$data["keyword"] = $sess_keyword;
		}else{
			$sess_keyword = $this->session->userdata('keyword');
			if(!empty($sess_keyword)){
				$keyword = $sess_keyword;
			}else{
				$keyword = "";
			}
			$data["page"] = "";
			$data["keyword"] = $keyword;
		}

		$par_filter = array(
				"offset"	=> (int)$this->uri->segment($this->uri_segment),
				"limit"		=> $this->per_page,
				"q"			=> $keyword,
				"site_id"	=> $this->site_id
			);

		$this->data_table = $this->M->content($par_filter);
		$data = $this->_data_web(array(
				"base_url"	=> site_url('search'),
			));
		$judul_item = "Pencarian berita dengan kata kunci '".$keyword."'";
		$headline = "Pencarian berita dengan kata kunci '".$keyword."'";
			
		$this->meta_title = $judul_item;
		$this->meta_desc = $headline;
		$this->meta_keyword = "honda, informasi honda, informasi komunitas,".str_replace(" ",",",$judul_item);	
			
		$this->_v("search",$data);
	}
	
	function contact(){
		$data = array();
		$this->_v('contact',$data);
	}

	function about(){
		$data = array();
		$this->_v('about',$data);
	}

	function sejarah(){
		$data = array();
		$this->_v('sejarah',$data);
	}

	function photo(){
		$data = array();
		$this->cur_menu = "photo";

		$this->per_page = 16;
		$par_filter = array(
				"offset"	=> $this->uri->segment($this->uri_segment),
				"limit"		=> $this->per_page,
				"site_id"	=> $this->site_id
			);

		$this->data_table = $this->M->photo($par_filter);
		$data = $this->_data_web(array(
				"base_url"	=> site_url('photo')
			));
		
		$judul_item 		= "Photo Gallery HondaCBRcommunity";
		$headline 			= "disini banyak photo gallery kegiatan komunitas-komunitas honda indonesia";	
		$this->meta_title 	= $judul_item;
		$this->meta_desc 	= $headline;
		$this->meta_keyword = "honda, photo, kegiatan, komunitas,".str_replace(" ",",",$judul_item);	


		$this->_v('photo',$data);
	}
	
	function anggota_klub(){
		$data = array();
		$this->cur_menu = "anggota-klub";

		$this->per_page = 16;
		$par_filter = array(
				"offset"	=> $this->uri->segment($this->uri_segment),
				"limit"		=> $this->per_page,
				"site_id"	=> $this->site_id
			);

		$this->data_table = $this->M->anggota_klub($par_filter);
		$data = $this->_data_web(array(
				"base_url"	=> site_url('anggota-klub')
			));
		
		$judul_item 		= "Anggota Klub Hondacbrcommunity";
		$headline 			= "disini banyak photo gallery kegiatan komunitas-komunitas honda indonesia";	
		$this->meta_title 	= $judul_item; 
		$this->meta_desc 	= $headline;
		$this->meta_keyword = "honda, photo, kegiatan, komunitas,".str_replace(" ",",",$judul_item);	


		$this->_v('anggota_klub',$data);
	}

	function photo_detail($photo_id="",$photo_slug=""){
		$this->cur_menu = "gallery";
		$this->current_page ="photo-detail";

		if( trim($photo_id)!="" ){
			$detail = $this->M->photo(array(
					"id"	=> $photo_id
				));

			if( count($detail['data']) > 0){
				$data['detail'] = $detail['data'][0];

				$data['items'] = $this->db->get_where("web_photo_gallery",array(
					"gal_photo_id"	=> $data['detail']->photo_id
				))->result();
				
				$judul_item 		= "Photo ".$data['detail']->photo_title;
				$headline 			= "disini banyak photo gallery kegiatan komunitas-komunitas honda indonesia ".$data['detail']->photo_title;	
				$this->meta_title 	= $judul_item;
				$this->meta_desc 	= $headline;
				$this->meta_keyword = "honda, photo, kegiatan, komunitas,".str_replace(" ",",",$judul_item);	
				
				$this->_v('photo_detail',$data);
			}
		}
	}

	function video(){
		$data = array();
		$this->cur_menu = "video";

		$this->per_page = 16;
		$par_filter = array(
				"offset"	=> $this->uri->segment($this->uri_segment),
				"limit"		=> $this->per_page,
				"site_id"	=> $this->site_id
			);

		$this->data_table = $this->M->video($par_filter);
		$data = $this->_data_web(array(
				"base_url"	=> site_url('video')
			));
		
		$judul_item 		= "Video HondaCBRcommunity";
		$headline 			= "disini banyak video kegiatan komunitas-komunitas honda indonesia";	
		$this->meta_title 	= $judul_item;
		$this->meta_desc 	= $headline;
		$this->meta_keyword = "honda, photo, video, kegiatan, komunitas,".str_replace(" ",",",$judul_item);
		
		$this->_v('video',$data);
	}

	function video_detail($video_id="",$video_slug=""){
		$this->cur_menu = "video";
		if( trim($video_id)!="" && trim($video_slug)!="" ){
			$detail = $this->M->video(array(
					"id"	=> $video_id
				));

			if( count($detail['data']) > 0){
				$data['detail'] = $detail['data'][0]; 
				$judul_item = "Photo ".$data['detail']->video_title;
				$headline = "disini banyak video kegiatan komunitas-komunitas honda indonesia ".$data['detail']->video_title;				
				$this->meta_title = $judul_item;
				$this->meta_desc = $headline; 
				$this->meta_keyword = "honda, photo, kegiatan, komunitas,".str_replace(" ",",",$judul_item);	
				
				$this->db->query("
					UPDATE 
						web_video 
					SET video_watch = video_watch + 1
					WHERE 
					video_id = '".$video_id."'
				");
				
				$this->_v('video_detail',$data);
			}
		}
	}

	function agenda(){
		$data = array();
		$this->cur_menu = "agenda";

		$this->per_page = 20;
		$par_filter = array(
				"offset"	=> $this->uri->segment($this->uri_segment),
				"limit"		=> $this->per_page,
				"site_id"	=> $this->site_id
			);

		$this->data_table = $this->M->agenda($par_filter);
		$data = $this->_data_web(array(
				"base_url"	=> site_url('agenda')
			));
		
		$judul_item 		= "Agenda HondaCBRcommunity";
		$headline 			= "disini banyak Agenda kegiatan komunitas-komunitas honda indonesia";	
		$this->meta_title 	= $judul_item;
		$this->meta_desc 	= $headline;
		$this->meta_keyword = "honda, photo, Agenda, video, kegiatan, komunitas,".str_replace(" ",",",$judul_item);

		$this->_v('agenda',$data);
	}

	function agenda_detail($agenda_id="",$agenda_slug=""){
		$this->cur_menu = "gallery";
		if( trim($agenda_id)!="" && trim($agenda_slug)!="" ){
			$detail = $this->M->agenda(array(
					"id"	=> $agenda_id
				));

			if( count($detail['data']) > 0){
				$data['m'] = $detail['data'][0];
				
				$judul_item = "Agenda ".$data['m']->event_tema;
				$headline = "disini banyak Agenda kegiatan komunitas-komunitas honda indonesia ".$data['m']->event_tema;	
				$this->meta_title = $judul_item;
				$this->meta_desc = $headline;
				$this->meta_keyword = "honda, photo, Agenda, komunitas,".str_replace(" ",",",$judul_item);	
				
				$this->_v('agenda_detail',$data);
			}
		}
	}
	
}
