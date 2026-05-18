<?php
class Mdl_province extends CI_Model{ 
	
	var $tabel = 'app_propinsi';
	
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
			ORDER BY propinsi_nama ASC 
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
}