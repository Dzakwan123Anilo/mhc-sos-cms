TRAW = {
    init: function(){
		$("#transraw_clientid").change(function(){
		    $.post(GET_SERVICE_TYPE,{id:$(this).val()},function(o){
			    obj = JSON.parse(o);
			    TRAW.fillServiceType(obj.st);
		    });
	    });

		if(SID != '') {
			$.post(GET_SERVICE_TYPE,{id:CID},function(o){
			    obj = JSON.parse(o);
			    TRAW.fillServiceType(obj.st,SID);
		    });
		}
    },
    fillServiceType: function(obj,sid) {
	ap=$('#transraw_serviceid');
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
	    $('#table-family').html('<tr><td colspan="10">loading...</td></tr>');
	    TRAW._clearMainTRaw();
	    $.post(URL_GET_DATA_TRAW,{id:id},function(o){
		$('#loading-item').fadeOut("fast",function(){
			$('.panel-detail').css('opacity','1');
			$('#first').removeAttr('class');
			$('#second').attr('class','active');
			$('#tab-first').removeAttr('class').attr('class','tab-pane');
			$('#tab-second').removeAttr('class').attr('class','tab-pane active');
		});
		    obj = JSON.parse(o);
		    TRAW.htmlFamily(obj.family);
		    TRAW.htmlTRaw(obj.member);

	    });
    },
    htmlTRaw: function(obj){
	    $('#cardno').text(obj.member_cardno);
	$('#name').text(obj.member_name);
	$('#sex').text(obj.member_sex);
	$('#birthdate').text(obj.member_birthdate);
	$('#address').text(obj.member_address);
	$('#phone').text(obj.member_phone);
	$('#clientname').text(obj.client_name);
	$('#relationship').text(obj.member_relationshiptype);
	$('#status').text(obj.member_employmentstatus);
	$('#statuscard').text(obj.member_statuscard);
	$('#plandesc').text(obj.plan_description);
	$('#dental').text(obj.member_dentallimit);

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
    _clearMainTRaw: function(){
	    $('#cardno').html('');
	$('#name').html('');
	$('#sex').html('');
	$('#birthdate').html('');
	$('#address').html('');
	$('#phone').html('');
	$('#clientname').html('');
	$('#relationship').html('');
	$('#status').html('');
	$('#statuscard').html('');
	$('#plandesc').html('');
	$('#dental').html('');
    }
}

$(document).ready(function(){
	TRAW.init();
});
