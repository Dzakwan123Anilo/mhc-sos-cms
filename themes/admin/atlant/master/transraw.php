<form id="form-validated" class="form-horizontal" action="" method="post" class="input">
	<input type="hidden" name="page" id="page" value="">
	<?php getFormSearch();?>
	<div class="alert alert-warning" id="div_info_msg" style="padding:10px;margin:-10px 0px 15px 0px;display: none; ">
	        <p id="info_export">Silahkan tunggu sedang memproses <b id="jumlah_progress">0</b> dari <b id="total_progress">0</b> data</p>
	</div>


<div class="panel panel-default tabs">                            
	<ul class="nav nav-tabs" role="tablist">
	    <li class="active" id="first"><a href="#tab-first" role="tab" data-toggle="tab">Tabel Data <?php echo isset($title)?$title:'';?></a></li>
	    <li id="second"><a href="#tab-second" role="tab" data-toggle="tab">Detail Information</a></li>
		<li class="pull-right" id="loading-item" style="display:none;"><img src="<?php echo themeUrl();?>img/ajax-loader-bar.gif" /></li>                                    
	</ul>                            
	<div class="panel-body tab-content">	
	    <div class="tab-pane active" id="tab-first">	    
			<div class="panel panel-default" style="margin-top:-10px;">
			    <div class="panel-heading">
			        <div class="panel-title-box">
			            <h3>Tabel Data <?php echo isset($title)?$title:'';?></h3>
			            <span>Data <?php echo isset($title)?$title:'';?> dari <?php echo cfg('app_name');?></span>
			        </div>                    
			        <ul class="panel-controls" style="margin-top: 2px;">
			            <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li> 
			            <li><a href="#" id="export_trans_xls"><span class="fa fa-download"></span></a></li>                                        
			        </ul>
			    </div>
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
			                    <th>Provider</th>     
			                    <th>Layanan</th>     
			                    <th>Kelas Perawatan</th> 
			                    <th>Tgl. Claim</th>                       
			                    <th>Deskripsi</th>                         
			                    <th>Status Claim</th>                         
			                    <!-- th>Terminal</th>                       
			                    <th>Mercan</th>                       
			                    <th>MSISDN</th -->                       
			                    <th>Fraud</th>
			                </tr>
			                </thead>
			               <tbody>
			                <?php $no=0;
			                	if( count($data) > 0 ){            
									$no=$start;                	
			                    	foreach($data as $r){?>
			                        <tr id="<?php echo _encrypt($r->claimId);?>" style="cursor: pointer;" onclick="TRAW._showData(<?php echo $r->claimId;?>);">
			                            <td><?php echo ++$no;?></td>
			                            <td><?php echo $r->client_name?></td>
			                            <td><?php echo $r->member_cardno?></td>
			                            <td><?php echo $r->member_employeeid?></td>
			                            <td><?php echo $r->member_name?></td>
			                            <td><?php echo $r->relationship_type?></td>
			                            <td><?php echo $r->provider_name?></td>
			                            <td><?php echo $r->service_type?></td>
			                            <td><?php echo $r->room_class?></td>
			                            <td><?php echo !empty($r->date_claim)?myDate($r->date_claim,"d M Y",false):''?></td>
			                            <td><?php echo $r->description?></td>
			                            <td><?php echo $r->claim_status?></td>
			                            <!-- td></?php echo $r->terminalId?></td>
			                            <td></?php echo $r->merchantId?></td>
			                            <td></?php echo $r->msisdn?></td -->
			                            <td><?php echo $r->flag_fraud?></td>
			                            <!-- td></?php echo $r->provider_status==1?'<span class="label label-info">Tampil</span>':'<span class="label label-warning">Tidak Tampil</span>';?></td -->
			                        </tr>
			                <?php } 
			                	}?>
			                </tbody>
			            </table>
			            </div>
			    </div>
			</div>
			<div class="pull-right" style="text-align: right;">
			    <?php echo $no>0?(isset($start)?("Showing ".($start+1)." to ".$no." of ".$cRec." entries"):""):"";?>
				<?php echo isset($paging)?$paging:'';?>
			</div>
		</div>
	    <div class="tab-pane panel-detail" id="tab-second" style="opacity:0.4;">
			<div class="row"> 
				<h5 class="heading-form">Member Detail</h5>
				<div class="col-md-6">
					<div class="row form-group"> 
						<div class="col-md-3 control-label">No. Kartu</div>
						<div class="col-md-9 control-label">: <b><span id="cardno"></span></b></div>
					</div>
					
					<div class="row form-group">
						<div class="col-md-3 control-label">Nama Member</div>
						<div class="col-md-9 control-label">: <b><span id="name"></span></b></div>
					</div>
					
					<div class="row form-group">
						<div class="col-md-3 control-label">Jenis Kelamin</div>
						<div class="col-md-9 control-label">: <b><span id="sex"></span></b></div>
					</div>
					
					<div class="row form-group">
						<div class="col-md-3 control-label">Tanggal Lahir</div>
						<div class="col-md-9 control-label">: <b><span id="birthdate"></span></b></div>
					</div>
					
					<div class="row form-group">
						<div class="col-md-3 control-label">Alamat</div>
						<div class="col-md-9 control-label">: <b><span id="address"></span></b></div>
					</div>
					
					<div class="row form-group">
						<div class="col-md-3 control-label">Telp</div>
						<div class="col-md-9 control-label">: <b><span id="phone"></span></b></div>
					</div>				
				</div>
				<div class="col-md-6">				
					<div class="row form-group">
						<div class="col-md-3 control-label">Perusahaan</div>
						<div class="col-md-9 control-label">: <b><span id="clientname"></span></b></div>
					</div>
					
					<div class="row form-group">
						<div class="col-md-3 control-label">Tipe Relasi</div>
						<div class="col-md-9 control-label">: <b><span id="relationship"></span></b></div>
					</div>
					
					<div class="row form-group">
						<div class="col-md-3 control-label">Status Karyawan</div>
						<div class="col-md-9 control-label">: <b><span id="status"></span></b></div>
					</div>
					
					<div class="row form-group">
						<div class="col-md-3 control-label">Status Kartu</div>
						<div class="col-md-9 control-label">: <b><span id="statuscard"></span></b></div>
					</div>
				</div>
			</div>
			<br />
			
			<div class="row" id="benefit">
				<h5 class="heading-form">Benefit</h5>
				<div class="col-md-6">
					<div class="row form-group">
						<div class="col-md-3 control-label">Kelas Perawatan</div>
						<div class="col-md-9 control-label">: <b><span id="plandesc"></span></b></div>
					</div>
					
					<div class="row form-group">
						<div class="col-md-3 control-label">Dental Limit</div>
						<div class="col-md-9 control-label">: <b><span id="dental"></span></b></div>
					</div>
				</div>			
			</div>
			<br />
			
			<div class="row"><br />
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
								<tbody id="table-family">
								
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</form>
<script type="text/javascript">
var URL_EXPORT_TRANS = '<?php echo $own_links;?>/export_data';
var URL_GET_DATA_TRAW = '<?php echo $own_links;?>/get_info_raw';
function page(pg) {
	$('#page').val(pg);
	$('#form-validated').submit();
}
</script>
