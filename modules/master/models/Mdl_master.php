<?php
class Mdl_master extends CI_Model{ 
		
	function __construct(){
		parent::__construct();
	}

	function _safe_date_range($table_col, $date_start, $date_end) {
		if (empty($date_start) || empty($date_end)) return;
		if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_start)) return;
		if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_end)) return;
		$this->db->group_start();
		$this->db->where($table_col." >=", $date_start." 01:00:00");
		$this->db->where($table_col." <=", $date_end." 23:59:00");
		$this->db->group_end();
	}

	function _safe_like_search($params, $keyword) {
		if (empty($keyword)) return;
		$this->db->group_start();
		$i=0;
		foreach ($params as $key => $value) {
			if($key != ""){
				if($i==0) $this->db->like($key, $keyword);
				else $this->db->or_like($key, $keyword);
				$i++;
			}
		}
		$this->db->group_end();
	}

	function _safe_column_like($colum, $keyword) {
		if (empty($colum) || empty($keyword)) return;
		if (!preg_match('/^[a-zA-Z0-9_\.]+$/', $colum)) return;
		$this->db->like($colum, $keyword);
	}

	function _safe_order_by($order_by, $order_dir) {
		if (empty($order_by)) return;
		if (!preg_match('/^[a-zA-Z0-9_\.]+$/', $order_by)) return;
		$order_dir = strtoupper($order_dir) === 'ASC' ? 'ASC' : 'DESC';
		$this->db->order_by($order_by, $order_dir);
	}

	function client($p=array(),$count=FALSE){
		
		$total = 0;

		$this->db->select('sos_client.*,app_site.site_name');
		$this->db->join("app_site","app_site.site_id = sos_client.client_site_id");

		$this->_safe_date_range("sos_client.time_add", $this->jCfg['search']['date_start'] ?? '', $this->jCfg['search']['date_end'] ?? '');

		if( $this->jCfg['user']['is_all'] != 1 ){
			$this->db->where("sos_client.client_site_id",$this->jCfg['user']['site']['id']);
		}

		if( trim($this->jCfg['search']['colum'] ?? '') == "" && trim($this->jCfg['search']['keyword'] ?? '') != "" ){
			$this->_safe_like_search($p['param'], $this->jCfg['search']['keyword']);
		}

		if( trim($this->jCfg['search']['colum'] ?? '') != "" && trim($this->jCfg['search']['keyword'] ?? '') != "" ){
			$this->_safe_column_like($this->jCfg['search']['colum'],$this->jCfg['search']['keyword']);
		}
	
		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
			$start = empty($p['offset'])?0:$p['offset'];
		}

		$this->db->order_by('sos_client.time_add','DESC');
		
		$this->_safe_order_by($this->jCfg['search']['order_by'] ?? '', $this->jCfg['search']['order_dir'] ?? '');
		
		$qry = $this->db->get("sos_client");
		if($count==FALSE){
			$total = $this->client($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"start" => $start,
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	

	} 

	function member($p=array(),$count=FALSE){
		
		$total = 0;
		
//		$this->db->distinct();
		$this->db->select("sos_member.*,sos_client.client_name,sos_ref_plan_type.plan_description");
		$this->db->join("app_site","app_site.site_id = sos_member.member_site_id");
		$this->db->join("sos_client","sos_client.client_id = sos_member.member_clientid");
		$this->db->join("sos_ref_plan_type","sos_ref_plan_type.plan_type = sos_member.member_plantype AND sos_ref_plan_type.plan_clientid = sos_member.member_clientid","LEFT");

		$this->_safe_date_range("sos_member.time_add", $this->jCfg['search']['date_start'] ?? '', $this->jCfg['search']['date_end'] ?? '');
		
		/*advanced search*/
		$this->db->not_like("sos_member.member_cardno","99999");
		if(trim($this->jCfg['member_search']['clientid'])!="")
			$this->db->where("sos_member.member_clientid",$this->jCfg['member_search']['clientid']);
			
		if(trim($this->jCfg['member_search']['cardno'])!="")
			$this->db->like("sos_member.member_cardno",$this->jCfg['member_search']['cardno']);
			
		if(trim($this->jCfg['member_search']['name'])!="")
			$this->db->like("sos_member.member_name",$this->jCfg['member_search']['name']);
			
	
		if(isset($this->jCfg['member_search']['relationship']) && trim($this->jCfg['member_search']['relationship'])!="") {
			if($this->jCfg['member_search']['relationship']=="employee")
				$this->db->where("sos_member.member_relationshiptype",$this->jCfg['member_search']['relationship']);
			else
				$this->db->where_not_in("sos_member.member_relationshiptype",array('Employee'));
		}
			
		if(trim($this->jCfg['member_search']['sex'])!="")
			$this->db->where("sos_member.member_sex",$this->jCfg['member_search']['sex']);
			
		if(trim($this->jCfg['member_search']['birthdate'])!="")
			$this->db->where("sos_member.member_birthdate",$this->jCfg['member_search']['birthdate']);
			
		if(trim($this->jCfg['member_search']['phone'])!="")
			$this->db->like("sos_member.member_phone",$this->jCfg['member_search']['phone']);
			
		if(trim($this->jCfg['member_search']['email'])!="")
			$this->db->like("sos_member.member_email",$this->jCfg['member_search']['email']);
			
		if(trim($this->jCfg['member_search']['region'])!="")
			$this->db->where("sos_member.member_region",$this->jCfg['member_search']['region']);
			
		if(trim($this->jCfg['member_search']['location'])!="")
			$this->db->where("sos_member.member_location",$this->jCfg['member_search']['location']);
			
		if(trim($this->jCfg['member_search']['plantype'])!="")
			$this->db->where("sos_member.member_plantype",$this->jCfg['member_search']['plantype']);
			
		if(trim($this->jCfg['member_search']['employment'])!="")
			$this->db->where("sos_member.member_employmentstatus",$this->jCfg['member_search']['employment']);
			
		if(trim($this->jCfg['member_search']['cardstatus'])!="")
			$this->db->where("sos_member.member_statuscard",$this->jCfg['member_search']['cardstatus']);
		/*end of advanced search*/

		if( $this->jCfg['user']['is_all'] != 1 ){
			$this->db->where("sos_member.member_site_id",$this->jCfg['user']['site']['id']);
		}

		if( trim($this->jCfg['search']['colum'] ?? '') == "" && trim($this->jCfg['search']['keyword'] ?? '') != "" ){
			$this->_safe_like_search($p['param'], $this->jCfg['search']['keyword']);
		}

		if( trim($this->jCfg['search']['colum'] ?? '') != "" && trim($this->jCfg['search']['keyword'] ?? '') != "" ){
			$this->_safe_column_like($this->jCfg['search']['colum'],$this->jCfg['search']['keyword']);
		}
	
		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
			$start = empty($p['offset'])?0:$p['offset'];
		}

		$this->_safe_order_by($this->jCfg['search']['order_by'] ?? '', $this->jCfg['search']['order_dir'] ?? '');
		
		$qry = $this->db->get("sos_member");
		if($count==FALSE){
			$total = $this->member($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"start" => isset($start)?$start:0,
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	

	}

	function member_family($p=array(),$count=FALSE){
		
		$total = 0;

		$this->db->select('sos_member.*,app_site.site_name,sos_client.client_name,sos_ref_plan_type.plan_description');
		$this->db->join("app_site","app_site.site_id = sos_member.member_site_id");
		$this->db->join("sos_client","sos_client.client_id = sos_member.member_clientid");
		$this->db->join("sos_ref_plan_type","sos_ref_plan_type.plan_type = sos_member.member_plantype AND sos_ref_plan_type.plan_clientid = sos_member.member_clientid");
	
		if( $this->jCfg['user']['is_all'] != 1 ){
			$this->db->where("sos_member.member_site_id",$this->jCfg['user']['site']['id']);
		}
				
		$this->db->where($p);
		$this->db->order_by('member_cardno','ASC');
		
		$qry = $this->db->get("sos_member");
		if($count==FALSE){
			$total = $this->member_family($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"start" => isset($start)?$start:0,
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	

	}
	
	function provider($p=array(),$count=FALSE){
		
		$total = 0;

//		$this->db->select("sos_provider.*,app_site.site_name,sos_client.client_name");
//		$this->db->join("app_site","app_site.site_id = sos_provider.provider_site_id");
//		$this->db->join("sos_client","sos_client.client_id = sos_provider.provider_clientid");

		$this->_safe_date_range("sos_provider.time_add", $this->jCfg['search']['date_start'] ?? '', $this->jCfg['search']['date_end'] ?? '');
		
		/*advanced search*/
		if(isset($this->jCfg['provider_search']['name']) && trim($this->jCfg['provider_search']['name'])!="")
			$this->db->like("sos_provider.provider_name",$this->jCfg['provider_search']['name']);
			
		if(isset($this->jCfg['provider_search']['type']) && trim($this->jCfg['provider_search']['type'])!="")
			$this->db->where("sos_provider.provider_type",$this->jCfg['provider_search']['type']);
			
		if(isset($this->jCfg['provider_search']['region']) && trim($this->jCfg['provider_search']['region'])!="")
			$this->db->where("sos_provider.provider_region",$this->jCfg['provider_search']['region']);
			
		if(isset($this->jCfg['provider_search']['location']) && trim($this->jCfg['provider_search']['location'])!="")
			$this->db->where("sos_provider.provider_location",$this->jCfg['provider_search']['location']);
		/*end of advanced search*/

		/*if( $this->jCfg['user']['is_all'] != 1 ){
			$this->db->where("sos_provider.provider_site_id",$this->jCfg['user']['site']['id']);
		}*/

		if( trim($this->jCfg['search']['colum'] ?? '') == "" && trim($this->jCfg['search']['keyword'] ?? '') != "" ){
			$this->_safe_like_search($p['param'], $this->jCfg['search']['keyword']);
		}

		if( trim($this->jCfg['search']['colum'] ?? '') != "" && trim($this->jCfg['search']['keyword'] ?? '') != "" ){
			$this->_safe_column_like($this->jCfg['search']['colum'],$this->jCfg['search']['keyword']);
		}
	
		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
			$start = empty($p['offset'])?0:$p['offset'];
		}

		$this->_safe_order_by($this->jCfg['search']['order_by'] ?? '', $this->jCfg['search']['order_dir'] ?? '');
		
		$qry = $this->db->get("sos_provider");
		if($count==FALSE){
			$total = $this->provider($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"start" => $start,
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	

	}
	
	function provider_client($p=array(),$count=FALSE){
		
		$total = 0;

		if( $this->jCfg['user']['is_all'] != 1 ){
			$this->db->where("provider_site_id",$this->jCfg['user']['site']['id']);
		}
	
		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
			$start = empty($p['offset'])?0:$p['offset'];
		}
		$this->db->where($p['where']);
		$this->db->order_by('provider_name','ASC');
		
		$qry = $this->db->get("sos_provider");
		if($count==FALSE){
			$total = $this->provider_client($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"start" => $start,
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	

	}

	function news($p=array(),$count=FALSE){
		
		$total = 0;

		$this->_safe_date_range("time_add", $this->jCfg['search']['date_start'] ?? '', $this->jCfg['search']['date_end'] ?? '');

		if( $this->jCfg['user']['is_all'] != 1 ){
			$this->db->where("news_site_id",$this->jCfg['user']['site']['id']);
		}

		if( trim($this->jCfg['search']['colum'] ?? '') == "" && trim($this->jCfg['search']['keyword'] ?? '') != "" ){
			$this->_safe_like_search($p['param'], $this->jCfg['search']['keyword']);
		}

		if( trim($this->jCfg['search']['colum'] ?? '') != "" && trim($this->jCfg['search']['keyword'] ?? '') != "" ){
			$this->_safe_column_like($this->jCfg['search']['colum'],$this->jCfg['search']['keyword']);
		}
	
		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
			$start = empty($p['offset'])?0:$p['offset'];
		}

		$this->db->order_by('time_add','DESC');
		$this->_safe_order_by($this->jCfg['search']['order_by'] ?? '', $this->jCfg['search']['order_dir'] ?? '');
		
		$qry = $this->db->get("sos_news");
		if($count==FALSE){
			$total = $this->news($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"start" => $start,
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	

	}

	function panduan($p=array(),$count=FALSE){
		
		$total = 0;

		$this->_safe_date_range("time_add", $this->jCfg['search']['date_start'] ?? '', $this->jCfg['search']['date_end'] ?? '');

		if( $this->jCfg['user']['is_all'] != 1 ){
			$this->db->where("panduan_site_id",$this->jCfg['user']['site']['id']);
		}

		if( trim($this->jCfg['search']['colum'] ?? '') == "" && trim($this->jCfg['search']['keyword'] ?? '') != "" ){
			$this->_safe_like_search($p['param'], $this->jCfg['search']['keyword']);
		}

		if( trim($this->jCfg['search']['colum'] ?? '') != "" && trim($this->jCfg['search']['keyword'] ?? '') != "" ){
			$this->_safe_column_like($this->jCfg['search']['colum'],$this->jCfg['search']['keyword']);
		}
	
		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
			$start = empty($p['offset'])?0:$p['offset'];
		}
		$this->db->order_by('time_add','DESC');
		$this->_safe_order_by($this->jCfg['search']['order_by'] ?? '', $this->jCfg['search']['order_dir'] ?? '');
		
		$qry = $this->db->get("sos_panduan");
		if($count==FALSE){
			$total = $this->panduan($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"start" => $start,
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	

	}

	function fraud($p=array(),$count=FALSE){
		
		$total = 0;

		$this->_safe_date_range("time_add", $this->jCfg['search']['date_start'] ?? '', $this->jCfg['search']['date_end'] ?? '');

		if( $this->jCfg['user']['is_all'] != 1 ){
			$this->db->where("panduan_site_id",$this->jCfg['user']['site']['id']);
		}

		if( trim($this->jCfg['search']['colum'] ?? '') == "" && trim($this->jCfg['search']['keyword'] ?? '') != "" ){
			$this->_safe_like_search($p['param'], $this->jCfg['search']['keyword']);
		}

		if( trim($this->jCfg['search']['colum'] ?? '') != "" && trim($this->jCfg['search']['keyword'] ?? '') != "" ){
			$this->_safe_column_like($this->jCfg['search']['colum'],$this->jCfg['search']['keyword']);
		}
	
		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
			$start = empty($p['offset'])?0:$p['offset'];
		}
		$this->db->order_by('time_add','DESC');
		$this->_safe_order_by($this->jCfg['search']['order_by'] ?? '', $this->jCfg['search']['order_dir'] ?? '');
		
		$qry = $this->db->get("sos_fraud_notification");
		if($count==FALSE){
			$total = $this->fraud($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"start" => $start,
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	

	}

	function inpatient($p=array(),$count=FALSE){
		
		$total = 0;

		$this->_safe_date_range("time_add", $this->jCfg['search']['date_start'] ?? '', $this->jCfg['search']['date_end'] ?? '');

		if( $this->jCfg['user']['is_all'] != 1 ){
			$this->db->where("panduan_site_id",$this->jCfg['user']['site']['id']);
		}

		if( trim($this->jCfg['search']['colum'] ?? '') == "" && trim($this->jCfg['search']['keyword'] ?? '') != "" ){
			$this->_safe_like_search($p['param'], $this->jCfg['search']['keyword']);
		}

		if( trim($this->jCfg['search']['colum'] ?? '') != "" && trim($this->jCfg['search']['keyword'] ?? '') != "" ){
			$this->_safe_column_like($this->jCfg['search']['colum'],$this->jCfg['search']['keyword']);
		}
	
		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
			$start = empty($p['offset'])?0:$p['offset'];
		}
		$this->db->order_by('time_add','DESC');
		$this->_safe_order_by($this->jCfg['search']['order_by'] ?? '', $this->jCfg['search']['order_dir'] ?? '');
		
		$qry = $this->db->get("sos_inpatient_notification");
		if($count==FALSE){
			$total = $this->inpatient($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"start" => $start,
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	

	}
	
	function transraw($p=array(),$count=FALSE){
		
		$total = 0;

//		$this->db->select("sos_ttrans_claim.*,sos_member.*,sos_client.*,sos_provider.*,sos_ref_service_type.*");
//		$this->db->join("app_site","app_site.site_id = sos_ttrans_claim.claim_site_id");
		$this->db->join("sos_client","sos_client.client_id = sos_ttrans_claim.clientid");
		$this->db->join("sos_member","sos_member.member_id = sos_ttrans_claim.memberid");
		$this->db->join("sos_provider","sos_provider.provider_id = sos_ttrans_claim.providerid");
		$this->db->join("sos_ref_service_type","sos_ref_service_type.service_id = sos_ttrans_claim.servicetypeId");

		$this->_safe_date_range("sos_ttrans_claim.createdDate", $this->jCfg['search']['date_start'] ?? '', $this->jCfg['search']['date_end'] ?? '');
		
		/*advanced search*/
		if(isset($this->jCfg['transraw_search']['providerid']) && trim($this->jCfg['transraw_search']['providerid'])!="")
			$this->db->where("sos_ttrans_claim.providerId",$this->jCfg['transraw_search']['providerid']);
			
		if(isset($this->jCfg['transraw_search']['serviceid']) && trim($this->jCfg['transraw_search']['serviceid'])!="")
			$this->db->where("sos_ttrans_claim.servicetypeId",$this->jCfg['transraw_search']['serviceid']);
			
		if(isset($this->jCfg['transraw_search']['name']) && trim($this->jCfg['transraw_search']['name'])!="")
			$this->db->like("sos_member.member_name",$this->jCfg['transraw_search']['name']);
	
		if( trim($this->jCfg['transraw_search']['date_start'] ?? '') !="" && trim($this->jCfg['transraw_search']['date_end'] ?? '') != "" ){
			if(preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->jCfg['transraw_search']['date_start']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->jCfg['transraw_search']['date_end'])){
				$this->db->group_start();
				$this->db->where("sos_ttrans_claim.date_claim >=", $this->jCfg['transraw_search']['date_start']." 01:00:00");
				$this->db->where("sos_ttrans_claim.date_claim <=", $this->jCfg['transraw_search']['date_end']." 23:59:00");
				$this->db->group_end();
			}
		}
		
		if(isset($this->jCfg['transraw_search']['clientid']) && trim($this->jCfg['transraw_search']['clientid'])!="")
			$this->db->where("sos_ttrans_claim.clientId",$this->jCfg['transraw_search']['clientid']);
						
		if(isset($this->jCfg['transraw_search']['region']) && trim($this->jCfg['transraw_search']['region'])!="")
			$this->db->where("sos_member.member_region",$this->jCfg['transraw_search']['region']);
			
		if(isset($this->jCfg['transraw_search']['location']) && trim($this->jCfg['transraw_search']['location'])!="")
			$this->db->where("sos_member.member_location",$this->jCfg['transraw_search']['location']);
			
		if(isset($this->jCfg['transraw_search']['cardno']) && trim($this->jCfg['transraw_search']['cardno'])!="")
			$this->db->like("sos_member.member_cardno",$this->jCfg['transraw_search']['cardno']);
			
		if(isset($this->jCfg['transraw_search']['relationship']) && trim($this->jCfg['transraw_search']['relationship'])!="") {
			if($this->jCfg['transraw_search']['relationship']=="employee")
				$this->db->where("sos_ttrans_claim.relationship_type",$this->jCfg['transraw_search']['relationship']);
			else
				$this->db->where_not_in("sos_ttrans_claim.relationship_type",array('Employee'));
		}
		/*end of advanced search*/

		/*if( $this->jCfg['user']['is_all'] != 1 ){
			$this->db->where("sos_ttrans_claim.provider_site_id",$this->jCfg['user']['site']['id']);
		}*/

		if( trim($this->jCfg['search']['colum'] ?? '') == "" && trim($this->jCfg['search']['keyword'] ?? '') != "" ){
			$this->_safe_like_search($p['param'], $this->jCfg['search']['keyword']);
		}

		if( trim($this->jCfg['search']['colum'] ?? '') != "" && trim($this->jCfg['search']['keyword'] ?? '') != "" ){
			$this->_safe_column_like($this->jCfg['search']['colum'],$this->jCfg['search']['keyword']);
		}
	
		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
			$start = empty($p['offset'])?0:$p['offset'];
		}

		$this->_safe_order_by($this->jCfg['search']['order_by'] ?? '', $this->jCfg['search']['order_dir'] ?? '');
		
		$qry = $this->db->get("sos_ttrans_claim");
		if($count==FALSE){
			$total = $this->transraw($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"start" => isset($start)?$start:0,
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	

	}

	function ai_log($p=array(),$count=FALSE){
		
		$total = 0;

		$this->db->select('sos_log_activate.*,sos_member.member_name,sos_member.member_employeeid');
		$this->db->join("sos_member","sos_member.member_cardno = sos_log_activate.log_cardno");

		$this->_safe_date_range("sos_log_activate.time_add", $this->jCfg['search']['date_start'] ?? '', $this->jCfg['search']['date_end'] ?? '');

		if( trim($this->jCfg['search']['colum'] ?? '') == "" && trim($this->jCfg['search']['keyword'] ?? '') != "" ){
			$this->_safe_like_search($p['param'], $this->jCfg['search']['keyword']);
		}

		if( trim($this->jCfg['search']['colum'] ?? '') != "" && trim($this->jCfg['search']['keyword'] ?? '') != "" ){
			$this->_safe_column_like($this->jCfg['search']['colum'],$this->jCfg['search']['keyword']);
		}
	
		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
			$start = empty($p['offset'])?0:$p['offset'];
		}

		$this->db->order_by('sos_log_activate.time_add','DESC');
		
		$this->_safe_order_by($this->jCfg['search']['order_by'] ?? '', $this->jCfg['search']['order_dir'] ?? '');
		
		$qry = $this->db->get("sos_log_activate");
		if($count==FALSE){
			$total = $this->ai_log($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"start" => $start,
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	

	} 
	
	function import_log($p=array(),$count=FALSE){
		
		$total = 0;
		
		$this->db->select("sos_import_log.*,app_site.site_name");
		$this->db->join("app_site","app_site.site_id = sos_import_log.import_site_id");

		$this->_safe_date_range("time_add", $this->jCfg['search']['date_start'] ?? '', $this->jCfg['search']['date_end'] ?? '');

		if( $this->jCfg['user']['is_all'] != 1 ){
			$this->db->where("import_site_id",$this->jCfg['user']['site']['id']);
		}

		if( isset($p['type']) && trim($p['type'])!="" ){
			$this->db->where("import_type",trim($p['type']));
		}

		if( trim($this->jCfg['search']['colum'] ?? '') == "" && trim($this->jCfg['search']['keyword'] ?? '') != "" ){
			$this->_safe_like_search($p['param'], $this->jCfg['search']['keyword']);
		}

		if( trim($this->jCfg['search']['colum'] ?? '') != "" && trim($this->jCfg['search']['keyword'] ?? '') != "" ){
			$this->_safe_column_like($this->jCfg['search']['colum'],$this->jCfg['search']['keyword']);
		}
	
		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
			$start = empty($p['offset'])?0:$p['offset'];
		}
		$this->db->order_by('time_add','DESC');
		$this->_safe_order_by($this->jCfg['search']['order_by'] ?? '', $this->jCfg['search']['order_dir'] ?? '');
		
		$qry = $this->db->get("sos_import_log");
		if($count==FALSE){
			$total = $this->import_log($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"start" => $start,
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	

	}
	
}
