CLIENT = {
    init: function(){        
    },
    _showData: function(id,offset){
	    $('#list-provider').html('<tr><td colspan="3">loading...</td></tr>');
	    $.post(URL_GET_DATA_PROVIDER,{id:id,offset:offset},function(o){
		    obj = eval('('+o+')');
		    CLIENT._clearDetail;
		    CLIENT.htmlProvider(obj.provider,offset);
		    $('#btn-previous, #btn-next').removeAttr('disabled');
		    $('#p-offset').val(parseInt(offset)-5);		    
		    $('#n-offset').val(parseInt(offset)+5);
		    if($('#p-offset').val()<0) $('#btn-previous').attr('disabled','disabled');
		    if($('#n-offset').val()>obj.max) $('#btn-next').attr('disabled','disabled'); 
	    });
    },
    htmlProvider: function(obj,offset){
    	html = '';ap=$('#list-provider');
    	ap.html('');
	    if(obj.length > 0){
		    
		    no=offset>0?offset:0;
	    	$.each(obj,function(k,v){
	    		 no++;
		    	 html ='<tr id="tr-detail-'+v.provider_id+'" style="cursor:pointer;" onclick="CLIENT._showDetail('+v.provider_id+')">';
			     html +='<td>'+no+'</td>';
			     html +='<td>'+v.provider_name+'</td>';
			     html +='<td>'+v.provider_description+'</td>';
				 html += '</tr>';
				 
				 ap.append(html);
	    	});
	    	
	    	
	    }else{
		     ap.html('<tr><td colspan="3">Data tidak ditemukan</td></tr>');
	    }
    },
    _showDetail: function(id){
    	CLIENT._clearDetail();
    	ap = $('#tr-detail-'+id);
    	$('.panel-detail').css('opacity','1');
    	
        var id = ['','provider-name','provider-description'], i = 0;
        ap.find("td").each(function(){
           if(i==1) $('#'+id[i]).html($(this).html());
           if(i==2) $('#'+id[i]).html($(this).html());
            i++;
        });
    },
    _clearDetail: function(){
    	$('.panel-detail').css('opacity','0.5');
	    $('#provider-name').html('');
    	$('#provider-description').html('');
    	
    }
}

$(document).ready(function(){
	CLIENT.init();
});