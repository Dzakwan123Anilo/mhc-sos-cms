<form id="form-validated" class="form-horizontal" action="" method="post" class="input">
	<input type="hidden" name="page" id="page" value="">
	<?php getFormSearch();?>
	<div class="panel panel-default" style="margin-top:-10px;">
	    <div class="panel-heading">
	        <div class="panel-title-box">
	            <h3>Tabel Data <?php echo isset($title)?$title:'';?></h3>
	            <span>Data <?php echo isset($title)?$title:'';?> dari <?php echo cfg('app_name');?></span>
	        </div>                                    
	        <ul class="panel-controls" style="margin-top: 2px;">
	            <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
	            <li><a href="<?php echo current_url();?>" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>                                       
	        </ul>
	    </div>
	    <div class="panel-body panel-body-table">
	
	        <div class="table-responsive">
	            <table class="table table-hover table-bordered table-striped">
	               <thead>
	                <tr>
	                    <th width="30px">No</th>
	                    <th>Nama Group</th>
	                    <th>Deskripsi</th>
	                    <th width="80">Status</th>
	                    <th width="80">Action</th>
	                </tr>
	                </thead>
	               <tbody>
	                <?php 
	                if( count($data) > 0 ){            
						$no=$start;        
	                    foreach($data as $r){	                
	                ?>
	                        <tr>
	                            <td><?php echo ++$no;?></td>
	                            <td><?php echo $r->group_name?></td>
	                            <td><?php echo $r->group_desc?></td>
	                            <td>
	                                <?php echo $r->group_status==1?'<span class="label label-info">Ditampilkan</span>':'<span class="label label-warning">Tidak Ditampilkan</span>';?>
	                            </td>
	                            <td><?php link_action($links_table_item,"?_id="._encrypt($r->group_id));?></td>
	                        </tr>
	                <?php } } ?>
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
function page(pg) {
	$('#page').val(pg);
	$('#form-validated').submit();
}
</script>