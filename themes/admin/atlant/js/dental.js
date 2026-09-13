DENTAL = {
	init: function(){
		$('#dental_clientid').change(function(){
			$.post(GET_PLANT_TYPE, {id: $(this).val()}, function(response){
				var data = (response && typeof response === 'object') ? response : JSON.parse(response);
				DENTAL.fillPlanType(data.pt);
			});
		});
		if(PTID !== ''){
			$.post(GET_PLANT_TYPE, {id: CID}, function(response){
				var data = (response && typeof response === 'object') ? response : JSON.parse(response);
				DENTAL.fillPlanType(data.pt, PTID);
			});
		}
	},
	fillPlanType: function(items, selectedId){
		var select = $('#dental_plantypeid');
		MEME.fillSelect(select, items, '- pilih plan type -', 'plan_id', function(item){
			return '[ ' + MEME.textValue(item.plan_type) + ' ] ' + MEME.textValue(item.plan_description);
		}, selectedId);
		select.selectpicker('refresh');
	}
};

$(document).ready(function(){
	DENTAL.init();
});
