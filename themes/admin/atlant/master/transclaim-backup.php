<form id="form-validated" class="form-horizontal" action="" method="post" class="input">
	<input type="hidden" name="page" id="page" value="">
	<?php getFormSearch();?>

<div class="alert alert-warning" id="div_info_msg" style="padding:10px;margin:-10px 0px 15px 0px;display: none; ">
        <p id="info_export">Silahkan tunggu sedang memproses <b id="jumlah_progress">0</b> dari <b id="total_progress">0</b> data</p>
</div>

<div class="panel panel-default tabs">                            
	<ul class="nav nav-tabs" role="tablist">
	    <li class="active"><a href="#tab-first" role="tab" data-toggle="tab">Tabel Data <?php echo isset($title)?$title:'';?></a></li>
	    <li><a href="#tab-second" role="tab" data-toggle="tab">Detail Information</a></li>
	</ul>                            
	<div class="panel-body tab-content">
	
	    <div class="tab-pane active" id="tab-first">	    
			<div class="panel panel-default" style="margin-top:-10px;">
			    <div class="panel-heading">
			        <div class="panel-title-box">
			            <h3>Tabel Data <?php echo isset($title)?$title:'';?></h3>
			            <span>Data <?php echo isset($title)?$title:'';?> dari <?php echo cfg('app_name');?></span>
			        </div>                                    
					<div class="btn-group pull-right" style="margin: 2px 0 0 5px;">
					    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
					    <ul class="dropdown-menu">
								<li><a href="#" id="export_trans_claim_xls"><img src='<?php echo themeUrl();?>img/icons/xls.png' width="24" id="excel_export" /> XLS</a></li>
					    </ul>
					</div>     
			        <ul class="panel-controls" style="margin-top: 2px;">
			            <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>                                       
			        </ul>
			    </div>
				<div class="row">
					<div class="col-md-6">
					    <div class="panel-body panel-body-table">
					        <div class="table-responsive">
					            <table class="table table-hover table-bordered table-striped" id="thistable">
					               <thead>
					                <tr>
					                    <th width="30px">No</th>
					                    <th>Client</th>
					                    <th>No. Kartu</th>
					                    <th>ID Karyawan</th>
					                    <th>Nama Member</th>
					                    <th>Tipe Relasi</th> 
					                    <th>Provinsi</th> 
					                    <th>Kota/Kabupaten</th> 
					                </tr>
					                </thead>
					               <tbody>
					                <?php $no=0;
					                	if( count($data) > 0 ){       
          									$no=$start;                     	
					                    	foreach($data as $r){?>
					                        <tr id="<?php echo _encrypt($r->memberId);?>" style="cursor: pointer;">
					                            <td><?php echo ++$no;?></td>
					                            <td nowrap="nowrap"><?php echo $r->client_name?></td>
					                            <td><?php echo $r->member_cardno?></td>
					                            <td><?php echo $r->member_employeeid?></td>
					                            <td nowrap="nowrap"><?php echo $r->member_name?></td>
					                            <td><?php echo $r->relationship_type?></td>
					                            <td><?php echo $r->member_region?></td>
					                            <td><?php echo $r->member_location?></td>
					                        </tr>
					                <?php } 
					                	}?>
					                </tbody>
					            </table>
							</div>
					    </div>
						<div class="pull-right" style="text-align: right;">
						    <?php echo $no>0?(isset($start)?("Showing ".($start+1)." to ".$no." of ".$cRec." entries"):""):"";?>
							<?php echo isset($paging)?$paging:'';?>
						</div>
					</form>
					</div>
					<div class="col-md-6">
						<div class="row">
							<h5 class="heading-form">Claim History List</h5>
							<div class="panel-body panel-body-table">		
								<div class="table-responsive">
									<table class="table table-hover table-bordered table-striped" id="histtable">
									<thead>
										<tr>
											<th width="30px">No</th>
											<th>Tgl. Claim</th>
											<th>Provider</th>
											<th>Tipe Layanan</th>
											<th>Nama Member</th>
											<th>No. Kartu</th>
											<th>ID Karyawan</th>
											<th>Tipe Relasi</th>
											<th>Kelas Kamar</th>
											<th>Status Claim</th>
										</tr>
									</thead>
									<tbody>
									<?php if( count($hist) > 0 ){
										$no=0;
									foreach($hist as $r){
									?>
										<tr style="cursor: pointer;">
											<input type="hidden" id="claim_id" name="claim_id" value="<?php echo _encrypt($r->claimId);?>">
											<td><?php echo ++$no;?>
												<input type="hidden" id="date" name="date" value="<?php echo _encrypt($r->date_claim);?>">
												<input type="hidden" id="planid" name="planid" value="<?php echo _encrypt($r->plan_id);?>">
												<input type="hidden" id="memberid" name="memberid" value="<?php echo _encrypt($r->member_id);?>">
												<input type="hidden" id="clientid" name="clientid" value="<?php echo _encrypt($r->client_id);?>">
												<input type="hidden" id="relationshiptype" name="relationshiptype" value="<?php echo _encrypt($r->member_relationshiptype);?>">
												<input type="hidden" id="roomclass" name="roomclass" value="<?php echo _encrypt($r->plan_description);?>">
												<input type="hidden" id="servicetype" name="servicetype" value="<?php echo $r->servicetypeId;?>">
											</td>
											<td nowrap="nowrap"><?php echo !empty($r->date_claim)?myDate($r->date_claim,"d M Y H:i:s",false):''?></td>
											<td nowrap="nowrap"><?php echo $r->provider_name;?></td>
											<td nowrap="nowrap"><?php echo $r->service_type;?></td>
											<td nowrap="nowrap"><?php echo $r->member_name;?></td>
											<td><?php echo $r->member_cardno;?></td>
											<td><?php echo $r->member_employeeid;?></td>
											<td><?php echo $r->relationship_type;?></td>
											<td nowrap="nowrap"><?php echo $r->room_class;?></td>
											<td><?php echo $r->claim_status;?></td>
										</tr>
									<?php } } ?>
									</tbody>
									</table>
								</div>
								<!--<div class="pull-right">
									</?php echo isset($paging)?$paging:'';?>
								</div>-->
							</div>
						</div><br /><br />
						<div class="row">
							<h5 class="heading-form">Informasi Claim</h5>
							<div class="row">	                  
								<input type="hidden" id="claim_date" name="claim_date" value="">    
								<input type="hidden" id="claim_planid" name="claim_planid" value="">
								<input type="hidden" id="claim_memberid" name="claim_memberid" value="">
								<input type="hidden" id="claim_clientid" name="claim_clientid" value="">
								<input type="hidden" id="claim_relationshiptype" name="claim_relationshiptype" value="">
								<input type="hidden" id="claim_roomclass" name="claim_roomclass" value="">
								<input type="hidden" id="claim_servicetype" name="claim_servicetype" value="">
								               
								<div class="btn-group pull-right">
								    <button class="btn btn-primary" data-toggle="dropdown" id="print-preview" disabled="disabled"><i class="fa fa-bars"></i> Print Struk</button>
								</div>   
							</div><br />
							<div class="row">
								<div class="col-md-6">				
									<div class="row form-group">
										<div class="col-md-4 control-label">Provider</div>
										<div class="col-md-8 control-label">: <b id="provider"></b></div>
									</div>
							
									<div class="row form-group">
										<div class="col-md-4 control-label">Tgl. Claim</div>
										<div class="col-md-8 control-label">: <b id="dclaim"></b></div>
									</div>
													
									<div class="row form-group">
										<div class="col-md-4 control-label">Tipe Layanan</div>
										<div class="col-md-8 control-label">: <b id="stype"></b></div>
									</div>
									
									<div class="row form-group">
										<div class="col-md-4 control-label">Nama Client</div>
										<div class="col-md-8 control-label">: <b id="client"></b></div>
									</div>
									
									<div class="row form-group">
										<div class="col-md-4 control-label">Nama</div>
										<div class="col-md-8 control-label">: <b id="name"></b></div>
									</div>
											
									<div class="row form-group">
										<div class="col-md-4 control-label">No. Kartu</div>
										<div class="col-md-8 control-label">: <b id="cardno"></b></div>
									</div>
								</div>
								<div class="col-md-6">				
									<div class="row form-group">
										<div class="col-md-4 control-label">Tgl. Lahir</div>
										<div class="col-md-8 control-label">: <b id="dbirth"></b></div>
									</div>
									
									<div class="row form-group">
										<div class="col-md-4 control-label">Jenis Kelamin</div>
										<div class="col-md-8 control-label">: <b id="sex"></b></div>
									</div>
									
									<div class="row form-group">
										<div class="col-md-4 control-label">Hak Akses Pelayanan</div>
										<div class="col-md-8 control-label">: <b id="plan"></b></div>
									</div>
									
									<div class="row form-group">
										<div class="col-md-4 control-label">Dental Limit</div>
										<div class="col-md-8 control-label">: <b id="dental"></b></div>
									</div>
									
									<div class="row form-group">
										<div class="col-md-4 control-label">Deskripsi</div>
										<div class="col-md-8 control-label">: <b id="desc"></b></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
	    </div>
	    
	    
	    <div class="tab-pane" id="tab-second">
			<div class="row">
				<h5 class="heading-form">Member Detail</h5>
				<div class="col-md-6">
					<div class="row form-group">
						<div class="col-md-4 control-label">No. Kartu</div>
						<div class="col-md-8 control-label">: <b><?php echo isset($val->member_cardno)?$val->member_cardno:'';?></b></div>
					</div>
			
					<div class="row form-group">
						<div class="col-md-4 control-label">ID Karyawan</div>
						<div class="col-md-8 control-label">: <b><?php echo isset($val->member_employeeid)?$val->member_employeeid:'';?></b></div>
					</div>
					
					<div class="row form-group">
						<div class="col-md-4 control-label">Tipe Relasi</div>
						<div class="col-md-8 control-label">: <b><?php echo isset($val->member_relationshiptype)?$val->member_relationshiptype:'';?></b></div>
					</div>
					
					<div class="row form-group">
						<div class="col-md-4 control-label">Nama Member</div>
						<div class="col-md-8 control-label">: <b><?php echo isset($val->member_name)?$val->member_name:'';?></b></div>
					</div>
					
					<div class="row form-group">
						<div class="col-md-4 control-label">Jenis Kelamin</div>
						<div class="col-md-8 control-label">: <b><?php echo isset($val->member_sex)?($val->member_sex=='M'?'Laki-laki':'Perempuan'):'';?></b></div>
					</div>
					
					<div class="row form-group">
						<div class="col-md-4 control-label">Tanggal Lahir</div>
						<div class="col-md-8 control-label">: <b><?php echo isset($val->member_birthdate)?myDate($val->member_birthdate, 'd M Y', false):'';?></b></div>
					</div>
				</div>
				<div class="col-md-6">		
					<div class="row form-group">
						<div class="col-md-4 control-label">Propinsi</div>
						<div class="col-md-8 control-label">: <b><?php echo isset($val->member_region)?$val->member_region:'';?></b></div>
					</div>
					
					<div class="row form-group">
						<div class="col-md-4 control-label">Kota/Kabupaten</div>
						<div class="col-md-8 control-label">: <b><?php echo isset($val->member_location)?$val->member_location:'';?></b></div>
					</div>
					
					<div class="row form-group">
						<div class="col-md-4 control-label">Perusahaan</div>
						<div class="col-md-8 control-label">: <b><?php echo isset($val->client_name)?$val->client_name:'';?></b></div>
					</div>
					
					<div class="row form-group">
						<div class="col-md-4 control-label">Hak Akses Pelayanan</div>
						<div class="col-md-8 control-label">: <b><?php echo isset($val->plan_description)?$val->plan_description:'';?></b></div>
					</div>
					
					<div class="row form-group">
						<div class="col-md-4 control-label">Deskripsi</div>
						<div class="col-md-8 control-label">: <b><?php echo isset($val->member_description)?$val->member_description:'';?></b></div>
					</div>
				</div>
			</div>
			<br /><br />
			<div class="row">
				<h5 class="heading-form">Family Member List</h5>
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
									<th>Propinsi</th>
									<th>Kota/Kabupaten</th>
									<th>Hak Akses Pelayanan</th>
									<th>Status Karyawan</th>
									<th>Status Kartu</th>
									<th>Dental Limit</th>
								</tr>
							</thead>
							<tbody>
							<?php if( count($family) > 0 ){
								$no = 0;
								foreach($family as $r){
							?>
								<tr>
									<td><?php echo ++$no;?></td>
									<td><?php echo $r->client_name;?></td>
									<td><?php echo $r->member_cardno;?></td>
									<td><?php echo $r->member_name;?></td>
									<td><?php echo $r->member_relationshiptype;?></td>
									<td><?php echo $r->member_sex=='M'?'Laki-laki':'Perempuan';?></td>
									<td><?php echo myDate($r->member_birthdate,"d M Y",false);?></td>
									<td><?php echo $r->member_region;?></td>
									<td><?php echo $r->member_location;?></td>
									<td><?php echo $r->plan_description;?></td>
									<td><?php echo $r->member_employmentstatus;?></td>
									<td><?php echo $r->member_statuscard;?></td>
									<td><?php echo $r->member_dentallimit;?></td>
								</tr>
							<?php } } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>	    
	    </div>
	</div>
