<?php
class Mdl_api extends CI_Model{ 
		
	function __construct(){
		parent::__construct();
	} 

	function content($p=array()){
		$total = 0;		
		$this->db->select('
berita.*,kategori.nama_kategori,kategori.domain,kategori.kategori_seo, 
				daftarklub.weburl,daftarklub.namaclub
			');
		$this->db->join('kategori','kategori.id_kategori = berita.id_kategori',"LEFT");
		$this->db->join('daftarklub','daftarklub.domain = berita.domain');
		
		$this->db->where('berita.tanggal <= NOW()');
		if( isset($p['domain']) ){
			$this->db->where('daftarklub.domain',$p['domain']);
		}
		
		if( isset($p['cat']) ){
			$this->db->where('berita.id_kategori',$p['cat']);
		}
		
		if( isset($p['tag']) ){
			$this->db->like('berita.tag',$p['tag']);
		}
		

		if( isset($p['not_in']) && is_array($p['not_in']) && count($p['not_in'])> 0){
			$this->db->where_not_in('berita.id_berita',$p['not_in']);
		}

		if( isset($p['is_headline']) && $p['is_headline']==TRUE ){
			//$this->db->where('berita.artpilihan',1);
			$this->db->where("daftarklub.domain","main");
		}
		
		$this->db->where("berita.status",'aktif');
		
		if( isset($p['top']) ){
			$this->db->where("berita.tanggal >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
		}
		if( isset($p['q']) && trim($p['q'])!="" ){
			$q = $p['q'];
			$this->db->like("nama_kategori",$q);
			$this->db->or_like("judul",$q);
			$this->db->or_like("isi_berita",$q);
			$this->db->or_like("headline",$q);
			$this->db->or_like("username",$q);
		}

		if( isset($p['share']) && trim($p['share'])!="" ){
			$this->db->where("berita.share",$p['share']);
		}
		
		if( isset($p['last_id']) && trim($p['last_id'])!="" && trim($p['last_id'])!=0 ){
			$this->db->where("berita.id_berita <",$p['last_id']);
		}
		
		if( isset($p['new_id']) && trim($p['new_id'])!="" && trim($p['new_id'])!=0 ){
			$this->db->where("berita.id_berita >",$p['new_id']);
		}


		if( isset($p['id_cat']) && trim($p['id_cat'])!="" ){
			$this->db->where("berita.id_kategori",$p['id_cat']);
		}

		if( isset($p['id']) && trim($p['id'])!="" ){
			$this->db->where("berita.id_berita",$p['id']);
		}

		if( isset($p['id_kategori']) && trim($p['id_kategori'])!="" ){
			$this->db->where("berita.id_kategori",$p['id_kategori']);
		}

		if( isset($p['domain']) && trim($p['domain'])!="" ){
			$this->db->where("berita.domain",$p['domain']);
		}

		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
		}

		if( isset($p['top']) ){
			$this->db->order_by('dibaca','desc');
		}elseif( isset($p['related']) ){
			$this->db->order_by('rand()','desc');
		}else{
			$this->db->order_by('tanggal','desc');
		}
		$qry = $this->db->get("berita");
		return array(
					"data"	=> $qry->result()
				);	

	}
	
	function peserta_hsa($p=array(),$count=FALSE){
		
		$total = 0;

		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
		}

		if( isset($p['id']) && trim($p['id'])!="" ){
			$this->db->where("hsa_id",$p['id']);
		}
		
		$this->db->order_by('hsa_date','desc');
		$qry = $this->db->get("app_hsa");
		if($count==FALSE){
			$total = $this->peserta_hsa($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}
		
	}
	function klub($p=array()){
		
		$total = 0;

		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
		}

		if( isset($p['domain']) ){
			$this->db->where('domain',$p['domain']);
		}
		
		if( isset($p['last_id']) && trim($p['last_id'])!="" && trim($p['last_id'])!=0 ){
			$this->db->where("daftarklub.iddaftarklub <",$p['last_id']);
		}
		
		if( isset($p['new_id']) && trim($p['new_id'])!="" && trim($p['new_id'])!=0 ){
			$this->db->where("daftarklub.iddaftarklub >",$p['new_id']);
		}
		
		if( isset($p['have_logo']) ){
			$this->db->where("logo !=",'');
		}
		$this->db->order_by('iddaftarklub','desc');
		$qry = $this->db->get("daftarklub");
		return array(
					"data"	=> $qry->result()
				);

	}

	function member($p=array(),$count=FALSE){
		
		$total = 0;

		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
		}
		
		$this->db->order_by('idpass','desc');
		$qry = $this->db->get("userout");
		if($count==FALSE){
			$total = $this->member($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}

	}

