GROUP_PROVIDER = {
    init: function(){
        GROUP_PROVIDER.selectAll();
        $("#provider_clientid").change(function(){
        	if($('#providergroupId').val() == '') {
        	    $.post(URL_GET_DATA_PROVIDER,{id:$(this).val()},function(o){
        		    obj = eval('('+o+')');
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
		    	 html ='<tr>';
		    	 html +='<td>';
		    	 html +='<input type="checkbox" name="item_select['+v.provider_id+']" value="'+v.provider_id+'" class="item_select" id="item_select_'+v.provider_id+'" />';
		    	 html +='</td>';
			     html +='<td>'+v.provider_name+'</td>';
			     html +='<td>'+v.provider_name_edc+'</td>';
			     html +='<td>'+v.provider_code+'</td>';
			     html +='<td>'+v.provider_type+'</td>';
			     html +='<td>'+v.provider_phone+'</td>';
			     html +='<td>'+v.provider_email+'</td>';
			     html +='<td>'+v.provider_region+'</td>';
			     html +='<td>'+v.provider_location+'</td>';
				 html += '</tr>';
				 
				 ap.append(html);
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