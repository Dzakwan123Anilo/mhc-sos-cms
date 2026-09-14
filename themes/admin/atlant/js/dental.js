DENTAL = {
    init: function(){
		$("#dental_clientid").change(function(){
	    $.post(GET_PLANT_TYPE,{id:$(this).val()},function(o){
		    obj = JSON.parse(o);
		    DENTAL.fillPlanType(obj.pt);
	    });
	    });

		if(PTID != '') {
			$.post(GET_PLANT_TYPE,{id:CID},function(o){
			    obj = JSON.parse(o);
			    DENTAL.fillPlanType(obj.pt,PTID);
		    });
		}
    },
    fillPlanType: function(obj,sid) {
	ap=$('#dental_plantypeid');
	ap.empty().append($('<option>', {value: '', text: ' - pilih plan type - '}));
	    if(obj.length > 0){
		$.each(obj,function(k,v){
			$('<option>', {
				value: v.plan_id,
				text: '[ ' + (v.plan_type || '') + ' ] ' + (v.plan_description || ''),
				selected: sid == v.plan_id
			}).appendTo(ap);
		});
	    }
	ap.selectpicker('refresh');
	}
}

$(document).ready(function(){
	DENTAL.init();
});
