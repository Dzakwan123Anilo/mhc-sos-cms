<?php
class mdl_site extends CI_Model{ 
		
	function __construct(){
		parent::__construct();
	} 

	function content($p=array(),$count=FALSE){
		$total = 0;		
		if($count==FALSE){
			$this->db->select('
				web_content.*,web_kategori.cat_name, 
				app_site.site_name,app_site.site_url
			');
		}else{
			$this->db->select('
				COUNT(web_content.content_id) as jumlah
			');
		}
		$this->db->join('web_kategori','web_kategori.cat_id = web_content.content_cat_id',"LEFT");
		$this->db->join('app_site','app_site.site_id = web_content.content_site_id');
		
		$this->db->where('web_content.content_date <= NOW()');
	
		if( isset($p['not_site']) ){
			$this->db->where('app_site.site_id !=',$p['not_site']);
		}
		
		if( isset($p['tag']) ){
			$this->db->like('web_content.content_tag',$p['tag']);
		}
	
		if( isset($p['not_in']) && is_array($p['not_in']) && count($p['not_in'])> 0){
			$this->db->where_not_in('web_content.content_id',$p['not_in']);
		}

		if( isset($p['is_headline']) && $p['is_headline']==TRUE ){
			$this->db->where("web_content.content_is_headline",1);
		}
		
		$this->db->where("web_content.content_status",1);
		
		if( isset($p['top']) ){
			$this->db->where("web_content.content_date >= DATE_SUB(NOW(), INTERVAL 14 DAY)");
		}
		if( isset($p['q']) && trim($p['q'])!="" ){
			$q = $p['q'];
			$this->db->like("cat_name",$q);
			$this->db->or_like("content_title",$q);
			$this->db->or_like("content_tag",$q);
			$this->db->or_like("content_headline",$q);
			$this->db->or_like("site_name",$q);
		}

		if( isset($p['share']) ){
			$this->db->where("web_content.content_is_share",1);
		}

		if( isset($p['cat']) && trim($p['cat'])!="" ){
			$this->db->where("web_content.content_cat_id",$p['cat']);
		}

		if( isset($p['cat_slug']) && trim($p['cat_slug'])!="" ){
			$this->db->where("web_kategori.cat_slug",$p['cat_slug']);
		}

		if( isset($p['id']) && trim($p['id'])!="" ){
			$this->db->where("web_content.content_id",$p['id']);
		}

		if( isset($p['site_id']) && trim($p['site_id'])!="" ){
			$this->db->where("app_site.site_id",$p['site_id']);
		}

		if($count==FALSE){
			$p['offset'] = empty($p['offset'])?0:$p['offset'];
			if( isset($p['offset']) && isset($p['limit']) ){
				$this->db->limit($p['limit'],$p['offset']);
			}
		}

		if( isset($p['top']) ){
			$this->db->order_by('content_read','desc');
		}elseif( isset($p['related']) ){
			$this->db->order_by('rand()','desc');
		}else{
			$this->db->order_by('content_date','desc');
		}
		$qry = $this->db->get("web_content");
		if($count==FALSE){
			$total = $this->content($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"total" => $total
				);
		}else{
			$dx = $qry->row();
			return $dx->jumlah;
		}	

	}
	
	function klub($p=array(),$count=FALSE){
		
		$total = 0;

		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
		}

		if( isset($p['site_id']) ){
			$this->db->where('site_id',$p['site_id']);
		}
		
		if( isset($p['have_logo']) ){
			$this->db->where("site_logo !=",'');
		}
		$this->db->order_by('site_id','desc');
		$qry = $this->db->get("app_site");
		if($count==FALSE){
			$total = $this->klub($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}

	}

	function anggota_klub($p=array(),$count=FALSE){
		
		$total = 0;

		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
		}

		if( isset($p['angg_site_id']) ){
			$this->db->where('angg_site_id',$p['angg_site_id']);
		}

		if( isset($p['angg_id']) ){
			$this->db->where('angg_id',$p['angg_id']);
		}
		
		if( isset($p['have_logo']) ){
			$this->db->where("angg_image !=",'');
		}
		$this->db->order_by('angg_id','desc');
		$qry = $this->db->get("web_anggota_klub");
		if($count==FALSE){
			$total = $this->anggota_klub($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}

	}

	function member($p=array(),$count=FALSE){
		
		$total = 0;


		$this->db->select('web_member.*,app_site.site_name');
		$this->db->join("app_site","app_site.site_id = web_member.member_site_id");

		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
		}
		
		$this->db->order_by('member_id','desc');
		$qry = $this->db->get("web_member");
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
		
		$this->db->select("web_video.*,app_site.site_name");
		$this->db->join("app_site","app_site.site_id = web_video.video_site_id");
		$this->db->where("video_status",1);
		if( isset($p['site_id']) ){
			$this->db->where('app_site.site_id',$p['site_id']);
		}
		if(isset($p['id'])){
			$this->db->where("video_id",$p['id']);
		}
		if(isset($p['not_id'])){
			$this->db->where("video_id !=",$p['not_id']);
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
			$this->db->order_by('video_id','desc');
		}

		$qry = $this->db->get("web_video");
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
		$this->db->select("web_event.*,app_site.site_name");
		$this->db->join("app_site","app_site.site_id = web_event.event_site_id");

		if( isset($p['site_id']) ){
			$this->db->where('app_site.site_id',$p['site_id']);
		}
		if(isset($p['id'])){
			$this->db->where("event_id",$p['id']);
		}

		if(isset($p['not_id'])){
			$this->db->where("event_id !=",$p['not_id']);
		}
		
		if(isset($p['in_month'])){
			$this->db->where("DATE_FORMAT(web_event.event_start,'%Y-%m') = '".$p['in_month']."'");
		}
		
		$this->db->where("web_event.event_end >= '".date("Y-m-d")."'");
		
		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
		}

		if(isset($p['order_by_date_asc'])){
			$this->db->order_by('event_start','asc');
		}if(isset($p['order_by_rand'])){
			$this->db->order_by('rand()','desc');
		}else{
			$this->db->order_by('event_id','desc');
		}
		$qry = $this->db->get("web_event");
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
		
		$this->db->select("web_kopdar.*,app_site.site_name");
		$this->db->join("app_site","app_site.site_id = web_kopdar.kopdar_site_id");
		$this->db->where("web_kopdar.kopdar_status",1);
		if( isset($p['site_id']) ){
			$this->db->where('app_site.site_id',$p['site_id']);
		}
		if(isset($p['id'])){
			$this->db->where("kopdar_id",$p['id']);
		}
		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
		}
		$this->db->order_by('kopdar_id','desc');
		$qry = $this->db->get("web_kopdar");
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

