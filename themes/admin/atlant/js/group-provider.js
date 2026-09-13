GROUP_PROVIDER = {
	init: function(){
		GROUP_PROVIDER.bindSelection();
		$('#provider_clientid').change(function(){
			if($('#providergroupId').val() !== ''){
				return;
			}
			$.post(URL_GET_DATA_PROVIDER, function(response){
				var data = (response && typeof response === 'object') ? response : JSON.parse(response);
				$('#dt-provider').dataTable().fnDestroy();
				GROUP_PROVIDER.renderProviders(data.provider);
				$('#dt-provider').dataTable({
					bPaginate: false,
					pageLength: 1000000
				});
			});
		});
	},
	renderProviders: function(providers){
		var table = $('#table-provider').empty();
		$.each(providers || [], function(index, provider){
			var providerId = MEME.textValue(provider.provider_id);
			var row = $('<tr>');
			var checkbox = $('<input>', {
				type: 'checkbox',
				name: 'item_select[' + providerId + ']',
				value: providerId,
				'class': 'item_select',
				id: 'item_select_' + providerId
			});
			row.append($('<td>').append(checkbox));
			$.each([
				provider.provider_name,
				provider.provider_name_edc,
				provider.provider_code,
				provider.provider_type,
				provider.provider_phone,
				provider.provider_email,
				provider.provider_region,
				provider.provider_location
			], function(fieldIndex, value){
				row.append($('<td>').text(MEME.textValue(value)));
			});
			table.append(row);
		});
	},
	bindSelection: function(){
		$('#select_all').change(function(){
			$('input.item_select').prop('checked', this.checked);
		});
		$(document).off('click.groupProvider', 'input.item_select').on('click.groupProvider', 'input.item_select', function(){
			var allChecked = $('input.item_select').length > 0 && $('input.item_select:not(:checked)').length === 0;
			$('#select_all').prop('checked', allChecked);
			$('.datatable').DataTable().search('').draw();
			$('#dt-provider_filter label input').val('');
		});
	}
};

$(document).ready(function(){
	GROUP_PROVIDER.init();
});
