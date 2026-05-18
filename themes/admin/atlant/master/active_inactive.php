<?php js_validate();?>
<div class="panel panel-default" style="margin-top:0px; padding:15px;">
	<div class="well">
		<form id="form-show" action="<?php echo $own_links;?>" class="form-horizontal" method="post" autocomplete="off"> 
			<div class="row">         
          		<div class="col-md-12">   
		            <div class="row form-group">  
						<div class="col-md-4 control-label">Nama Client</div>
						<div class="col-md-4">
						    <select class="form-control select" name="activation_clientid" id="activation_clientid">
						        <?php 
                        			$cli = isset($val)?$val->member_clientid:'';
							        echo option_client($cli);
						        ?>
						    </select>  
						</div>
		            </div> 
		            
		            <div class="row form-group">  
						<div class="col-md-4 control-label">Nomor Kartu</div>
						<div class="col-md-4">          
							<div class="input-group">
                				<input type="text" id="activation_cardno" name="activation_cardno" class="validate[required] form-control" value="<?php echo isset($val)?$val->member_cardno:$card_no;?>" />
							    <span class="input-group-btn">
			                    	<button type="button" name="btn_show" id="btn_show" class="btn btn-primary"><i class="fa fa-search"></i></button>
							    </span>
							</div>    
						</div>
		            </div> 
	            </div>
			</div>
		</form>
	</div>
	<div class="row" id="div-error" style="<?php echo isset($validate)?"":"display: none;"?>">
          <div class="col-md-12">   
            <div class="row form-group">  
				<div class="col-md-3">&nbsp;</div>
				<div id="display-error" class="col-md-6" style="font-size: 20px; font-weight: bold; text-align: center;"><?php echo isset($validate)?$validate['error']:""?></div>
			</div>
		</div>	
	</div>
