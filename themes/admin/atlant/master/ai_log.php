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
	            <li><a href="#" id="export_log_xls"><span class="fa fa-download"></span></a></li>                                       
	        </ul>
	    </div>
	    <div class="panel-body panel-body-table">

	        <div class="table-responsive">
	            <table class="table table-hover table-bordered table-striped">
	               <thead>
	                <tr>
	                    <th width="30px">No</th>
	                    <th>No. Kartu</th>
	                    <th>Nama Member</th>
	                    <th>ID Karyawan</th>     
	                    <th>Aksi</th>     
	                    <th>Tanggal</th>     
	                    <th>Oleh</th> 
	                    <th>IP</th>
	                </tr>
	                </thead>
	               <tbody>
	                <?php if( count($data) > 0 ){                	
	                    	foreach($data as $r){?>
	                        <tr>
	                            <td><?php echo ++$no;?></td>
	                            <td><?php echo $r->log_cardno?></td>
	                            <td><?php echo $r->member_name?></td>
	                            <td><?php echo $r->member_employeeid?></td>
	                            <td><?php echo $r->log_action?></td>
	                            <td><?php echo myDate($r->time_add);?></td>
	                            <td><?php echo get_user_name($r->user_add)?></td>
	                            <td><?php echo $r->log_ip?></td>
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
var URL_EXPORT_LOG = '<?php echo $own_links;?>/export_data';

function page(pg) {
	$('#page').val(pg);
	$('#form-validated').submit();
}
</script>