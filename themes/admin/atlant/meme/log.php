<form id="form-validated" class="form-horizontal" action="" method="post" class="input">
	<input type="hidden" name="page" id="page" value="">
	<?php getFormSearch();?>
	<div class="panel panel-default" style="margin-top:-10px;">
	    <div class="panel-heading">
	        <div class="panel-title-box">
	            <h3>Tabel Data Log</h3>
	            <span>Data Log dari <?php echo cfg('app_name');?> (<?php echo $cRec;?>)</span>
	        </div>                                    
	        <ul class="panel-controls" style="margin-top: 2px;">
	            <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>                                       
	        </ul>
	    </div>
	    <div class="panel-body panel-body-table">
	        <div class="table-responsive">
	            <table class="table table-hover table-striped table-bordered">
	               <thead>
	                <tr>
	                    <th width="10px">No</th>
	                    <th width="300">Info</th>
	                    <th>Description</th>
	                </tr>
	                </thead>
	               <tbody> 
	                <?php 
	                if(count($data) > 0){              	
	          			$no=$start;          	
	                    foreach($data as $r){?>
	                        <tr>
	                            <td><?php echo ++$no;?></td>
	                            <td><?php echo myDate($r->log_date,"d/m/Y H:i:s");?>
	                            <br /> Oleh : <?php echo $r->log_user_name;?>
	                            <br />Menu/Module : <?php echo trim($r->acc_menu ?? '')!=""?$r->acc_menu:$r->log_class;?>
	                            <br />Action : <?php echo $r->log_function;?>
	                            <br />IP : <?php echo $r->log_ip;?></td>
	                            <td>
	                            User <b><?php echo $r->log_user_name;?></b> mengakses menu/module <b><?php echo trim($r->acc_menu ?? '')!=""?$r->acc_menu:$r->log_class;?></b> dan melakukan action <b><?php echo get_masking_action($r->log_function);?></b> data <b>(<?php echo $r->log_function;?>)</b> pada tanggal <b><?php echo myDate($r->log_date,"d/m/Y H:i:s",false);?> </b> dengan menggunakan browser
	                            <i><?php echo $r->log_user_agent;?></i></td>
	                        </tr>
	                <?php } 
	                }
	                ?>
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