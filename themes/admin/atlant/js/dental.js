DENTAL = {
    init: function(){ 
		$("#dental_clientid").change(function(){
    	    $.post(GET_PLANT_TYPE,{id:$(this).val()},function(o){
    		    obj = eval('('+o+')');
    		    DENTAL.fillPlanType(obj.pt);
    	    });
	    });
		
		if(PTID != '') {
			$.post(GET_PLANT_TYPE,{id:CID},function(o){
			    obj = eval('('+o+')');
			    DENTAL.fillPlanType(obj.pt,PTID);
		    });
		}
    },
    fillPlanType: function(obj,sid) {  
    	ap=$('#dental_plantypeid');
    	
    	ap.empty();
    	html = '<option value=""> - pilih plan type - </option>';
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
	DENTAL.init();
});