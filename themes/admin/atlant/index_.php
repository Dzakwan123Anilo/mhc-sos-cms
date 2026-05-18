<?php js_validate();?>
<div class="panel panel-default tabs">                            
	<ul class="nav nav-tabs" role="tablist">
	    <li class="active"><a href="javascript:void(0)" role="tab">Verifikasi Data Member</a></li>
	    <?php if( $this->jCfg['user']['is_all']==1 ){ ?>
	    <li><a href="<?php echo site_url('meme/me/dashboard')?>" role="tab">Dashboard Reporting</a></li>
	    <?php } ?>
	    <li><a href="<?php echo site_url('meme/me/guide')?>" role="tab">Panduan</a></li>
	    <li><a href="<?php echo site_url('meme/me/news')?>" role="tab">Berita</a></li>
	</ul>                            
	<div class="panel-body tab-content">
	    <div class="tab-pane active">
	  		<div class="well">
				<form id="form-show" action="<?php echo $own_links;?>" class="form-horizontal" method="post" autocomplete="off"> 
					<div class="row">         
	          			<div class="col-md-12">   
	          				
	          				<?php 
	          				if($this->jCfg['user']['is_all']==1){?>
				            <div class="row form-group">  
								<div class="col-md-4 control-label">Nama Client</div>
								<div class="col-md-4">
								    <select class="form-control select" name="verify_clientid" id="verify_clientid">
								        <?php 
	                        				$cli = isset($val)?$val->member_clientid:'';
									        echo option_client($cli);
								        ?>
								    </select>  
								</div>
				            </div> 
				            <?php }else{ ?>
				            
				            <div class="row form-group">  
								<div class="col-md-4 control-label">Nama Client</div>
								<div class="col-md-4 control-label" style="text-align:left;">
									<input type="hidden" name="verify_clientid" id="verify_clientid" value="<?php echo $this->jCfg['user']['client'];?>" />
								     <b><?php echo get_client_name($this->jCfg['user']['client']);?></b>
								</div>
				            </div> 
				            
				            
				            <?php } ?>
				            
				            <div class="row form-group">  
								<div class="col-md-4 control-label">Nomor Kartu</div>
								<div class="col-md-4">        
									<div class="input-group">
										<!-- ,custom[ajaxVerification] -->
		                				<input type="text" id="verify_cardno" name="verify_cardno" class="validate[required] form-control" value="<?php echo isset($val)?$val->member_cardno:$card_no;?>" />
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
	              <div class="col-md-9 control-label">: <b><?php echo myDate($val->member_birthdate,"d M Y",false);?></b></div>
	            </div>
	
	            <div class="row form-group">  
	              <div class="col-md-3 control-label">Perusahaan</div>
	              <div class="col-md-9 control-label">: <b><?php echo $val->client_name;?></b></div>
	            </div>
	            
	          </div> 
	
	          <div class="col-md-6">
	
	            <div class="row form-group">  
	              <div class="col-md-3 control-label">Tipe Relasi</div>
	              <div class="col-md-9 control-label">: <b><?php echo $val->member_relationshiptype;?></b></div>
	            </div>
	
	            <div class="row form-group">  
	              <div class="col-md-3 control-label">Status Karyawan</div>
	              <div class="col-md-9 control-label">: <b><?php echo $val->member_employmentstatus;?></b></div>
	            </div>
	            
	            <div class="row form-group">  
	              <div class="col-md-3 control-label">Status Kartu</div>
	              <div class="col-md-9 control-label">: <b><?php echo $val->member_statuscard;?></b></div>
	            </div>
	            
	            <!-- <div class="row form-group">  
	              <div class="col-md-3 control-label">Tanggal Aktifasi</div>
	              <div class="col-md-9 control-label">: <b></?php echo !empty($val->member_activationdate)&&$val->member_activationdate!='0000-00-00'?date('d-m-Y', strtotime($val->member_activationdate)):'';?></b></div>
	            </div> -->
	            
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
			                            <td><?php echo $r->member_plantype=='PLAN_FE'?'':$r->dental_limit;?></td>
			                        </tr>
			                	<?php } 
				                } ?>
								</tbody>
			            	</table>
			            </div>
		            </div>
			    </div>
	        </div>
	        <br /><br />
	        <div class="row" id="form_verify">
	        	<div class="well">
					<form id="form-validated" action="<?php echo $own_links;?>/save_claim" class="form-horizontal" method="post">
						<input type="hidden" id="claim_planid" name="claim_planid" value="<?php echo _encrypt($val->plan_id);?>">
						<input type="hidden" id="claim_memberid" name="claim_memberid" value="<?php echo _encrypt($val->member_id);?>">
						<input type="hidden" id="claim_clientid" name="claim_clientid" value="<?php echo _encrypt($val->client_id);?>">
						<input type="hidden" id="claim_relationshiptype" name="claim_relationshiptype" value="<?php echo _encrypt($val->member_relationshiptype);?>">
						<input type="hidden" id="claim_roomclass" name="claim_roomclass" value="<?php echo _encrypt($val->plan_description);?>"> 
						<div class="row form-group">       
							<div class="col-md-2 control-label">Jenis Layanan Klaim</div>
							<div class="col-md-4">
								<select class="form-control select" name="claim_servicetype" id="claim_servicetype">
									<option value="">- pilih jenis layanan -</option>
								<?php 
									echo option_servicetype('');
								?>
								</select>
							</div>
							<div class="col-md-2">
								<!-- <input type="submit" value="Kirim Klaim" style="margin-right:5px;" name="btn_claim" id="btn_claim" class="btn btn-primary" /> -->
								<input type="button" value="Proses" style="margin-right:5px;" name="btn_claim" id="btn_claim" class="btn btn-primary" disabled="disabled" />
							</div>
						</div>
						<div class="row" id="form_claim" style="display:none;">
							<div class="form-group">
								<div class="col-md-2 control-label">Tanggal Klaim</div>
								<div class="col-md-2">
									
									<div class="input-group">
					                      <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
					                      <input type="text" id="tgl_claim_cetak" name="tgl_claim_cetak" class="datepicker form-control" value="<?php echo date("Y-m-d");?>" />
					                </div>
                
								</div>
								<div class="col-md-2">
									<div class="input-group bootstrap-timepicker">
                                        <input type="text" id="jam_claim_cetak" name="jam_claim_cetak" class="form-control timepicker24"/>
                                        <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                                    </div>
                                                
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-2 control-label">Description</div>
								<div class="col-md-4">
									<textarea cols="40" rows="3" name="desc_claim_cetak" id="desc_claim_cetak" class="form-control"></textarea>
								</div>
							</div>
						</div>
					</form>
				</div>
		        <div class="row"><br />
					<div class="panel panel-default" style="margin-top:-10px;">
						<div class="panel-heading">
							<div class="panel-title-box">
					            <h3>Claim Histories</h3>
					            <span>Data Histori Klaim</span>
							</div>
						</div>
						<div class="panel-body panel-body-table">					
							<div class="table-responsive">
								<table class="table table-hover table-bordered table-striped" id="thistable">
									<thead>
										<tr>
											<th width="30px">No</th>
											<th>Nama Member</th>
											<th>Tipe Layanan</th>
											<th>Tgl. Claim</th>
											<th>Deskipsi</th>
											<th>Status Claim</th>
											<th width="30px">#</th>
										</tr>
									</thead>
									<tbody>
									<?php 
									if( count($history) > 0 ){
										$no=0;
										foreach($history as $r){
									?>
										<tr>
											<td><?php echo ++$no;?>
												<input type="hidden" id="date_<?php echo $r->claimId;?>" value="<?php echo _encrypt($r->date_claim);?>">
												<input type="hidden" id="planid_<?php echo $r->claimId;?>" value="<?php echo _encrypt($r->plan_id);?>">
												<input type="hidden" id="memberid_<?php echo $r->claimId;?>" value="<?php echo _encrypt($r->member_id);?>">
												<input type="hidden" id="clientid_<?php echo $r->claimId;?>" value="<?php echo _encrypt($r->client_id);?>">
												<input type="hidden" id="relationshiptype_<?php echo $r->claimId;?>" value="<?php echo _encrypt($r->member_relationshiptype);?>">
												<input type="hidden" id="roomclass_<?php echo $r->claimId;?>" value="<?php echo _encrypt($r->plan_description);?>">
												<input type="hidden" id="servicetype_<?php echo $r->claimId;?>" value="<?php echo $r->servicetypeId;?>">
											</td>
											<td nowrap="nowrap"><?php echo $r->member_name;?></td>
											<td nowrap="nowrap"><?php echo $r->service_type;?></td>
											<td nowrap="nowrap"><?php echo myDate($r->date_claim,"d M Y H:i:s",false)?></td>
											<td nowrap="nowrap"><?php echo $r->description;?></td>
											<td><?php echo $r->claim_status;?></td>
											<td><button class="btn btn-condensed btn-primary" data-toggle="dropdown" onclick="re_print('<?php echo $r->claimId;?>')"<?php echo $r->claim_status=='fail'?' disabled="disabled"':'';?>><i class="glyphicon glyphicon-print"></i></button></td>
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
	</div>
</div>   
<script type="text/javascript">
var AJAX_URL = '<?php echo site_url("ajax/data");?>';
$(document).ready(function(){
	$('#claim_servicetype').change(function(){
		showFormClaim();
	});
    $('#panel-content-wrap').removeClass('panel');
	$('#panel-content-wrap').css('padding','0');
    $('#border-header').css('border','none');
	$('.heading-form').css('fontSize','16px');
	$('#verify_cardno').keyup(function(){
		$('#div-error, #member_detail, #benefit, #member_list, #form_verify').hide();
	});
	$('#btn_show').click(function(){
		$.post(AJAX_URL+'/check_number',{clientid:$('#verify_clientid').val(),number:$('#verify_cardno').val()},function(o){
		    obj = eval('('+o+')');
			if(obj.status){
				$('#form-show').submit();
			} else {
				$('#div-error').show();
				$('#display-error').html(obj.error);
			}	
	    });
	});
	$('#claim_servicetype').change(function(){
		if($(this).val()=="") $('#btn_claim').attr('disabled','disabled');
		else $('#btn_claim').removeAttr('disabled');
	});
	$('#btn_claim').click(function(){
		var planid = $('#claim_planid').val(),
			memberid = $('#claim_memberid').val(),
			tgl_klaim = $('#tgl_claim_cetak').val()+" "+$('#jam_claim_cetak').val(),
			desc_klaim = $('#desc_claim_cetak').val(),
			clientid = $('#claim_clientid').val(),
			relationtype = $('#claim_relationshiptype').val(),
			roomclass = $('#claim_roomclass').val(),
			servicetype = $('#claim_servicetype').val(),
			h = screen.height,
			w = screen.width;

		window.open("<?php echo $this->own_link."/print_preview3/";?>?save=1&pid="+planid+"&mid="+memberid+"&cid="+clientid+"&rt="+relationtype+"&dk="+desc_klaim+"&dt="+tgl_klaim+"&rc="+roomclass+"&st="+servicetype, "Print Preview", "width=400,height=600,left="+((w-400)/2)+",top="+((h-650)/2));
	});
}); 

function pdf_preview(filename) {
	var h = screen.height,
		w = screen.width;
	
	window.open("<?php echo $this->own_link."/pdf_preview/";?>?fl="+filename, "PDF Preview", "width=600,height=650,left="+((w-600)/2)+",top="+((h-700)/2));
}
function re_print(idx){
	if (typeof win != "undefined") win.close();
	var date = $('#date_'+idx).val(),
    	planid = $('#planid_'+idx).val(),
		memberid = $('#memberid_'+idx).val(),
		clientid = $('#clientid_'+idx).val(),
		relationtype = $('#relationshiptype_'+idx).val(),
		roomclass = $('#roomclass_'+idx).val(),
		servicetype = $('#servicetype_'+idx).val(),
		h = screen.height,
		w = screen.width;

	win = window.open("<?php echo $this->own_link."/print_preview/";?>?save=0&pid="+planid+"&dt="+date+"&mid="+memberid+"&cid="+clientid+"&rt="+relationtype+"&rc="+roomclass+"&st="+servicetype, "Print Preview", "width=400,height=600,left="+((w-400)/2)+",top="+((h-650)/2));
}

function showFormClaim(){
	v = $('#claim_servicetype').val();
	if( $.trim(v) != '' ){
		$('#form_claim').fadeIn();
	}else{
		$('#form_claim').hide();
	}
}
</script>