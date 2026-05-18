<?php
include_once(APPPATH."libraries/FrontController.php");
class Import extends FrontController {

    function __construct()  
    {
        parent::__construct();      
    }
     
    function index() {
        die('access denied');
    }

    function cek_region($name=""){
		return true;
    }
    function cek_location($name=""){
		return true;
    }
    function member_plantype($name=""){
    	$cek = $this->db->get_where("sos_ref_plan_type",array(
    		"plan_type" => trim($name)
    	))->num_rows();
    	if( $cek > 0 ){
    		return true;
    	}else{
			return false;
		}
    }	
    function cek_group($name=""){
    	$groupid = array(); $flag = false;
    	$arrname = explode(',',$name);    	
    	for($i=0; $i<count($arrname); $i++) {
			$cek = $this->db->get_where("sos_tref_provider_group",array(
	    		"provider_group" => trim($arrname[$i])
	    	))->num_rows();
	    	if( $cek > 0 ){
	    		$flag = true;
	    	}else{
				$flag = false;
				break;
			}
    	}
    	return $flag;
    }
    
    function get_group_id($name=""){
    	$groupid = array();
    	$arrname = explode(',',$name);    	
    	for($i=0; $i<count($arrname); $i++) {
		    $cek = $this->db->get_where("sos_tref_provider_group",array(
	    		"provider_group" => trim($arrname[$i])
	    	))->row();
	    	if( isset($cek->providergroupId) ){
	    		$groupid[] = $cek->providergroupId;
	    	}
		}
		
		return $groupid;
    }

