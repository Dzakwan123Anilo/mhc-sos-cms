$(document).ready(function(){
    login._init();
});
 
var login = {
    _init : function(){
        $('#login_form').on('submit', function(event){
            event.preventDefault();
            login.actLogin();
        });
        $('#btn_refresh_captcha').click(function(){
            login.refreshCaptcha();
        });
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
                $('#alert_message').removeClass('alert-success').addClass('alert-danger').fadeIn();
                $('#txt_message').text(obj.message);
                $('#login_pass,#login_captcha').val('');
                login.refreshCaptcha(obj.data && obj.data.captcha_url ? obj.data.captcha_url : null);
                $('#login_pass').focus();
            }else{
                $('#alert_message').removeClass('alert-danger').addClass('alert-success').fadeIn();
                $('#txt_message').text(obj.message);
                var destination = login.sameOriginUrl(obj.data && obj.data.go_to);
                if (!destination) {
                    $('#alert_message').removeClass('alert-success').addClass('alert-danger');
                    $('#txt_message').text('Tujuan login tidak valid.');
                    $('#btn_login').prop('disabled', false).text('Login');
                    return false;
                }
                window.location.assign(destination);
            }
            $('#btn_login').prop('disabled', false).text('Login');
            return false;
        });

        request.fail(function() {
            $('#alert_message').removeClass('alert-success').addClass('alert-danger').fadeIn();
            $('#txt_message').text('Login tidak dapat diproses. Silakan coba lagi.');
            $('#btn_login').prop('disabled', false).text('Login');
            $('#login_pass,#login_captcha').val('');
            login.refreshCaptcha();
        });
    },
    refreshCaptcha : function(url){
        var separator = CAPTCHA_URL.indexOf('?') === -1 ? '?' : '&';
        var fallback = CAPTCHA_URL + separator + 'refresh=1&t=' + Date.now();
        $('#captcha_image').attr('src', login.sameOriginUrl(url) || fallback);
    },
    sameOriginUrl : function(url){
        if (typeof url !== 'string' || url === '') {
            return null;
        }

        var parser = document.createElement('a');
        parser.href = url;
        if ((parser.protocol !== 'http:' && parser.protocol !== 'https:')
            || parser.host !== window.location.host
            || parser.username
            || parser.password) {
            return null;
        }

        return parser.href;
    }
};
