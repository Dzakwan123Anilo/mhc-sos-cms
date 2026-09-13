<!DOCTYPE html>
<html lang="en">
    <head>        
        <!-- META SECTION -->
        <title><?php echo cfg('app_name');?> - CMS</title>            
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        
        <link rel="icon" href="favicon.ico" type="image/x-icon" />
        <!-- END META SECTION -->
        
        <!-- START PLUGINS -->
        <script type="text/javascript" src="<?php echo themeUrl();?>js/plugins/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo themeUrl();?>js/plugins/jquery/jquery-ui.min.js"></script>
        <script type="text/javascript" src="<?php echo themeUrl();?>js/plugins/bootstrap/bootstrap.min.js"></script>        
        <!-- END PLUGINS -->
        <script type="text/javascript">
            var BASE_URL = '<?php echo base_url();?>';  
            var THEME_URL = '<?php echo themeUrl();?>';  
            var CURRENT_URL = '<?php echo current_url();?>';
            var MEME = {};
        </script>

        <!-- CSS INCLUDE -->        
        <link rel="stylesheet" type="text/css" id="theme" href="<?php echo themeUrl();?>css/theme-sos.css?t=23921042"/>
        <?php load_css();?>
        <style type="text/css">
         .heading-form{
                  font-size: 18px;
                  font-weight: normal;
                  border-bottom: 1px dotted #33414E;
                  border-left:4px solid #33414E;
                  padding-left: 10px;
                  padding-bottom: 4px;
            }
        </style>
        <!-- EOF CSS INCLUDE -->                                     
    </head>
    <body>
        <?php get_info_message();?>
        <!-- START PAGE CONTAINER -->
        <div class="page-container page-navigation-top page-navigation-top-custom">            
            <!-- PAGE CONTENT -->
            <div class="page-content">
                
                <!-- START PAGE CONTENT HEADER -->
                <?php if($this->jCfg['theme_setting']['header']==true){?>
                <div class="page-content-header">
                    <div class="pull-left">
                        <a href="<?php echo site_url('meme/me/welcome');?>">
                            <img src="<?php echo $this->jCfg['logo'];?>?1" height="50" />
                        </a>
                    </div>
                    <div class="pull-right">                        
                        <div class="socials">
                            <!--<a href="#"><span class="fa fa-facebook-square"></span></a>
                            <a href="#"><span class="fa fa-twitter-square"></span></a>
                            <a href="#"><span class="fa fa-pinterest-square"></span></a>
                            <a href="#"><span class="fa fa-linkedin-square"></span></a>
                            <a href="#"><span class="fa fa-dribbble"></span></a> 
                            -->
                            <?php echo myDate(date("Y-m-d H:i:s"),"d M Y H:i");?>                                                     
                        </div>
                        <div class="contacts">
                            <a href="#"><span class="fa fa-user"></span> <?php echo $this->jCfg['user']['fullname'];?>, <?php echo $this->jCfg['user']['name'];?></a>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <!-- END PAGE CONTENT HEADER -->
                
                <!-- START X-NAVIGATION VERTICAL -->
                <ul class="x-navigation x-navigation-horizontal">
                    <?php if($this->jCfg['theme_setting']['header']==true){?>
                    <li class="xn-navigation-control">
                        <a href="<?php echo site_url('meme/me');?>" class="x-navigation-control"></a>
                    </li>
                    <?php }else{ ?>
                    <li class="xn-logo">
                        <img src="<?php echo themeUrl();?>img/logo-login.png" height="40" />
                        <a href="<?php echo site_url('meme/me');?>" class="x-navigation-control"></a>
                    </li>
                    <?php } ?>
                    <?php top_menu($this->jCfg['menu']);?>
                    <!-- POWER OFF -->
                    <li class="xn-icon-button pull-right last">
                        <a href="#"><span class="fa fa-power-off"></span></a>
                        <ul class="xn-drop-left animated zoomIn">
                            <li><a href="<?php echo site_url('meme/me/change_password');?>"><span class="fa fa-key"></span> Change Password</a></li>
                            <li><a href="<?php echo site_url('meme/me/edit_profile');?>"><span class="fa fa-User"></span> Edit Profile</a></li>
                            <li><a href="<?php echo site_url('auth/out');?>" class="act_confirm" data-title="Logout" data-body="Apakah anda yakin akan logout ?" data-desc="Tekan Tidak jika anda ingin melanjutkan pekerjaan anda. Tekan Ya untuk keluar." data-icon="fa-sign-out"><span class="fa fa-sign-out"></span> Sign Out</a></li>
                        </ul>                        
                    </li> 
                    <!--<li class="xn-icon-button pull-right last">
                        <a href="<?php echo site_url('meme/me/set_theme')."?next=".current_url();?>"><span class="fa fa-cog"></span></a>                      
                    </li>-->
                    <!-- <li class="pull-right last">
                        <a href="#"><span class="fa fa-question-circle"></span> Panduan</a>
                        <ul class="xn-drop-left animated zoomIn">
                            <li><a href="</?php echo site_url('meme/me/help');?>"><i class="fa fa-question-circle"></i> Dashboard</a></li>
                            <li><a href="</?php echo site_url('meme/me/help?page=login');?>"><i class="fa fa-question-circle"></i> Menu Login</a></li>
                            <li><a href="</?php echo site_url('meme/me/help?page=setting');?>"><i class="fa fa-question-circle"></i> Menu Setting</a></li>
                            <li><a href="</?php echo site_url('meme/me/help?page=artikel');?>"><i class="fa fa-question-circle"></i> Menu Artikel</a></li>
                            <li><a href="</?php echo site_url('meme/me/help?page=master');?>"><i class="fa fa-question-circle"></i> Menu Master</a></li>
                            <li><a href="</?php echo site_url('meme/me/help?page=media');?>"><i class="fa fa-question-circle"></i> Menu Media</a></li>
                            <li><a href="</?php echo site_url('meme/me/help?page=manajemen');?>"><i class="fa fa-question-circle"></i> Menu Manajemen</a></li>                        
                        </ul>                        
                    </li> --> 

                    

                    <!-- END POWER OFF -->   
                    <!--<li class="xn-search pull-right">
                        <form role="form">
                            <input type="text" name="search" placeholder="Search..."/>
                        </form>
                    </li>-->
                </ul>
                <!-- END X-NAVIGATION VERTICAL -->                     
                
                <!-- START BREADCRUMB -->
                <?php get_breadcrumb($this->breadcrumb);?>
                <!-- END BREADCRUMB -->                
                <div class="row">
                    <div class="col-md-6">
                        <div class="page-title">                    
                            <h2><span class="fa fa-arrow-circle-o-left"></span> <?php echo isset($title)?$title:'';?></h2>
                        </div>   
                    </div>   
                    <div class="col-md-6">
                        <!-- START TABS -->                                
                        <div class="panel panel-default tabs" style="border-top-width:0px;">   
                        <?php if($this->is_dashboard==FALSE){?>
                                                 
                            
                            <ul class="nav nav-tabs pull-right" role="tablist">
                                <?php isset($links)?getLinksWebArch($links):'';?> 
                            </ul>                            
                            
                              
                        <?php }else{ ?>

                            <ul class="nav nav-tabs pull-right" role="tablist" style="margin-top:-30px;">
                                <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                                  <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                  <span></span> <b class="caret"></b>
                                </div>
                            </ul>

                        <?php } ?>  
                         </div>                                                   
                        <!-- END TABS -->               
                    </div>  
                    <div style="clear:both;"></div>
                    <div style="border-bottom:1px solid #009F9A;margin:-20px 10px 10px 10px;" id="border-header"></div>           
                </div>
                <!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">
                    <div class="row" style="margin:-20px 10px 10px 10px;">
                        <div class="col-md-12 panel" id="panel-content-wrap" style="border-radius:0px;padding:20px;">


