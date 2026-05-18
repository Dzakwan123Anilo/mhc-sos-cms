<?php
class Mdl_reference extends CI_Model{ 
		
	function __construct(){
		parent::__construct();
	} 

	function province($p=array(),$count=FALSE){
		
		$total = 0;

		$this->db->select('app_propinsi.*');

		/* where or like conditions */
		if( trim($this->jCfg['search']['date_start'])!="" && trim($this->jCfg['search']['date_end']) != "" ){
			$this->db->where("( app_propinsi.time_add >= '".$this->jCfg['search']['date_start']." 01:00:00' AND app_propinsi.time_add <= '".$this->jCfg['search']['date_end']." 23:59:00' )");
		}


		// dont modified....
		if( trim($this->jCfg['search']['colum'])=="" && trim($this->jCfg['search']['keyword']) != "" ){
			$str_like = "( ";
			$i=0;
			foreach ($p['param'] as $key => $value) {
				if($key != ""){
					$str_like .= $i!=0?"OR":"";
					$str_like .=" ".$key." LIKE '%".$this->jCfg['search']['keyword']."%' ";			
					$i++;
				}
			}
			$str_like .= " ) ";
			$this->db->where($str_like);
		}

		if( trim($this->jCfg['search']['colum'])!="" && trim($this->jCfg['search']['keyword']) != "" ){
			$this->db->like($this->jCfg['search']['colum'],$this->jCfg['search']['keyword']);
		}


		
		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
			$start = empty($p['offset'])?0:$p['offset'];
		}

		$this->db->order_by("app_propinsi.propinsi_id","DESC");
		if(trim($this->jCfg['search']['order_by'])!="")
			$this->db->order_by($this->jCfg['search']['order_by'],$this->jCfg['search']['order_dir']);
		
