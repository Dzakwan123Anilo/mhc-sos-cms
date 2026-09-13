TRAW = {
	init: function(){
		$('#transraw_clientid').change(function(){
			$.post(GET_SERVICE_TYPE, {id: $(this).val()}, function(response){
				var data = (response && typeof response === 'object') ? response : JSON.parse(response);
				TRAW.fillServiceType(data.st);
			});
		});
		if(SID !== ''){
			$.post(GET_SERVICE_TYPE, {id: CID}, function(response){
				var data = (response && typeof response === 'object') ? response : JSON.parse(response);
				TRAW.fillServiceType(data.st, SID);
			});
		}
	},
	fillServiceType: function(items, selectedId){
		var select = $('#transraw_serviceid');
		MEME.fillSelect(select, items, '- pilih tipe layanan -', 'service_id', function(item){
			return item.service_type;
		}, selectedId);
		select.selectpicker('refresh');
	},
	_showData: function(id){
		$('#loading-item').fadeIn();
		$('#table-family').empty().append($('<tr>').append($('<td>', {colspan: 10}).text('loading...')));
		TRAW._clearMainTRaw();
		$.post(URL_GET_DATA_TRAW, {id: id}, function(response){
			$('#loading-item').fadeOut('fast', function(){
				$('.panel-detail').css('opacity', '1');
				$('#first').removeAttr('class');
				$('#second').attr('class', 'active');
				$('#tab-first').attr('class', 'tab-pane');
				$('#tab-second').attr('class', 'tab-pane active');
			});
			var data = (response && typeof response === 'object') ? response : JSON.parse(response);
			TRAW.renderFamily(data.family);
			TRAW.renderMember(data.member);
		});
	},
	renderMember: function(member){
		var fields = {
			cardno: member.member_cardno,
			name: member.member_name,
			sex: member.member_sex,
			birthdate: member.member_birthdate,
			address: member.member_address,
			phone: member.member_phone,
			clientname: member.client_name,
			relationship: member.member_relationshiptype,
			status: member.member_employmentstatus,
			statuscard: member.member_statuscard,
			plandesc: member.plan_description,
			dental: member.member_dentallimit
		};
		$.each(fields, function(field, value){
			$('#' + field).text(MEME.textValue(value));
		});
	},
	renderFamily: function(family){
		var table = $('#table-family').empty();
		if(!family || family.length === 0){
			table.append($('<tr>').append($('<td>', {colspan: 20}).text('Data tidak ditemukan')));
			return;
		}
		$.each(family, function(index, member){
			MEME.appendTextRow(table, [
				index + 1,
				member.client_name,
				member.member_cardno,
				member.member_name,
				member.member_relationshiptype,
				member.member_sex,
				member.member_birthdate,
				member.member_region,
				member.member_location,
				member.plan_description,
				member.member_employmentstatus,
				member.member_statuscard,
				member.member_dentallimit
			]);
		});
	},
	_clearMainTRaw: function(){
		$('#cardno,#name,#sex,#birthdate,#address,#phone,#clientname,#relationship,#status,#statuscard,#plandesc,#dental').text('');
	}
};

$(document).ready(function(){
	TRAW.init();
});
