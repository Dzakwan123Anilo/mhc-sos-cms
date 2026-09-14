BENEFIT = {
    init: function(){
		$("#benefit_clientid").change(function(){
		    $.post(GET_SERVICE_TYPE,{id:$(this).val()},function(o){
			    obj = JSON.parse(o);
			    BENEFIT.fillServiceType(obj.st);
		    });

		    $.post(GET_PLANT_TYPE,{id:$(this).val()},function(o){
			    obj = JSON.parse(o);
			    BENEFIT.fillPlanType(obj.pt);
		    });
	    });

		if(STID != '') {
			$.post(GET_SERVICE_TYPE,{id:CID},function(o){
			    obj = JSON.parse(o);
			    BENEFIT.fillServiceType(obj.st,STID);
		    });
		}

		if(PTID != '') {
			$.post(GET_PLANT_TYPE,{id:CID},function(o){
			    obj = JSON.parse(o);
			    BENEFIT.fillPlanType(obj.pt,PTID);
		    });
		}
    },
    fillServiceType: function(obj,sid) {
	ap=$('#benefit_serviceid');
	ap.empty().append($('<option>', {value: '', text: ' - pilih tipe pelayanan - '}));
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
    fillPlanType: function(obj,sid) {
	ap=$('#benefit_planid');
	ap.empty().append($('<option>', {value: '', text: ' - pilih kelas perawatan - '}));
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
	BENEFIT.init();
});
