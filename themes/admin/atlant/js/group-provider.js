GROUP_PROVIDER = {
    init: function(){
        GROUP_PROVIDER.selectAll();
        $("#provider_clientid").change(function(){
	if($('#providergroupId').val() == '') {
	    $.post(URL_GET_DATA_PROVIDER,function(o){
		    obj = JSON.parse(o);

		    $('#dt-provider').dataTable().fnDestroy();
		    GROUP_PROVIDER.htmlProvider(obj.provider);
		    $('#dt-provider').dataTable({
				"bPaginate": false,
				"pageLength": 1000000
			});
	    });
	}
        });
    },
    htmlProvider: function(obj){
	html = '';ap=$('#table-provider');
	ap.html('');
	    if(obj.length > 0){

		    no=0;
		$.each(obj,function(k,v){
				var providerId = String(v.provider_id == null ? '' : v.provider_id);
				var row = $('<tr>');
				var checkbox = $('<input>', {
					type: 'checkbox',
					name: 'item_select[' + providerId + ']',
					value: providerId,
					'class': 'item_select',
					id: 'item_select_' + providerId
				});
				$('<td>').append(checkbox).appendTo(row);
				$.each([
					v.provider_name, v.provider_name_edc, v.provider_code,
					v.provider_type, v.provider_phone, v.provider_email,
					v.provider_region, v.provider_location
				], function(index, value){
					$('<td>').text(value == null ? '' : value).appendTo(row);
				});
				ap.append(row);
		});

	    }
    },
    selectAll: function(){

        $("#select_all").change(function(){
            if(this.checked){
              $("input.item_select").each(function(){
                this.checked=true;
              })
            }else{
              $("input.item_select").each(function(){
                this.checked=false;
              })
            }
        });

        $("input.item_select").click(function () {
            if ($(this).is(":checked")){
              var isAllChecked = 0;
              $("input.item_select").each(function(){
                if(!this.checked)
                   isAllChecked = 1;
              })
              if(isAllChecked == 0){ $("#select_all").prop("checked", true); }
            }
            else {
              $("#select_all").prop("checked", false);
            }
            $(".datatable").DataTable().search("").draw();
            $('#dt-provider_filter label input').val("");
        });

    }
}


$(document).ready(function(){
    GROUP_PROVIDER.init();
});
