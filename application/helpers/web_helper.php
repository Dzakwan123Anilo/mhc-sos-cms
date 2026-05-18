<?php
function get_category_content($param=array()){
	$CI = getCI();
	if( count($param) > 0 ){
		$CI->db->where($param);
	}
	$m = $CI->db->get("web_kategori")->result();
	return $m;
}

function get_content($par=array()){
	$CI = getCI();
	$CI->load->model("mdl_site","S");
	$data = $CI->S->content($par);
	return $data;
}

function get_video($par=array()){
	$CI = getCI();
	$CI->load->model("mdl_site","S");
	$data = $CI->S->video($par);
	return $data;
}

function get_banner($par=array()){
	$CI = getCI();
	$CI->load->model("mdl_site","S");
	$data = $CI->S->banner($par);
	return $data;
}

function get_photo($par=array()){
	$CI = getCI();
	$CI->load->model("mdl_site","S");
	$data = $CI->S->photo($par);
	return $data;
}

function get_agenda($par=array()){
	$CI = getCI();
	$CI->load->model("mdl_site","S");
	$data = $CI->S->agenda($par);
	return $data;
}

function get_count_category(){
	$CI = getCI();
	$CI->db->select("web_kategori.cat_id,web_kategori.cat_name, count(web_content.content_id) as jumlah");
	$CI->db->join("web_content","web_kategori.cat_id = web_content.content_cat_id","LEFT");
	$CI->db->group_by("web_kategori.cat_id");
	$CI->db->where("web_kategori.cat_site_id",$CI->site_info['site']['site_id']);
	$m = $CI->db->get("web_kategori")->result();
	return $m;
}

function get_email_member($id=""){
	$CI = getCI();
	$id = trim($id)!=""?$id:"xx";
	$x = $CI->db->get_where("web_member",array(
			"member_id"	=> $id
		))->row();
	return isset($x->email)?$x->email:'';
}

function url_content($par=array()){
	return site_url('read/'.strtolower(url_title(ucwords($par['cat'])))."/".$par['id']."-".strtolower(url_title($par['title'])));
}

function url_blog($par=array()){
	return site_url('read/'.strtolower(url_title(ucwords($par['cat'])))."/".$par['id']."-".strtolower(url_title($par['title'])));
}

function url_category($par=array()){
	return site_url('cat/'.$par['id'].".".strtolower(url_title($par['title'])));
}

function url_member($par=array()){
	return site_url('profile/'.$par['id']."-".strtolower(url_title($par['title'])));
}

function url_photo($par=array()){
	return site_url('photo/detail/'.$par['id']."-".strtolower(url_title($par['title'])));
}

function url_agenda($par=array()){
	return site_url('agenda/detail/'.$par['id']."-".strtolower(url_title($par['title'])));
}

function url_video($par=array()){
	return site_url('video/detail/'.$par['id']."-".strtolower(url_title($par['title'])));
}

function url_klub($par=array()){
	return site_url('klub-detail/'.$par['id'].".".strtolower(url_title($par['title'])));
}

function url_anggota_klub($par=array()){
	return site_url('anggota-klub/detail/'.$par['id']."-".strtolower(url_title($par['title'])));
}

function url_content_klub($par=array()){
	$tgl = str_replace("-", "/", $par['tgl']);
	return $par['url']."read/".$tgl."/".$par['id']."/".strtolower(url_title($par['title']));
}

function get_user_publish($id="",$field="user_name"){
	$CI = getCI();
	$m = $CI->db->get_where("app_user",array(
			"user_id"	=> $id
		))->row_array();
	return isset($m[$field])?$m[$field]:'n/a';
}

function get_popular_post($p=array()){
	$CI =getCI();
	$CI->load->model("mdl_site","M");
	$limit = isset($p['limit'])?$p['limit']:15;
	$par_filter = array(
				"offset"	=> 0,
				"dibaca"	=> TRUE,
				"limit"		=> $limit
			);
	$data_table = $CI->M->content($par_filter);
	return $data_table['data'];
}

function get_headline_post($p=array()){
	$CI =getCI();
	$CI->load->model("mdl_site","M");
	$limit = isset($p['limit'])?$p['limit']:1;
	$par_filter = array(
				"offset"		=> 0,
				"is_headline"	=> TRUE,
				"limit"			=> $limit
			);
	$data_table = $CI->M->content($par_filter);
	return $data_table['data'];
}

function get_klub($p=array()){
	$CI =getCI();
	$CI->load->model("mdl_site","M");
	$limit = isset($p['limit'])?$p['limit']:15;
	$par_filter = array(
				"offset"	=> 0,
				"limit"		=> $limit,
				"have_logo"	=> true
			);
	$data_table = $CI->M->klub($par_filter);	
	return $data_table;
}

function get_anggota_klub($p=array()){
	$CI =getCI();
	$CI->load->model("mdl_site","M");
	$limit = isset($p['limit'])?$p['limit']:15;
	$par_filter = array(
				"offset"		=> 0,
				"limit"			=> $limit,
				"have_logo"		=> true,
				"angg_site_id" 	=> $p['angg_site_id']
			);
	$data_table = $CI->M->anggota_klub($par_filter);	
	return $data_table;
}

function get_member($p=array()){
	$CI =getCI();
	$CI->load->model("mdl_site","M");
	$limit = isset($p['limit'])?$p['limit']:15;
	$par_filter = array(
				"offset"	=> 0,
				"limit"		=> $limit
			);

	$data_table = $CI->M->member($par_filter);
		
	return $data_table;
}

function char_limiter($str,$int){
	if(strlen($str)<$int)
		return substr($str, 0,$int);
	else
		return substr($str, 0,$int)."..";
}

function clear_domain($domain_url=""){
	$m = str_replace("http://", "", $domain_url);
	return str_replace("/", "", $m);
}

function add_this(){
	?>
<!-- Go to www.addthis.com/dashboard to customize your tools -->
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4dafd81d588369c6" async="async"></script>
<!-- Go to www.addthis.com/dashboard to customize your tools -->
<div class="addthis_native_toolbox"></div>
	<?php
}