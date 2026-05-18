EXPORT_DATA = {
    init: function(){
       $('#export_member_xls').click(function(){
            EXPORT_DATA.memberExport(0,"");
       });

       $('#export_trans_xls').click(function(){
            EXPORT_DATA.transExport(0,"");
       });
       
       $('#export_trans_claim_xls').click(function(){
            EXPORT_DATA.tclaimExport(0,"");
       });
	   
	   $('#export_provider_xls').click(function(){
            EXPORT_DATA.providerExport(0,"");
       });
       
       $('#export_log_xls').click(function(){
           EXPORT_DATA.logExport(0,"");
       });

    },
    memberExport: function(offset,filename){
        $('#div_info_msg').fadeIn();
        iJumlah = $('#jumlah_progress');
        iTotal = $('#total_progress');
        $.post(URL_EXPORT_DATA,{limit:500,offset:offset,filename:filename},function(o){
            obj = eval('('+o+')');
            if( obj.status == 1 ){
                if( $.trim(obj.data.url_download) != '' ){
                    $('#info_export').html(obj.data.message);
                    setTimeout(function(){
                        $('#div_info_msg').fadeOut();
                    },5000);
                    document.location = obj.data.url_download;
                }else{
                    tmpJml = parseInt(iJumlah.html())+obj.data.success;
                    iJumlah.html(tmpJml);
                    iTotal.html(obj.data.total);
                    EXPORT_DATA.memberExport(obj.data.offset,obj.data.filename);
                }
            }else{
                $('#info_export').html(obj.data.message);
                setTimeout(function(){
                    $('#div_info_msg').fadeOut();
                },5000);
            }
        });
    },

    transExport: function(offset,filename){
        $('#div_info_msg').fadeIn();
        iJumlah = $('#jumlah_progress');
        iTotal = $('#total_progress');
        $.post(URL_EXPORT_TRANS,{limit:500,offset:offset,filename:filename},function(o){
            obj = eval('('+o+')');
            if( obj.status == 1 ){
                if( $.trim(obj.data.url_download) != '' ){
                    $('#info_export').html(obj.data.message);
                    setTimeout(function(){
                        $('#div_info_msg').fadeOut();
                    },5000);
                    document.location = obj.data.url_download;
                }else{
                    tmpJml = parseInt(iJumlah.html())+obj.data.success;
                    iJumlah.html(tmpJml);
                    iTotal.html(obj.data.total);
                    EXPORT_DATA.transExport(obj.data.offset,obj.data.filename);
                }
            }else{
                $('#info_export').html(obj.data.message);
                setTimeout(function(){
                    $('#div_info_msg').fadeOut();
                },5000);
            }
        });
    },
    
    tclaimExport: function(offset,filename){
        $('#div_info_msg').fadeIn();
        iJumlah = $('#jumlah_progress');
        iTotal = $('#total_progress');
        $.post(URL_EXPORT_TCLAIM,{limit:500,offset:offset,filename:filename},function(o){
            obj = eval('('+o+')');
            if( obj.status == 1 ){
                if( $.trim(obj.data.url_download) != '' ){
                    $('#info_export').html(obj.data.message);
                    setTimeout(function(){
                        $('#div_info_msg').fadeOut();
                    },5000);
                    document.location = obj.data.url_download;
                }else{
                    tmpJml = parseInt(iJumlah.html())+obj.data.success;
                    iJumlah.html(tmpJml);
                    iTotal.html(obj.data.total);
                    EXPORT_DATA.tclaimExport(obj.data.offset,obj.data.filename);
                }
            }else{
                $('#info_export').html(obj.data.message);
                setTimeout(function(){
                    $('#div_info_msg').fadeOut();
                },5000);
            }
        });
    },
    
    providerExport: function(offset,filename){
        $('#div_info_msg').fadeIn();
        iJumlah = $('#jumlah_progress');
        iTotal = $('#total_progress');
        $.post(URL_EXPORT_PROVIDER,{limit:300,offset:offset,filename:filename},function(o){
            obj = eval('('+o+')');
            if( obj.status == 1 ){
                if( $.trim(obj.data.url_download) != '' ){
                    $('#info_export').html(obj.data.message);
                    setTimeout(function(){
                        $('#div_info_msg').fadeOut();
                    },5000);
                    document.location = obj.data.url_download;
                }else{
                    tmpJml = parseInt(iJumlah.html())+obj.data.success;
                    iJumlah.html(tmpJml);
                    iTotal.html(obj.data.total);
                    EXPORT_DATA.providerExport(obj.data.offset,obj.data.filename);
                }
            }else{
                $('#info_export').html(obj.data.message);
                setTimeout(function(){
                    $('#div_info_msg').fadeOut();
                },5000);
            }
        });
    },
    
    logExport: function(offset,filename){
    	
        $('#div_info_msg').fadeIn();
        iJumlah = $('#jumlah_progress');
        iTotal = $('#total_progress');
        $.post(URL_EXPORT_LOG,{limit:300,offset:offset,filename:filename},function(o){
            obj = eval('('+o+')');
            if( obj.status == 1 ){
                if( $.trim(obj.data.url_download) != '' ){
                    $('#info_export').html(obj.data.message);
                    setTimeout(function(){
                        $('#div_info_msg').fadeOut();
                    },5000);
                    document.location = obj.data.url_download;
                }else{
                    tmpJml = parseInt(iJumlah.html())+obj.data.success;
                    iJumlah.html(tmpJml);
                    iTotal.html(obj.data.total);
                    EXPORT_DATA.logExport(obj.data.offset,obj.data.filename);
                }
            }else{
                $('#info_export').html(obj.data.message);
                setTimeout(function(){
                    $('#div_info_msg').fadeOut();
                },5000);
            }
        });
    },
    
    activeMemberExport: function(offset,filename,cid,date,type){
    	
        $('#div_info_msg').fadeIn();
        iJumlah = $('#jumlah_progress');
        iTotal = $('#total_progress');
        $.post(URL_EXPORT_DATA_ACTIVE_MEMBER,{limit:500,offset:offset,filename:filename,cid:cid,date:date,type:type},function(o){
            obj = eval('('+o+')');
            if( obj.status == 1 ){
                if( $.trim(obj.data.url_download) != '' ){
                    $('#info_export').html(obj.data.message);
                    setTimeout(function(){
                        $('#div_info_msg').fadeOut();
                    },5000);
                    document.location = obj.data.url_download;
                }else{
                    tmpJml = parseInt(iJumlah.html())+obj.data.success;
                    iJumlah.html(tmpJml);
                    iTotal.html(obj.data.total);
                    EXPORT_DATA.activeMemberExport(obj.data.offset,obj.data.filename,obj.data.cid,obj.data.date,obj.data.type);
                }
            }else{
                $('#info_export').html(obj.data.message);
                setTimeout(function(){
                    $('#div_info_msg').fadeOut();
                },5000);
            }
        });
    },
    
    swipeByClientExport: function(offset,filename,cid,date,today,type){
    	
        $('#div_info_msg').fadeIn();
        iJumlah = $('#jumlah_progress');
        iTotal = $('#total_progress');
        $.post(URL_EXPORT_DATA_SWIPE_BY_CLIENT,{limit:500,offset:offset,filename:filename,cid:cid,date:date,today:today,type:type},function(o){
            obj = eval('('+o+')');
            if( obj.status == 1 ){
                if( $.trim(obj.data.url_download) != '' ){
                    $('#info_export').html(obj.data.message);
                    setTimeout(function(){
                        $('#div_info_msg').fadeOut();
                    },5000);
                    document.location = obj.data.url_download;
                }else{
                    tmpJml = parseInt(iJumlah.html())+obj.data.success;
                    iJumlah.html(tmpJml);
                    iTotal.html(obj.data.total);
                    EXPORT_DATA.swipeByClientExport(obj.data.offset,obj.data.filename,obj.data.cid,obj.data.date,obj.data.today,obj.data.type);
                }
            }else{
                $('#info_export').html(obj.data.message);
                setTimeout(function(){
                    $('#div_info_msg').fadeOut();
                },5000);
            }
        });
    },
    
    swipeMoreExport: function(offset,filename,cid,date,today,type){
    	
        $('#div_info_msg').fadeIn();
        iJumlah = $('#jumlah_progress');
        iTotal = $('#total_progress');
        $.post(URL_EXPORT_DATA_SWIPE_MORE,{limit:500,offset:offset,filename:filename,cid:cid,date:date,today:today,type:type},function(o){
            obj = eval('('+o+')');
            if( obj.status == 1 ){
                if( $.trim(obj.data.url_download) != '' ){
                    $('#info_export').html(obj.data.message);
                    setTimeout(function(){
                        $('#div_info_msg').fadeOut();
                    },5000);
                    document.location = obj.data.url_download;
                }else{
                    tmpJml = parseInt(iJumlah.html())+obj.data.success;
                    iJumlah.html(tmpJml);
                    iTotal.html(obj.data.total);
                    EXPORT_DATA.swipeMoreExport(obj.data.offset,obj.data.filename,obj.data.cid,obj.data.date,obj.data.today,obj.data.type);
                }
            }else{
                $('#info_export').html(obj.data.message);
                setTimeout(function(){
                    $('#div_info_msg').fadeOut();
                },5000);
            }
        });
    },
    
    swipeByProviderExport: function(offset,filename,pid,date,today,type){
    	
        $('#div_info_msg').fadeIn();
        iJumlah = $('#jumlah_progress');
        iTotal = $('#total_progress');
        $.post(URL_EXPORT_DATA_SWIPE_BY_PROVIDER,{limit:500,offset:offset,filename:filename,pid:pid,date:date,today:today,type:type},function(o){
            obj = eval('('+o+')');
            if( obj.status == 1 ){
                if( $.trim(obj.data.url_download) != '' ){
                    $('#info_export').html(obj.data.message);
                    setTimeout(function(){
                        $('#div_info_msg').fadeOut();
                    },5000);
                    document.location = obj.data.url_download;
                }else{
                    tmpJml = parseInt(iJumlah.html())+obj.data.success;
                    iJumlah.html(tmpJml);
                    iTotal.html(obj.data.total);
                    EXPORT_DATA.swipeByProviderExport(obj.data.offset,obj.data.filename,obj.data.pid,obj.data.date,obj.data.today,obj.data.type);
                }
            }else{
                $('#info_export').html(obj.data.message);
                setTimeout(function(){
                    $('#div_info_msg').fadeOut();
                },5000);
            }
        });
    },
    
    userLoginExport: function(offset,filename,pid,type){
    	
        $('#div_info_msg').fadeIn();
        iJumlah = $('#jumlah_progress');
        iTotal = $('#total_progress');
        $.post(URL_EXPORT_DATA_USER_LOGIN,{limit:500,offset:offset,filename:filename,pid:pid,type:type},function(o){
            obj = eval('('+o+')');
            if( obj.status == 1 ){
                if( $.trim(obj.data.url_download) != '' ){
                    $('#info_export').html(obj.data.message);
                    setTimeout(function(){
                        $('#div_info_msg').fadeOut();
                    },5000);
                    document.location = obj.data.url_download;
                }else{
                    tmpJml = parseInt(iJumlah.html())+obj.data.success;
                    iJumlah.html(tmpJml);
                    iTotal.html(obj.data.total);
                    EXPORT_DATA.userLoginExport(obj.data.offset,obj.data.filename,obj.data.pid,obj.data.type);
                }
            }else{
                $('#info_export').html(obj.data.message);
                setTimeout(function(){
                    $('#div_info_msg').fadeOut();
                },5000);
            }
        });
    }
    
    

}

$(document).ready(function(){
    EXPORT_DATA.init();
});