<?php if(isset($val)) {?>	
	<div class="row" id="status">
		<div class="col-md-4"></div>
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading">
				    <h3 class="panel-title">Status Kartu Saat Ini : <b style="color:#b64645"><?php echo $val->member_statuscard;?></b></h3>
				</div>
				<div class="panel-body">
					<form id="form-validated" action="<?php echo $own_links;?>/activated" class="form-horizontal" method="post">
	                	<input type="hidden" name="member_statuscard" id="member_statuscard" value="<?php echo $val->member_statuscard;?>" />
	                	<input type="hidden" name="activation_clientid" id="activation_clientid" value="<?php echo $val->member_clientid;?>" />
	                	<input type="hidden" name="activation_cardno" id="activation_cardno" value="<?php echo $val->member_cardno;?>" />
				        <div class="form-group">               
			                <div class="btn-group btn-group-lg col-md-12">
			                    <button type="submit" name="btn_activated" class="btn btn-<?php echo $val->member_statuscard=='Active'?'primary':'default';?> col-md-6" <?php echo $val->member_statuscard=='Active'?'':'disabled="disabled"';?>>InActive</button>               
			                    <button type="submit" name="btn_activated" class="btn btn-<?php echo $val->member_statuscard=='Active'?'default':'primary';?> col-md-6" <?php echo $val->member_statuscard=='Active'?'disabled="disabled"':'';?>>Active</button>        
			                </div>      
				        </div>
				    </form>
				</div>
			</div>
		</div>	
	</div>
	<div class="row" id="member_detail">
        <h5 class="heading-form">Member Detail</h5>
          <div class="col-md-6">

            <div class="row form-group">  
              <div class="col-md-3 control-label">No. Kartu</div>
              <div class="col-md-9 control-label">: <b><?php echo $val->member_cardno;?></b></div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Nama Member</div>
              <div class="col-md-9 control-label">: <b><?php echo $val->member_name;?></b></div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Jenis Kelamin</div>
              <div class="col-md-9 control-label">: <b><?php echo jenkel($val->member_sex);?></b></div>
            </div>
            
            <div class="row form-group">  
              <div class="col-md-3 control-label">Tanggal Lahir</div>
              <div class="col-md-9 control-label">: <b><?php echo date('d-m-Y', strtotime($val->member_birthdate));?></b></div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Perusahaan</div>
              <div class="col-md-9 control-label">: <b><?php echo $val->client_name;?></b></div>
            </div>
            
          </div> 

          <div class="col-md-6">

            <div class="row form-group">  
              <div class="col-md-3 control-label">Kelas Perawatan</div>
              <div class="col-md-9 control-label">: <b><?php echo $val->plan_description;?></b></div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Tipe Relasi</div>
              <div class="col-md-9 control-label">: <b><?php echo $val->member_relationshiptype;?></b></div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Status Karyawan</div>
              <div class="col-md-9 control-label">: <b><?php echo $val->member_employmentstatus;?></b></div>
            </div>
            
            <div class="row form-group">  
              <div class="col-md-3 control-label">Tanggal Aktifasi</div>
              <div class="col-md-9 control-label">: <b><?php echo !empty($val->member_activationdate)&&$val->member_activationdate!='0000-00-00'?date('d-m-Y', strtotime($val->member_activationdate)):'';?></b></div>
            </div>
            
            <div class="row form-group">  
              <div class="col-md-3 control-label">Dental Limit</div>
              <div class="col-md-9 control-label">: <b><?php echo $val->member_dentallimit;?></b></div>
            </div>
            
          </div>

        </div>
        <br />
		<div class="row" id="benefit">
            <h5 class="heading-form">Benefit</h5>
          <div class="col-md-6">

            <div class="row form-group">  
              <div class="col-md-3 control-label">Kelas Perawatan</div>
              <div class="col-md-9 control-label">: <b><?php echo $val->plan_description;?></b></div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Dental Limit</div>
              <div class="col-md-9 control-label">: <b><?php echo $val->member_dentallimit;?></b></div>
            </div>
            
          </div>

        </div>
        <br />
        <div class="row" id="member_list"><br />
			<div class="panel panel-default" style="margin-top:-10px;">
				<div class="panel-heading">
					<div class="panel-title-box">
			            <h3>Family Member List</h3>
			            <span>Data Anggota Keluarga</span>
					</div>
				</div>
				<div class="panel-body panel-body-table">					
					<div class="table-responsive">
			            <table class="table table-hover table-bordered table-striped">
							<thead>
				                <tr>
				                    <th width="30px">No</th>
				                    <th>Client</th>
				                    <th>No. Kartu</th>
				                    <th>Nama Member</th>
				                    <th>Tipe Relasi</th>
				                    <th>Jenis Kelamin</th>
				                    <th>Tgl. Lahir</th>
				                    <th>Provinsi</th>
				                    <th>Kota/Kabupaten</th>
				                    <th>Kelas Perawatan</th>
				                    <th>Status Karyawan</th>
				                    <th>Status Kartu</th>
				                    <th>Dental Limit</th>
				                </tr>
							</thead>
							<tbody>
			                <?php if( count($data) > 0 ){
		                	$no = 0;
		                    foreach($data as $r){?>
		                        <tr>
		                            <td><?php echo ++$no;?></td>
		                            <td><?php echo $r->client_name;?></td>
		                            <td><?php echo $r->member_cardno;?></td>
		                            <td><?php echo $r->member_name;?></td>
		                            <td><?php echo $r->member_relationshiptype;?></td>
		                            <td><?php echo jenkel($r->member_sex);?></td>
		                            <td><?php echo myDate($r->member_birthdate,"d M Y",false);?></td>
		                            <td><?php echo $r->member_region;?></td>
		                            <td><?php echo $r->member_location;?></td>
		                            <td><?php echo $r->plan_description;?></td>
		                            <td><?php echo $r->member_employmentstatus;?></td>
		                            <td><?php echo $r->member_statuscard;?></td>
		                            <td><?php echo $r->member_dentallimit;?></td>
		                        </tr>
		                	<?php } 
			                } ?>
							</tbody>
		            	</table>
		            </div>
	            </div>
		    </div>
        </div>
<?php }?>
</div>
<script type="text/javascript">
var AJAX_URL = '<?php echo site_url("ajax/data");?>';
$(document).ready(function(){
    $('#panel-content-wrap').removeClass('panel');
	$('#panel-content-wrap').css('padding','0');
    $('#border-header').css('border','none');
	$('.heading-form').css('fontSize','16px');
	$('#activation_cardno').keyup(function(){
		$('#div-error, #status, #member_detail, #benefit, #member_list').hide();
	});
	$('#btn_show').click(function(){
		$.post(AJAX_URL+'/check_number',{clientid:$('#activation_clientid').val(),number:$('#activation_cardno').val(),field:'ai'},function(o){
		    obj = eval('('+o+')');
			if(obj.status){
				$('#form-show').submit();
			} else {
				$('#div-error').show();
				$('#display-error').html(obj.error);
			}	
	    });
	});
}); 
</script>