	function photo($p=array(),$count=FALSE){
		
		$total = 0;
		
		$this->db->select('web_photo.*,app_site.site_name');
		$this->db->join("app_site","app_site.site_id = web_photo.photo_site_id");
		$this->db->where("web_photo.photo_status",1);

		if( isset($p['site_id']) ){
			$this->db->where('app_site.site_id',$p['site_id']);
		}
		
		if( isset($p['not_site']) ){
			$this->db->where('app_site.site_id !=',$p['not_site']);
		}

		if( isset($p['not_in']) ){
			$this->db->where('web_photo.photo_id !=',$p['not_in']);
		}

		if(isset($p['id']) && trim($p['id'])!=""){
			$this->db->where('photo_id',$p['id']);
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
			$this->db->order_by('photo_id','desc');
		}
	
		$qry = $this->db->get("web_photo");
		if($count==FALSE){
			$total = $this->photo($p,TRUE);
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

		
		$this->db->select('web_banner.*,app_site.site_name');
		$this->db->join("app_site","app_site.site_id = web_banner.banner_site_id");
		$this->db->where("web_banner.banner_status",1);

		if( isset($p['posisi'])){
			$this->db->where("web_banner.banner_position",$p['posisi']);
		}

		if( isset($p['site_id'])){
			$this->db->where("app_site.site_id",$p['site_id']);
		}

		if(isset($p['id']) && trim($p['id'])!=""){
			$this->db->where('banner_id',$p['id']);
		}

		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
		}
		$this->db->order_by('banner_id','desc');
		$qry = $this->db->get("web_banner");
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

}