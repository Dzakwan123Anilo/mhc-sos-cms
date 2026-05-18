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
		<li class="pull-right" id="loading-item" style="display:none;"><img src="<?php echo themeUrl();?>img/ajax-loader-bar.gif" /></li>                                    
	</ul>                            
	<div class="panel-body tab-content">
	
	    <div class="tab-pane active" id="tab-first">	    
			<div class="col-md-6">
			<div class="panel panel-default" style="margin-top:-10px;">
			    <div class="panel-heading">
			        <div class="panel-title-box">
			            <h3>Tabel Data <?php echo isset($title)?$title:'';?></h3>
			            <span>Data <?php echo isset($title)?$title:'';?> dari <?php echo cfg('app_name');?></span>
			        </div>                                         
			        <ul class="panel-controls" style="margin-top: 2px;">
			            <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
			            <li><a href="#" id="export_trans_claim_xls"><span class="fa fa-download"></span></a></li>   
			        </ul>
			    </div>
				<div class="row">
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
					                        <tr id="<?php echo _encrypt($r->memberId);?>" style="cursor: pointer;" onclick="TCLAIM._showData(<?php echo $r->memberId;?>);">
					                            <td><?php echo ++$no;?></td>
					                            <td nowrap="nowrap"><?php echo $r->client_name?></td>
					                            <td><?php echo $r->member_cardno?></td>
					                            <td><?php echo $r->member_employeeid?></td>
					                            <td nowrap="nowrap"><?php echo $r->member_name?></td>
					                            <td><?php echo $r->relationship_type?></td>
					                            <td nowrap="nowrap"><?php echo $r->member_region?></td>
					                            <td nowrap="nowrap"><?php echo $r->member_location?></td>
					                        </tr>
					                <?php } 
					                	}?>
					                </tbody>
					            </table>
							</div>
					    </div>
						<div class="pull-right" style="text-align: right; padding:10px 20px 0 0;">
			    			<?php echo $no>0?(isset($start)?("Showing ".($start+1)." to ".$no." of ".$cRec." entries"):""):"";?>
							<?php echo isset($paging)?$paging:'';?>
						</div>
					</form>
				</div>
			</div>
			</div>
			<div class="col-md-6 panel-detail" style="opacity:0.4;">
				
				<!-- start table 1 -->
				
				<div class="panel panel-default" style="margin-top:-10px;">
				    <div class="panel-heading">
				        <div class="panel-title-box">
				            <h3>Claim History List</h3>
				            <span>Data history klaim member</span>
				        </div>             
				    </div>
	    
					<div class="panel-body panel-body-table">	
						<div class="table-responsive">
							<input type="hidden" id="claim_id" name="claim_id" value="">
							<input type="hidden" id="claim_date" name="claim_date" value="">    
							<input type="hidden" id="claim_planid" name="claim_planid" value="">
							<input type="hidden" id="claim_memberid" name="claim_memberid" value="">
							<input type="hidden" id="claim_clientid" name="claim_clientid" value="">
							<input type="hidden" id="claim_relationshiptype" name="claim_relationshiptype" value="">
							<input type="hidden" id="claim_roomclass" name="claim_roomclass" value="">
							<input type="hidden" id="claim_servicetype" name="claim_servicetype" value="">
							<table class="table table-hover table-bordered table-striped">
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
								<tbody id="table-history">
								
								</tbody>									
							</table>						
						</div>
						<div class="panel-footer"> 
	        				<input type="hidden" name="p-offset" id="p-offset" value="0" />
	        				<input type="hidden" name="n-offset" id="n-offset" value="5" />
							<button type="button" id="btn-previous" class="btn btn-primary pull-left" disabled="disabled" onclick="TCLAIM._showHistory($('#claim_id').val(),$('#p-offset').val());">Previous</button>
							<button type="button" id="btn-next" class="btn btn-primary pull-right" disabled="disabled" onclick="TCLAIM._showHistory($('#claim_id').val(),$('#n-offset').val());">Next</button>
						</div>
					</div>
				</div>
				
				<!-- end table 1 -->
				
				<!-- start table 2 -->
				<div style="clear:both;"></div>
				<h3 class="heading-form">Informasi Claim
					
					<a href="#" onclick="TCLAIM.printPreview();" style="font-size:15px;display:none;" class="pull-right" id="print-preview"><i class="fa fa-print"></i> Print Struk</a>

				</h3>
				<div class="panel panel-default panel-detail-2" style="opacity:0.5;">
				    
					<div class="panel-body panel-body-table">	
						<div class="table-responsive">
							
							<table class="table table-bordered">
								<tbody>
								
								<tr>
									<td width="40%">Provider</td>
									<td width="5">:</td>
									<td><span id="provider-hist"></span></td>
								</tr>
								
								<tr>
									<td>Tgl. Claim</td>
									<td>:</td>
									<td><span id="tgl-claim-hist"></span></td>
								</tr>
								<tr>
									<td>Tipe Layanan</td>
									<td>:</td>
									<td><span id="tipe-layanan-hist"></span></td>
								</tr>
								<tr>
									<td>Nama Client</td>
									<td>:</td>
									<td><span id="nama-client-hist"></span></td>
								</tr>
								<tr>
									<td>Nama</td>
									<td>:</td>
									<td><span id="nama-member-hist"></span></td>
								</tr>
								<tr>
									<td>No. Kartu</td>
									<td>:</td>
									<td><span id="card-no-hist"></span></td>
								</tr>
								<tr>
									<td>Tgl. Lahir</td>
									<td>:</td>
									<td><span id="tgl-lahir-hist"></span></td>
								</tr>
								<tr>
									<td>Jenis Kelamin</td>
									<td>:</td>
									<td><span id="jenkel-hist"></span></td>
								</tr>
								<tr>
									<td>Kelas Perawatan</td>
									<td>:</td>
									<td><span id="hak-layanan-hist"></span></td>
								</tr>
								<tr>
									<td>Dental Limit</td>
									<td>:</td>
									<td><span id="dental-limit-hist"></span></td>
								</tr>
								<tr>
									<td>Deskripsi</td>
									<td>:</td>
									<td><span id="desc-hist"></span></td>
								</tr>
								
								</tbody>
									
							</table>
						
						</div>
					</div>
				</div>
				
				<!-- end tabel 2 -->
				
			</div>
	    </div>
	    
	    
	    <div class="tab-pane panel-detail" id="tab-second" style="opacity:0.4;">
			<div class="row">
				<h5 class="heading-form">Member Detail</h5>
				<div class="col-md-6">
					
					<div class="table-responsive">
							
						<table class="table table-bordered">
							<tbody>
							
							<tr>
								<td width="30%">No Kartu</td>
								<td width="5">:</td>
								<td><span id="card-no-main"></span></td>
							</tr>
							
							<tr>
								<td>ID Karyawan</td>
								<td>:</td>
								<td><span id="id-karyawan-main"></span></td>
							</tr>
							<tr>
								<td>Tipe Relasi</td>
								<td>:</td>
								<td><span id="tipe-relasi-main"></span></td>
							</tr>
							<tr>
								<td>Nama Member</td>
								<td>:</td>
								<td><span id="member-name-main"></span></td>
							</tr>
							<tr>
								<td>Jenis Kelamin</td>
								<td>:</td>
								<td><span id="jenkel-main"></span></td>
							</tr>
							<tr>
								<td>Tgl.Lahir</td>
								<td>:</td>
								<td><span id="tgl-lahir-main"></span></td>
							</tr>
							
							</tbody>
								
						</table>
					
					</div>
						
				</div>
				<div class="col-md-6">		
					
					
					<div class="table-responsive">
							
						<table class="table table-bordered">
							<tbody>
							
							
							<tr>
								<td width="30%">Provinsi</td>
								<td width="5">:</td>
								<td><span id="provinsi-main"></span></td>
							</tr>
							<tr>
								<td>Kota/Kabupaten</td>
								<td>:</td>
								<td><span id="kab-main"></span></td>
							</tr>
							<tr>
								<td>Perusahaan</td>
								<td>:</td>
								<td><span id="perusahaan-main"></span></td>
							</tr>
							<tr>
								<td>Kelas Perawatan</td>
								<td>:</td>
								<td><span id="hak-akses-main"></span></td>
							</tr>
							<tr>
								<td>Deskripsi</td>
								<td>:</td>
								<td><span id="desc-main"></span></td>
							</tr>
							
							</tbody>
								
						</table>
					
					</div>
					
					
					
				</div>
			</div>
			<br /><br />
			<div class="row">
				
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

<script type="text/javascript">
var URL_EXPORT_TCLAIM = '<?php echo $own_links;?>/export_data';
var URL_GET_DATA_CLAIM = '<?php echo $own_links;?>/get_info_claim';
var URL_GET_DATA_HIST_CLAIM = '<?php echo $own_links;?>/fill_information';
var URL_CETAK_HIST = '<?php echo $own_links;?>/print_preview2';
function page(pg) {
	$('#page').val(pg);
	$('#form-validated').submit();
}
</script>