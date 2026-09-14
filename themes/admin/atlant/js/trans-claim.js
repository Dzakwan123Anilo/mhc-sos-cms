TCLAIM = {
    init: function(){
		$("#transclaim_clientid").change(function(){
		    $.post(GET_SERVICE_TYPE,{id:$(this).val()},function(o){
			    obj = JSON.parse(o);
			    TCLAIM.fillServiceType(obj.st);
		    });
	    });

		if(SID != '') {
			$.post(GET_SERVICE_TYPE,{id:CID},function(o){
			    obj = JSON.parse(o);
			    TCLAIM.fillServiceType(obj.st,SID);
		    });
		}
    },
    fillServiceType: function(obj,sid) {
	ap=$('#transclaim_serviceid');
	ap.empty().append($('<option>', {value: '', text: ' - pilih tipe layanan - '}));
	    if(obj.length > 0){
		$.each(obj,function(k,v){
			$('<option>', {
				value: v.service_id,
				text: v.service_type,
				selected: sid == v.service_id
			}).appendTo(ap);
		});
	    }
	ap.selectpicker('refresh');
	},
    _showData: function(id){
	    $('#loading-item').fadeIn();
	    $('#table-history').html('<tr><td colspan="10">loading...</td></tr>');
	    $('#table-family').html('<tr><td colspan="10">loading...</td></tr>');
	    TCLAIM._clearHistDetail();
	    TCLAIM._clearMainMember();
	    $('#print-preview').hide();
	    $.post(URL_GET_DATA_CLAIM,{id:id},function(o){
		$('#loading-item').fadeOut("fast",function(){
			$('.panel-detail').css('opacity','1');
		});
		    obj = JSON.parse(o);
		$('#claim_id').val(id);
		$('#btn-next').removeAttr('disabled');
		    $('#p-offset').val(0);
		    $('#n-offset').val(5);
		    TCLAIM.htmlHistory(obj.history);
		    TCLAIM.htmlFamily(obj.family);
		    TCLAIM.htmlMember(obj.member);

	    });
    },
    _showHistory: function(id,offset){
	    $('#table-history').html('<tr><td colspan="10">loading...</td></tr>');
	    $.post(URL_GET_DATA_CLAIM,{id:id,offset:offset},function(o){
		    obj = JSON.parse(o);
		    TCLAIM._clearHistDetail();
		    TCLAIM.htmlHistory(obj.history,offset);
		    $('#btn-previous, #btn-next').removeAttr('disabled');
		    $('#p-offset').val(parseInt(offset)-5);
		    $('#n-offset').val(parseInt(offset)+5);
		    if($('#p-offset').val()<0) $('#btn-previous').attr('disabled','disabled');
		    if($('#n-offset').val()>obj.max) $('#btn-next').attr('disabled','disabled');
	    });
    },
    htmlMember: function(obj){
	    $('#card-no-main').text(obj.member_cardno);
	$('#id-karyawan-main').text(obj.member_employeeid);
	$('#tipe-relasi-main').text(obj.member_relationshiptype);
	$('#member-name-main').text(obj.member_name);
	$('#jenkel-main').text(obj.member_sex);
	$('#tgl-lahir-main').text(obj.member_birthdate);
	$('#propinsi-main').text(obj.member_region);
	$('#kab-main').text(obj.member_location);
	$('#perusahaan-main').text(obj.client_name);
	$('#hak-akses-main').text(obj.plan_description);
	$('#desc-main').text(obj.member_description);

	$('#claim_relationshiptype').val(obj.member_relationshiptype);


    },
    htmlHistory: function(obj,offset){
	html = '';ap=$('#table-history');
	ap.html('');
	    if(obj.length > 0){
		    no=offset>0?offset:0;
		$.each(obj,function(k,v){
			no++;
				var claimId = parseInt(v.claimId, 10);
				var row = $('<tr>').css('cursor', 'pointer').on('click', function(){
					if (!isNaN(claimId)) {
						TCLAIM._showHistDetail(claimId);
					}
				});
				$.each([
					no, v.date_claim, v.provider_name, v.service_type, v.member_name,
					v.member_cardno, v.member_employeeid, v.relationship_type,
					v.room_class, v.claim_status
				], function(index, value){
					$('<td>').text(value == null ? '' : value).appendTo(row);
				});
				ap.append(row);
		});
	    }else{
		    ap.html('<tr><td colspan="10">Data tidak ditemukan</td></tr>');
	    }
    },
    htmlFamily: function(obj){
	html = '';ap=$('#table-family');
	ap.html('');
	    if(obj.length > 0){

		    no=0;
		$.each(obj,function(k,v){
			no++;
				var row = $('<tr>');
				$.each([
					no, v.client_name, v.member_cardno, v.member_name,
					v.member_relationshiptype, v.member_sex, v.member_birthdate,
					v.member_region, v.member_location, v.plan_description,
					v.member_employmentstatus, v.member_statuscard, v.member_dentallimit
				], function(index, value){
					$('<td>').text(value == null ? '' : value).appendTo(row);
				});
				ap.append(row);
		});


	    }else{
		     ap.html('<tr><td colspan="20">Data tidak ditemukan</td></tr>');
	    }
    },
    _clearHistDetail: function(){
	$('#print-preview').hide();
	$('.panel-detail-2').css('opacity','0.5');
	    $('#provider-hist').html('');
	$('#tgl-claim-hist').html('');
	$('#tipe-layanan-hist').html('');
	$('#nama-client-hist').html('');
	$('#nama-member-hist').html('');
	$('#card-no-hist').html('');
	$('#tgl-lahir-hist').html('');
	$('#jenkel-hist').html('');
	$('#hak-layanan-hist').html('');
	$('#dental-limit-hist').html('');
	$('#desc-hist').html('');

    },
    _clearMainMember: function(){
	    $('#card-no-main').html('');
	$('#id-karyawan-main').html('');
	$('#tipe-relasi-main').html('');
	$('#member-name-main').html('');
	$('#jenkel-main').html('');
	$('#tgl-lahir-main').html('');
	$('#propinsi-main').html('');
	$('#kab-main').html('');
	$('#perusahaan-main').html('');
	$('#hak-akses-main').html('');
	$('#desc-main').html('');

	$('#claim_date').val('');
	$('#claim_planid').val('');
	$('#claim_memberid').val('');
	$('#claim_clientid').val('');
	$('#claim_relationshiptype').val('');
	$('#claim_roomclass').val('');
	$('#claim_servicetype').val('');

    },
    _showHistDetail: function(id){
	$('#loading-item').fadeIn();
	TCLAIM._clearHistDetail();
	    $.post(URL_GET_DATA_HIST_CLAIM,{id:id},function(o){

		    obj = JSON.parse(o);
		    if( obj.status == 1 ){
			if(obj.data.n=='success') {
				    $('#print-preview').fadeIn("fast",function(){
					$('.panel-detail-2').css('opacity','1');
					$('#loading-item').fadeOut();
				});
			} else {
				$('.panel-detail-2').css('opacity','1');
				$('#loading-item').fadeOut();
			}

			$('#provider-hist').text(obj.data.a);
			$('#tgl-claim-hist').text(obj.data.b);
			$('#tipe-layanan-hist').text(obj.data.c);
			$('#nama-client-hist').text(obj.data.d);
			$('#nama-member-hist').text(obj.data.e);
			$('#card-no-hist').text(obj.data.f);
			$('#tgl-lahir-hist').text(obj.data.g);
			$('#jenkel-hist').text(obj.data.h);
			$('#hak-layanan-hist').text(obj.data.i);
			$('#dental-limit-hist').text(obj.data.j);
			$('#desc-hist').text(obj.data.k);

			//console.log(obj.item);
			$('#claim_date').val(obj.item.date_claim);
			$('#claim_planid').val(obj.item.plan_id);
			$('#claim_memberid').val(obj.item.memberId);
			$('#claim_clientid').val(obj.item.client_id);
			$('#claim_roomclass').val(obj.item.room_class);
			$('#claim_servicetype').val(obj.item.servicetypeId);

		    }else{
			    alert('Terjadi Error.');
		    }

	    });
    },
    printPreview: function(){

	    var date = $('#claim_date').val(),
		planid = $('#claim_planid').val(),
			memberid = $('#claim_memberid').val(),
		clientid = $('#claim_clientid').val(),
		relationtype = $('#claim_relationshiptype').val(),
		roomclass = $('#claim_roomclass').val(),
		servicetype = $('#claim_servicetype').val(),
		h = screen.height,
		w = screen.width;
			URL_PRINT = URL_CETAK_HIST+"?save=0&pid="+planid+"&dt="+date+"&mid="+memberid+"&cid="+clientid+"&rt="+relationtype+"&rc="+roomclass+"&st="+servicetype;
			console.log(URL_PRINT);
	window.open(URL_PRINT, "Print Preview", "width=400,height=600,left="+((w-400)/2)+",top="+((h-650)/2));

    }
}

$(document).ready(function(){
	TCLAIM.init();
});
