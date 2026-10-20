JS_MEMBER = {
    init: function(){
        
        
		        
    },
    _showData: function(id){
	    $('#loading-item').fadeIn();
	    $('#table-family').html('<tr><td colspan="10">loading...</td></tr>');
	    JS_MEMBER._clearMainMember();
	    $.post(URL_GET_DATA_MEMBER,{id:id},function(o){
	    	$('#loading-item').fadeOut("fast",function(){
		    	$('.panel-detail').css('opacity','1');
		    	$('#first').removeAttr('class');
		    	$('#second').attr('class','active');
		    	$('#tab-first').removeAttr('class').attr('class','tab-pane');
		    	$('#tab-second').removeAttr('class').attr('class','tab-pane active');
	    	});
		    obj = eval('('+o+')');
		    JS_MEMBER.htmlFamily(obj.family);
		    JS_MEMBER.htmlMember(obj.member);
		    
	    });
    },
    htmlMember: function(obj){
	   
	    $('#cardno').html(obj.member_cardno);
    	$('#name').html(obj.member_name);
    	$('#sex').html(obj.member_sex);
    	$('#birthdate').html(obj.member_birthdate);
    	$('#address').html(obj.member_address);
    	$('#phone').html(obj.member_phone);
    	$('#clientname').html(obj.client_name);
    	$('#relationship').html(obj.member_relationshiptype);
    	$('#status').html(obj.member_employmentstatus);
    	$('#statuscard').html(obj.member_statuscard);    	
    	$('#plandesc').html(obj.plan_description);
    	$('#dental').html(obj.member_dentallimit);  	
    	
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
    _clearMainMember: function(){
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
	JS_MEMBER.init();
});