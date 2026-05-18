<?php
include_once(APPPATH."libraries/FrontController.php");
class Main extends FrontController {

	var $per_page 		= 10;
	var $uri_segment 	= 2;
	var $par_lang 		= "";
	var $curr_menu 		= "";
	var $site_id = "";
	function __construct()  
	{	
		parent::__construct(); 	 
		$this->load->model("mdl_site","M");
		$this->site_id = $this->site_info['site']->site_id;
		//$this->output->cache(60);
		//$this->output->enable_profiler(true);
	}
	
	
	function index(){
		$data = array();
		$this->cur_menu = "home";
		$this->meta_title 	= 'hondaCBRcommunity';
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

	function blog(){
		$data = array();
		$this->meta_title = "";
		$this->meta_desc = "";
		$this->meta_keyword = "";
		
		//headline kanal..
		$headline_kanal = get_content(array(
				              "offset"      => 0,
                              "is_headline" => true,
                              "not_site"	=> $this->site_id,
				              "limit"       => 5
				            ));
		$not_in = array();
		foreach ((array)$headline_kanal['data'] as $k => $v) {
			$not_in[] = $v->content_id;
		}
		$this->per_page = 10;
		$this->uri_segment = 2;
		$par_filter = array(
				"offset"	=> (int)$this->uri->segment($this->uri_segment),
				"limit"		=> $this->per_page,
				"not_site"	=> $this->site_id,
				"not_in"	=> $not_in
			);

		$this->data_table = $this->M->content($par_filter);
		$data = $this->_data_web(array(
				"base_url"	=> site_url('blog')
			));
		$data['headline_kanal'] = $headline_kanal;
		$this->_v('blog',$data);
	}

	function news(){
		$data = array();
		$this->meta_title = "";
		$this->meta_desc = "";
		$this->meta_keyword = "";
		
		//headline kanal..
		$headline_kanal = get_content(array(
				              "offset"      => 0,
                              "is_headline" => true,
				              "limit"       => 5
				            ));
		$not_in = array();
		foreach ((array)$headline_kanal['data'] as $k => $v) {
			$not_in[] = $v->content_id;
		}
		$this->per_page = 10;
		$this->uri_segment = 2;
		$par_filter = array(
				"offset"	=> (int)$this->uri->segment($this->uri_segment),
				"limit"		=> $this->per_page,
				"not_in"	=> $not_in
			);

		$this->data_table = $this->M->content($par_filter);
		$data = $this->_data_web(array(
				"base_url"	=> site_url('news')
			));
		$data['headline_kanal'] = $headline_kanal;
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
				$this->meta_title = $data['m']->content_title;
				$this->meta_desc = word_limiter($data['m']->content_headline,30,'');
				$this->meta_image = get_new_image(array(
					"url" 		=> cfg('upload_path_content')."/".$data['m']->content_image,
					"url_klub" 	=> cfg('upload_path_content')."/".$data['m']->content_image,
					"folder"	=> "content",
					"width"		=> 550,
					"height"	=> 320,
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

		$this->per_page = 10;
		$this->uri_segment = 3;

		//headline kanal..
		$headline_kanal = get_content(array(
				              "offset"      => 0,
                              "is_headline" => true,
				              "cat_slug"	=> $slug,
				              "limit"       => 5
				            ));
		$not_in = array();
		foreach ((array)$headline_kanal['data'] as $k => $v) {
			$not_in[] = $v->content_id;
		}
		$par_filter = array(
				"offset"	=> $this->uri->segment($this->uri_segment),
				"limit"		=> $this->per_page,
				"not_in"	=> $not_in,
				"cat_slug"	=> $slug
			);

		$this->data_table = $this->M->content($par_filter);
		$data = $this->_data_web(array(
				"base_url"	=> site_url().'/category/'.$slug
			));
		$data['cat_slug'] = $slug;
		$data['headline_kanal'] = $headline_kanal;
		$this->_v('kanal',$data);
	}

	function search(){
		$data = array();
		$this->cur_menu = "search";
		$this->per_page = 12;
		$this->uri_segment = 2;
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
				"q"			=> $keyword
			);

		$this->data_table = $this->M->content($par_filter);
		$data = $this->_data_web(array(
				"base_url"	=> site_url('search'),
			));
		$judul_item = "Pencarian berita dengan kata kunci '".$keyword."'";
		$headline = "Pencarian berita dengan kata kunci '".$keyword."'";
			
		$this->meta_title = $judul_item;
		$this->meta_desc = $headline;
		$this->meta_keyword = "honda, informasi otomotif, informasi Otojurnalisme,".str_replace(" ",",",$judul_item);	
			
		$this->_v("search",$data);
	}
	
	function contact(){
		$data = array();
		$this->_v('contact',$data);
	}

	function page($slug=""){
		$data = array();
		$m = $this->db->get_where('web_static_content',array(
				"content_site_id" => $this->site_id,
				"content_type"	=> $slug
			))->row();
		if( isset($m->content_id) ){
			$data['m'] = $m;
			$this->_v('page',$data);
		}else{
			redirect(site_url());
		}
	}

	function photo(){
		$data = array();
		$this->cur_menu = "photo";

		$this->per_page = 16;
		$this->uri_segment = 2;
		$par_filter = array(
				"offset"	=> (int)$this->uri->segment($this->uri_segment),
				"limit"		=> $this->per_page
			);

		$this->data_table = $this->M->photo($par_filter);
		$data = $this->_data_web(array(
				"base_url"	=> site_url('photo')
			));
		
		$judul_item 		= "Photo Gallery Otojurnalisme";
		$headline 			= "disini banyak photo gallery kegiatan Otojurnalisme";	
		$this->meta_title 	= $judul_item;
		$this->meta_desc 	= $headline;
		$this->meta_keyword = "honda, photo, kegiatan, Otojurnalisme,".str_replace(" ",",",$judul_item);	

		$this->_v('photo',$data);
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

		$this->per_page = 12;
		$this->uri_segment = 2;
		$par_filter = array(
				"offset"	=> (int)$this->uri->segment($this->uri_segment),
				"limit"		=> $this->per_page
			);

		$this->data_table = $this->M->video($par_filter);
		$data = $this->_data_web(array(
				"base_url"	=> site_url('video')
			));
		
		$judul_item 		= "Video Otojurnalisme";
		$headline 			= "Video terkini Otojurnalisme";	
		$this->meta_title 	= $judul_item;
		$this->meta_desc 	= $headline;
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

		$this->per_page = 10;
		$this->uri_segment = 2;
		$par_filter = array(
				"offset"	=> (int)$this->uri->segment($this->uri_segment),
				"limit"		=> $this->per_page
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
	
	// laporan..
	function laporan_artikel(){
		//error_reporting(-1);
		//ini_set('display_errors', 1);
		
		if( isset($_GET['xls']) ){
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=laporan-artikel.xls");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
		}
		
		echo "<style>
		*{
			font-size:13px;
			font-family:tahoma;
		}	
		table{
			border-collapse:collapse;
			margin-top:10px;
			width:840px;
		}
		table td,table th{
			padding:3px 5px;
		}
		</style>";
		$this->db->select("content_title,app_site.site_name,web_content.content_read");
		$this->db->join("app_site","app_site.site_id = web_content.content_site_id");
		
		
		$start = date("Y-m-d");
		$end = date("Y-m-d");
		if( isset($_GET['start']) && isset($_GET['end'])  ){
			
			$start = $this->input->get('start');
			$end = $this->input->get('end');
			
		}
		$this->db->where("( web_content.content_date >= '".$start." 01:00:00' AND web_content.content_date <= '".$end." 23:59:00' )");
		
		
		
		$this->db->order_by("web_content.content_read","DESC");
		$this->db->order_by("web_content.content_id","DESC");
		$data = $this->db->get("web_content")->result(); 

		echo "<h4>LAPORAN ARTIKEL</h4>";
		if( !isset($_GET['xls']) ){
			echo "<b>Download Excel <a href='".current_url()."?xls'>XLS disini</a></b>";
		}
		echo "<table border='1'><tr><td>No</td><td width='500px'>Judul Artikel </td><td width='50px'>Domain</td><td>Jumlah dibaca</td></tr>";
		$no=1;
		foreach((array)$data as $k=>$v){			
			echo "<tr><td>".$no.".</td><td>".$v->content_title."</td><td>".ucwords($v->site_name)."</td><td>".$v->content_read."</td></tr>";
			$no++;
			
		}
		echo "</table>";
		die();
		
	}	
	function laporan_foto(){
		//error_reporting(-1);
		//ini_set('display_errors', 1);
		
		if( isset($_GET['xls']) ){
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=laporan-foto.xls");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
		}
		
		echo "<style>
		*{
			font-size:13px;
			font-family:tahoma;
		}	
		table{
			border-collapse:collapse;
			margin-top:10px;
			width:840px;
		}
		table td,table th{
			padding:3px 5px;
		}
		</style>";
		$this->db->select("photo_title,app_site.site_name");
		$this->db->join("app_site","app_site.site_id = web_photo.photo_site_id");
		
		$start = date("Y-m-d");
		$end = date("Y-m-d");
		if( isset($_GET['start']) && isset($_GET['end'])  ){
			
			$start = $this->input->get('start');
			$end = $this->input->get('end');
			
		}
		$this->db->where("( web_photo.time_add >= '".$start." 01:00:00' AND web_photo.time_add <= '".$end." 23:59:00' )");
		
		$this->db->order_by("app_site.site_name","DESC");
		$this->db->order_by("web_photo.photo_id","DESC");
		$data = $this->db->get("web_photo")->result(); 
		
		if( !isset($_GET['xls']) ){
			echo "<h4><a href='".site_url('report/artikel')."'>LAPORAN ARTIKEL </a> - <a href='".site_url('report/foto')."'>LAPORAN FOTO </a> - <a href='".site_url('report/video')."'>LAPORAN VIDEO </a> </h4><br />";
		}
		
		echo "<h4>LAPORAN FOTO</h4>";
		if( !isset($_GET['xls']) ){
			echo "<b>Download Excel <a href='".current_url()."?xls'>XLS disini</a></b>";
		}
		echo "<table border='1'><tr><td>No</td><td width='500px'>Judul Foto </td><td width='50px'>Domain</td></tr>";
		$no=1;
		foreach((array)$data as $k=>$v){			
			echo "<tr><td>".$no.".</td><td>".$v->photo_title."</td><td>".ucwords($v->site_name)."</td></tr>";
			$no++;
			
		}
		echo "</table>";
		die();
	}
	function laporan_video(){
		//error_reporting(-1);
		//ini_set('display_errors', 1);
		
		if( isset($_GET['xls']) ){
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=laporan-video.xls");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
		}
		
		echo "<style>
		*{
			font-size:13px;
			font-family:tahoma;
		}	
		table{
			border-collapse:collapse;
			margin-top:10px;
			width:840px;
		}
		table td,table th{
			padding:3px 5px;
		}
		</style>";
		$this->db->select("video_title,app_site.site_name");
		$this->db->join("app_site","app_site.site_id = web_video.video_site_id");
		
		$start = date("Y-m-d");
		$end = date("Y-m-d");
		if( isset($_GET['start']) && isset($_GET['end'])  ){
			
			$start = $this->input->get('start');
			$end = $this->input->get('end');
			
		}
		$this->db->where("( web_video.time_add >= '".$start." 01:00:00' AND web_video.time_add <= '".$end." 23:59:00' )");
		
		
		$this->db->order_by("app_site.site_name","DESC");
		$this->db->order_by("web_video.video_id","DESC");
		$data = $this->db->get("web_video")->result(); 
		if( !isset($_GET['xls']) ){
			echo "<h4><a href='".site_url('report/artikel')."'>LAPORAN ARTIKEL </a> - <a href='".site_url('report/foto')."'>LAPORAN FOTO </a> - <a href='".site_url('report/video')."'>LAPORAN VIDEO </a> </h4><br />";
		}
		echo "<h4>LAPORAN VIDEO </h4>";
		if( !isset($_GET['xls']) ){
			echo "<b>Download Excel <a href='".current_url()."?xls'>XLS disini</a></b>";
		}
		echo "<table border='1'><tr><td>No</td><td width='500px'>Judul Video </td><td width='50px'>Domain</td></tr>";
		$no=1;
		foreach((array)$data as $k=>$v){			
			echo "<tr><td>".$no.".</td><td>".$v->video_title."</td><td>".ucwords($v->site_name)."</td></tr>";
			$no++;
			
		}
		echo "</table>";
		die();
	}
	
	function laporan_summary(){
		
		echo "<style>
		*{
			font-size:13px;
			font-family:tahoma;
		}	
		table{
			border-collapse:collapse;
			margin-top:10px;
			width:320px;
		}
		table td,table th{
			padding:3px 5px;
		}
		</style>";
		
		if( isset($_GET['xls']) ){
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=laporan-summary.xls");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
		}


		//video..
		$this->db->select("count(video_title) as jumlah");
		$this->db->join("app_site","app_site.site_id = web_video.video_site_id");
		
		$start = date("Y-m-d");
		$end = date("Y-m-d");
		if( isset($_GET['start']) && isset($_GET['end'])  ){
			
			$start = $this->input->get('start');
			$end = $this->input->get('end');
			
		}
		$this->db->where("( web_video.time_add >= '".$start." 01:00:00' AND web_video.time_add <= '".$end." 23:59:00' )");
		$video = $this->db->get("web_video")->row(); 
		
		
		// Foto...
		$this->db->select("count(photo_title) as jumlah");
		$this->db->join("app_site","app_site.site_id = web_photo.photo_site_id");
		
		$start = date("Y-m-d");
		$end = date("Y-m-d");
		if( isset($_GET['start']) && isset($_GET['end'])  ){
			
			$start = $this->input->get('start');
			$end = $this->input->get('end');
			
		}
		$this->db->where("( web_photo.time_add >= '".$start." 01:00:00' AND web_photo.time_add <= '".$end." 23:59:00' )");
		
		$foto = $this->db->get("web_photo")->row(); 
		
		// article..
		$this->db->select("count(content_title) as jumlah");
		$this->db->join("app_site","app_site.site_id = web_content.content_site_id");
		
		
		$start = date("Y-m-d");
		$end = date("Y-m-d");
		if( isset($_GET['start']) && isset($_GET['end'])  ){
			
			$start = $this->input->get('start');
			$end = $this->input->get('end');
			
		}
		$this->db->where("( web_content.content_date >= '".$start." 01:00:00' AND web_content.content_date <= '".$end." 23:59:00' )");
		$content = $this->db->get("web_content")->row(); 
		
		if( !isset($_GET['xls']) ){
			echo "<b>Download Excel <a href='".current_url()."?xls'>XLS disini</a></b>";
		}
		
		echo "<h4>LAPORAN SUMMARY ".myDate($start,"d M Y",false)." s.d ".myDate($end,"d M Y",false)." </h4>";
		echo "<table border='1'><tr><td>No</td><td width='500px'>Content </td><td width='50px'>Jumlah</td></tr>";
		echo "<tr><td>1</td><td>Konten Artikel</td><td>".$content->jumlah."</td></tr>";
		echo "<tr><td>2</td><td>Konten Foto</td><td>".$foto->jumlah."</td></tr>";
		echo "<tr><td>3</td><td>Konten Video</td><td>".$video->jumlah."</td></tr>";
		echo "<tr><td colspan='2'>JUMLAH</td><td>".($video->jumlah+$foto->jumlah+$content->jumlah)."</td></tr>";

		echo "</table>";
		die();
	}
	
}
