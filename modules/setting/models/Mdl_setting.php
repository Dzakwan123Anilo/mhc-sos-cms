<?php
class Mdl_setting extends CI_Model{ 
    function __construct(){
        parent::__construct();
    } 

    function site($p=array(),$count=FALSE){
        
        $total = 0;

        $this->db->select("app_site.*,app_site_template.template_bg_head,
            app_site_template.template_bg_head_show,app_site_template.template_color,app_site_template.template_bg,
            app_site_template.template_bg_show,app_site_template.template_name
            ");

        if( isset($p['id']) && trim($p['id'])!="" && trim($p['id'])!=0 ){
            $this->db->where("app_site.site_id",$p['id']);
        }
    
        if($count==FALSE){
            if( isset($p['offset']) && isset($p['limit']) ){
                $p['offset'] = empty($p['offset'])?0:$p['offset'];
                $this->db->limit($p['limit'],$p['offset']);
            }
        }

        $qry = $this->db->get("app_site");
        if($count==FALSE){
            $total = $this->site($p,TRUE);
            return array(
                    "data"  => $qry->result(),
                    "total" => $total
                );
        }else{
            return $qry->num_rows();
        }   

    }

    function rank($p=array(),$count=FALSE){
        
        $total = 0;
        $this->db->select("web_rank.*,app_site.site_name");
        $this->db->join("app_site","app_site.site_id = web_rank.rank_site_id");

        /* where or like conditions */
        if( trim($this->jCfg['search']['date_start'])!="" && trim($this->jCfg['search']['date_end']) != "" ){
            $this->db->where("( web_rank.time_add >= '".$this->jCfg['search']['date_start']." 01:00:00' AND web_rank.time_add <= '".$this->jCfg['search']['date_end']." 23:59:00' )");
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
        }
        $this->db->order_by('time_add','DESC');
        if(trim($this->jCfg['search']['order_by'])!="")
            $this->db->order_by($this->jCfg['search']['order_by'],$this->jCfg['search']['order_dir']);
        
        $qry = $this->db->get("web_rank");
        if($count==FALSE){
            $total = $this->rank($p,TRUE);
            return array(
                    "data"  => $qry->result(),
                    "total" => $total
                );
        }else{
            return $qry->num_rows();
        }   

    }

    function badge($p=array(),$count=FALSE){
        
        $total = 0;
        $this->db->select("web_badge.*,app_site.site_name");
        $this->db->join("app_site","app_site.site_id = web_badge.badge_site_id");

        /* where or like conditions */
        if( trim($this->jCfg['search']['date_start'])!="" && trim($this->jCfg['search']['date_end']) != "" ){
            $this->db->where("( web_badge.time_add >= '".$this->jCfg['search']['date_start']." 01:00:00' AND web_badge.time_add <= '".$this->jCfg['search']['date_end']." 23:59:00' )");
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
        }
        $this->db->order_by('time_add','DESC');
        if(trim($this->jCfg['search']['order_by'])!="")
            $this->db->order_by($this->jCfg['search']['order_by'],$this->jCfg['search']['order_dir']);
        
        $qry = $this->db->get("web_badge");
        if($count==FALSE){
            $total = $this->badge($p,TRUE);
            return array(
                    "data"  => $qry->result(),
                    "total" => $total
                );
        }else{
            return $qry->num_rows();
        }   

    }

}