</div>

<script type="text/javascript">
var URL_EXPORT_TCLAIM = '<?php echo $own_links;?>/export_data';
$(document).ready(function(){
    $("#thistable tr").click(function(){
		window.location.href = '<?php echo $this->own_link."/?_id="?>'+$(this).attr('id');
    });
    $("#histtable tr").click(function(){
        var id = ['provider','dclaim','stype','client','name','cardno','dbirth','sex','plan','dental','desc'], 
	        idabj = ['a','b','c','d','e','f','g','h','i','j','k'], 
	        inputid = ['claim_date','claim_planid','claim_memberid','claim_clientid','claim_relationshiptype','claim_roomclass','claim_servicetype'], 
	    	ci = $.ajax({type:'GET', url:'<?php echo $this->own_link."/fill_information/?cid="?>'+$(this).find("input").val(),async:false}).responseText,
			claiminf = JSON.parse(ci);

        if(claiminf.status == 1) {
	        for(var i=0; i<id.length; i++) {
	        	$('#'+id[i]).html(claiminf.data[idabj[i]]);
	        }
	    	
	        $(this).find("td").each(function(){
	            var j = 0;
	            $(this).find("input").each(function(){
	                $('#'+inputid[j]).val($(this).val());
	                j++;
	            });
	        });
	        $('#print-preview').removeAttr('disabled');
        }
    });
    $('#print-preview').click(function(){
    	var date = $('#claim_date').val(),
	    	planid = $('#claim_planid').val(),
			memberid = $('#claim_memberid').val(),
    		clientid = $('#claim_clientid').val(),
    		relationtype = $('#claim_relationshiptype').val(),
    		roomclass = $('#claim_roomclass').val(),
    		servicetype = $('#claim_servicetype').val(),
    		h = screen.height,
    		w = screen.width;

    	window.open("<?php echo $this->own_link."/print_preview/";?>?save=0&pid="+planid+"&dt="+date+"&mid="+memberid+"&cid="+clientid+"&rt="+relationtype+"&rc="+roomclass+"&st="+servicetype, "Print Preview", "width=400,height=600,left="+((w-400)/2)+",top="+((h-650)/2));
    });
});

function page(pg) {
	$('#page').val(pg);
	$('#form-validated').submit();
}
</script>