    function import_data(){
        $return = array(
                "status" => 0,
                "message"=> "Tidak Ada Pesan",
                "data"   => array()
            );

        $key = md5($this->jCfg['user']['id'].$this->jCfg['user']['name']);
        if( trim($this->input->post('key')) == trim($key) ){
			
			
			//cek item data...
			$msg_error = array();
			$cek_location = $this->cek_location($this->input->post('address'));
			if( $cek_location == false ){
				$msg_error[] = 'Location salah';
			}
			
			$cek_region = $this->cek_region($this->input->post('region'));
			if( $cek_region == false ){
				$msg_error[] = 'Region salah';
			}
			
			$cek_plantype = $this->member_plantype($this->input->post('plan_type'));
			if( $cek_plantype == false ){
				$msg_error[] = 'Plan Type salah';
			}
			
			$cek_group = $this->cek_group($this->input->post('group'));
			if( $cek_group == false ){
				$msg_error[] = 'Group salah';
			}
			
			if( count($msg_error) > 0 ){
				$return = array(
	                "status" => 0,
	                "message"=> implode(",", $msg_error)." silahkan cek kembali ",
	                "data"   => array()
	            );
				die(json_encode($return));
			}
			
            //cek tabel member by card no..
            $cek = $this->db->get_where("sos_member",array(
                    "member_cardno" => $this->input->post('card_no')
                ));

            $site_id = $this->input->post('import_site_id');
            if($this->jCfg['user']['is_all'] != 1){
                $site_id = $this->jCfg['user']['site']['id'];
            }

            $data = array(
                        "member_clientid" => $this->input->post('cid'),
                        "member_name" => $this->input->post('member_name'),
                        "member_relationshiptype" => $this->input->post('relationship'),
                        "member_sex" => $this->input->post('gender'),
                        "member_birthdate" => $this->input->post('bod'),
                        "member_location" => trim($this->input->post('address')), // dari address -> cek ke table tref_location
                        "member_region" => trim($this->input->post('region')), // cek ke table tref_region
                        "member_roomclass" => $this->input->post('room_class'),
                        "member_plantype" => trim($this->input->post('plan_type')), // cek tref_plan_type
                        "member_employmentstatus" => $this->input->post('emp_status'),
                        "member_statuscard" => $this->input->post('emp_status'),
                        "member_updatecode" => $this->input->post('upd_code'),
                        "member_dentallimit" => $this->input->post('dental_unit'),
                        "member_inport_id" => $this->input->post('import_id'),
                        "member_remark" => $this->input->post('remark')
                    );


            $row = $cek->row();
            if( $cek->num_rows() == 0 ){
                //jika belum terdaftar..
                $data['user_add'] = $this->jCfg['user']['id'];
                $data['time_add'] = date("Y-m-d H:i:s");
                $data['member_cardno'] = $this->input->post('card_no');
                $data['member_employeeid'] = $this->input->post('card_no');
                $data['member_cardserialno'] = '00';
                $data['member_status'] = 1;
                $data['member_site_id'] = $site_id;
                
                $m = $this->db->insert("sos_member",$data);
                $member_id_last = $this->db->insert_id();
                           
                if($m){
                	
                	//update.....
	                $group_id = $this->get_group_id($this->input->post('group'));
	                for($i=0; $i<count($group_id); $i++) {
		                $this->db->insert("sos_tmas_member_provider",array(
	                        "providergroupId"   => $group_id[$i],
	                        "memberId"  		=> $member_id_last,
	                        "createdDate"       => date("Y-m-d H:i:s"),
	                        "createdBy"         => $this->jCfg['user']['id']
	                    ));
	                }
                    
                    $this->db->update("sos_member_tmp",array(
                            "status_import" => 1,
                            "import_time" => date("Y-m-d H:i:s")
                        ),array(
                            "id" => $this->input->post('id')
                        ));
                        
                    $return = array(
                        "status" => 1,
                        "message"=> "Insert Data Success",
                        "data"   => array(
                                "last_id" => $member_id_last
                            )
                    );
                }else{
                    $return = array(
                        "status" => 0,
                        "message"=> "Insert Data Gagal",
                        "data"   => array()
                    );
                }
            }else{
                // jika sudah ada..
                $upd_code = $this->input->post('upd_code');

                if( trim($upd_code) == "N" ){
                    $return = array(
                        "status" => 0,
                        "message"=> "Data Dengan Nomor Kartu ".$this->input->post('card_no')." sudah ada",
                        "data"   => array()
                    );
                }

                if( trim($upd_code) == "U" ){
                
                	$member_row = $this->db->get_where("sos_member",array(
                            "member_cardno" => $this->input->post('card_no')
                        ))->row();
                        
					$this->db->delete("sos_tmas_member_provider",array("memberId" => $member_row->member_id));
	                $group_id = $this->get_group_id($this->input->post('group'));
	                for($i=0; $i<count($group_id); $i++) {
		                $this->db->insert("sos_tmas_member_provider",array(
	                        "providergroupId"   => $group_id[$i],
	                        "memberId"  		=> $member_row->member_id,
	                        "createdDate"       => date("Y-m-d H:i:s"),
	                        "createdBy"         => $this->jCfg['user']['id']
	                    ));
	                }
	                
//                    $serial_no_tmp = (int)$row->member_cardserialno+1;
                    $serial_no = str_repeat("0", 2-strlen($serial_no_tmp)).$serial_no_tmp;
                    $data['user_update'] = $this->jCfg['user']['id'];
                    $data['time_update'] = date("Y-m-d H:i:s");
//                    $data['member_cardserialno'] = $serial_no;
                    $m = $this->db->update("sos_member",$data,array(
                            "member_cardno" => $this->input->post('card_no')
                        ));

                    if($m){

                        $this->db->update("sos_member_tmp",array(
                            "status_import" => 1,
                            "note"  => "Update Data",
                            "import_time" => date("Y-m-d H:i:s")
                        ),array(
                            "id" => $this->input->post('id')
                        ));

                        $return = array(
                            "status" => 1,
                            "message"=> "Update Data Success",
                            "data"   => array(
                                    "last_id" => $row->member_id
                                )
                        );
                    }else{
                        $return = array(
                            "status" => 0,
                            "message"=> "Update Data Gagal",
                            "data"   => array()
                        );
                    }

                }

                if( trim($upd_code) == "D" ){

                    $data['user_update'] = $this->jCfg['user']['id'];
                    $data['time_update'] = date("Y-m-d H:i:s");
                    $data['member_statuscard'] = 'Inactive';
                    $m = $this->db->update("sos_member",$data,array(
                            "member_cardno" => $this->input->post('card_no')
                        ));

                    if($m){

                        $this->db->update("sos_member_tmp",array(
                            "status_import" => 1,
                            "note"  => "Inactive Data",
                            "import_time" => date("Y-m-d H:i:s")
                        ),array(
                            "id" => $this->input->post('id')
                        ));

                        $return = array(
                            "status" => 1,
                            "message"=> "Inactive Data Success",
                            "data"   => array(
                                    "last_id" => $row->member_id
                                )
                        );
                    }else{
                        $return = array(
                            "status" => 0,
                            "message"=> "Inactive Data Gagal",
                            "data"   => array()
                        );
                    }

                }

            }


        }else{
            $return = array(
                "status" => 0,
                "message"=> "Akses Ditolak!",
                "data"   => array()
            );
        }

        die(json_encode($return));
    }

    function export_member(){
        
    }
}