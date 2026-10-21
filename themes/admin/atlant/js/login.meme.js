$(document).ready(function(){
    login._init();
});
 
login = {
    _init : function(){
        $('#btn_login').click(function(){
            login.actLogin();
        });
        $('#login_pass,#login_captcha').keypress(function(e) {
            if(e.which == 13) {
                login.actLogin();
            }
        });
        $('#captcha_img').click(function(){
            login.refreshCaptcha();
        });
    },
    refreshCaptcha : function(){
        $('#captcha_img').attr('src', CAPTCHA_URL + '?t=' + Date.now());
    },
    _applyChallenge : function(obj){
        if( obj.data && obj.data.login_token ){
            LOGIN_TOKEN = obj.data.login_token;
        }
        if( obj.data && obj.data.captcha_url ){
            $('#captcha_img').attr('src', obj.data.captcha_url);
        }else{
            $('#captcha_img').attr('src', CAPTCHA_URL + '?t=' + Date.now());
        }
        $('#login_captcha').val('');
    },
    actLogin : function(){
        $('#btn_login').html('Loading...');
        var request = $.ajax({
          url: AUTH_URL,
          type: "POST",
          data: { 
            username      : $('#login_username').val(),
            password      : $('#login_pass').val(),
            captcha       : $('#login_captcha').val(),
            login_token   : LOGIN_TOKEN,
            login         : true
          },
          dataType: "json"
        });
        
        request.done(function( obj ) {
            if(obj.status==0){
                $('#alert_message').removeClass('alert-success').addClass('alert-danger').fadeIn();
                $('#txt_message').html(obj.message);
                $('#login_pass').val('');
                if(obj.data && obj.data.captcha_url){
                    $('#login_captcha').focus();
                }
                login._applyChallenge(obj);
            }else{
                $('#alert_message').removeClass('alert-danger').addClass('alert-success').fadeIn();
                $('#txt_message').html(obj.message);
                document.location = obj.data.go_to;
            }
            $('#btn_login').html('Login');
            return false;
        });
    }
}
