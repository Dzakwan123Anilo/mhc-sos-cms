TCLAIM = {
    init: function(){	
		$("#transclaim_clientid").change(function(){
		    $.post(GET_SERVICE_TYPE,{id:$(this).val()},function(o){
			    obj = eval('('+o+')');
			    TCLAIM.fillServiceType(obj.st);
		    });
	    });

		if(SID != '') {
			$.post(GET_SERVICE_TYPE,{id:CID},function(o){
			    obj = eval('('+o+')');
			    TCLAIM.fillServiceType(obj.st,SID);
		    });
		}
    },
    fillServiceType: function(obj,sid) { 
    	ap=$('#transclaim_serviceid');
    	
    	ap.empty();
    	html = '<option value=""> - pilih tipe layanan - </option>';
	    if(obj.length > 0){
		    
	    	$.each(obj,function(k,v){
	    		var s = sid==v.service_id?' selected="selected"':'';
				html += '<option value="'+v.service_id+'"'+s+'>'+v.service_type+'</option>';				 
	    	});	    	
	    	
	    }
    	ap.append(html);
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
		    obj = eval('('+o+')');
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
		    obj = eval('('+o+')');
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
	   
	    $('#card-no-main').html(obj.member_cardno);
    	$('#id-karyawan-main').html(obj.member_employeeid);
    	$('#tipe-relasi-main').html(obj.member_relationshiptype);
    	$('#member-name-main').html(obj.member_name);
    	$('#jenkel-main').html(obj.member_sex);
    	$('#tgl-lahir-main').html(obj.member_birthdate);
    	$('#propinsi-main').html(obj.member_region);
    	$('#kab-main').html(obj.member_location);
    	$('#perusahaan-main').html(obj.client_name);
    	$('#hak-akses-main').html(obj.plan_description);
    	$('#desc-main').html(obj.member_description);
    	
    	$('#claim_relationshiptype').val(obj.member_relationshiptype);
    	
    	
    },
    htmlHistory: function(obj,offset){
    	html = '';ap=$('#table-history');
    	ap.html('');
	    if(obj.length > 0){
		    no=offset>0?offset:0;
	    	$.each(obj,function(k,v){
	    		 no++;
		    	 html ='<tr style="cursor:pointer;" onclick="TCLAIM._showHistDetail('+v.claimId+')">';
			     html +='<td>'+no+'</td>';
			     html +='<td nowrap="nowrap">'+v.date_claim+'</td>';
			     html +='<td nowrap="nowrap">'+v.provider_name+'</td>';
			     html +='<td nowrap="nowrap">'+v.service_type+'</td>';
			     html +='<td nowrap="nowrap">'+v.member_name+'</td>';
			     html +='<td nowrap="nowrap">'+v.member_cardno+'</td>';
			     html +='<td nowrap="nowrap">'+v.member_employeeid+'</td>';
			     html +='<td>'+v.relationship_type+'</td>';
			     html +='<td nowrap="nowrap">'+v.room_class+'</td>';
			     html +='<td>'+v.claim_status+'</td>';
				 html += '</tr>';
				 ap.append(html); 
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
		    	 html ='<tr>';
			     html +='<td>'+no+'</td>';
			     html +='<td>'+v.client_name+'</td>';
			     html +='<td>'+v.member_cardno+'</td>';
			     html +='<td>'+v.member_name+'</td>';
			     html +='<td>'+v.member_relationshiptype+'</td>';
			     html +='<td>'+v.member_sex+'</td>';
			     html +='<td>'+v.member_birthdate+'</td>';
			     html +='<td>'+v.member_region+'</td>';
			     html +='<td>'+v.member_location+'</td>';
			     html +='<td>'+v.plan_description+'</td>';
			     html +='<td>'+v.member_employmentstatus+'</td>';
			     html +='<td>'+v.member_statuscard+'</td>';
			     html +='<td>'+v.member_dentallimit+'</td>';
				 html += '</tr>';
				 
				 ap.append(html);
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
	    	
		    obj = eval('('+o+')');
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
		    	
		    	$('#provider-hist').html(obj.data.a);
		    	$('#tgl-claim-hist').html(obj.data.b);
		    	$('#tipe-layanan-hist').html(obj.data.c);
		    	$('#nama-client-hist').html(obj.data.d);
		    	$('#nama-member-hist').html(obj.data.e);
		    	$('#card-no-hist').html(obj.data.f);
		    	$('#tgl-lahir-hist').html(obj.data.g);
		    	$('#jenkel-hist').html(obj.data.h);
		    	$('#hak-layanan-hist').html(obj.data.i);
		    	$('#dental-limit-hist').html(obj.data.j);
		    	$('#desc-hist').html(obj.data.k);
		    	
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