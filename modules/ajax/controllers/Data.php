<?php
include_once(APPPATH."libraries/FrontController.php");
class Data extends FrontController {

	function __construct()  
	{
		parent::__construct(); 		
	}
	
	function index() {}

	/**
	 * Escape values rendered into the small HTML fragments returned by this
	 * controller. These endpoints are consumed by jQuery .html(), so values
	 * read from the database must never be interpolated as markup.
	 */
	private function _html($value){
		return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
	}

	private function _export_link($method, array $args, $value){
		$json = json_encode(array_values($args), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
		if ($json === false) {
			return $this->_html($value);
		}

		return '<a href="#" class="js-export-link" data-export-method="'
			. $this->_html($method) . '" data-export-args="'
			. $this->_html($json) . '">' . $this->_html($value) . '</a>';
	}

	private function _numeric_or_link($value, $method, array $args){
		$value = (int)$value;
		return $value > 0 ? $this->_export_link($method, $args, $value) : $this->_html($value);
	}

	function kota(){
		
		$prov = $this->input->post('prov');
		$kota = $this->input->post('kota');
		$where = array('kab_status' => 1);
		if (!empty($prov)) {
			$province = $this->db->get_where("app_propinsi", array("propinsi_nama" => $prov))->row();
			if ($province && isset($province->propinsi_id)) {
				$where['kab_propinsi_id'] = $province->propinsi_id;
			}
		}
		
		$this->db->order_by("kab_nama");
		$m = $this->db->get_where("app_kabupaten",$where)->result();

		$html = "<option value=''> - pilih kota/kabupaten - </option>";
		foreach ((array)$m as $k => $v) {
			$name = $this->_html($v->kab_nama);
			$s = $v->kab_nama==$kota?'selected="selected"':'';
			$html .= "<option value=\"".$name."\" $s >".$name."</option>";
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
          $clientName = $cname != $r->client_name ? $this->_html($r->client_name) : '';
          $clientId = (string)$r->client_id;
          $genDate = (string)$r->gen_date;
          $mainInsured = (int)$r->main_insured;
          $dependent = (int)$r->dependent;

          $html .= '<tr>';
          $html .= '	<td>'.++$no.'</td>';
          $html .= '	<td nowrap="nowrap">'.$clientName.'</td>';
          $html .= '	<td nowrap="nowrap">'.$this->_html($r->showit).'</td>';
          $html .= '	<td>'.$this->_numeric_or_link($mainInsured, 'activeMemberExport', array(0, '', $clientId, $genDate, 0)).'</td>';
          $html .= '	<td>'.$this->_numeric_or_link($dependent, 'activeMemberExport', array(0, '', $clientId, $genDate, 1)).'</td>';
          $html .= '	<td>'.$this->_numeric_or_link($mainInsured + $dependent, 'activeMemberExport', array(0, '', $clientId, $genDate, 2)).'</td>';
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
          $clientName = $cname != $r->client_name ? $this->_html($r->client_name) : '';
          $clientId = (string)$r->client_id;
          $genDate = (string)$r->gen_date;
          $inpatient = (int)$r->inpatient;
          $outpatient = (int)$r->outpatient;
          $dental = (int)$r->dental;
          $emergency = (int)$r->emergency;
          $total = $inpatient + $outpatient + $dental + $emergency;

          $html .= '<tr>';
          $html .= '	<td>'.++$no.'</td>';
          $html .= '	<td nowrap="nowrap">'.$clientName.'</td>';
          $html .= '	<td nowrap="nowrap">'.$this->_html($r->showit).'</td>';
          $html .= '	<td>'.$this->_numeric_or_link($inpatient, 'swipeByClientExport', array(0, '', $clientId, $genDate, $today, 1)).'</td>';
          $html .= '	<td>'.$this->_numeric_or_link($outpatient, 'swipeByClientExport', array(0, '', $clientId, $genDate, $today, 2)).'</td>';
          $html .= '	<td>'.$this->_numeric_or_link($dental, 'swipeByClientExport', array(0, '', $clientId, $genDate, $today, 3)).'</td>';
          $html .= '	<td>'.$this->_numeric_or_link($emergency, 'swipeByClientExport', array(0, '', $clientId, $genDate, $today, 4)).'</td>';
          $html .= '	<td>'.$this->_numeric_or_link($total, 'swipeByClientExport', array(0, '', $clientId, $genDate, $today, 0)).'</td>';
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
        $clientName = $cname != $r->client_name ? $this->_html($r->client_name) : '';
        $clientId = (string)$r->client_id;
        $genDate = (string)$r->gen_date;
        $inpatient = (int)$r->inpatient;
        $outpatient = (int)$r->outpatient;
        $dental = (int)$r->dental;
        $emergency = (int)$r->emergency;
        $total = $inpatient + $outpatient + $dental + $emergency;

        $html .= '<tr>';
        $html .= '	<td>'.++$no.'</td>';
        $html .= '	<td nowrap="nowrap">'.$clientName.'</td>';
        $html .= '	<td nowrap="nowrap">'.$this->_html($r->showit).'</td>';
        $html .= '	<td>'.$this->_numeric_or_link($inpatient, 'swipeMoreExport', array(0, '', $clientId, $genDate, $today, 1)).'</td>';
        $html .= '	<td>'.$this->_numeric_or_link($outpatient, 'swipeMoreExport', array(0, '', $clientId, $genDate, $today, 2)).'</td>';
        $html .= '	<td>'.$this->_numeric_or_link($dental, 'swipeMoreExport', array(0, '', $clientId, $genDate, $today, 3)).'</td>';
        $html .= '	<td>'.$this->_numeric_or_link($emergency, 'swipeMoreExport', array(0, '', $clientId, $genDate, $today, 4)).'</td>';
        $html .= '	<td>'.$this->_numeric_or_link($total, 'swipeMoreExport', array(0, '', $clientId, $genDate, $today, 0)).'</td>';
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
			$providerName = $pname != $s->provider_name ? $this->_html($s->provider_name) : '';
			$providerId = (string)$s->provider_id;
			$genDate = (string)$s->gen_date;
			$inpatient = (int)$s->inpatient;
			$outpatient = (int)$s->outpatient;
			$dental = (int)$s->dental;
			$emergency = (int)$s->emergency;
			$total = (int)$s->total;
		 	
			$html .= '<tr>';
			$html .= '	<td>'.++$no.'</td>';
			$html .= '	<td nowrap="nowrap">'.$providerName.'</td>';
			$html .= '	<td nowrap="nowrap">'.$this->_html($s->showit).'</td>';
			$html .= '	<td>'.$this->_numeric_or_link($inpatient, 'swipeByProviderExport', array(0, '', $providerId, $genDate, $today, 1)).'</td>';
			$html .= '	<td>'.$this->_numeric_or_link($outpatient, 'swipeByProviderExport', array(0, '', $providerId, $genDate, $today, 2)).'</td>';
			$html .= '	<td>'.$this->_numeric_or_link($dental, 'swipeByProviderExport', array(0, '', $providerId, $genDate, $today, 3)).'</td>';
			$html .= '	<td>'.$this->_numeric_or_link($emergency, 'swipeByProviderExport', array(0, '', $providerId, $genDate, $today, 4)).'</td>';
			$html .= '	<td>'.$this->_numeric_or_link($total, 'swipeByProviderExport', array(0, '', $providerId, $genDate, $today, 0)).'</td>';
			$html .= '</tr>';
			
			$pname = $s->provider_name;
		}

    die($html);
	}

	function get_user_login(){		
		$providerid = $this->input->post('providerid');
		
		$html = '';
		$data = getNumberUserLogin(array('providerid' => $providerid));
		$no = 0;
		foreach($data as $r){
			$providerId = (string)$r->provider_id;
			$providerName = $this->_html($r->provider_name);
			$prev = (int)$r->prev;
			$curr = (int)$r->curr;
			$today = (int)$r->today;
			$html .= '<tr>';
			$html .= '	<td>'.++$no.'</td>';
			$html .= '	<td nowrap="nowrap">'.$providerName.'</td>';
			$html .= '	<td>'.$this->_numeric_or_link($prev, 'userLoginExport', array(0, '', $providerId, 1)).'</td>';
			$html .= '	<td>'.$this->_numeric_or_link($curr, 'userLoginExport', array(0, '', $providerId, 2)).'</td>';
			$html .= '	<td>'.$this->_numeric_or_link($today, 'userLoginExport', array(0, '', $providerId, 3)).'</td>';
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
