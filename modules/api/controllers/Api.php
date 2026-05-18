<?php
include_once(APPPATH."libraries/FrontController.php");
class Api extends FrontController {

	function __construct()  
	{	
		parent::__construct(); 	 
		$this->load->model("mdl_api","M");
		$this->load->helper("web");
	}
	
	function index(){
		
	}	
	
	function content($id_news=""){
		$domain  = trim($this->input->get('domain'))==""?"":$this->input->get('domain');
		$limit   = trim($this->input->get('limit'))==""?10:$this->input->get('limit');
		$last_id = trim($this->input->get('last_id'))==""?0:$this->input->get('last_id');
		$new_id  = trim($this->input->get('new_id'))==""?0:$this->input->get('new_id');
		$param = array(
			"limit"	=> $limit,
			"offset"=> 0
		);
		if( trim($last_id) != 0 ){
			$param['last_id'] 	= $last_id;
		}
		if( trim($domain) != "" ){
			$param['domain'] = $domain;
		}
		if( trim($new_id) != 0 ){
			$param['new_id'] = $new_id;
		}
		if( trim($this->input->get('q')) != "" ){
			$param['q'] = $this->input->get('q');
		}
		if( isset($_GET['top']) ){
			$param['top'] = true;
		}
		if( trim($id_news) == "" ){
			$data = $this->M->content($param);
			$content = array();
			foreach((array)$data['data'] as $k=>$v ){
				$img_small = $image_url = get_new_image(array(
						"url" 		=> cfg('upload_path_berita')."/".$v->gambar,
						"url_klub" 	=> cfg('upload_path_berita_klub')."/".$v->gambar,
						"folder"	=> "berita",
						"width"		=> 185,
						"height"	=> 125,
						"domain"	=> $v->domain
					),true);
					
				$img_medium = get_new_image(array(
						"url" 		=> cfg('upload_path_berita')."/".$v->gambar,
						"url_klub" 	=> cfg('upload_path_berita_klub')."/".$v->gambar,
						"folder"	=> "berita",
						"width"		=> 600,
						"height"	=> 400,
						"domain"	=> $v->domain
					),true);
					
				$content[] = array(
						"id"		=> $v->id_berita,
						"date"		=> myDate($v->tanggal,"d M Y H:i"),
						"title"		=> $v->judul,
						"summary"	=> $v->headline,
						"domain"	=> $v->domain,
						"image_thumb" => $img_small,
						"image_medium"=> $img_medium,
						"detail_url"  => 'http://hondacommunity.net/api/content/'.$v->id_berita
				);
			}
			header('Content-Type: application/json');
			header('Access-Control-Allow-Origin: *'); 
			die(json_encode(array("data"=>$content)));
		}else{
			$data = $this->M->content(array(
				"id"	=> $id_news
			));
			$this->db->query("
						UPDATE 
							berita 
						SET dibaca = dibaca + 1
						WHERE 
						id_berita = '".$id_news."'
					");
					
			if( count($data['data']) > 0 ){
				$v = $data['data'][0];
				$img_small = $image_url = get_new_image(array(
						"url" 		=> cfg('upload_path_berita')."/".$v->gambar,
						"url_klub" 	=> cfg('upload_path_berita_klub')."/".$v->gambar,
						"folder"	=> "berita",
						"width"		=> 185,
						"height"	=> 125,
						"domain"	=> $v->domain
					),true);
					
				$img_medium = get_new_image(array(
						"url" 		=> cfg('upload_path_berita')."/".$v->gambar,
						"url_klub" 	=> cfg('upload_path_berita_klub')."/".$v->gambar,
						"folder"	=> "berita",
						"width"		=> 600,
						"height"	=> 400,
						"domain"	=> $v->domain
					),true);
					
				$content = array(
						"id"		=> $v->id_berita,
						"date"		=> myDate($v->tanggal,"d M Y H:i"),
						"title"		=> $v->judul,
						"summary"	=> $v->headline,
						"domain"	=> $v->domain,
						"image_thumb" => $img_small,
						"image_medium"=> $img_medium,
						"content"	  => strip_tags($v->isi_berita,"<p>")
				);
				header('Content-Type: application/json');
				header('Access-Control-Allow-Origin: *'); 
				die(json_encode(array("data"=>$content)));
			}
			
		}
		
	}
	
	function agenda($id_news=""){
		$domain  = trim($this->input->get('domain'))==""?"":$this->input->get('domain');
		$limit   = trim($this->input->get('limit'))==""?10:$this->input->get('limit');
		$last_id = trim($this->input->get('last_id'))==""?0:$this->input->get('last_id');
		$new_id  = trim($this->input->get('new_id'))==""?0:$this->input->get('new_id');
		$param = array(
			"limit"	=> $limit,
			"offset"=> 0
		);
		if( trim($last_id) != 0 ){
			$param['last_id'] 	= $last_id;
		}
		if( trim($domain) != "" ){
			$param['domain'] = $domain;
		}
		if( trim($new_id) != 0 ){
			$param['new_id'] = $new_id;
		}


		if( trim($id_news) == "" ){
			$data = $this->M->agenda($param);
			$content = array();
			foreach((array)$data['data'] as $k=>$v ){
					
				$img_medium = get_new_image(array(
						"url" 		=> cfg('upload_path_klub')."/".$v->logo,
						"url_klub" 	=> cfg('upload_path_klub')."/".$v->logo,
						"folder"	=> "klub",
						"width"		=> 400,
						"height"	=> 400,
						"domain"	=> $v->domain
					),true);
					
				$content[] = array(
						"id"		=> $v->id_agenda,
						"start"		=> myDate($v->tgl_mulai,"d M Y H:i"),
						"end"		=> myDate($v->tgl_selesai,"d M Y H:i"),
						"tema"		=> $v->tema,
						"tempat"	=> $v->tempat,
						"domain"	=> $v->domain,
						"image_medium"=> $img_medium,
						"detail_url"  => 'http://hondacommunity.net/api/agenda/'.$v->id_agenda
				);
			}
			header('Content-Type: application/json');
			header('Access-Control-Allow-Origin: *'); 
			die(json_encode(array("data"=>$content)));
		}else{
			$data = $this->M->agenda(array(
				"id"	=> $id_news
			));					
			if( count($data['data']) > 0 ){
				$v = $data['data'][0];
					
				$img_medium = get_new_image(array(
						"url" 		=> cfg('upload_path_klub')."/".$v->logo,
						"url_klub" 	=> cfg('upload_path_klub')."/".$v->logo,
						"folder"	=> "klub",
						"width"		=> 400,
						"height"	=> 400,
						"domain"	=> $v->domain
					),true);
					
				$content = array(
						"id"		=> $v->id_agenda,
						"start"		=> myDate($v->tgl_mulai,"d M Y H:i"),
						"end"		=> myDate($v->tgl_selesai,"d M Y H:i"),
						"tema"		=> $v->tema,
						"tempat"	=> $v->tempat,
						"domain"	=> $v->domain,
						"image_medium"=> $img_medium,
						"content"	  => strip_tags($v->isi_agenda,"<p>")
				);
				header('Content-Type: application/json');
				header('Access-Control-Allow-Origin: *'); 
				die(json_encode(array("data"=>$content)));
			}
			
		}
		
	}

	
	function klub($id_klub=""){		
		$domain  = trim($this->input->get('domain'))==""?"":$this->input->get('domain');
		$limit   = trim($this->input->get('limit'))==""?20:$this->input->get('limit');
		$last_id = trim($this->input->get('last_id'))==""?0:$this->input->get('last_id');
		$new_id  = trim($this->input->get('new_id'))==""?0:$this->input->get('new_id');
		$param = array(
			"limit"	=> $limit,
			"offset"=> 0
		);
		if( trim($last_id) != 0 ){
			$param['last_id'] 	= $last_id;
		}
		if( trim($domain) != "" ){
			$param['domain'] = $domain;
		}
		if( trim($new_id) != 0 ){
			$param['new_id'] = $new_id;
		}
		
		$data = $this->M->klub($param);
		$content = array();
		
		foreach((array)$data['data'] as $k=>$v ){
			$img_small = $image_url = get_new_image(array(
					"url" 		=> cfg('upload_path_klub')."/".$v->logo,
					"url_klub" 	=> cfg('upload_path_klub')."/".$v->logo,
					"folder"	=> "klub",
					"width"		=> 80,
					"height"	=> 80,
					"domain"	=> $v->domain
				),true);
				
			$content[] = array(
					"id"		=> $v->iddaftarklub,
					"name"		=> $v->namaclub,
					"weburl"	=> $v->weburl,
					"domain"	=> $v->domain,
					"image_thumb" => $img_small,
					"news_url"    => 'http://hondacommunity.net/api/content?domain='.$v->domain
			);
		}
		header('Content-Type: application/json');
		header('Access-Control-Allow-Origin: *'); 
		die(json_encode(array("data"=>$content)));
			
	}

	function photo($id_klub=""){		
		$domain  = trim($this->input->get('domain'))==""?"":$this->input->get('domain');
		$limit   = trim($this->input->get('limit'))==""?20:$this->input->get('limit');
		$last_id = trim($this->input->get('last_id'))==""?0:$this->input->get('last_id');
		$new_id  = trim($this->input->get('new_id'))==""?0:$this->input->get('new_id');
		$param = array(
			"limit"	=> $limit,
			"offset"=> 0
		);
		if( trim($last_id) != 0 ){
			$param['last_id'] 	= $last_id;
		}
		if( trim($domain) != "" ){
			$param['domain'] = $domain;
		}
		if( trim($new_id) != 0 ){
			$param['new_id'] = $new_id;
		}
		
		$data = $this->M->photo_album($param);
		$content = array();

		foreach((array)$data['data'] as $k=>$v ){
			$img_small = $image_url = get_new_image(array(
					"url" 		=> cfg('upload_path_album')."/".$v->gbr_album,
					"url_klub" 	=> cfg('upload_path_album_klub')."/".$v->gbr_album,
					"folder"	=> "gallery",
					"width"		=> 200,
					"height"	=> 200,
					"domain"	=> $v->domain
				),true);
				
			$content[] = array(
					"id"		=> $v->id_album,
					"name"		=> $v->jdl_album,
					"klub"		=> $v->namaclub,
					"domain"	=> $v->domain,
					"image_thumb" => $img_small,
					"detail_url"  => 'http://hondacommunity.net/api/detail_photo/'.$v->id_album
			);
		}
		header('Content-Type: application/json');
		header('Access-Control-Allow-Origin: *'); 
		die(json_encode(array("data"=>$content))); 
			
	}
	
	function detail_photo($id=""){
		if( trim($id)!=""){
			$data = $this->M->detail_photo($id);

			$content = array();
			foreach((array)$data['data'] as $k=>$v ){
				$img_small = $image_url = get_new_image(array(
						"url" 		=> cfg('upload_path_photo')."/".$v->gbr_gallery,
						"url_klub" 	=> cfg('upload_path_photo_klub')."/".$v->gbr_gallery,
						"folder"	=> "gallery",
						"width"		=> 600,
						"height"	=> 500,
						"domain"	=> $v->domain
					),true);
					
				$content[] = array(
						"id"		=> $v->id_gallery,
						"title"		=> $v->jdl_gallery,
						"desc"		=> $v->keterangan,
						"domain"	=> $v->domain,
						"image_thumb" => $img_small,
						"album_url"	  => "http://hondacommunity.net/api/photo"				
				);
			}
			header('Content-Type: application/json');
			header('Access-Control-Allow-Origin: *'); 
			die(json_encode($content));
		}else{
			header('Content-Type: application/json');
			header('Access-Control-Allow-Origin: *'); 
			die(json_encode(array('status'=>'error')));
		}
	}
	
	function video($id_video=""){		
		$domain  = trim($this->input->get('domain'))==""?"":$this->input->get('domain');
		$limit   = trim($this->input->get('limit'))==""?20:$this->input->get('limit');
		$last_id = trim($this->input->get('last_id'))==""?0:$this->input->get('last_id');
		$new_id  = trim($this->input->get('new_id'))==""?0:$this->input->get('new_id');
		$param = array(
			"limit"	=> $limit,
			"offset"=> 0
		);
		if( trim($last_id) != 0 ){
			$param['last_id'] 	= $last_id;
		}
		if( trim($domain) != "" ){
			$param['domain'] = $domain;
		}
		if( trim($new_id) != 0 ){
			$param['new_id'] = $new_id;
		}
		
		$content = array();
		if( trim($id_video)=="" ){
			$data = $this->M->video($param);
			foreach((array)$data['data'] as $k=>$v ){
				$img_small = $image_url = get_new_image(array(
						"url" 		=> cfg('upload_path_video')."/".$v->gambar,
						"url_klub" 	=> cfg('upload_path_video_klub')."/".$v->gambar,
						"folder"	=> "video",
						"width"		=> 185,
						"height"	=> 125,
						"domain"	=> $v->domain
					),true);
					
				$content[] = array(
						"id"		=> $v->id_video,
						"title"		=> $v->judul,
						"klub"		=> $v->namaclub,
						"domain"	=> $v->domain,
						"image_thumb" => $img_small,
						"detail_url"  => 'http://hondacommunity.net/api/video/'.$v->id_video
				);
			}
		}else{
			$data = $this->M->video(array(
				"id"	=> $id_video
			));
			
			if( count($data['data']) > 0 ){
				$v = $data['data'][0];
				
				$img_small = $image_url = get_new_image(array(
						"url" 		=> cfg('upload_path_video')."/".$v->gambar,
						"url_klub" 	=> cfg('upload_path_video_klub')."/".$v->gambar,
						"folder"	=> "video",
						"width"		=> 600,
						"height"	=> 400,
						"domain"	=> $v->domain
					),true);
					
				$content = array(
						"id"		=> $v->id_video,
						"title"		=> $v->judul,
						"klub"		=> $v->namaclub,
						"domain"	=> $v->domain,
						"image_thumb" => $img_small,
						"video"		  => urlencode($v->filevideo)
				);

			}else{
				$content = array("status"=>"error");
			}
			
		}
		header('Content-Type: application/json');
		header('Access-Control-Allow-Origin: *'); 
		die(json_encode(array("data"=>$content))); 
			
	}

	
}
