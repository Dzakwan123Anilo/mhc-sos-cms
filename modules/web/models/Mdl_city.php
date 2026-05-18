<?php
class mdl_city extends CI_Model{ 
	
	var $tabel = 'app_kabupaten';
	
	function __construct(){
		parent::__construct();
	} 
		
	function data($p=array()){
	
		$where = '';

		$sql = "
		 	select 
				SQL_CALC_FOUND_ROWS *
			FROM ".$this->tabel." 
			WHERE 1=1
				".$where." 
			ORDER BY kab_nama ASC 
		";	 
		
		if( isset($p['limit']) && isset($p['offset']) ){
			$offset = empty($p['offset'])?0:$p['offset'];				
			$sql .= " LIMIT ".$offset.",".$p['limit']." ";
		}
		$query = $this->db->query($sql);
		
		$found_rows = $this->db->query('SELECT FOUND_ROWS() as found_rows');

		return array(
			"data" 	=> $query->result(),
			"count"	=> $found_rows->row()
		);
	}
	
	function getcity($id){
        $this->db->order_by("city_title", "ASC");
        $this->db->where("city_province_id", $id);
        $query = $this->db->get("mt_app_city");
        if ($query->num_rows() > 0) return $query->result();              
    } 
}