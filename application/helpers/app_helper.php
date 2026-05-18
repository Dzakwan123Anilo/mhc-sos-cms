<?php
//dashboard....
function get_jumlah_member($date=""){
	$CI = getCI();
	if(trim($date)!=""){
		$CI->db->where("member_date",$date);
	}
	return $CI->db->get_where("web_member",array(
			"member_id !=" => ""
		))->num_rows();
}

function get_masking_action($k=""){
	$m = cfg('action_mask');
	return isset($m[$k])?$m[$k]:$k;
}
function get_artikel($date="",$site_id=""){
	$CI = getCI();
	if(trim($date)!=""){
		$CI->db->where("(content_date >= '".$date." 00:00:00' AND content_date <= '".$date." 23:59:00' ) ");
	}
	if(trim($site_id)!=""){
		$CI->db->where("content_site_id",$site_id);
	}
	
	return $CI->db->get_where("web_content",array(
			"content_id !=" => ""
		))->num_rows();
}

function get_artikel_list(){
	$CI = getCI();
	$CI->db->order_by("content_id","DESC");
	$CI->db->limit(15);
	return $CI->db->get_where("web_content",array(
			"content_id !=" => ""
		))->result();
}

function get_jumlah_klub($date=""){
	$CI = getCI();
	return $CI->db->get_where("app_site",array(
			"site_id !=" => ""
		))->num_rows();
}


function get_user(){
	$CI = getCI();
	return $CI->db->get_where("app_user",array(
			"is_trash !=" => "1"
		))->num_rows();
}

function get_site($type="1"){
	$CI = getCI();
	$CI->db->order_by("site_name","ASC");
	return $CI->db->get_where("app_site",array(
			"site_status" 	=> 1,
			"site_type"		=> $type
		))->result();
}

function get_cart_main($site_id="",$start="",$end=""){
	$CI = getCI();
	$m = $CI->db->query("
		SELECT content_date,user_add, count(content_id) as jumlah FROM web_content 
		WHERE content_site_id = '".$site_id."'
		AND (
			DATE_FORMAT(web_content.content_date,'%Y-%m-%d') >= '".$start."' 
			AND DATE_FORMAT(web_content.content_date,'%Y-%m-%d') <= '".$end."'
		)
		AND contant_status = 1
		GROUP BY user_add,DATE_FORMAT(web_content.content_date,'%Y-%m-%d')
		ORDER BY web_content.content_date ASC
	")->result();
	return $m;
}

function get_cart_klub(){
	$CI = getCI();
	$CI->db->select('app_site.site_name as nama, count(web_content.content_id) as jumlah');
	$CI->db->join('app_site','app_site.site_id=web_content.content_site_id');
	$CI->db->group_by('web_content.content_site_id');
	$CI->db->limit(20);
	$CI->db->order_by("(count(web_content.content_id))","DESC");
	$m = $CI->db->get_where("web_content",array(
			"web_content.content_id !="	=> ''
		))->result();
	return $m;
}
function get_pie_gender_member(){
	$CI = getCI();
	$CI->db->select('member_gender as nama, count(member_id) as jumlah');
	$CI->db->group_by('member_gender');
	$m = $CI->db->get_where("web_member",array(
			"member_id !="	=> '0'
		))->result();
	return $m;
}

function get_line_byday($day=30){
	$CI = getCI();
	$m = $CI->db->query("
		SELECT DATE_FORMAT(content_date,'%Y-%m-%d') as tgl, count(content_id) as jumlah 
		FROM web_content 
		GROUP BY DATE_FORMAT(content_date,'%Y-%m-%d')
		ORDER BY DATE_FORMAT(content_date,'%Y-%m-%d') DESC
		LIMIT ".$day."
	")->result();
	return $m;
}


function get_berita($id=""){
	$CI =getCI(); 
	return $CI->db->query("
		SELECT *
		FROM web_content
		WHERE content_id = '".$id."'
	")->row();
}

function count_berita($dom=""){
	$CI =getCI(); 
	$m = $CI->db->query("
		SELECT count(*) as jumlah
		FROM web_content
		WHERE content_site_id = '".$dom."'
	")->row();
	
	return isset($m->jumlah)?$m->jumlah:0;
}

function get_provinsi(){
	$CI =getCI();

	$CI->db->order_by("propinsi_nama");
	return $CI->db->get_where("app_propinsi",array(
			"propinsi_status"	=> 0,
			"is_trash"			=> 0
		))->result();
}

function get_template(){
	$CI =getCI();
	$CI->db->order_by("template_name");
	return $CI->db->get("app_site_template")->result();
}

function get_content_category(){
	$CI = getCI(); 
	$CI->db->order_by('cat_name','ASC');
	$CI->db->where('cat_status',1);
	$CI->db->where('cat_name !=','');
	if( $CI->jCfg['user']['is_all'] != 1 ){
		$CI->db->where("cat_site_id",$CI->jCfg['user']['site']['id']);
	}
	return $CI->db->get("web_kategori")->result();
}
