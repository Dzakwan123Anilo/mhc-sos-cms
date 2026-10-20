BENEFIT = {
    init: function(){ 
		$( 'textarea#wysiwg_full' ).ckeditor({
			filebrowserUploadUrl: '/upload_ckeditor.php'
		});
		
		$("#benefit_clientid").change(function(){
		    $.post(GET_SERVICE_TYPE,{id:$(this).val()},function(o){
			    obj = eval('('+o+')');
			    BENEFIT.fillServiceType(obj.st);
		    });

		    $.post(GET_PLANT_TYPE,{id:$(this).val()},function(o){
			    obj = eval('('+o+')');
			    BENEFIT.fillPlanType(obj.pt);
		    });
	    });
		
		if(STID != '') {
			$.post(GET_SERVICE_TYPE,{id:CID},function(o){
			    obj = eval('('+o+')');
			    BENEFIT.fillServiceType(obj.st,STID);
		    });
		}
		
		if(PTID != '') {
			$.post(GET_PLANT_TYPE,{id:CID},function(o){
			    obj = eval('('+o+')');
			    BENEFIT.fillPlanType(obj.pt,PTID);
		    });
		}
    },
    fillServiceType: function(obj,sid) { 
    	ap=$('#benefit_serviceid');
    	
    	ap.empty();
    	html = '<option value=""> - pilih tipe pelayanan - </option>';
	    if(obj.length > 0){
		    
	    	$.each(obj,function(k,v){
	    		var s = sid==v.service_id?' selected="selected"':'';
				html += '<option value="'+v.service_id+'"'+s+'>'+v.service_type+'</option>';			 
	    	});	    	
	    	
	    }
    	ap.append(html);
    	ap.selectpicker('refresh');
	},
    fillPlanType: function(obj,sid) { 
    	ap=$('#benefit_planid');
    	
    	ap.empty();
    	html = '<option value=""> - pilih kelas perawatan - </option>';
	    if(obj.length > 0){
		    
	    	$.each(obj,function(k,v){
	    		var s = sid==v.plan_id?' selected="selected"':'';
				html += '<option value="'+v.plan_id+'"'+s+'>[ '+v.plan_type+' ] '+v.plan_description+'</option>';				 
	    	});	    	
	    	
	    }
    	ap.append(html);
    	ap.selectpicker('refresh');
	}
}

$(document).ready(function(){
	BENEFIT.init();
});