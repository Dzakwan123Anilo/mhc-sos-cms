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
    // CP 1
    $services = [
      'Rawat Inap' => [
        'weeks' => 0,
        'months' => 0,
        'years' => 0
      ], 
      'Rawat Jalan' => [
        'weeks' => 0,
        'months' => 0,
        'years' => 0
      ], 
      'Emergency' => [
        'weeks' => 0,
        'months' => 0,
        'years' => 0
      ]
    ];
    
    $datetime = new DateTime();
    $weeks = $datetime->format("yW"); 
    $months = $datetime->format("ym"); 
    $years = $datetime->format("y"); 

		$transactions = getTransactions();
    
    foreach ($transactions as $transaction) {
      foreach ($services as $serviceType => $service) {
        if ($transaction->service_type != $serviceType) continue;
        $transactionDatetime = new DateTime($transaction->date_claim);
        $transactionWeeks = $transactionDatetime->format("yW"); 
        $transactionMonths = $transactionDatetime->format("ym"); 
        $transactionYears = $transactionDatetime->format("y"); 

        if ($transactionWeeks == $weeks) $services[$serviceType]['weeks'] += 1;
        if ($transactionMonths == $months) $services[$serviceType]['months'] += 1;
        if ($transactionYears == $years) $services[$serviceType]['years'] += 1;
      }
    }

		$datas = [];
		foreach ($services as $serviceType => $service) {
			$datas[] = [
        $serviceType,
        $service['weeks'],
        $service['months'],
        $service['years']
      ];
		}
		
		echo json_encode($datas);	
	}

	function get_active_member(){
    // CP 2 
		$clientid = $this->input->post('clientid');
		$period = $this->input->post('period');
    $period = $period ? $period : '';
		$p = explode('-', $period);
		$fmonth = count($p) > 1 ? $p[0] : '';
		$year = count($p) > 1 ? $p[1] : date('Y');
    // debugCode(array($clientid,$fmonth,$year));

    $periodFrom = '01-' . $this->input->post('periodFrom');
    // $periodFrom = '01-1-2024';
    $periodFrom = date('Y-m', strtotime($periodFrom));
    $periodFrom = new DateTime($periodFrom);

    $periodTo = '01-' . $this->input->post('periodTo');
    // $periodTo = '01-3-2024';
    $periodTo = date('Y-m', strtotime($periodTo));
    $periodTo = new DateTime($periodTo);
    $periodTo->modify('+1 month');

    // Buat objek DatePeriod dengan rentang tanggal awal dan akhir, dan interval bulanan
    $interval = DateInterval::createFromDateString('1 month');
    $period = new DatePeriod($periodFrom, $interval, $periodTo);

    // Iterasi melalui setiap bulan dalam periode
    $periods = [];
    foreach ($period as $date) {
        // Format tanggal ke dalam Y-m
        $periods[] = $date->format('Y-m');
    }

		$html = '';
    $month = empty($fmonth) ? date('n') : $fmonth;
    $data = getNumberActiveMember(array('clientid' => $clientid, 'periods' => $periods));
     
    if(count($data) > 0 ) {
        $no = 0; $cname = ''; $m = 1;
        foreach($data as $r) {
          $date = $year.'-'.($month-$m).'-'.date('d');
          
          $html .= '<tr>';
          $html .= '	<td>'.++$no.'</td>';
          $html .= '	<td nowrap="nowrap">'.($cname!=$r->client_name?$r->client_name:'').'</td>';
          $html .= '	<td nowrap="nowrap">'.$r->showit.'</td>';
          $html .= '	<td>'.($r->main_insured>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.activeMemberExport(0,\'\',\''.$r->client_id.'\',\''.$r->gen_date.'\',0)">'.$r->main_insured.'</a>':$r->main_insured).'</td>';
          $html .= '	<td>'.($r->dependent>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.activeMemberExport(0,\'\',\''.$r->client_id.'\',\''.$r->gen_date.'\',1)">'.$r->dependent.'</a>':$r->dependent).'</td>';
          $html .= '	<td>'.(($r->main_insured+$r->dependent)>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.activeMemberExport(0,\'\',\''.$r->client_id.'\',\''.$r->gen_date.'\',2)">'.($r->main_insured+$r->dependent).'</a>':($r->main_insured+$r->dependent)).'</td>';
          $html .= '</tr>';
                  
          $cname = $r->client_name;
          $m--;
        }
    }
    
		
		die($html);
	}

	function get_swipe_by_client(){		
    // CP 3
		$clientid = $this->input->post('clientid');
		$period = $this->input->post('period');
    $period = $period ? $period : '';
		$p = explode('-',$period);
		$fmonth = count($p)>1?$p[0]:'';
		$year = count($p)>1?$p[1]:$p[0];

    $periodFrom = '01-' . $this->input->post('periodFrom');
    // $periodFrom = '01-1-2024';
    $periodFrom = date('Y-m', strtotime($periodFrom));
    $periodFrom = new DateTime($periodFrom);

    $periodTo = '01-' . $this->input->post('periodTo');
    // $periodTo = '01-3-2024';
    $periodTo = date('Y-m', strtotime($periodTo));
    $periodTo = new DateTime($periodTo);
    $periodTo->modify('+1 month');

    // Buat objek DatePeriod dengan rentang tanggal awal dan akhir, dan interval bulanan
    $interval = DateInterval::createFromDateString('1 month');
    $period = new DatePeriod($periodFrom, $interval, $periodTo);

    // Iterasi melalui setiap bulan dalam periode
    $periods = [];
    foreach ($period as $date) {
        // Format tanggal ke dalam Y-m
        $periods[] = $date->format('Y-m');
    }
		
		$html = '';
    $month = empty($fmonth)?date('n'):$fmonth;
    $data = getNumberSwipeByClient(array('clientid' => $clientid, 'periods' => $periods));
    
    if( count($data) > 0 ) {
        $no = 0; $cname = ''; $m = 1;
        foreach($data as $r) {
          $date = $m<0?date('Y-m-d'):$year.'-'.($month-$m).'-'.date('d');
          $today = $m<0?'':$m+1;
          
          $html .= '<tr>';
          $html .= '	<td>'.++$no.'</td>';
          $html .= '	<td nowrap="nowrap">'.($cname!=$r->client_name?$r->client_name:'').'</td>';
          $html .= '	<td nowrap="nowrap">'.$r->showit.'</td>';
          $html .= '	<td>'.($r->inpatient>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeByClientExport(0,\'\',\''.$r->client_id.'\',\''.$r->gen_date.'\',\''.$today.'\',1)">'.$r->inpatient.'</a>':$r->inpatient).'</td>';
          $html .= '	<td>'.($r->outpatient>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeByClientExport(0,\'\',\''.$r->client_id.'\',\''.$r->gen_date.'\',\''.$today.'\',2)">'.$r->outpatient.'</a>':$r->outpatient).'</td>';
          $html .= '	<td>'.($r->dental>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeByClientExport(0,\'\',\''.$r->client_id.'\',\''.$r->gen_date.'\',\''.$today.'\',3)">'.$r->dental.'</a>':$r->dental).'</td>';
          $html .= '	<td>'.($r->emergency>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeByClientExport(0,\'\',\''.$r->client_id.'\',\''.$r->gen_date.'\',\''.$today.'\',4)">'.$r->emergency.'</a>':$r->emergency).'</td>';
          $html .= '	<td>'.(($r->inpatient+$r->outpatient+$r->dental+$r->emergency)>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeByClientExport(0,\'\',\''.$r->client_id.'\',\''.$r->gen_date.'\',\''.$today.'\',0)">'.($r->inpatient+$r->outpatient+$r->dental+$r->emergency).'</a>':($r->inpatient+$r->outpatient+$r->dental+$r->emergency)).'</td>';
          $html .= '</tr>';
                  
          $cname = $r->client_name;
          $m--;
        }
    }
		
		die($html);
	}

	function get_swipe_more(){		
		$clientid = $this->input->post('clientid');
		$period = $this->input->post('period');
    $period = $period ? $period : '';
		$p = explode('-',$period);
		$fmonth = count($p)>1?$p[0]:'';
		$year = count($p)>1?$p[1]:$p[0];

    $periodFrom = '01-' . $this->input->post('periodFrom');
    // $periodFrom = '01-1-2024';
    $periodFrom = date('Y-m', strtotime($periodFrom));
    $periodFrom = new DateTime($periodFrom);

    $periodTo = '01-' . $this->input->post('periodTo');
    // $periodTo = '01-3-2024';
    $periodTo = date('Y-m', strtotime($periodTo));
    $periodTo = new DateTime($periodTo);
    $periodTo->modify('+1 month');

    // Buat objek DatePeriod dengan rentang tanggal awal dan akhir, dan interval bulanan
    $interval = DateInterval::createFromDateString('1 month');
    $period = new DatePeriod($periodFrom, $interval, $periodTo);

    // Iterasi melalui setiap bulan dalam periode
    $periods = [];
    foreach ($period as $date) {
        // Format tanggal ke dalam Y-m
        $periods[] = $date->format('Y-m');
    }
		
		$html = '';
    $month = empty($fmonth)?date('n'):$fmonth;
    $data = getNumberMemberSwipeMoreThanOnce(array('clientid' => $clientid, 'periods' => $periods));
    
    if( count($data) > 0 ) {
      $no = 0; $cname = ''; $m = 1;
      foreach($data as $r) {
        $date = $m<0?date('Y-m-d'):$year.'-'.($month-$m).'-'.date('d');
        $today = $m<0?'':$m+1;
        
        $html .= '<tr>';
        $html .= '	<td>'.++$no.'</td>';
        $html .= '	<td nowrap="nowrap">'.($cname!=$r->client_name?$r->client_name:'').'</td>';
        $html .= '	<td nowrap="nowrap">'.$r->showit.'</td>';
        $html .= '	<td>'.($r->inpatient>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeMoreExport(0,\'\',\''.$r->client_id.'\',\''.$r->gen_date.'\',\''.$today.'\',1)">'.$r->inpatient.'</a>':$r->inpatient).'</td>';
        $html .= '	<td>'.($r->outpatient>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeMoreExport(0,\'\',\''.$r->client_id.'\',\''.$r->gen_date.'\',\''.$today.'\',2)">'.$r->outpatient.'</a>':$r->outpatient).'</td>';
        $html .= '	<td>'.($r->dental>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeMoreExport(0,\'\',\''.$r->client_id.'\',\''.$r->gen_date.'\',\''.$today.'\',3)">'.$r->dental.'</a>':$r->dental).'</td>';
        $html .= '	<td>'.($r->emergency>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeMoreExport(0,\'\',\''.$r->client_id.'\',\''.$r->gen_date.'\',\''.$today.'\',4)">'.$r->emergency.'</a>':$r->emergency).'</td>';
        $html .= '	<td>'.(($r->inpatient+$r->outpatient+$r->dental+$r->emergency)>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeMoreExport(0,\'\',\''.$r->client_id.'\',\''.$r->gen_date.'\',\''.$today.'\',0)">'.($r->inpatient+$r->outpatient+$r->dental+$r->emergency).'</a>':($r->inpatient+$r->outpatient+$r->dental+$r->emergency)).'</td>';
        $html .= '</tr>';
                
        $cname = $r->client_name;
        $m--;
      }
    }
		
		die($html);
	}

	function get_swipe_by_provider(){		
		$providerid = $this->input->post('providerid');
		$period = $this->input->post('period');
    $period = $period ? $period : '';
		$p = explode('-',$period);
		$fmonth = count($p)>1?$p[0]:'';
		$year = count($p)>1?$p[1]:$p[0];
		
		$x = empty($fmonth)?3:2;		
		$f = empty($fmonth)?false:true; 
		$month = empty($fmonth)?date('n'):$fmonth-1;
		$amonth = empty($fmonth)?date('n'):$fmonth;

    $periodFrom = '01-' . $this->input->post('periodFrom');
    // $periodFrom = '01-1-2024';
    $periodFrom = date('Y-m', strtotime($periodFrom));
    $periodFrom = new DateTime($periodFrom);

    $periodTo = '01-' . $this->input->post('periodTo');
    // $periodTo = '01-3-2024';
    $periodTo = date('Y-m', strtotime($periodTo));
    $periodTo = new DateTime($periodTo);
    $periodTo->modify('+1 month');

    // Buat objek DatePeriod dengan rentang tanggal awal dan akhir, dan interval bulanan
    $interval = DateInterval::createFromDateString('1 month');
    $period = new DatePeriod($periodFrom, $interval, $periodTo);

    // Iterasi melalui setiap bulan dalam periode
    $periods = [];
    foreach ($period as $date) {
        // Format tanggal ke dalam Y-m
        $periods[] = $date->format('Y-m');
    }
		
		// $swipeprovider = getNumberSwipeByProvider(array('providerid' => $providerid, 'month' => $fmonth, 'year' => $year));
    $datas = getSingleNumberSwipeByProvider(array('providerid' => $providerid, 'periods' => $periods));
    $m = empty($fmonth)?2:0; $mt = 1;	
    $html = ''; $xno = 0;
    $pname = ''; $no = $xno;
		foreach($datas as $s){
			$date = $mt<0?date('Y-m-d'):$year.'-'.($amonth-$mt).'-1';
			$today = $mt<0?'':$mt+1;
		 	
			$html .= '<tr>';
			$html .= '	<td>'.++$no.'</td>';
			$html .= '	<td nowrap="nowrap">'.($pname!=$s->provider_name?$s->provider_name:'').'</td>';
			$html .= '	<td nowrap="nowrap">'.$s->showit.'</td>';
			$html .= '	<td>'.($s->inpatient>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeByProviderExport(0,\'\',\''.$s->provider_id.'\',\''.$s->gen_date.'\',\''.$today.'\',1)">'.$s->inpatient.'</a>':$s->inpatient).'</td>';
			$html .= '	<td>'.($s->outpatient>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeByProviderExport(0,\'\',\''.$s->provider_id.'\',\''.$s->gen_date.'\',\''.$today.'\',2)">'.$s->outpatient.'</a>':$s->outpatient).'</td>';
			$html .= '	<td>'.($s->dental>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeByProviderExport(0,\'\',\''.$s->provider_id.'\',\''.$s->gen_date.'\',\''.$today.'\',3)">'.$s->dental.'</a>':$s->dental).'</td>';
			$html .= '	<td>'.($s->emergency>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeByProviderExport(0,\'\',\''.$s->provider_id.'\',\''.$s->gen_date.'\',\''.$today.'\',4)">'.$s->emergency.'</a>':$s->emergency).'</td>';
			$html .= '	<td>'.($s->total>0?'<a href="javascript:void(0)" onclick="EXPORT_DATA.swipeByProviderExport(0,\'\',\''.$s->provider_id.'\',\''.$s->gen_date.'\',\''.$today.'\',0)">'.$s->total.'</a>':$s->total).'</td>';
			$html .= '</tr>';
			
			$pname = $s->provider_name;
		}

    die($html);

    /*
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
    */
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
