<?php 
function getTransactions() {
	$CI  = getCI();
	
	$q = $CI->db->query("
		SELECT * FROM (
			SELECT 'Rawat Inap' AS layanan, 1 AS nourut,
				sum(case when servicetypeId = 1 AND date_format(date_claim,'%y%u') = date_format(now(),'%y%u') then 1 else 0 end) as weeks,
				sum(case when servicetypeId = 1 AND date_format(date_claim,'%y%m') = date_format(now(),'%y%m') then 1 else 0 end) as months,
				sum(case when servicetypeId = 1 AND date_format(date_claim,'%y') = date_format(now(),'%y') then 1 else 0 end) as years
			FROM sos_ttrans_claim
			UNION
			SELECT 'Rawat Jalan' AS layanan, 2 AS nourut, 
				sum(case when servicetypeId = 2 AND date_format(date_claim,'%y%u') = date_format(now(),'%y%u') then 1 else 0 end) as weeks,
				sum(case when servicetypeId = 2 AND date_format(date_claim,'%y%m') = date_format(now(),'%y%m') then 1 else 0 end) as months,
				sum(case when servicetypeId = 2 AND date_format(date_claim,'%y') = date_format(now(),'%y') then 1 else 0 end) as years
			FROM sos_ttrans_claim
			UNION
			SELECT 'Rawat Gigi' AS layanan, 3 AS nourut, 
				sum(case when servicetypeId = 3 AND date_format(date_claim,'%y%u') = date_format(now(),'%y%u') then 1 else 0 end) as weeks,
				sum(case when servicetypeId = 3 AND date_format(date_claim,'%y%m') = date_format(now(),'%y%m') then 1 else 0 end) as months,
				sum(case when servicetypeId = 3 AND date_format(date_claim,'%y') = date_format(now(),'%y') then 1 else 0 end) as years
			FROM sos_ttrans_claim
			UNION
			SELECT 'Emergency' AS layanan, 4 AS nourut, 
				sum(case when servicetypeId = 4 AND date_format(date_claim,'%y%u') = date_format(now(),'%y%u') then 1 else 0 end) as weeks,
				sum(case when servicetypeId = 4 AND date_format(date_claim,'%y%m') = date_format(now(),'%y%m') then 1 else 0 end) as months,
				sum(case when servicetypeId = 4 AND date_format(date_claim,'%y') = date_format(now(),'%y') then 1 else 0 end) as years
			FROM sos_ttrans_claim
		) AS x ORDER BY nourut ASC
	")->result();
		
	if(count($q) > 0){
		return $q;
	}
	
	return array();
}

function getNumberActiveMember($param=array()) {
	$CI  = getCI();
	
	$date = count($param)>0?(empty($param['fmonth'])?substr($param['period'],0,4).date('-m-d'):$param['period'].date('-d')):date('Y-m-d');
	$q = $CI->db->query("
		SELECT * FROM (
			SELECT client_id, client_name, '".(empty($param['fmonth'])?"Previous Month":get_month(str_replace("-","",substr($param['period'],-2))-1))."' as showit, '1' as nourut, 
				sum(case when right(member_cardNo,1) = 0 AND date_format(m.time_add,'%y%m') <= date_format(date_add('$date',INTERVAL -1 MONTH),'%y%m') AND member_statuscard = 'Active' then 1 else 0 end) as main_insured,
				sum(case when right(member_cardNo,1) != 0 AND date_format(m.time_add,'%y%m') <= date_format(date_add('$date',INTERVAL -1 MONTH),'%y%m') AND member_statuscard = 'Active' then 1 else 0 end) as dependent
			FROM sos_member AS m
				RIGHT JOIN sos_client AS c ON m.member_clientid = c.client_id
			WHERE member_status != 999
				GROUP BY client_name
			UNION
			SELECT client_id, client_name, '".(empty($param['fmonth'])?"Current Month":get_month(str_replace("-","",substr($param['period'],-2))))."' as showit, '2' as nourut, 
				sum(case when right(member_cardNo,1) = 0 AND date_format(m.time_add,'%y%m') <= date_format('$date','%y%m') AND member_statuscard = 'Active' then 1 else 0 end) as main_insured,
				sum(case when right(member_cardNo,1) != 0 AND date_format(m.time_add,'%y%m') <= date_format('$date','%y%m') AND member_statuscard = 'Active' then 1 else 0 end) as dependent
			FROM sos_member AS m
				RIGHT JOIN sos_client AS c ON m.member_clientid = c.client_id
			WHERE member_status = 1
				GROUP BY client_name
		) AS x ORDER BY client_name, nourut ASC
	")->result();
		
	if(count($q) > 0){
		return $q;
	}
	
	return array();
}

function getSingleNumberActiveMember($param=array()) {
	$CI  = getCI();
	
	$date = empty($param['month'])?$param['year'].date('-m-1'):$param['year'].'-'.$param['month'].date('-1');
	$showit = empty($param['fmonth'])?get_period(date('n')-$param['month']):get_month($param['month']);
	$where = " WHERE member_status != 999";
	$where .= !empty($param['clientid'])?" AND c.client_id = ".$param['clientid']:"";
	$q = $CI->db->query("
		SELECT client_id, client_name, '$showit' as showit,
			sum(case when right(member_cardNo,1) = 0 AND date_format(m.time_add,'%y%m') <= date_format('$date','%y%m') AND member_statuscard = 'Active' then 1 else 0 end) as main_insured,
			sum(case when right(member_cardNo,1) != 0 AND date_format(m.time_add,'%y%m') <= date_format('$date','%y%m') AND member_statuscard = 'Active' then 1 else 0 end) as dependent
		FROM sos_member AS m
		WHERE member_status = 1
			RIGHT JOIN sos_client AS c ON m.member_clientid = c.client_id$where
			GROUP BY client_name
	")->row();
		
	if(count($q) > 0){
		return $q;
	}
	
	return array();
}
 
function getNumberSwipeByClient($param=array()) {
	$CI  = getCI();
	
	$date = count($param)>0?(empty($param['fmonth'])?substr($param['period'],0,4).date('-m-d'):$param['period'].date('-d')):date('Y-m-d');
	$today = empty($param['fmonth'])?"UNION
			SELECT client_id, client_name, 'Today' as showit, '3' as nourut,
				sum(case when servicetypeId = 1 AND date_format(date_claim,'%y%m%d') = date_format('$date','%y%m%d') then 1 else 0 end) as inpatient,
				sum(case when servicetypeId = 2 AND date_format(date_claim,'%y%m%d') = date_format('$date','%y%m%d') then 1 else 0 end) as outpatient,
				sum(case when servicetypeId = 3 AND date_format(date_claim,'%y%m%d') = date_format('$date','%y%m%d') then 1 else 0 end) as dental,
				sum(case when servicetypeId = 4 AND date_format(date_claim,'%y%m%d') = date_format('$date','%y%m%d') then 1 else 0 end) as emergency
			FROM sos_ttrans_claim AS tc
				RIGHT JOIN sos_client AS c ON tc.clientId = c.client_id
				INNER JOIN sos_member AS m ON tc.memberId = m.member_id AND member_status = 1 
				GROUP BY client_name":"";
	$q = $CI->db->query("
		SELECT * FROM (
			SELECT client_id, client_name, '".(empty($param['fmonth'])?"Previous Month":get_month(str_replace("-","",substr($param['period'],-2))-1))."' as showit, '1' as nourut, 
				sum(case when servicetypeId = 1 AND date_format(date_claim,'%y%m') = date_format(date_add('$date',INTERVAL -1 MONTH),'%y%m') then 1 else 0 end) as inpatient,
				sum(case when servicetypeId = 2 AND date_format(date_claim,'%y%m') = date_format(date_add('$date',INTERVAL -1 MONTH),'%y%m') then 1 else 0 end) as outpatient,
				sum(case when servicetypeId = 3 AND date_format(date_claim,'%y%m') = date_format(date_add('$date',INTERVAL -1 MONTH),'%y%m') then 1 else 0 end) as dental,
				sum(case when servicetypeId = 4 AND date_format(date_claim,'%y%m') = date_format(date_add('$date',INTERVAL -1 MONTH),'%y%m') then 1 else 0 end) as emergency
			FROM sos_ttrans_claim AS tc
				RIGHT JOIN sos_client AS c ON tc.clientId = c.client_id
				INNER JOIN sos_member AS m ON tc.memberId = m.member_id AND member_status = 1
				GROUP BY client_name
			UNION
			SELECT client_id, client_name, '".(empty($param['fmonth'])?"Current Month":get_month(str_replace("-","",substr($param['period'],-2))))."' as showit, '2' as nourut,
				sum(case when servicetypeId = 1 AND date_format(date_claim,'%y%m') = date_format('$date','%y%m') then 1 else 0 end) as inpatient,
				sum(case when servicetypeId = 2 AND date_format(date_claim,'%y%m') = date_format('$date','%y%m') then 1 else 0 end) as outpatient,
				sum(case when servicetypeId = 3 AND date_format(date_claim,'%y%m') = date_format('$date','%y%m') then 1 else 0 end) as dental,
				sum(case when servicetypeId = 4 AND date_format(date_claim,'%y%m') = date_format('$date','%y%m') then 1 else 0 end) as emergency
			FROM sos_ttrans_claim AS tc
				RIGHT JOIN sos_client AS c ON tc.clientId = c.client_id
				INNER JOIN sos_member AS m ON tc.memberId = m.member_id AND member_status = 1
				GROUP BY client_name
			$today
		) AS x ORDER BY client_name, nourut ASC
	")->result();
		
	if(count($q) > 0){
		return $q;
	}
	
	return array();
}
 
function getSingleNumberSwipeByClient($param=array()) {
	$CI  = getCI();
	
	$month = $param['multi']>2?$param['month']+1:$param['month'];
	$date = empty($param['month'])?$param['year'].date('-m-d'):$param['year'].'-'.$month.date('-1');
	
	$showit = empty($param['fmonth'])?get_period(date('n')-$param['month'],$param['flag']):get_month($param['month']);
	$case = $param['multi']>2?(date('n')-$param['month']>0?"AND date_format(date_claim,'%y%m') = date_format('$date','%y%m')":"AND date_format(date_claim,'%y%m%d') = date_format('".$param['year']."-".$param['month']."-".date('d')."','%y%m%d')"):"AND date_format(date_claim,'%y%m') = date_format('$date','%y%m')";
	$where = !empty($param['clientid'])?" WHERE client_id = ".$param['clientid']:"";
	$q = $CI->db->query("
		SELECT client_id, client_name, '$showit' as showit, 
			sum(case when servicetypeId = 1 $case then 1 else 0 end) as inpatient,
			sum(case when servicetypeId = 2 $case then 1 else 0 end) as outpatient,
			sum(case when servicetypeId = 3 $case then 1 else 0 end) as dental,
			sum(case when servicetypeId = 4 $case then 1 else 0 end) as emergency
		FROM sos_ttrans_claim AS tc
			RIGHT JOIN sos_client AS c ON tc.clientId = c.client_id
			INNER JOIN sos_member AS m ON tc.memberId = m.member_id AND member_status = 1$where
			GROUP BY client_name
	")->row();
		
	if(count($q) > 0){
		return $q;
	}
	
	return array();
}
 
function getNumberMemberSwipeMoreThanOnce($param=array()) {
	$CI  = getCI();
	
	$date = count($param)>0?(empty($param['fmonth'])?substr($param['period'],0,4).date('-m-d'):$param['period'].date('-d')):date('Y-m-d');
	$today = empty($param['fmonth'])?"UNION
			SELECT c.client_id, c.client_name, 'Today' as showit, '3' as nourut,
				sum(case when servicetypeId = 1 AND date_format(x.createdDate,'%y%m%d') = date_format('$date','%y%m%d') then 1 else 0 end) as inpatient,
				sum(case when servicetypeId = 2 AND date_format(x.createdDate,'%y%m%d') = date_format('$date','%y%m%d') then 1 else 0 end) as outpatient,
				sum(case when servicetypeId = 3 AND date_format(x.createdDate,'%y%m%d') = date_format('$date','%y%m%d') then 1 else 0 end) as dental,
				sum(case when servicetypeId = 4 AND date_format(x.createdDate,'%y%m%d') = date_format('$date','%y%m%d') then 1 else 0 end) as emergency
			FROM (
				SELECT clientId, memberId, servicetypeId, createdDate
				FROM sos_ttrans_claim  
				WHERE flag_fraud = 1
				GROUP BY memberId,date_format(createdDate,'%y%m%d')
				HAVING count(memberId)>1
			) AS x 
			RIGHT JOIN sos_client AS c ON x.clientId = c.client_id
			INNER JOIN sos_member AS m ON x.memberId = m.member_id AND member_status = 1
			GROUP BY x.clientId":"";
//	,date_format(createdDate,'%y%m%d')
	$q = $CI->db->query("
		SELECT * FROM (
			SELECT c.client_id, c.client_name, '".(empty($param['fmonth'])?"Previous Month":get_month(str_replace("-","",substr($param['period'],-2))-1))."' as showit, '1' as nourut, 
				sum(case when servicetypeId = 1 AND date_format(x.createdDate,'%y%m') = date_format(date_add('$date',INTERVAL -1 MONTH),'%y%m') then 1 else 0 end) as inpatient,
				sum(case when servicetypeId = 2 AND date_format(x.createdDate,'%y%m') = date_format(date_add('$date',INTERVAL -1 MONTH),'%y%m') then 1 else 0 end) as outpatient,
				sum(case when servicetypeId = 3 AND date_format(x.createdDate,'%y%m') = date_format(date_add('$date',INTERVAL -1 MONTH),'%y%m') then 1 else 0 end) as dental,
				sum(case when servicetypeId = 4 AND date_format(x.createdDate,'%y%m') = date_format(date_add('$date',INTERVAL -1 MONTH),'%y%m') then 1 else 0 end) as emergency 
			FROM (
				SELECT clientId, memberId, servicetypeId, createdDate
				FROM sos_ttrans_claim 
				WHERE flag_fraud = 1
				GROUP BY memberId,date_format(createdDate,'%y%m%d')
				HAVING count(memberId)>1
			) AS x 
			RIGHT JOIN sos_client AS c ON x.clientId = c.client_id
			INNER JOIN sos_member AS m ON x.memberId = m.member_id AND member_status = 1
			GROUP BY x.clientId
			UNION
			SELECT c.client_id, c.client_name, '".(empty($param['fmonth'])?"Current Month":get_month(str_replace("-","",substr($param['period'],-2))))."' as showit, '2' as nourut,
				sum(case when servicetypeId = 1 AND date_format(x.createdDate,'%y%m') = date_format('$date','%y%m') then 1 else 0 end) as inpatient,
				sum(case when servicetypeId = 2 AND date_format(x.createdDate,'%y%m') = date_format('$date','%y%m') then 1 else 0 end) as outpatient,
				sum(case when servicetypeId = 3 AND date_format(x.createdDate,'%y%m') = date_format('$date','%y%m') then 1 else 0 end) as dental,
				sum(case when servicetypeId = 4 AND date_format(x.createdDate,'%y%m') = date_format('$date','%y%m') then 1 else 0 end) as emergency 
			FROM (
				SELECT clientId, memberId, servicetypeId, createdDate
				FROM sos_ttrans_claim  
				WHERE flag_fraud = 1
				GROUP BY memberId,date_format(createdDate,'%y%m%d')
				HAVING count(memberId)>1
			) AS x 
			RIGHT JOIN sos_client AS c ON x.clientId = c.client_id
			INNER JOIN sos_member AS m ON x.memberId = m.member_id AND member_status = 1
			GROUP BY x.clientId
			$today
		) AS x ORDER BY client_name, nourut ASC
	")->result();
//	$param[] = "SELECT * FROM (
//			SELECT client_name, '".(empty($param['fmonth'])?"Previous Month":get_month(str_replace("-","",substr($param['period'],-2))-1))."' as showit, '1' as nourut, 
//				sum(case when servicetypeId = 1 AND date_format(x.createdDate,'%y%m') = date_format(date_add('$date',INTERVAL -1 MONTH),'%y%m') then 1 else 0 end) as inpatient,
//				sum(case when servicetypeId = 2 AND date_format(x.createdDate,'%y%m') = date_format(date_add('$date',INTERVAL -1 MONTH),'%y%m') then 1 else 0 end) as outpatient,
//				sum(case when servicetypeId = 3 AND date_format(x.createdDate,'%y%m') = date_format(date_add('$date',INTERVAL -1 MONTH),'%y%m') then 1 else 0 end) as dental,
//				sum(case when servicetypeId = 4 AND date_format(x.createdDate,'%y%m') = date_format(date_add('$date',INTERVAL -1 MONTH),'%y%m') then 1 else 0 end) as emergency 
//			FROM (
//				SELECT clientId, memberId, servicetypeId, createdDate
//				FROM sos_ttrans_claim 
//				WHERE flag_fraud = 1
//				GROUP BY memberId
//				HAVING count(memberId)>1
//			) AS x 
//			RIGHT JOIN sos_client AS c ON x.clientId = c.client_id
//			GROUP BY x.clientId
//			UNION
//			SELECT client_name, '".(empty($param['fmonth'])?"Current Month":get_month(str_replace("-","",substr($param['period'],-2))))."' as showit, '2' as nourut,
//				sum(case when servicetypeId = 1 AND date_format(x.createdDate,'%y%m') = date_format('$date','%y%m') then 1 else 0 end) as inpatient,
//				sum(case when servicetypeId = 2 AND date_format(x.createdDate,'%y%m') = date_format('$date','%y%m') then 1 else 0 end) as outpatient,
//				sum(case when servicetypeId = 3 AND date_format(x.createdDate,'%y%m') = date_format('$date','%y%m') then 1 else 0 end) as dental,
//				sum(case when servicetypeId = 4 AND date_format(x.createdDate,'%y%m') = date_format('$date','%y%m') then 1 else 0 end) as emergency 
//			FROM (
//				SELECT clientId, memberId, servicetypeId, createdDate
//				FROM sos_ttrans_claim  
//				WHERE flag_fraud = 1
//				GROUP BY memberId
//				HAVING count(memberId)>1
//			) AS x 
//			RIGHT JOIN sos_client AS c ON x.clientId = c.client_id
//			GROUP BY x.clientId
//			$today
//		) AS x ORDER BY client_name, nourut ASC";
//			debugCode($param);
		
	if(count($q) > 0){
		return $q;
	}
	
	return array();
}
 
function getSingleMemberSwipeMoreThanOnce($param=array()) {
	$CI  = getCI();
	
	$month = $param['multi']>2?$param['month']+1:$param['month'];
	$date = empty($param['month'])?$param['year'].date('-m-d'):$param['year'].'-'.$month.date('-1');
	$showit = empty($param['fmonth'])?get_period(date('n')-$param['month'],$param['flag']):get_month($param['month']);
	$case = $param['multi']>2?(date('n')-$param['month']>0?"AND date_format(x.createdDate,'%y%m') = date_format('$date','%y%m')":"AND date_format(x.createdDate,'%y%m%d') = date_format('".$param['year']."-".$param['month']."-".date('d')."','%y%m%d')"):"AND date_format(x.createdDate,'%y%m') = date_format('$date','%y%m')";
	$where = !empty($param['clientid'])?" WHERE client_id = ".$param['clientid']:"";
	$q = $CI->db->query("
		SELECT c.lient_id, c.client_name, '$showit' as showit, 
			sum(case when x.servicetypeId = 1 $case then 1 else 0 end) as inpatient,
			sum(case when x.servicetypeId = 2 $case then 1 else 0 end) as outpatient,
			sum(case when x.servicetypeId = 3 $case then 1 else 0 end) as dental,
			sum(case when x.servicetypeId = 4 $case then 1 else 0 end) as emergency
		FROM (
			SELECT clientId, memberId, servicetypeId, createdDate
			FROM sos_ttrans_claim  
			WHERE flag_fraud = 1
			GROUP BY memberId,date_format(createdDate,'%y%m%d')
			HAVING count(memberId)>1
		) AS x 
		RIGHT JOIN sos_client AS c ON x.clientId = c.client_id
		INNER JOIN sos_member AS m ON x.memberId = m.member_id AND member_status = 1$where
		GROUP BY x.clientId
	")->row();
		
	if(count($q) > 0){
		return $q;
	}
	
	return array();
}
 
function getNumberSwipeByProvider($param=array()) {
	$CI  = getCI();

	$month = empty($param['month'])?date('n'):$param['month'];
	$date = count($param)>0?(empty($param['month'])?$param['year'].date('-m-1'):$param['year'].'-'.$month.date('-1')):date('Y-m-1');
	$where = !empty($param['providerid'])?" WHERE provider_id = ".$param['providerid']:"";
	$limit = !empty($param['providerid'])?" LIMIT 0, 1":"";
	$q = $CI->db->query("
		SELECT provider_id, 
				sum(case when date_format(date_claim,'%y%m') BETWEEN date_format(date_add('$date',INTERVAL -1 MONTH),'%y%m')
			AND date_format('$date','%y%m') then 1 else 0 end) as total
		FROM sos_ttrans_claim AS tc
		RIGHT JOIN sos_provider AS c ON tc.providerId = c.provider_id
		INNER JOIN sos_member AS m ON tc.memberId = m.member_id AND member_status = 1$where
		GROUP BY provider_name
		ORDER BY provider_name ASC
		$limit
	")->result();
		
	if(count($q) > 0){
		return $q;
	}
	
	return array();
}
 
function getSingleNumberSwipeByProvider($param=array()) {
	$CI  = getCI();

	$month = $param['multi']>2?$param['month']+1:$param['month'];
	$date = count($param)>0?(empty($param['month'])?$param['year'].date('-m-d'):$param['year'].'-'.$month.date('-1')):date('Y-m-1');	
	$showit = empty($param['fmonth'])?get_period(date('n')-$param['month'],$param['flag']):get_month($param['month']);
	$case = $param['multi']>2?(date('n')-$param['month']>0?"AND date_format(date_claim,'%y%m') = date_format('$date','%y%m')":"AND date_format(date_claim,'%y%m%d') = date_format('".$param['year']."-".$param['month']."-".date('d')."','%y%m%d')"):"AND date_format(date_claim,'%y%m') = date_format('$date','%y%m')";
	$where = !empty($param['providerid'])?" AND provider_id = ".$param['providerid']:"";
	$limit = !empty($param['providerid'])?" LIMIT 0, 1":"";
	$q = $CI->db->query("
		SELECT provider_name, '$showit' as showit, 
				sum(case when servicetypeId = 1 $case then 1 else 0 end) as inpatient,
				sum(case when servicetypeId = 2 $case then 1 else 0 end) as outpatient,
				sum(case when servicetypeId = 3 $case then 1 else 0 end) as dental,
				sum(case when servicetypeId = 4 $case then 1 else 0 end) as emergency,
				sum(case when servicetypeId = 1 $case then 1 else 0 end) +
				sum(case when servicetypeId = 2 $case then 1 else 0 end) +
				sum(case when servicetypeId = 3 $case then 1 else 0 end) +
				sum(case when servicetypeId = 4 $case then 1 else 0 end) as total
			FROM sos_ttrans_claim AS tc
				RIGHT JOIN sos_provider AS c ON tc.providerId = c.provider_id
				INNER JOIN sos_member AS m ON tc.memberId = m.member_id AND member_status = 1
			WHERE 1=1$where 
			GROUP BY provider_name
			ORDER BY sum(case when servicetypeId = 1 $case then 1 else 0 end) +
				sum(case when servicetypeId = 2 $case then 1 else 0 end) +
				sum(case when servicetypeId = 3 $case then 1 else 0 end) +
				sum(case when servicetypeId = 4 $case then 1 else 0 end) DESC
			$limit
	")->result();
		
	if(count($q) > 0){
		return $q;
	}
	
	return array();
}
 
function getNumberUserLogin($param=array()) {
	$CI  = getCI();
	
	$where = isset($param['providerid'])&&!empty($param['providerid'])?" WHERE p.provider_id = ".$param['providerid']:"";
	$q = $CI->db->query("
		SELECT provider_id, provider_name, 
			sum(case when date_format(log_date,'%y%m') = date_format(date_add(now(),INTERVAL -1 MONTH),'%y%m') AND log_function = 'out' then 1 else 0 end) as prev,
			sum(case when date_format(log_date,'%y%m') = date_format(now(),'%y%m') AND log_function = 'out' then 1 else 0 end) as curr,
			sum(case when date_format(log_date,'%y%m%d') = date_format(now(),'%y%m%d') AND log_function = 'out' then 1 else 0 end) as today
		FROM app_user AS au
		INNER JOIN app_log AS al ON al.log_user_id = au.user_id
		RIGHT JOIN sos_provider AS p ON au.user_providerid = p.provider_id$where
		GROUP BY provider_name
		ORDER BY provider_name ASC
	")->result();
		
	if(count($q) > 0){
		return $q;
	}
	
	return array();
}

function getExportActiveMember($p=array(),$count=FALSE) {
	$CI  = getCI();
	
	$type = $p['type']!=2?" AND right(member_cardNo,1) ".($p['type']==0?"= 0":"!= 0")." ":"";
	$limit = "";
	
	if($count==FALSE){
		if( isset($p['offset']) && isset($p['limit']) ){
			$p['offset'] = empty($p['offset'])?0:$p['offset'];
			$limit = " LIMIT ".$p['offset'].",".$p['limit'];
		}
	}
	
	$q = $CI->db->query("
		SELECT m.*, c.client_name
		FROM sos_member AS m
			RIGHT JOIN sos_client AS c ON m.member_clientid = c.client_id
		WHERE member_status != 999 AND member_clientid = ".$p['cid']." 
			$type
			AND date_format(m.time_add,'%y%m') <= date_format('".$p['date']."','%y%m') 
			AND member_statuscard = 'Active'
		ORDER BY member_name ASC
		$limit
	");
		
	if($count==FALSE){
		$total = getExportActiveMember($p,TRUE);
		return array(
				"data"	=> $q->result(),
				"total" => $total
			);
	}else{
		return $q->num_rows();
	}
}

function getExportSwipeByClient($p=array(),$count=FALSE) {
	$CI  = getCI();
	
	$type = $p['type']>0?" AND tc.servicetypeId = ".$p['type']:"";
	$month = date('n', strtotime($p['date']));
	$year = date('Y', strtotime($p['date']));
	$lastday = cal_days_in_month(CAL_GREGORIAN,$month,$year);
	$date = " AND date_format(tc.date_claim,'%y%m%d') ".(empty($p['today'])?"= date_format('".$p['date']."','%y%m%d')":"BETWEEN date_format('".$year."-".$month."-1','%y%m%d') AND date_format('".$year."-".$month."-".$lastday."','%y%m%d')");
	$limit = "";
		
	if($count==FALSE){
		if( isset($p['offset']) && isset($p['limit']) ){
			$p['offset'] = empty($p['offset'])?0:$p['offset'];
			$limit = " LIMIT ".$p['offset'].",".$p['limit'];
		}
	}
	
	$q = $CI->db->query("
		SELECT tc.*, m.member_cardno, m.member_name, c.client_name, p.provider_name, srst.service_type
		FROM sos_ttrans_claim AS tc
			RIGHT JOIN sos_client AS c ON tc.clientId = c.client_id
			INNER JOIN sos_member AS m ON tc.memberId = m.member_id
			LEFT JOIN sos_provider AS p ON tc.providerId = p.provider_id
			INNER JOIN sos_ref_service_type AS srst ON tc.servicetypeId = srst.service_id
		WHERE tc.clientId = ".$p['cid']." 
			$type
			$date
		ORDER BY tc.date_claim DESC
		$limit
	");
		
	if($count==FALSE){
		$total = getExportSwipeByClient($p,TRUE);
		return array(
				"data"	=> $q->result(),
				"total" => $total
			);
	}else{
		return $q->num_rows();
	}
}

function getExportSwipeMore($p=array(),$count=FALSE) {
	$CI  = getCI();
	
	$type = $p['type']>0?" AND tc.servicetypeId = ".$p['type']:"";
	$month = date('n', strtotime($p['date']));
	$year = date('Y', strtotime($p['date']));
	$lastday = cal_days_in_month(CAL_GREGORIAN,$month,$year);
	$date = " AND date_format(tc.date_claim,'%y%m%d') ".(empty($p['today'])?"= date_format('".$p['date']."','%y%m%d')":"BETWEEN date_format('".$year."-".$month."-1','%y%m%d') AND date_format('".$year."-".$month."-".$lastday."','%y%m%d')");
	$limit = "";
		
	if($count==FALSE){
		if( isset($p['offset']) && isset($p['limit']) ){
			$p['offset'] = empty($p['offset'])?0:$p['offset'];
			$limit = " LIMIT ".$p['offset'].",".$p['limit'];
		}
	}
	
	$q = $CI->db->query("
		SELECT tc.*, m.member_cardno, m.member_name, c.client_name, p.provider_name, srst.service_type
		FROM (
			SELECT *
			FROM sos_ttrans_claim  
			WHERE flag_fraud = 1
			GROUP BY memberId,date_format(createdDate,'%y%m%d')
			HAVING count(memberId)>1
		) AS tc 
			RIGHT JOIN sos_client AS c ON tc.clientId = c.client_id
			INNER JOIN sos_member AS m ON tc.memberId = m.member_id
			LEFT JOIN sos_provider AS p ON tc.providerId = p.provider_id
			INNER JOIN sos_ref_service_type AS srst ON tc.servicetypeId = srst.service_id
		WHERE tc.clientId = ".$p['cid']." 
			$type
			$date
		ORDER BY tc.createdDate DESC
		$limit
	");
		
	if($count==FALSE){
		$total = getExportSwipeMore($p,TRUE);
		return array(
				"data"	=> $q->result(),
				"total" => $total
			);
	}else{
		return $q->num_rows();
	}
}

function getExportSwipeByProvider($p=array(),$count=FALSE) {
	$CI  = getCI();
	
	$type = $p['type']>0?" AND tc.servicetypeId = ".$p['type']:"";
	$month = date('n', strtotime($p['date']));
	$year = date('Y', strtotime($p['date']));
	$lastday = cal_days_in_month(CAL_GREGORIAN,$month,$year);
	$date = " AND date_format(tc.date_claim,'%y%m%d') ".(empty($p['today'])?"= date_format('".$p['date']."','%y%m%d')":"BETWEEN date_format('".$year."-".$month."-1','%y%m%d') AND date_format('".$year."-".$month."-".$lastday."','%y%m%d')");
	$limit = "";
	
	if($count==FALSE){
		if( isset($p['offset']) && isset($p['limit']) ){
			$p['offset'] = empty($p['offset'])?0:$p['offset'];
			$limit = " LIMIT ".$p['offset'].",".$p['limit'];
		}
	}
	
	$q = $CI->db->query("
		SELECT tc.*, m.member_cardno, m.member_name, c.client_name, p.provider_name, srst.service_type
		FROM sos_ttrans_claim AS tc
			RIGHT JOIN sos_provider AS p ON tc.providerId = p.provider_id
			RIGHT JOIN sos_client AS c ON tc.clientId = c.client_id
			INNER JOIN sos_member AS m ON tc.memberId = m.member_id
			INNER JOIN sos_ref_service_type AS srst ON tc.servicetypeId = srst.service_id
		WHERE tc.providerId = ".$p['pid']." 
			$type
			$date
		ORDER BY tc.date_claim DESC
		$limit
	");
		
	if($count==FALSE){
		$total = getExportSwipeByProvider($p,TRUE);
		return array(
				"data"	=> $q->result(),
				"total" => $total
			);
	}else{
		return $q->num_rows();
	}
}

function getExportUserLogin($p=array(),$count=FALSE) {
	$CI  = getCI();
	
	$date = " AND ".($p['type']==1?"date_format(log_date,'%y%m') = date_format(date_add(now(),INTERVAL -1 MONTH),'%y%m')":($p['type']==2?"date_format(log_date,'%y%m') = date_format(now(),'%y%m')":"date_format(log_date,'%y%m%d') = date_format(now(),'%y%m%d')"));
	$limit = "";
	
	if($count==FALSE){
		if( isset($p['offset']) && isset($p['limit']) ){
			$p['offset'] = empty($p['offset'])?0:$p['offset'];
			$limit = " LIMIT ".$p['offset'].",".$p['limit'];
		}
	}
	
	$q = $CI->db->query("
		SELECT au.*, al.*, p.provider_name
		FROM app_user AS au
			INNER JOIN app_log AS al ON al.log_user_id = au.user_id
			RIGHT JOIN sos_provider AS p ON au.user_providerid = p.provider_id
		WHERE au.user_providerid = ".$p['pid']."
			$date
			AND log_function = 'out'
		ORDER BY log_date DESC
		$limit
	");
		
	if($count==FALSE){
		$total = getExportUserLogin($p,TRUE);
		return array(
				"data"	=> $q->result(),
				"total" => $total
			);
	}else{
		return $q->num_rows();
	}
}

function get_name($table="",$field="",$param=array()){
	$CI = getCI();
	if(!empty($table)) {
		$data = $CI->db->get_where($table,$param)->row();

		return isset($data->{$field})?$data->{$field}:'';
	} return '';
}

function get_month($m=""){
	$month = array(
			'1' 	=> "Januari",
			'2' 	=> "Februari",
			'3' 	=> "Maret",
			'4' 	=> "April",
			'5' 	=> "Mei",
			'6' 	=> "Juni",
			'7'		=> "Juli",
			'8' 	=> "Agustus",
			'9' 	=> "September",
			'10'  	=> "Oktober",
			'11'  	=> "November",
			'12' 	=> "Desember"
		);

	return $month[$m];
}

function get_month_half($m=""){
	$month = array(
			'1' 	=> "Jan",
			'2' 	=> "Feb",
			'3' 	=> "Mar",
			'4' 	=> "Apr",
			'5' 	=> "May",
			'6' 	=> "Jun",
			'7'		=> "Jul",
			'8' 	=> "Aug",
			'9' 	=> "Sep",
			'10'  	=> "Oct",
			'11'  	=> "Nov",
			'12' 	=> "Dec"
		);

	return $month[$m];
}

function jenkel($v){
	$jenkel = cfg('jenis_kelamin');
	$m = isset($jenkel[$v])?$jenkel[$v]:$v;
	return $m;
}
function get_period($p="",$flag=true){
	$period = $flag?array("Current Month", "Previous Month", "Two Months Ago"):array("Today", "Current Month", "Previous Month");

	return $period[$p];
}

function get_provider_group(){
	$CI= getCI();
	$CI->db->order_by("provider_group");
	return $CI->db->get_where("sos_tref_provider_group",array(
		"status_data" => 1
	))->result();
}

function get_client_name($id=""){
	$CI= getCI();
	$m = $CI->db->get_where("sos_client",array(
		"client_id" => $id
	))->row();
	return isset($m->client_name)?$m->client_name:'n/a';
}



function check_number($clientid="",$number="",$field="") {
	$CI = getCI();
	
    $flag = FALSE; $error = "";		
    if(!empty($number)) {
    	$memberid = get_name('sos_member', 'member_id', array('member_cardno' => $number));
    	/*start get provider group by member*/
		$mgroup = $CI->db->get_where("sos_tmas_member_provider",array(
				"memberId" => $memberid
			))->result();
		$arr_groupid = array();
		foreach ((array)$mgroup as $p => $q) {
			$arr_groupid[] = $q->providergroupId;
		}
		/*end get provider group by member*/
		
		if( count($CI->jCfg['user']['providergroupid']) == 0 ){
			$CI->jCfg['user']['providergroupid'] = $arr_groupid;
		}
		//debugCode($arr_groupid);
		$row = $CI->db->get_where('sos_member', array('member_clientid' => $clientid, 'member_cardno' => $number))->row();	
		if(count($row)>0) {
			$row = $CI->db->get_where('sos_member', array('member_cardno' => $number, 'member_statuscard' => 'Active'))->row();	
			if(count($row)>0 || !empty($field)) {			
				$compare_result = array_intersect($arr_groupid, $CI->jCfg['user']['providergroupid']);
				if(count($compare_result)>0) {
						$flag = TRUE;
				} else $error = 'verification_outofregion';
			} else $error = 'verification_memberinactive';
		} else $error = 'verification_memberfail';
    }
    
	if($flag) {
		return array('status' => true, 'error' => '');
	} else {
		$msg = get_name('sos_ref_message_email','message_content',array('message_type' => $error));
		return array('status' => false, 'error' => $msg); 
	}
}
	
?>