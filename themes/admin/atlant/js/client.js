CLIENT = {
	init: function(){},
	_showData: function(id, offset){
		$('#list-provider').empty().append($('<tr>').append($('<td>', {colspan: 3}).text('loading...')));
		$.post(URL_GET_DATA_PROVIDER, {id: id, offset: offset}, function(response){
			var data = (response && typeof response === 'object') ? response : JSON.parse(response);
			CLIENT._clearDetail();
			CLIENT.renderProviders(data.provider, offset);
			$('#btn-previous, #btn-next').removeAttr('disabled');
			$('#p-offset').val(parseInt(offset, 10) - 5);
			$('#n-offset').val(parseInt(offset, 10) + 5);
			if($('#p-offset').val() < 0){
				$('#btn-previous').attr('disabled', 'disabled');
			}
			if($('#n-offset').val() > data.max){
				$('#btn-next').attr('disabled', 'disabled');
			}
		});
	},
	renderProviders: function(providers, offset){
		var table = $('#list-provider').empty();
		if(!providers || providers.length === 0){
			table.append($('<tr>').append($('<td>', {colspan: 3}).text('Data tidak ditemukan')));
			return;
		}
		var start = parseInt(offset, 10) || 0;
		$.each(providers, function(index, provider){
			var providerId = MEME.textValue(provider.provider_id);
			var row = MEME.appendTextRow(table, [
				start + index + 1,
				provider.provider_name,
				provider.provider_description
			]);
			row.attr('id', 'tr-detail-' + providerId).css('cursor', 'pointer').on('click', function(){
				CLIENT._showDetail(providerId);
			});
		});
	},
	_showDetail: function(id){
		CLIENT._clearDetail();
		var cells = $('#tr-detail-' + id).find('td');
		$('.panel-detail').css('opacity', '1');
		$('#provider-name').text(cells.eq(1).text());
		$('#provider-description').text(cells.eq(2).text());
	},
	_clearDetail: function(){
		$('.panel-detail').css('opacity', '0.5');
		$('#provider-name,#provider-description').text('');
	}
};

$(document).ready(function(){
	CLIENT.init();
});