	function video($p=array(),$count=FALSE){
		
		$total = 0;
		$this->db->where("video.status",'aktif');
		$this->db->select('
			video.*, 
			daftarklub.weburl,daftarklub.namaclub
			');
		$this->db->join('daftarklub','daftarklub.domain = video.domain');

		if( isset($p['domain']) ){
			$this->db->where('daftarklub.domain',$p['domain']);
		}
		
		if( isset($p['last_id']) && trim($p['last_id'])!="" && trim($p['last_id'])!=0 ){
			$this->db->where("video.id_video <",$p['last_id']);
		}
		
		if( isset($p['new_id']) && trim($p['new_id'])!="" && trim($p['new_id'])!=0 ){
			$this->db->where("video.id_video >",$p['new_id']);
		}
		
		if(isset($p['id'])){
			$this->db->where("id_video",$p['id']);
		}

		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
		}

		if( isset($p['related']) ){
			$this->db->order_by('rand()','desc');
		}else{
			$this->db->order_by('id_video','desc');
		}

		$qry = $this->db->get("video");
		if($count==FALSE){
			$total = $this->video($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	
		
		

	}

	function agenda($p=array(),$count=FALSE){
		
		$total = 0;
		$this->db->where("agenda.status",'aktif');
		$this->db->select('
			agenda.*, 
			daftarklub.weburl,daftarklub.namaclub,daftarklub.logo
			');
		$this->db->join('daftarklub','daftarklub.domain = agenda.domain');

		if( isset($p['domain']) ){
			$this->db->where('daftarklub.domain',$p['domain']);
		}
		
		if( isset($p['last_id']) && trim($p['last_id'])!="" && trim($p['last_id'])!=0 ){
			$this->db->where("agenda.id_agenda <",$p['last_id']);
		}
		
		if( isset($p['new_id']) && trim($p['new_id'])!="" && trim($p['new_id'])!=0 ){
			$this->db->where("agenda.id_agenda >",$p['new_id']);
		}

		if(isset($p['id'])){
			$this->db->where("id_agenda",$p['id']);
		}
		
		if(isset($p['in_month'])){
			$this->db->where("DATE_FORMAT(agenda.tgl_mulai,'%Y-%m') = '".$p['in_month']."'");
		}
		

		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
		}

		if(isset($p['order_by_date_asc'])){
			$this->db->order_by('tgl_mulai','asc');
		}else{
			$this->db->order_by('id_agenda','desc');
		}
		$qry = $this->db->get("agenda");
		if($count==FALSE){
			$total = $this->agenda($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	
		
		

	}

	function kopdar($p=array(),$count=FALSE){
		
		$total = 0;
		$this->db->where("kopdar.status",'aktif');
		$this->db->select('
			kopdar.*, 
			daftarklub.weburl,daftarklub.namaclub,daftarklub.logo
			');
		$this->db->join('daftarklub','daftarklub.domain = kopdar.domain');

		if( isset($p['domain']) ){
			$this->db->where('daftarklub.domain',$p['domain']);
		}

		if(isset($p['id'])){
			$this->db->where("idkopdar",$p['id']);
		}
		

		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
		}


		$this->db->order_by('idkopdar','desc');
		
		$qry = $this->db->get("kopdar");
		if($count==FALSE){
			$total = $this->kopdar($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	
		
		

	}

	function photo_album($p=array(),$count=FALSE){
		
		$total = 0;
		$this->db->where("album.status",'aktif');
		$this->db->select('
			album.*, 
			daftarklub.weburl,daftarklub.namaclub
			');

		if( isset($p['domain']) ){
			$this->db->where('daftarklub.domain',$p['domain']);
		}
		
		if( isset($p['last_id']) && trim($p['last_id'])!="" && trim($p['last_id'])!=0 ){
			$this->db->where("album.id_album <",$p['last_id']);
		}
		
		if( isset($p['new_id']) && trim($p['new_id'])!="" && trim($p['new_id'])!=0 ){
			$this->db->where("album.id_album >",$p['new_id']);
		}
		
		if( isset($p['not_in']) ){
			$this->db->where('album.id_album !=',$p['not_in']);
		}
		
		if( isset($p['hbd']) && $p['hbd'] == true){
			$this->db->like('jdl_album','hbd2015');
		}

		$this->db->join('daftarklub','daftarklub.domain = album.domain');

		if(isset($p['id']) && trim($p['id'])!=""){
			$this->db->where('id_album',$p['id']);
		}

		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
		}

		if( isset($p['related']) ){
			$this->db->order_by('rand()','desc');
		}else{
			$this->db->order_by('id_album','desc');
		}
		
		
		$qry = $this->db->get("album");
		if($count==FALSE){
			$total = $this->photo_album($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	

	}

	function banner($p=array(),$count=FALSE){
		
		$total = 0;

		$this->db->select('
			banner.*, 
			daftarklub.weburl,daftarklub.namaclub
			');
		
		$this->db->where("banner.status",'aktif');

		if( isset($p['posisi'])){
			$this->db->where("banner.posisi",$p['posisi']);
		}

		if( isset($p['domain'])){
			$this->db->where("banner.domain",$p['domain']);
		}

		
		$this->db->join('daftarklub','daftarklub.domain = banner.domain');

		if(isset($p['id']) && trim($p['id'])!=""){
			$this->db->where('id_album',$p['id']);
		}

		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
		}

		$this->db->order_by('id_banner','desc');
		
		$qry = $this->db->get("banner");
		if($count==FALSE){
			$total = $this->banner($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	

	}

	function sponsor($p=array(),$count=FALSE){
		
		$total = 0;
		$this->db->where("web_sponsor.is_trash",0);
		$this->db->select('web_sponsor.*');		
		if( isset($p['id']) && trim($p['id'])!="" ){
			$this->db->where("sponsor_id",$p['id']);
		}
		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
		}

		$this->db->order_by('sponsor_order','asc');
		
		$qry = $this->db->get("web_sponsor");
		if($count==FALSE){
			$total = $this->sponsor($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	

	}
	
	function detail_photo($id_album=""){
		$this->db->select("gallery.*,album.domain");
		$this->db->join("album","album.id_album = gallery.id_album");
		$data['data'] = $this->db->get_where("gallery",array(
				"album.id_album"	=> $id_album
			))->result();
		return $data;
	}

}