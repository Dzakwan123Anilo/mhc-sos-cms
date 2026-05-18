<form id="form-validated" class="form-horizontal" action="" method="post" class="input">
	<input type="hidden" name="page" id="page" value="">
	<?php getFormSearch();?>

	<div class="alert alert-warning" id="div_info_msg" style="padding:10px;margin:-10px 0px 15px 0px;display: none; ">
	        <p id="info_export">Silahkan tunggu sedang memproses <b id="jumlah_progress">0</b> dari <b id="total_progress">0</b> data</p>
	</div>

	<div class="panel panel-default" style="margin-top:-10px;">
	    <div class="panel-heading">
	        <div class="panel-title-box">
	            <h3>Tabel Data <?php echo isset($title)?$title:'';?></h3>
	            <span>Data <?php echo isset($title)?$title:'';?> dari <?php echo cfg('app_name');?></span>
	        </div>
	        <ul class="panel-controls" style="margin-top: 2px;">
	            <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>  
	            <li><a href="#" id="export_provider_xls"><span class="fa fa-download"></span></a></li>                                     
	        </ul>           
	    </div>
	    <div class="panel-body panel-body-table">

	        <div class="table-responsive">
	            <table class="table table-hover table-bordered table-striped">
	               <thead>
	                <tr>
	                    <th width="30px">No</th>
	                    <!--<th>Client</th>-->
	                    <th>Nama Provider</th>
	                    <th>Nama Provider EDC</th>
	                    <th>Kode Provider</th>
	                    <th>Tipe Provider</th>
	                    <th>Telepon</th>     
	                    <th>Email</th>     
	                    <th>Provinsi</th> 
	                    <th>Kota/Kabupaten</th>                       
	                    <th>Status</th>
	                    <th width="60">Action</th>
	                </tr>
	                </thead>
	               <tbody>
	                <?php if( count($data) > 0 ){                  
							$no=$start;           		
	                    	foreach($data as $r){?>
	                        <tr>
	                            <td><?php echo ++$no;?></td>
	                            <!--<td><?php echo $r->client_name?></td>-->
	                            <td><?php echo $r->provider_name?></td>
	                            <td><?php echo $r->provider_name_edc?></td>
	                            <td><?php echo $r->provider_code?></td>
	                            <td><?php echo $r->provider_type?></td>
	                            <td><?php echo $r->provider_phone?></td>
	                            <td><?php echo $r->provider_email?></td>
	                            <td><?php echo $r->provider_region?></td>
	                            <td><?php echo $r->provider_location?></td>
	                            <td><?php echo $r->provider_status==1?'<span class="label label-info">Tampil</span>':'<span class="label label-warning">Tidak Tampil</span>';?></td>
	                            <td><?php link_action($links_table_item,"?_id="._encrypt($r->provider_id));?></td>
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
</form>
<script type="text/javascript">
var URL_EXPORT_PROVIDER = '<?php echo $own_links;?>/export_data';

function page(pg) {
	$('#page').val(pg);
	$('#form-validated').submit();
}
</script>