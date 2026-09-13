TCLAIM = {
    init: function(){
		$("#transclaim_clientid").change(function(){
		    $.post(GET_SERVICE_TYPE,{id:$(this).val()},function(o){
			    var obj = (o && typeof o === 'object') ? o : JSON.parse(o);
			    TCLAIM.fillServiceType(obj.st);
		    });
	    });

		if(SID != '') {
			$.post(GET_SERVICE_TYPE,{id:CID},function(o){
			    var obj = (o && typeof o === 'object') ? o : JSON.parse(o);
			    TCLAIM.fillServiceType(obj.st,SID);
		    });
		}
    },
    fillServiceType: function(items, selectedId) {
		var select = $('#transclaim_serviceid');
		MEME.fillSelect(select, items, '- pilih tipe layanan -', 'service_id', function(item){
			return item.service_type;
		}, selectedId);
		select.selectpicker('refresh');
	},
    _showData: function(id){
	    $('#loading-item').fadeIn();
	    $('#table-history').empty().append($('<tr>').append($('<td>', {colspan: 10}).text('loading...')));
	    $('#table-family').empty().append($('<tr>').append($('<td>', {colspan: 10}).text('loading...')));
	    TCLAIM._clearHistDetail();
	    TCLAIM._clearMainMember();
	    $('#print-preview').hide();
	    $.post(URL_GET_DATA_CLAIM,{id:id},function(o){
		$('#loading-item').fadeOut("fast",function(){
			$('.panel-detail').css('opacity','1');
		});
		    var obj = (o && typeof o === 'object') ? o : JSON.parse(o);
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
	    $('#table-history').empty().append($('<tr>').append($('<td>', {colspan: 10}).text('loading...')));
	    $.post(URL_GET_DATA_CLAIM,{id:id,offset:offset},function(o){
		    var obj = (o && typeof o === 'object') ? o : JSON.parse(o);
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

	    $('#card-no-main').text(MEME.textValue(obj.member_cardno));
		$('#id-karyawan-main').text(MEME.textValue(obj.member_employeeid));
		$('#tipe-relasi-main').text(MEME.textValue(obj.member_relationshiptype));
		$('#member-name-main').text(MEME.textValue(obj.member_name));
		$('#jenkel-main').text(MEME.textValue(obj.member_sex));
		$('#tgl-lahir-main').text(MEME.textValue(obj.member_birthdate));
		$('#propinsi-main').text(MEME.textValue(obj.member_region));
		$('#kab-main').text(MEME.textValue(obj.member_location));
		$('#perusahaan-main').text(MEME.textValue(obj.client_name));
		$('#hak-akses-main').text(MEME.textValue(obj.plan_description));
		$('#desc-main').text(MEME.textValue(obj.member_description));

	$('#claim_relationshiptype').val(obj.member_relationshiptype);


    },
    htmlHistory: function(history, offset){
		var table = $('#table-history').empty();
		var start = parseInt(offset, 10) || 0;
		if(!history || history.length === 0){
			table.append($('<tr>').append($('<td>', {colspan: 10}).text('Data tidak ditemukan')));
			return;
		}
		$.each(history, function(index, claim){
			var row = MEME.appendTextRow(table, [
				start + index + 1,
				claim.date_claim,
				claim.provider_name,
				claim.service_type,
				claim.member_name,
				claim.member_cardno,
				claim.member_employeeid,
				claim.relationship_type,
				claim.room_class,
				claim.claim_status
			]);
			row.css('cursor', 'pointer').on('click', function(){
				TCLAIM._showHistDetail(claim.claimId);
			});
			row.find('td').slice(1, 7).add(row.find('td').eq(8)).attr('nowrap', 'nowrap');
		});
    },
    htmlFamily: function(family){
		var table = $('#table-family').empty();
		if(!family || family.length === 0){
			table.append($('<tr>').append($('<td>', {colspan: 20}).text('Data tidak ditemukan')));
			return;
		}
		$.each(family, function(index, member){
			MEME.appendTextRow(table, [
				index + 1,
				member.client_name,
				member.member_cardno,
				member.member_name,
				member.member_relationshiptype,
				member.member_sex,
				member.member_birthdate,
				member.member_region,
				member.member_location,
				member.plan_description,
				member.member_employmentstatus,
				member.member_statuscard,
				member.member_dentallimit
			]);
		});
    },
    _clearHistDetail: function(){
	$('#print-preview').hide();
	$('.panel-detail-2').css('opacity','0.5');
	    $('#provider-hist').text('');
	$('#tgl-claim-hist').text('');
	$('#tipe-layanan-hist').text('');
	$('#nama-client-hist').text('');
	$('#nama-member-hist').text('');
	$('#card-no-hist').text('');
	$('#tgl-lahir-hist').text('');
	$('#jenkel-hist').text('');
	$('#hak-layanan-hist').text('');
	$('#dental-limit-hist').text('');
	$('#desc-hist').text('');

    },
    _clearMainMember: function(){
	    $('#card-no-main').text('');
	$('#id-karyawan-main').text('');
	$('#tipe-relasi-main').text('');
	$('#member-name-main').text('');
	$('#jenkel-main').text('');
	$('#tgl-lahir-main').text('');
	$('#propinsi-main').text('');
	$('#kab-main').text('');
	$('#perusahaan-main').text('');
	$('#hak-akses-main').text('');
	$('#desc-main').text('');

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

		    var obj = (o && typeof o === 'object') ? o : JSON.parse(o);
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

			$('#provider-hist').text(MEME.textValue(obj.data.a));
			$('#tgl-claim-hist').text(MEME.textValue(obj.data.b));
			$('#tipe-layanan-hist').text(MEME.textValue(obj.data.c));
			$('#nama-client-hist').text(MEME.textValue(obj.data.d));
			$('#nama-member-hist').text(MEME.textValue(obj.data.e));
			$('#card-no-hist').text(MEME.textValue(obj.data.f));
			$('#tgl-lahir-hist').text(MEME.textValue(obj.data.g));
			$('#jenkel-hist').text(MEME.textValue(obj.data.h));
			$('#hak-layanan-hist').text(MEME.textValue(obj.data.i));
			$('#dental-limit-hist').text(MEME.textValue(obj.data.j));
			$('#desc-hist').text(MEME.textValue(obj.data.k));

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
