<!DOCTYPE html>
<html lang="en" class="body-full-height">
    <head>        
        <!-- META SECTION -->
        <title>LOGIN - <?php echo cfg('app_name');?></title>            
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        
        <link rel="icon" href="favicon.ico" type="image/x-icon" />
        <!-- END META SECTION -->
        <script type="text/javascript" src="<?php echo themeUrl();?>js/plugins/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo themeUrl();?>js/plugins/jquery/jquery-migrate.min.js"></script>
        <!-- CSS INCLUDE -->        
        <link rel="stylesheet" type="text/css" id="theme" href="<?php echo themeUrl();?>css/theme-sos.css"/>
        <!-- EOF CSS INCLUDE -->        
        <style type="text/css">
        .ctheme{
            color: <?php echo cfg('color_theme');?>;
        }
        body{
            background-color: #fff;
        }
        </style>                       
    </head>
    <body>
        
        <div class="login-container login-v2">
            
            <div class="login-box animated fadeInDown">
                <div class="login-body">
                    <div class="login-title">
                        <?php
                        //echo cfg('upload_path_photo')."/".$this->site_info['site']->site_logo;
                        $image_logo = get_new_image(array(
                                  "url"     => cfg('upload_path_photo')."/".$this->site_info['site']->site_logo,
                                  "folder"  => "site"
                                ),true);
                        ?>
                        <center><img src="<?php echo $image_logo;?>" height="65" /></center>
                    </div>
                    <form id="login_form" action="" class="form-horizontal" method="post">
                    
                    <div class="alert alert-success" id="alert_message" role="alert" style="display:none;">
                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <strong id="txt_message"></strong>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <span class="fa fa-user"></span>
                                </div>
                                <input type="text" value="" class="form-control" id="login_username" placeholder="Username" autocomplete="username"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <label for="login_captcha">Kode verifikasi</label>
                            <div class="login-captcha">
                                <div class="login-captcha-preview" role="group" aria-label="Gambar dan tombol pembaruan kode verifikasi">
                                    <div class="login-captcha-image-frame">
                                        <img id="login_captcha_image" src="<?php echo site_url('auth/captcha');?>" width="180" height="56" alt="Kode verifikasi" />
                                    </div>
                                    <button type="button" class="btn btn-default" id="refresh_captcha" title="Muat kode baru" aria-label="Muat kode verifikasi baru">
                                        <span class="fa fa-refresh"></span>
                                    </button>
                                </div>
                                <input type="text" value="" class="form-control" id="login_captcha" placeholder="Masukkan kode 6 karakter" maxlength="6" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <span class="fa fa-lock"></span>
                                </div>                                
                                <input type="password" value="" class="form-control" id="login_pass" placeholder="Password" autocomplete="current-password"/>
                            </div>
                        </div>
                    </div>
                    <!--<div class="form-group">
                        <div class="col-md-6">
                            <a href="#">Lupa password?</a>
                        </div>          
                        <div class="col-md-6 text-right">
                            <a href="#">Buat account baru</a>
                        </div>              
                    </div>-->
                    <div class="form-group">
                        <div class="col-md-12">
                            <button class="btn btn-primary btn-lg btn-block" id="btn_login">Login</button>
                        </div>
                    </div>
                    </form>
                </div>
                <div class="login-footer">
                    <center>
                         &copy; <?php echo date("Y")." ".cfg('app_name');?>
                    </center>
                    <!--<div class="pull-left">
                       
                    </div>
                    <div class="pull-right">
                        <!--<a href="#">About</a> |
                        <a href="#">Privacy</a> |
                        <a href="#">Contact Us</a>-->
                    </div>
                </div>
            </div>
            
        </div>
        

<script type="text/javascript">

  var AUTH_URL = '<?php echo site_url("auth/act_auth");?>';
  var CAPTCHA_URL = '<?php echo site_url("auth/captcha");?>';

</script>

<script src="<?php echo themeUrl();?>js/login.meme.js" type="text/javascript"></script>


    </body>
</html>


