<?php js_validate();?>
<?php get_data_table();?>
<form id="form-validated" enctype="multipart/form-data" action="<?php echo $own_links;?>/save" class="form-horizontal" method="post"> 
<input type="hidden" name="providergroupId" id="providergroupId" value="<?php echo isset($val->providergroupId)?$val->providergroupId:'';?>" />
	<div class="row">
		<div class="col-md-6">            
            <div class="row form-group">  
              <div class="col-md-3 control-label">Nama Client</div>
              <div class="col-md-9">
                  <select class="validate[required] form-control select" name="provider_clientid" id="provider_clientid">
                      <option value=""> - pilih client - </option>
                      <?php 
                        $cli = isset($val)?$val->provider_clientid:'';
                        echo option_client($cli);
                      ?>
                  </select>  
              </div>
            </div>
            		
			<div class="row form-group">
				<div class="col-md-3 control-label">Group</div>
				<div class="col-md-6">
					<input type="text" id="provider_group" name="provider_group" class="form-control" value="<?php echo isset($val->provider_group)?$val->provider_group:'';?>" />
				</div>
			</div> 
		
			<div class="row form-group">
				<div class="col-md-3 control-label">Deskripsi</div>
				<div class="col-md-9">
					<input type="text" id="description" name="description" class="form-control" value="<?php echo isset($val->description)?$val->description:'';?>" />
				</div>
			</div>
		
			<div class="row form-group">
				<div class="col-md-3 control-label">Status</div>
				<div class="col-md-4">
					<select name="status_data" id="status_data" class="form-control select">
					<?php
						foreach ((array)cfg('status_tampil') as $k1 => $v1) {
							$slc = isset($val)&&$val->status_data==$k1?'selected="selected"':'';
							echo "<option value='".$k1."' $slc >".$v1."</option>";
						}
					?>
					</select>
				</div>
			</div>		
		</div>
	</div><br />
	
	<div class="row">	    
		<div class="panel panel-default" style="margin-top:-10px; padding: 0;">
		    <div class="panel-heading">
		        <div class="panel-title-box">
		            <h3>Provider List</h3>
		            <span>Data Provider</span>
		        </div>             
		    </div><br>
    
			<div class="panel-body panel-body-table">	
				<div class="table-responsive">
					<table id="dt-provider" class="table table-hover table-bordered table-striped datatable">
						<thead>
							<tr>
								<th width="10">
								<input type="checkbox" value="1" id="select_all" class="select_all" />
								</th>
			                    <th>Nama Provider</th>
			                    <th>Nama Provider EDC</th>
			                    <th>Kode Provider</th>
			                    <th>Tipe Provider</th>
			                    <th>Telepon</th>     
			                    <th>Email</th>     
			                    <th>Propinsi</th> 
			                    <th>Kota/Kabupaten</th>
							</tr>
						</thead>
						<tbody id="table-provider">
						<?php 
						$no=0;
						if(isset($provider) && count($provider) > 0 ){
							foreach($provider as $r){
							$idx = $r->provider_id;
						?>
							<tr id="tr_item_<?php echo $idx;?>" class="tr_item">
								<td>
								<input type="checkbox" name="item_select[<?php echo $idx;?>]" value="<?php echo $idx;?>" class="item_select" id="item_select_<?php echo $idx;?>"<?php echo empty($r->memberprovidergroupId)?'':' checked="checked"';?>/>
								</td>
	                            <td><?php echo $r->provider_name?></td>
	                            <td><?php echo $r->provider_name_edc?></td>
	                            <td><?php echo $r->provider_code?></td>
	                            <td><?php echo $r->provider_type?></td>
	                            <td><?php echo $r->provider_phone?></td>
	                            <td><?php echo $r->provider_email?></td>
	                            <td><?php echo $r->provider_region?></td>
	                            <td><?php echo $r->provider_location?></td>
							</tr>
						<?php } 
						} ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>	  
	</div>

	<div class="panel-footer">
		<div class="pull-left">
			<button type="button" onclick="document.location='<?php echo $own_links;?>'" class="btn btn-white btn-cons">Cancel</button>
		</div>
		<div class="pull-right">
			<button type="submit" name="simpan" class="btn btn-primary btn-cons"><i class="icon-ok"></i> Save</button>
		</div>
	</div>
</form>

<script type="text/javascript">
var URL_GET_DATA_PROVIDER = '<?php echo $own_links;?>/get_data_provider';	
$(document).ready(function() {
	$(".datatable").dataTable({
		"bPaginate": false,
		"pageLength": 1000000
	});
});
</script>
