$(document).ready(function(){
    login._init();
});
 
login = {
	    _init : function(){
	        $('#refresh_captcha').click(function(){
	            login.refreshCaptcha();
	        });
	        $('#login_form').on('submit', function(e){
	            e.preventDefault();
	            login.actLogin();
	        });
	    },
	    refreshCaptcha : function(url){
	        var source = url || CAPTCHA_URL;
	        var separator = source.indexOf('?') === -1 ? '?' : '&';
	        $('#login_captcha_image').attr('src', source + separator + 'v=' + Date.now());
	        $('#login_captcha').val('');
	    },
	    showError : function(obj){
	        var message = obj && obj.message ? obj.message : 'Login gagal. Silakan coba lagi.';
	        $('#alert_message').removeClass('alert-success').addClass('alert-danger').fadeIn();
	        $('#txt_message').text(message);
	        $('#login_pass').val('');
	        login.refreshCaptcha(obj && obj.data ? obj.data.captcha_url : null);
	        $('#login_captcha').focus();
	    },
	    redirectInternal : function(target){
	        target = typeof target === 'string' ? $.trim(target) : '';
	        if(target === '' || /^\/\//.test(target) || /[\u0000-\u001f\u007f\\\\]/.test(target)){
	            login.showError({message: 'Tujuan login tidak valid.'});
	            return;
	        }
	        var link = document.createElement('a');
	        link.href = target;
	        if(link.protocol === window.location.protocol && link.host === window.location.host && !link.username && !link.password){
	            window.location.assign(link.href);
	            return;
	        }
	        login.showError({message: 'Tujuan login tidak valid.'});
	    },
	    actLogin : function(){
	        $('#btn_login').prop('disabled', true).text('Loading...');
	        var request = $.ajax({
          url: AUTH_URL,
          type: "POST",
          data: { 
	            username      : $('#login_username').val(),
	            password      : $('#login_pass').val(),
	            captcha       : $('#login_captcha').val(),
	            login         : true
          },
          dataType: "json"
        });
        
        
	        request.done(function( obj ) {
	            if(obj.status==0){
	                login.showError(obj);
	            }else{
	                $('#alert_message').removeClass('alert-danger').addClass('alert-success').fadeIn();
	                $('#txt_message').text(obj.message);
	                login.redirectInternal(obj.data.go_to);
	            }
	            $('#btn_login').prop('disabled', false).text('Login');
	            return false;
	        });

	        request.fail(function(jqXHR){
	            var response = jqXHR.responseJSON;
	            if(!response && jqXHR.responseText){
	                try {
	                    response = JSON.parse(jqXHR.responseText);
	                } catch (ignore) {
	                    response = null;
	                }
	            }
	            login.showError(response);
	            $('#btn_login').prop('disabled', false).text('Login');
	        });
	    }
}
