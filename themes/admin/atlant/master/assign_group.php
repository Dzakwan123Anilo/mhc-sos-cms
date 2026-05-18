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
	                    <th>#</th>
	                </tr>
	                </thead>
	               <tbody>
	                <?php if( count($data) > 0 ){            
						$no=$start;        
	                    foreach($data as $r){
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
	                            <td><?php echo $r->member_plantype;?></td>
	                            <td><?php echo $r->member_employmentstatus;?></td>
	                            <td><?php echo $r->member_statuscard;?></td>
	                            <td><?php echo $r->member_dentallimit;?></td>
	                            <td><?php link_action($links_table_item,"?_id="._encrypt($r->member_id));?></td>
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