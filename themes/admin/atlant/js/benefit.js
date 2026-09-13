BENEFIT = {
	init: function(){
		$('#benefit_clientid').change(function(){
			var clientId = $(this).val();
			$.post(GET_SERVICE_TYPE, {id: clientId}, function(response){
				var data = (response && typeof response === 'object') ? response : JSON.parse(response);
				BENEFIT.fillServiceType(data.st);
			});
			$.post(GET_PLANT_TYPE, {id: clientId}, function(response){
				var data = (response && typeof response === 'object') ? response : JSON.parse(response);
				BENEFIT.fillPlanType(data.pt);
			});
		});

		if(STID !== ''){
			$.post(GET_SERVICE_TYPE, {id: CID}, function(response){
				var data = (response && typeof response === 'object') ? response : JSON.parse(response);
				BENEFIT.fillServiceType(data.st, STID);
			});
		}
		if(PTID !== ''){
			$.post(GET_PLANT_TYPE, {id: CID}, function(response){
				var data = (response && typeof response === 'object') ? response : JSON.parse(response);
				BENEFIT.fillPlanType(data.pt, PTID);
			});
		}
	},
	fillServiceType: function(items, selectedId){
		var select = $('#benefit_serviceid');
		MEME.fillSelect(select, items, '- pilih tipe pelayanan -', 'service_id', function(item){
			return item.service_type;
		}, selectedId);
		select.selectpicker('refresh');
	},
	fillPlanType: function(items, selectedId){
		var select = $('#benefit_planid');
		MEME.fillSelect(select, items, '- pilih kelas perawatan -', 'plan_id', function(item){
			return '[ ' + MEME.textValue(item.plan_type) + ' ] ' + MEME.textValue(item.plan_description);
		}, selectedId);
		select.selectpicker('refresh');
	}
};

$(document).ready(function(){
	BENEFIT.init();
});
