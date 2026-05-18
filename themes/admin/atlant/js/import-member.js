IMPORT_MEMBER = {
    init: function(){
        IMPORT_MEMBER.selectAll();
        $('#btn_approve').click(function(){
            IMPORT_MEMBER.approveItem();
        });
        $('#btn_refresh').click(function(){
          document.location.reload(true);
        });
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
        });

    },
    approveItem: function(){
       IMPORT_MEMBER.btnOnLoading($('#btn_approve'),'START');
       ID = $("input.item_select:checked:first").val();
       if (typeof ID != "undefined") {

            $('#tr_item_info_'+ID).fadeIn("medium",function(){
              $('#msg_item_'+ID).html('Loading..');
              $('#tr_item_'+ID).css('opacity','0.3');
            });

            parData = {
                key:AJAX_KEY_IMPORT,
                id:ID,
                cid:$('#client_id_'+ID).val(),
                card_no:$('#card_number_'+ID).val(),
                member_name:$('#card_name_'+ID).val(),
                relationship:$('#relationship_'+ID).val(),
                gender:$('#gender_'+ID).val(),
                bod:$('#birthdate_'+ID).val(),
                address:$('#address_'+ID).val(),
                region:$('#province_'+ID).val(),
                upd_code:$('#code_'+ID).val(),
                plan_type:$('#plan_type_'+ID).val(),
                room_class:$('#room_class_'+ID).val(),
                dental_unit:$('#dental_unit_'+ID).val(),
                emp_status:$('#emp_status_'+ID).val(),
                remark:$('#remark_'+ID).val(),
                group:$('#group_'+ID).val(),
                import_site_id:IMPORT_SITE_ID,
                import_id:IMPORT_ID
            };
            $.post(AJAX_URL_IMPORT,parData,function(res){
                obj = eval('('+res+')');
                if( obj.status == 1 ){
                  SUKSES++;
                  $('#count_success').html(SUKSES);
                  $('#msg_item_'+ID).html("<span style='color:green;'>"+obj.message+"</span>");
                }
                if( obj.status == 0 ){
                  GAGAL++;
                  $('#count_gagal').html(GAGAL);
                  $('#msg_item_'+ID).html("<span style='color:red;'>"+obj.message+"</span>");
                  
                }
                
                $('#tr_item_'+ID).css('opacity','1');
                $("input.item_select:checked:first").remove();
                IMPORT_MEMBER.approveItem();
            });

       }else{
          IMPORT_MEMBER.btnOnLoading($('#btn_approve'),'SELESAI');
       }

    },
    btnOnLoading: function(obj,status){
        if( status=='START' ){
            $('#btn_approve').removeClass('btn-warning').html('silahkan tunggu!, Loading....');
        }
        if( status=='SELESAI'){
            $('#btn_approve').addClass('btn-warning').html('<i class="fa fa-check"></i> Approve');
        }
    }
}


$(document).ready(function(){
    IMPORT_MEMBER.init();
});