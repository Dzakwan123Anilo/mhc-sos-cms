<?php
include_once(APPPATH."libraries/FrontController.php");
class Data extends FrontController {

	function __construct()  
	{
		parent::__construct(); 		
	}
	
	function index() {}

	function kota(){
		
		$prov = $this->input->post('prov');
		$kota = $this->input->post('kota');
		$where = empty($prov)?array():array("kab_propinsi_id" => $this->db->get_where("app_propinsi",array("propinsi_nama" => $prov))->row()->propinsi_id, "kab_status" => 1);
		
		$this->db->order_by("kab_nama");
		$m = $this->db->get_where("app_kabupaten",$where)->result();

		$html = "<option value=''> - pilih kota/kabupaten - </option>";
		foreach ((array)$m as $k => $v) {
			$s = $v->kab_nama==$kota?'selected="selected"':'';
			$html .= "<option value='".$v->kab_nama."' $s >".$v->kab_nama."</option>";
		}

		die($html);
	}

	function get_transactions(){		
		$return = array();
		$data = getTransactions();
		foreach ($data as $k => $v) {
			$return[] = array($v->layanan,$v->weeks,$v->months,$v->years);
		}
		
		echo json_encode($return);	
	}

	function get_active_member(){		
		$clientid = $this->input->post('clientid');
		$period = $this->input->post('period');
		$p = explode('-',$period);
		$fmonth = count($p)>1?$p[0]:'';
		$year = count($p)>1?$p[1]:$p[0];
//		debugCode(array($clientid,$fmonth,$year));
		$html = '';
		if(empty($clientid)) { 
			$month = empty($fmonth)?date('n'):$fmonth;
			$data = getNumberActiveMember(array('period' => $year.'-'.$month, 'fmonth' => $fmonth));
			
			if( count($data) > 0 ) {
            	$no = 0; $cname = ''; $m = 1;
                foreach($data as $r) {
					$date = $year.'-'.($month-$m).'-'.date('d');
					
                	$html .= '<tr>';
					$html .= '	<td>'.++$no.'</td>';
					$html .= '	<td nowrap="nowrap">'.($cname!=$r->client_name?$r->client_name:'').'</td>';
					$html .= '	<td nowrap="nowrap">'.$r->showit.'</td>';
					$html .= '	<td>'.($r->main_insured>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.activeMemberExport(0,\'\',\''.$r->client_id.'\',\''.$date.'\',0)">'.$r->main_insured.'</a>':$r->main_insured).'</td>';
					$html .= '	<td>'.($r->dependent>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.activeMemberExport(0,\'\',\''.$r->client_id.'\',\''.$date.'\',1)">'.$r->dependent.'</a>':$r->dependent).'</td>';
					$html .= '	<td>'.(($r->main_insured+$r->dependent)>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.activeMemberExport(0,\'\',\''.$r->client_id.'\',\''.$date.'\',2)">'.($r->main_insured+$r->dependent).'</a>':($r->main_insured+$r->dependent)).'</td>';
					$html .= '</tr>';
                	
					$cname = $r->client_name;
					$m--;
                }
			}
		} else {
			$x = 2; $mt = 1;
			$m = empty($fmonth)?1:0; 
			$month = empty($fmonth)?date('n'):$fmonth-1;
			
			for($i=0; $i<$x; $i++) {
				$r = getSingleNumberActiveMember(array('clientid' => $clientid, 'month' => empty($fmonth)?($month-$m):($month+$i), 'fmonth' => $fmonth, 'year' => $year));
				$date = $year.'-'.($month-$mt).'-1';
			 
				$html .= '<tr>';
				$html .= '	<td>'.($i+1).'</td>';
				$html .= '	<td nowrap="nowrap">'.($i==0?$r->client_name:'').'</td>';
				$html .= '	<td nowrap="nowrap">'.$r->showit.'</td>';
				$html .= '	<td>'.($r->main_insured>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.activeMemberExport(0,\'\',\''.$r->client_id.'\',\''.$date.'\',0)">'.$r->main_insured.'</a>':$r->main_insured).'</td>';
				$html .= '	<td>'.($r->dependent>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.activeMemberExport(0,\'\',\''.$r->client_id.'\',\''.$date.'\',1)">'.$r->dependent.'</a>':$r->dependent).'</td>';
				$html .= '	<td>'.(($r->main_insured+$r->dependent)>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.activeMemberExport(0,\'\',\''.$r->client_id.'\',\''.$date.'\',2)">'.($r->main_insured+$r->dependent).'</a>':($r->main_insured+$r->dependent)).'</td>';
				$html .= '</tr>';
				
				if(empty($fmonth)) $m--;
				$mt--;
			}
			
		}
		
		die($html);
	}

	function get_swipe_by_client(){		
		$clientid = $this->input->post('clientid');
		$period = $this->input->post('period');
		$p = explode('-',$period);
		$fmonth = count($p)>1?$p[0]:'';
		$year = count($p)>1?$p[1]:$p[0];
		
		$html = '';
		if(empty($clientid)) { 
			$month = empty($fmonth)?date('n'):$fmonth;
			$data = getNumberSwipeByClient(array('period' => $year.'-'.$month, 'fmonth' => $fmonth));
			
			if( count($data) > 0 ) {
            	$no = 0; $cname = ''; $m = 1;
                foreach($data as $r) {
					$date = $m<0?date('Y-m-d'):$year.'-'.($month-$m).'-'.date('d');
					$today = $m<0?'':$m+1;
					
					$html .= '<tr>';
					$html .= '	<td>'.++$no.'</td>';
					$html .= '	<td nowrap="nowrap">'.($cname!=$r->client_name?$r->client_name:'').'</td>';
					$html .= '	<td nowrap="nowrap">'.$r->showit.'</td>';
					$html .= '	<td>'.($r->inpatient>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeByClientExport(0,\'\',\''.$r->client_id.'\',\''.$date.'\',\''.$today.'\',1)">'.$r->inpatient.'</a>':$r->inpatient).'</td>';
					$html .= '	<td>'.($r->outpatient>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeByClientExport(0,\'\',\''.$r->client_id.'\',\''.$date.'\',\''.$today.'\',2)">'.$r->outpatient.'</a>':$r->outpatient).'</td>';
					$html .= '	<td>'.($r->dental>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeByClientExport(0,\'\',\''.$r->client_id.'\',\''.$date.'\',\''.$today.'\',3)">'.$r->dental.'</a>':$r->dental).'</td>';
					$html .= '	<td>'.($r->emergency>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeByClientExport(0,\'\',\''.$r->client_id.'\',\''.$date.'\',\''.$today.'\',4)">'.$r->emergency.'</a>':$r->emergency).'</td>';
					$html .= '	<td>'.(($r->inpatient+$r->outpatient+$r->dental+$r->emergency)>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeByClientExport(0,\'\',\''.$r->client_id.'\',\''.$date.'\',\''.$today.'\',0)">'.($r->inpatient+$r->outpatient+$r->dental+$r->emergency).'</a>':($r->inpatient+$r->outpatient+$r->dental+$r->emergency)).'</td>';
					$html .= '</tr>';
                	
					$cname = $r->client_name;
					$m--;
                }
			}
		} else {
			$x = empty($fmonth)?3:2;		
			$m = empty($fmonth)?2:0;		
			$f = empty($fmonth)?false:true; 
			$month = empty($fmonth)?date('n'):$fmonth-1;
			
			$mt = 1;
			for($i=0; $i<$x; $i++) {
				$r = getSingleNumberSwipeByClient(array('clientid' => $clientid, 'month' => empty($fmonth)?($month-$m):($month+$i), 'flag' => $f, 'multi' => $x, 'fmonth' => $fmonth, 'year' => $year));
				$date = $mt<0?date('Y-m-d'):$year.'-'.($month-$mt).'-1';
				$today = $mt<0?'':$mt+1;
			 	
				$html .= '<tr>';
				$html .= '	<td>'.($i+1).'</td>';
				$html .= '	<td nowrap="nowrap">'.($i==0?$r->client_name:'').'</td>';
				$html .= '	<td nowrap="nowrap">'.$r->showit.'</td>';
				$html .= '	<td>'.($r->inpatient>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeByClientExport(0,\'\',\''.$r->client_id.'\',\''.$date.'\',\''.$today.'\',1)">'.$r->inpatient.'</a>':$r->inpatient).'</td>';
				$html .= '	<td>'.($r->outpatient>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeByClientExport(0,\'\',\''.$r->client_id.'\',\''.$date.'\',\''.$today.'\',2)">'.$r->outpatient.'</a>':$r->outpatient).'</td>';
				$html .= '	<td>'.($r->dental>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeByClientExport(0,\'\',\''.$r->client_id.'\',\''.$date.'\',\''.$today.'\',3)">'.$r->dental.'</a>':$r->dental).'</td>';
				$html .= '	<td>'.($r->emergency>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeByClientExport(0,\'\',\''.$r->client_id.'\',\''.$date.'\',\''.$today.'\',4)">'.$r->emergency.'</a>':$r->emergency).'</td>';
				$html .= '	<td>'.(($r->inpatient+$r->outpatient+$r->dental+$r->emergency)>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeByClientExport(0,\'\',\''.$r->client_id.'\',\''.$date.'\',\''.$today.'\',0)">'.($r->inpatient+$r->outpatient+$r->dental+$r->emergency).'</a>':($r->inpatient+$r->outpatient+$r->dental+$r->emergency)).'</td>';
				$html .= '</tr>';
				
				if(empty($fmonth)) $m--;
				$mt--;
			}
		}
		
		die($html);
	}

	function get_swipe_more(){		
		$clientid = $this->input->post('clientid');
		$period = $this->input->post('period');
		$p = explode('-',$period);
		$fmonth = count($p)>1?$p[0]:'';
		$year = count($p)>1?$p[1]:$p[0];
		
		$html = '';
		if(empty($clientid)) { 
			$month = empty($fmonth)?date('n'):$fmonth;
			$data = getNumberMemberSwipeMoreThanOnce(array('period' => $year.'-'.$month, 'fmonth' => $fmonth));
			
			if( count($data) > 0 ) {
            	$no = 0; $cname = ''; $m = 1;
                foreach($data as $r) {
					$date = $m<0?date('Y-m-d'):$year.'-'.($month-$m).'-'.date('d');
					$today = $m<0?'':$m+1;
					
					$html .= '<tr>';
					$html .= '	<td>'.++$no.'</td>';
					$html .= '	<td nowrap="nowrap">'.($cname!=$r->client_name?$r->client_name:'').'</td>';
					$html .= '	<td nowrap="nowrap">'.$r->showit.'</td>';
					$html .= '	<td>'.($r->inpatient>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeMoreExport(0,\'\',\''.$r->client_id.'\',\''.$date.'\',\''.$today.'\',1)">'.$r->inpatient.'</a>':$r->inpatient).'</td>';
					$html .= '	<td>'.($r->outpatient>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeMoreExport(0,\'\',\''.$r->client_id.'\',\''.$date.'\',\''.$today.'\',2)">'.$r->outpatient.'</a>':$r->outpatient).'</td>';
					$html .= '	<td>'.($r->dental>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeMoreExport(0,\'\',\''.$r->client_id.'\',\''.$date.'\',\''.$today.'\',3)">'.$r->dental.'</a>':$r->dental).'</td>';
					$html .= '	<td>'.($r->emergency>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeMoreExport(0,\'\',\''.$r->client_id.'\',\''.$date.'\',\''.$today.'\',4)">'.$r->emergency.'</a>':$r->emergency).'</td>';
					$html .= '	<td>'.(($r->inpatient+$r->outpatient+$r->dental+$r->emergency)>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeMoreExport(0,\'\',\''.$r->client_id.'\',\''.$date.'\',\''.$today.'\',0)">'.($r->inpatient+$r->outpatient+$r->dental+$r->emergency).'</a>':($r->inpatient+$r->outpatient+$r->dental+$r->emergency)).'</td>';
					$html .= '</tr>';
                	
					$cname = $r->client_name;
					$m--;
                }
			}
		} else {
			$x = empty($fmonth)?3:2;		
			$m = empty($fmonth)?2:0;		
			$f = empty($fmonth)?false:true; 
			$month = empty($fmonth)?date('n'):$fmonth-1;
			
			$mt = 1;
			for($i=0; $i<$x; $i++) {
				$r = getSingleMemberSwipeMoreThanOnce(array('clientid' => $clientid, 'month' => empty($fmonth)?($month-$m):($month+$i), 'flag' => $f, 'multi' => $x, 'fmonth' => $fmonth, 'year' => $year));
				$date = $mt<0?date('Y-m-d'):$year.'-'.($month-$mt).'-1';
				$today = $mt<0?'':$mt+1;
			 	
				$html .= '<tr>';
				$html .= '	<td>'.($i+1).'</td>';
				$html .= '	<td nowrap="nowrap">'.($i==0?$r->client_name:'').'</td>';
				$html .= '	<td nowrap="nowrap">'.$r->showit.'</td>';
				$html .= '	<td>'.($r->inpatient>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeMoreExport(0,\'\',\''.$r->client_id.'\',\''.$date.'\',\''.$today.'\',1)">'.$r->inpatient.'</a>':$r->inpatient).'</td>';
				$html .= '	<td>'.($r->outpatient>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeMoreExport(0,\'\',\''.$r->client_id.'\',\''.$date.'\',\''.$today.'\',2)">'.$r->outpatient.'</a>':$r->outpatient).'</td>';
				$html .= '	<td>'.($r->dental>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeMoreExport(0,\'\',\''.$r->client_id.'\',\''.$date.'\',\''.$today.'\',3)">'.$r->dental.'</a>':$r->dental).'</td>';
				$html .= '	<td>'.($r->emergency>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeMoreExport(0,\'\',\''.$r->client_id.'\',\''.$date.'\',\''.$today.'\',4)">'.$r->emergency.'</a>':$r->emergency).'</td>';
				$html .= '	<td>'.(($r->inpatient+$r->outpatient+$r->dental+$r->emergency)>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeMoreExport(0,\'\',\''.$r->client_id.'\',\''.$date.'\',\''.$today.'\',0)">'.($r->inpatient+$r->outpatient+$r->dental+$r->emergency).'</a>':($r->inpatient+$r->outpatient+$r->dental+$r->emergency)).'</td>';
				$html .= '</tr>';
	
				if(empty($fmonth)) $m--;
				$mt--;
			}
		}
		
		die($html);
	}

	function get_swipe_by_provider(){		
		$providerid = $this->input->post('providerid');
		$period = $this->input->post('period');
		$p = explode('-',$period);
		$fmonth = count($p)>1?$p[0]:'';
		$year = count($p)>1?$p[1]:$p[0];
		
		$x = empty($fmonth)?3:2;		
		$f = empty($fmonth)?false:true; 
		$month = empty($fmonth)?date('n'):$fmonth-1;
		$amonth = empty($fmonth)?date('n'):$fmonth;
		
		$swipeprovider = getNumberSwipeByProvider(array('providerid' => $providerid, 'month' => $fmonth, 'year' => $year));
		$html = ''; $xno = 0; 
		foreach($swipeprovider as $r){
			$m = empty($fmonth)?2:0; $mt = 1;	
			for($i=0; $i<$x; $i++) {
				$data = getSingleNumberSwipeByProvider(array('providerid' => $r->provider_id, 'month' => empty($fmonth)?($month-$m):($month+$i), 'flag' => $f, 'multi' => $x, 'fmonth' => $fmonth, 'year' => $year));
				$pname = ''; $no = $xno;
				foreach($data as $s){
					$date = $mt<0?date('Y-m-d'):$year.'-'.($amonth-$mt).'-1';
					$today = $mt<0?'':$mt+1;
				 	
					$html .= '<tr>';
					$html .= '	<td>'.++$no.'</td>';
					$html .= '	<td nowrap="nowrap">'.($pname!=$s->provider_name?$s->provider_name:'').'</td>';
					$html .= '	<td nowrap="nowrap">'.$s->showit.'</td>';
					$html .= '	<td>'.($s->inpatient>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeByProviderExport(0,\'\',\''.$r->provider_id.'\',\''.$date.'\',\''.$today.'\',1)">'.$s->inpatient.'</a>':$s->inpatient).'</td>';
					$html .= '	<td>'.($s->outpatient>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeByProviderExport(0,\'\',\''.$r->provider_id.'\',\''.$date.'\',\''.$today.'\',2)">'.$s->outpatient.'</a>':$s->outpatient).'</td>';
					$html .= '	<td>'.($s->dental>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeByProviderExport(0,\'\',\''.$r->provider_id.'\',\''.$date.'\',\''.$today.'\',3)">'.$s->dental.'</a>':$s->dental).'</td>';
					$html .= '	<td>'.($s->emergency>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeByProviderExport(0,\'\',\''.$r->provider_id.'\',\''.$date.'\',\''.$today.'\',4)">'.$s->emergency.'</a>':$s->emergency).'</td>';
					$html .= '	<td>'.($s->total>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeByProviderExport(0,\'\',\''.$r->provider_id.'\',\''.$date.'\',\''.$today.'\',0)">'.$s->total.'</a>':$s->total).'</td>';
					$html .= '</tr>';
					
					$pname = $s->provider_name;
				}
				$xno = $no;
				if(empty($fmonth)) $m--;
				$mt--;
			}
		}
		
		die($html);
	}

	function get_user_login(){		
		$providerid = $this->input->post('providerid');
		
		$html = '';
		$data = getNumberUserLogin(array('providerid' => $providerid));
		$no = 0;
		foreach($data as $r){
			$html .= '<tr>';
			$html .= '	<td>'.++$no.'</td>';
			$html .= '	<td nowrap="nowrap">'.$r->provider_name.'</td>';
			$html .= '	<td>'.($r->prev>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.userLoginExport(0,\'\',\''.$r->provider_id.'\',1)">'.$r->prev.'</a>':$r->prev).'</td>';
			$html .= '	<td>'.($r->curr>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.userLoginExport(0,\'\',\''.$r->provider_id.'\',2)">'.$r->curr.'</a>':$r->curr).'</td>';
			$html .= '	<td>'.($r->today>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.userLoginExport(0,\'\',\''.$r->provider_id.'\',3)">'.$r->today.'</a>':$r->today).'</td>';
			$html .= '</tr>';
		}
		
		die($html);
	}
	
	function check_number() {
		$clientid = $this->input->post('clientid');
		$number = $this->input->post('number');
		$field = $this->input->post('field');
		
		$ret = check_number($clientid,$number,$field);
		
		echo json_encode($ret);
        /*$number = empty($num)?$this->input->post('number'):$num;
       	$err = array("* Maaf No. Id yang anda masukan tidak terdaftar, silahkan di cek kembali no. Id yang dimasukkan",
       				"* Maaf No. ID yang anda masukan sudah tidak lagi Aktif, silahkan hubungi International SOS untuk keterangan lebih lanjut",
       				"* Kartu tidak dapat digunakan di fasilitas medis ini",
       				"* Maaf No. ID yang anda masukan tidak sesuai, silahkan di cek kembali"
       			); 
       	$errFlag = 0;
		
        $flag = FALSE; $error = "";		
        if(!empty($number)) {
        	$memberid = get_name('sos_member', 'member_id', array('member_cardno' => $number));

			$mgroup = $this->db->get_where("sos_tmas_member_provider",array(
					"memberId" => $memberid
				))->result();
			$arr_groupid = array();
			foreach ((array)$mgroup as $p => $q) {
				$arr_groupid[] = $q->providergroupId;
			}

			//debugCode($this->jCfg['user']['providergroupid']);
			if( count($this->jCfg['user']['providergroupid']) == 0 ){
				$this->jCfg['user']['providergroupid'] = $arr_groupid;
			}
			//debugCode($arr_groupid);
			$row = $this->db->get_where('sos_member', array('member_cardno' => $number, 'member_statuscard' => 'Active'))->row();	
			if(count($row)>0) {
				$row = $this->db->get_where('sos_member', array('member_cardno' => $number))->row();	
				if(count($row)>0) {			
					$compare_result = array_intersect($arr_groupid, $this->jCfg['user']['providergroupid']);
					if(count($compare_result)>0) {		
	//					$row = $this->db->get_where('sos_member', array('member_cardno' => $number))->row();
	//					if(count($row)>0) {
							$flag = TRUE;
	//					} else $errFlag = 3;
					} else $errFlag = 2;
				} else $errFlag = 1;
			} else $errFlag = 0;
        }
//		debugCode(array($flag,$inv_org));
		if($flag) {
			if(empty($num))
	        	echo json_encode(array('status' => true, 'error' => ''));
	        else return array('status' => true, 'error' => '');
		} else {
			$error = $err[$errFlag];
			if(empty($num))
	        echo json_encode(array('status' => false, 'error' => $error));
	        else return array('status' => false, 'error' => $error);
			
	        /*for($x=0;$x<1000000;$x++){
                if($x == 990000){
					$arrayToJs[1] = false;
					$arrayToJs[2] = $error;
					echo json_encode($arrayToJs);
                }
	        }*/   
		//}
	}
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */