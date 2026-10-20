<!DOCTYPE html>
<html lang="en" class="body-full-height">
    <head>        
        <!-- META SECTION -->
        <title><?php echo cfg('app_name');?></title>            
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        
        <link rel="icon" href="favicon.ico" type="image/x-icon" />
        <!-- END META SECTION -->
        <script type="text/javascript" src="<?php echo themeUrl();?>js/plugins/jquery/jquery.min.js"></script>
        <!-- CSS INCLUDE -->        
        <link rel="stylesheet" type="text/css" id="theme" href="<?php echo themeUrl();?>css/theme-sos.css"/>
        <!-- EOF CSS INCLUDE -->        
        <style type="text/css">
        .ctheme{
            color: <?php echo cfg('color_theme');?>;
        }
        @media print {
        	.hidden-print {
        		display: none !important;
        	}
        }
        </style>                       
    </head>
    <body style="background-color:#fff; padding: 0;">
        
        <div class="login-container login-v2" style="background-color:#fff;">
            
            <div class="login-box animated fadeInDown" style="background-color:#fff;">
                <div class="login-body" style="background-color:#fff;">
                    <div class="login-title" style="background-color:#fff;">
                        <?php
                        $image_logo = get_new_image(array(
                                  "url"     => $this->jCfg['logo'],
                                  "folder"  => "site"
                                ),false);
                        ?>
                        <center><img src="<?php echo $image_logo;?>" height="65" /></center>
                    </div>
                    <form action="#" class="form-horizontal">
                    	<h2 class="heading-form" style="text-align: center; width: 100%"><u><?php echo $claim;?></u></h2>
                    	
						<input type="hidden" id="claim_date" name="claim_date" value="<?php echo $param['date'];?>">
						<input type="hidden" id="claim_desc" name="claim_desc" value="<?php echo isset($param['desc'])?$param['desc']:'';?>">
						<input type="hidden" id="claim_planid" name="claim_planid" value="<?php echo $param['planid'];?>">
						<input type="hidden" id="claim_memberid" name="claim_memberid" value="<?php echo $param['memberid'];?>">
						<input type="hidden" id="claim_clientid" name="claim_clientid" value="<?php echo $param['clientid'];?>">
						<input type="hidden" id="claim_relationshiptype" name="claim_relationshiptype" value="<?php echo $param['relationtype'];?>">
						<input type="hidden" id="claim_roomclass" name="claim_roomclass" value="<?php echo $param['roomclass'];?>">
						<input type="hidden" id="claim_servicetype" name="claim_servicetype" value="<?php echo $param['servicetype'];?>">
                    	<table>
                    		<tr>
                    			<td width="120">Tanggal</td><td width="10">:</td>
                    			<td><?php echo myDate($date,'d M Y',false);?></td>
                    		</tr>
                    		<tr>
                    			<td>Jam</td><td>:</td>
                    			<td><?php echo myDate($date,'H:i:s',false);?></td>
                    		</tr>
                    		<tr>
                    			<td>Perusahaan</td><td>:</td>
                    			<td><?php echo $client;?></td>
                    		</tr>
                    	</table><br />
                    	<table>
                    		<tr>
                    			<td width="120"><b>Nama</b></td><td width="10"><b>:</b></td>
                    			<td><b><?php echo $member->member_name;?></b></td>
                    		</tr>
                    		<tr>
                    			<td><b>No Kartu</b></td><td><b>:</b></td>
                    			<td><b><?php echo $member->member_cardno;?></b></td>
                    		</tr>
                    		<tr>
                    			<td><b>Tanggal Lahir</b></td><td><b>:</b></td>
                    			<td><b><?php echo myDate($member->member_birthdate,"d M Y",false);?></b></td>
                    		</tr>
                    		<tr>
                    			<td><b>Jenis Kelamin</b></td><td><b>:</b></td>
                    			<td><b><?php echo jenkel($member->member_sex);?></b></td>
                    		</tr>
                    		<tr>
                    			<td><b>NIK</b></td><td><b>:</b></td>
                    			<td><b><?php echo $member->member_employeeid;?></b></td>
                    		</tr>
                    	<?php if($claim == "Rawat Inap") {?>
                    		<tr>
                    			<td><b>Hak Kelas</b></td><td><b>:</b></td>
                    			<td><b><?php echo $param['roomclass'];?></b></td>
                    		</tr>
                    	<?php }?>
                    	</table><br />
                    	<?php echo $benefit;?><br />
						<div class="row hidden-print">
		                    <div class="form-group">
		                    <?php if($save==1){?>
	                            <button class="btn btn-primary col-md-6" id="btn_login" onclick="window.print(); save_claim(); return false;"> Save & Print </button>
	                        <?php }else{?>
	                            <button class="btn btn-primary col-md-6" id="btn_login" onclick="window.print(); window.close();"> Print </button>
	                        <?php }?>
	                            <button class="btn btn-danger col-md-6" onclick="window.close()"> Cancel </button>
		                    </div>
						</div>
                    </form>
                </div>
            </div>            
        </div>        
    </body>
</html>
<script type="text/javascript">
function save_claim() {
//	alert('save');
	$.post('<?php echo $this->own_link."/save_claim"?>', {
		claim_date 				: $('#claim_date').val(),
		claim_memberid			: $('#claim_memberid').val(),
		claim_clientid			: $('#claim_clientid').val(),
		claim_desc				: $('#claim_desc').val(),
		claim_relationshiptype	: $('#claim_relationshiptype').val(),
		claim_roomclass			: $('#claim_roomclass').val(),
		claim_servicetype		: $('#claim_servicetype').val()			
	}, function( obj ) {
		o = eval('('+obj+')');
		if(o.status == 1) { 
			window.close();
			window.opener.location.href = '<?php echo $this->own_link."?msg="?>'+o.message+'&type_msg=success'; 
		} else { 
			window.close();
			window.opener.location.href = '<?php echo $this->own_link."?msg="?>'+o.message+'&type_msg=danger'; 
		}
	});		
}
</script>