		$qry = $this->db->get("app_propinsi");
		if($count==FALSE){
			$total = $this->province($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"start" => $start,
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	

	} 
	
	function group_provider($p=array(),$count=FALSE){
		
		$total = 0;

		$this->db->select('sos_tref_provider_group.*,sos_client.client_name');
		$this->db->join("sos_client","sos_tref_provider_group.provider_clientid = sos_client.client_id","left");

		/* where or like conditions */
		if( trim($this->jCfg['search']['date_start'])!="" && trim($this->jCfg['search']['date_end']) != "" ){
			$this->db->where("( time_add >= '".$this->jCfg['search']['date_start']." 01:00:00' AND time_add <= '".$this->jCfg['search']['date_end']." 23:59:00' )");
		}


		// dont modified....
		if( trim($this->jCfg['search']['colum'])=="" && trim($this->jCfg['search']['keyword']) != "" ){
			$str_like = "( ";
			$i=0;
			foreach ($p['param'] as $key => $value) {
				if($key != ""){
					$str_like .= $i!=0?"OR":"";
					$str_like .=" ".$key." LIKE '%".$this->jCfg['search']['keyword']."%' ";			
					$i++;
				}
			}
			$str_like .= " ) ";
			$this->db->where($str_like);
		}

		if( trim($this->jCfg['search']['colum'])!="" && trim($this->jCfg['search']['keyword']) != "" ){
			$this->db->like($this->jCfg['search']['colum'],$this->jCfg['search']['keyword']);
		}


		
		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
			$start = empty($p['offset'])?0:$p['offset'];
		}

		$this->db->order_by("providergroupId","DESC");
		if(trim($this->jCfg['search']['order_by'])!="")
			$this->db->order_by($this->jCfg['search']['order_by'],$this->jCfg['search']['order_dir']);
		
		$qry = $this->db->get("sos_tref_provider_group");
		if($count==FALSE){
			$total = $this->group_provider($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"start" => $start,
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	

	} 


	function kota($p=array(),$count=FALSE){
		
		$total = 0;

		$this->db->select('app_kabupaten.*,app_propinsi.propinsi_nama');
		$this->db->join("app_propinsi","app_propinsi.propinsi_id = app_kabupaten.kab_propinsi_id");

		/* where or like conditions */
		if( trim($this->jCfg['search']['date_start'])!="" && trim($this->jCfg['search']['date_end']) != "" ){
			$this->db->where("( app_kabupaten.time_add >= '".$this->jCfg['search']['date_start']." 01:00:00' AND app_kabupaten.time_add <= '".$this->jCfg['search']['date_end']." 23:59:00' )");
		}

		if( $this->jCfg['user']['is_all'] != 1 ){
			$this->db->where("app_kabupaten.content_site_id",$this->jCfg['user']['site']['id']);
		}

		// dont modified....
		if( trim($this->jCfg['search']['colum'])=="" && trim($this->jCfg['search']['keyword']) != "" ){
			$str_like = "( ";
			$i=0;
			foreach ($p['param'] as $key => $value) {
				if($key != ""){
					$str_like .= $i!=0?"OR":"";
					$str_like .=" ".$key." LIKE '%".$this->jCfg['search']['keyword']."%' ";			
					$i++;
				}
			}
			$str_like .= " ) ";
			$this->db->where($str_like);
		}

		if( trim($this->jCfg['search']['colum'])!="" && trim($this->jCfg['search']['keyword']) != "" ){
			$this->db->like($this->jCfg['search']['colum'],$this->jCfg['search']['keyword']);
		}


		
		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
			$start = empty($p['offset'])?0:$p['offset'];
		}

		$this->db->order_by("app_kabupaten.kab_id","DESC");
		if(trim($this->jCfg['search']['order_by'])!="")
			$this->db->order_by($this->jCfg['search']['order_by'],$this->jCfg['search']['order_dir']);
		
		$qry = $this->db->get("app_kabupaten");
		if($count==FALSE){
			$total = $this->kota($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"start" => $start,
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	

	}

	function plan($p=array(),$count=FALSE){
		
		$total = 0;

		$this->db->select('sos_ref_plan_type.*,sos_client.client_name');
		$this->db->join("sos_client","sos_client.client_id = sos_ref_plan_type.plan_clientid");

		/* where or like conditions */
		if( trim($this->jCfg['search']['date_start'])!="" && trim($this->jCfg['search']['date_end']) != "" ){
			$this->db->where("( sos_ref_plan_type.time_add >= '".$this->jCfg['search']['date_start']." 01:00:00' AND sos_ref_plan_type.time_add <= '".$this->jCfg['search']['date_end']." 23:59:00' )");
		}

		if( $this->jCfg['user']['is_all'] != 1 ){
			$this->db->where("sos_ref_plan_type.plan_site_id",$this->jCfg['user']['site']['id']);
		}

		// dont modified....
		if( trim($this->jCfg['search']['colum'])=="" && trim($this->jCfg['search']['keyword']) != "" ){
			$str_like = "( ";
			$i=0;
			foreach ($p['param'] as $key => $value) {
				if($key != ""){
					$str_like .= $i!=0?"OR":"";
					$str_like .=" ".$key." LIKE '%".$this->jCfg['search']['keyword']."%' ";			
					$i++;
				}
			}
			$str_like .= " ) ";
			$this->db->where($str_like);
		}

		if( trim($this->jCfg['search']['colum'])!="" && trim($this->jCfg['search']['keyword']) != "" ){
			$this->db->like($this->jCfg['search']['colum'],$this->jCfg['search']['keyword']);
		}


		
		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
			$start = empty($p['offset'])?0:$p['offset'];
		}

		$this->db->order_by("sos_ref_plan_type.plan_id","DESC");
		if(trim($this->jCfg['search']['order_by'])!="")
			$this->db->order_by($this->jCfg['search']['order_by'],$this->jCfg['search']['order_dir']);
		
		$qry = $this->db->get("sos_ref_plan_type");
		if($count==FALSE){
			$total = $this->plan($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"start" => $start,
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	

	}

	function dental($p=array(),$count=FALSE){
		
		$total = 0;

		$this->db->select('sos_ref_dental_limit.*, sos_client.client_name,sos_ref_plan_type.plan_type');
		$this->db->join("sos_client","sos_client.client_id = sos_ref_dental_limit.dental_clientid");
		$this->db->join("sos_ref_plan_type","sos_ref_plan_type.plan_id = sos_ref_dental_limit.dental_plantypeid");

		/* where or like conditions */
		if( trim($this->jCfg['search']['date_start'])!="" && trim($this->jCfg['search']['date_end']) != "" ){
			$this->db->where("( sos_ref_dental_limit.time_add >= '".$this->jCfg['search']['date_start']." 01:00:00' AND sos_ref_dental_limit.time_add <= '".$this->jCfg['search']['date_end']." 23:59:00' )");
		}

		if( $this->jCfg['user']['is_all'] != 1 ){
			$this->db->where("sos_ref_dental_limit.dental_site_id",$this->jCfg['user']['site']['id']);
		}

		// dont modified....
		if( trim($this->jCfg['search']['colum'])=="" && trim($this->jCfg['search']['keyword']) != "" ){
			$str_like = "( ";
			$i=0;
			foreach ($p['param'] as $key => $value) {
				if($key != ""){
					$str_like .= $i!=0?"OR":"";
					$str_like .=" ".$key." LIKE '%".$this->jCfg['search']['keyword']."%' ";			
					$i++;
				}
			}
			$str_like .= " ) ";
			$this->db->where($str_like);
		}

		if( trim($this->jCfg['search']['colum'])!="" && trim($this->jCfg['search']['keyword']) != "" ){
			$this->db->like($this->jCfg['search']['colum'],$this->jCfg['search']['keyword']);
		}


		
		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
			$start = empty($p['offset'])?0:$p['offset'];
		}

		$this->db->order_by("sos_ref_dental_limit.dental_id","DESC");
		if(trim($this->jCfg['search']['order_by'])!="")
			$this->db->order_by($this->jCfg['search']['order_by'],$this->jCfg['search']['order_dir']);
		
		$qry = $this->db->get("sos_ref_dental_limit");
		if($count==FALSE){
			$total = $this->dental($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"start" => $start,
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	

	}

	function employment($p=array(),$count=FALSE){
		
		$total = 0;

		$this->db->select('sos_ref_employment_status.*');

		/* where or like conditions */
		if( trim($this->jCfg['search']['date_start'])!="" && trim($this->jCfg['search']['date_end']) != "" ){
			$this->db->where("( sos_ref_employment_status.time_add >= '".$this->jCfg['search']['date_start']." 01:00:00' AND sos_ref_employment_status.time_add <= '".$this->jCfg['search']['date_end']." 23:59:00' )");
		}

		if( $this->jCfg['user']['is_all'] != 1 ){
			$this->db->where("sos_ref_employment_status.employment_site_id",$this->jCfg['user']['site']['id']);
		}

		// dont modified....
		if( trim($this->jCfg['search']['colum'])=="" && trim($this->jCfg['search']['keyword']) != "" ){
			$str_like = "( ";
			$i=0;
			foreach ($p['param'] as $key => $value) {
				if($key != ""){
					$str_like .= $i!=0?"OR":"";
					$str_like .=" ".$key." LIKE '%".$this->jCfg['search']['keyword']."%' ";			
					$i++;
				}
			}
			$str_like .= " ) ";
			$this->db->where($str_like);
		}

		if( trim($this->jCfg['search']['colum'])!="" && trim($this->jCfg['search']['keyword']) != "" ){
			$this->db->like($this->jCfg['search']['colum'],$this->jCfg['search']['keyword']);
		}


		
		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
			$start = empty($p['offset'])?0:$p['offset'];
		}

		$this->db->order_by("sos_ref_employment_status.employment_id","DESC");
		if(trim($this->jCfg['search']['order_by'])!="")
			$this->db->order_by($this->jCfg['search']['order_by'],$this->jCfg['search']['order_dir']);
		
		$qry = $this->db->get("sos_ref_employment_status");
		if($count==FALSE){
			$total = $this->employment($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"start" => $start,
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	

	}

	function message($p=array(),$count=FALSE){
		
		$total = 0;

		$this->db->select('sos_ref_message_email.*');

		/* where or like conditions */
		if( trim($this->jCfg['search']['date_start'])!="" && trim($this->jCfg['search']['date_end']) != "" ){
			$this->db->where("( sos_ref_message_email.time_add >= '".$this->jCfg['search']['date_start']." 01:00:00' AND sos_ref_message_email.time_add <= '".$this->jCfg['search']['date_end']." 23:59:00' )");
		}

		if( $this->jCfg['user']['is_all'] != 1 ){
			$this->db->where("sos_ref_message_email.message_site_id",$this->jCfg['user']['site']['id']);
		}

		// dont modified....
		if( trim($this->jCfg['search']['colum'])=="" && trim($this->jCfg['search']['keyword']) != "" ){
			$str_like = "( ";
			$i=0;
			foreach ($p['param'] as $key => $value) {
				if($key != ""){
					$str_like .= $i!=0?"OR":"";
					$str_like .=" ".$key." LIKE '%".$this->jCfg['search']['keyword']."%' ";			
					$i++;
				}
			}
			$str_like .= " ) ";
			$this->db->where($str_like);
		}

		if( trim($this->jCfg['search']['colum'])!="" && trim($this->jCfg['search']['keyword']) != "" ){
			$this->db->like($this->jCfg['search']['colum'],$this->jCfg['search']['keyword']);
		}


		
		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
			$start = empty($p['offset'])?0:$p['offset'];
		}

		$this->db->order_by("sos_ref_message_email.message_id","DESC");
		if(trim($this->jCfg['search']['order_by'])!="")
			$this->db->order_by($this->jCfg['search']['order_by'],$this->jCfg['search']['order_dir']);
		
		$qry = $this->db->get("sos_ref_message_email");
		if($count==FALSE){
			$total = $this->message($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"start" => $start,
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	

	}

	function provider($p=array(),$count=FALSE){
		
		$total = 0;

		$this->db->select('sos_ref_provider_type.*');

		/* where or like conditions */
		if( trim($this->jCfg['search']['date_start'])!="" && trim($this->jCfg['search']['date_end']) != "" ){
			$this->db->where("( sos_ref_provider_type.time_add >= '".$this->jCfg['search']['date_start']." 01:00:00' AND sos_ref_provider_type.time_add <= '".$this->jCfg['search']['date_end']." 23:59:00' )");
		}

		if( $this->jCfg['user']['is_all'] != 1 ){
			$this->db->where("sos_ref_provider_type.protype_site_id",$this->jCfg['user']['site']['id']);
		}

		// dont modified....
		if( trim($this->jCfg['search']['colum'])=="" && trim($this->jCfg['search']['keyword']) != "" ){
			$str_like = "( ";
			$i=0;
			foreach ($p['param'] as $key => $value) {
				if($key != ""){
					$str_like .= $i!=0?"OR":"";
					$str_like .=" ".$key." LIKE '%".$this->jCfg['search']['keyword']."%' ";			
					$i++;
				}
			}
			$str_like .= " ) ";
			$this->db->where($str_like);
		}

		if( trim($this->jCfg['search']['colum'])!="" && trim($this->jCfg['search']['keyword']) != "" ){
			$this->db->like($this->jCfg['search']['colum'],$this->jCfg['search']['keyword']);
		}


		
		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
			$start = empty($p['offset'])?0:$p['offset'];
		}

		$this->db->order_by("sos_ref_provider_type.protype_id","DESC");
		if(trim($this->jCfg['search']['order_by'])!="")
			$this->db->order_by($this->jCfg['search']['order_by'],$this->jCfg['search']['order_dir']);
		
		$qry = $this->db->get("sos_ref_provider_type");
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

	function relationship($p=array(),$count=FALSE){
		
		$total = 0;

		$this->db->select('sos_ref_relationship_type.*');

		/* where or like conditions */
		if( trim($this->jCfg['search']['date_start'])!="" && trim($this->jCfg['search']['date_end']) != "" ){
			$this->db->where("( sos_ref_relationship_type.time_add >= '".$this->jCfg['search']['date_start']." 01:00:00' AND sos_ref_relationship_type.time_add <= '".$this->jCfg['search']['date_end']." 23:59:00' )");
		}

		if( $this->jCfg['user']['is_all'] != 1 ){
			$this->db->where("sos_ref_relationship_type.relationship_site_id",$this->jCfg['user']['site']['id']);
		}

		// dont modified....
		if( trim($this->jCfg['search']['colum'])=="" && trim($this->jCfg['search']['keyword']) != "" ){
			$str_like = "( ";
			$i=0;
			foreach ($p['param'] as $key => $value) {
				if($key != ""){
					$str_like .= $i!=0?"OR":"";
					$str_like .=" ".$key." LIKE '%".$this->jCfg['search']['keyword']."%' ";			
					$i++;
				}
			}
			$str_like .= " ) ";
			$this->db->where($str_like);
		}

		if( trim($this->jCfg['search']['colum'])!="" && trim($this->jCfg['search']['keyword']) != "" ){
			$this->db->like($this->jCfg['search']['colum'],$this->jCfg['search']['keyword']);
		}


		
		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
			$start = empty($p['offset'])?0:$p['offset'];
		}

		$this->db->order_by("sos_ref_relationship_type.relationship_id","DESC");
		if(trim($this->jCfg['search']['order_by'])!="")
			$this->db->order_by($this->jCfg['search']['order_by'],$this->jCfg['search']['order_dir']);
		
		$qry = $this->db->get("sos_ref_relationship_type");
		if($count==FALSE){
			$total = $this->relationship($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"start" => $start,
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	

	}

	function service($p=array(),$count=FALSE){
		
		$total = 0;

		$this->db->select('sos_ref_service_type.*,sos_client.client_name');
		$this->db->join("sos_client","sos_client.client_id = sos_ref_service_type.service_clientid");

		/* where or like conditions */
		if( trim($this->jCfg['search']['date_start'])!="" && trim($this->jCfg['search']['date_end']) != "" ){
			$this->db->where("( sos_ref_service_type.time_add >= '".$this->jCfg['search']['date_start']." 01:00:00' AND sos_ref_service_type.time_add <= '".$this->jCfg['search']['date_end']." 23:59:00' )");
		}

		if( $this->jCfg['user']['is_all'] != 1 ){
			$this->db->where("sos_ref_service_type.service_site_id",$this->jCfg['user']['site']['id']);
		}

		// dont modified....
		if( trim($this->jCfg['search']['colum'])=="" && trim($this->jCfg['search']['keyword']) != "" ){
			$str_like = "( ";
			$i=0;
			foreach ($p['param'] as $key => $value) {
				if($key != ""){
					$str_like .= $i!=0?"OR":"";
					$str_like .=" ".$key." LIKE '%".$this->jCfg['search']['keyword']."%' ";			
					$i++;
				}
			}
			$str_like .= " ) ";
			$this->db->where($str_like);
		}

		if( trim($this->jCfg['search']['colum'])!="" && trim($this->jCfg['search']['keyword']) != "" ){
			$this->db->like($this->jCfg['search']['colum'],$this->jCfg['search']['keyword']);
		}


		
		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
			$start = empty($p['offset'])?0:$p['offset'];
		}

		$this->db->order_by("sos_ref_service_type.service_id","DESC");
		if(trim($this->jCfg['search']['order_by'])!="")
			$this->db->order_by($this->jCfg['search']['order_by'],$this->jCfg['search']['order_dir']);
		
		$qry = $this->db->get("sos_ref_service_type");
		if($count==FALSE){
			$total = $this->service($p,TRUE);
			return array(
					"data"	=> $qry->result(),
					"start" => $start,
					"total" => $total
				);
		}else{
			return $qry->num_rows();
		}	

	}

	function benefit($p=array(),$count=FALSE){
		
		$total = 0;

		$this->db->join("sos_client","sos_client.client_id = sos_ref_benefit.benefit_clientid");
		$this->db->join("sos_ref_service_type","sos_ref_service_type.service_id = sos_ref_benefit.benefit_serviceid");
		$this->db->join("sos_ref_plan_type","sos_ref_plan_type.plan_id = sos_ref_benefit.benefit_planid","left");

		/* where or like conditions */
		if( trim($this->jCfg['search']['date_start'])!="" && trim($this->jCfg['search']['date_end']) != "" ){
			$this->db->where("( sos_ref_benefit.time_add >= '".$this->jCfg['search']['date_start']." 01:00:00' AND sos_ref_benefit.time_add <= '".$this->jCfg['search']['date_end']." 23:59:00' )");
		}

		if( $this->jCfg['user']['is_all'] != 1 ){
			$this->db->where("sos_ref_benefit.benefit_site_id",$this->jCfg['user']['site']['id']);
		}

		// dont modified....
		if( trim($this->jCfg['search']['colum'])=="" && trim($this->jCfg['search']['keyword']) != "" ){
			$str_like = "( ";
			$i=0;
			foreach ($p['param'] as $key => $value) {
				if($key != ""){
					$str_like .= $i!=0?"OR":"";
					$str_like .=" ".$key." LIKE '%".$this->jCfg['search']['keyword']."%' ";			
					$i++;
				}
			}
			$str_like .= " ) ";
			$this->db->where($str_like);
		}

		if( trim($this->jCfg['search']['colum'])!="" && trim($this->jCfg['search']['keyword']) != "" ){
			$this->db->like($this->jCfg['search']['colum'],$this->jCfg['search']['keyword']);
		}


		
		if($count==FALSE){
			if( isset($p['offset']) && isset($p['limit']) ){
				$p['offset'] = empty($p['offset'])?0:$p['offset'];
				$this->db->limit($p['limit'],$p['offset']);
			}
			$start = empty($p['offset'])?0:$p['offset'];
		}

		$this->db->order_by("sos_ref_benefit.benefit_id","DESC");
		if(trim($this->jCfg['search']['order_by'])!="")
			$this->db->order_by($this->jCfg['search']['order_by'],$this->jCfg['search']['order_dir']);
		
		$qry = $this->db->get("sos_ref_benefit");
		if($count==FALSE){
			$total = $this->benefit($p,TRUE);
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