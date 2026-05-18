<?php js_validate();?>
<form id="form-validated" enctype="multipart/form-data" action="<?php echo $own_links;?>/save" class="form-horizontal" method="post"> 
        <input type="hidden" name="client_id" id="client_id" value="<?php echo isset($val->client_id)?$val->client_id:'';?>" />
        <div class="row">
          <div class="col-md-<?php echo isset($val)?6:10;?>">
            
            <?php if($this->jCfg['user']['is_all'] == 1){?>
            <div class="row form-group">  
              <div class="col-md-3 control-label">Community</div>
              <div class="col-md-9">
                  <select class="validate[required] form-control select" name="client_site_id" id="client_site_id">
                      <option value=""> - pilih domain - </option>
                      <?php 
                        $dom = isset($val)?$val->client_site_id:'';
                        echo option_domains($dom);
                      ?>
                  </select>  
              </div>   
              
            </div>
            <?php } ?>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Kode Client</div>
              <div class="col-md-9">
                <input type="text" id="client_code" name="client_code" class="form-control" value="<?php echo isset($val->client_code)?$val->client_code:'';?>" />
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Nama Client</div>
              <div class="col-md-9">
                <input type="text" id="client_name" name="client_name" class="form-control" value="<?php echo isset($val->client_name)?$val->client_name:'';?>" />
              </div>
            </div>
            
            <div class="row form-group">  
              <div class="col-md-3 control-label">Alamat</div>
              <div class="col-md-9">
                  <textarea class="form-control" name="client_address" id="client_address" rows="2"><?php echo isset($val)?$val->client_address:'';?></textarea>
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Telepon</div>
              <div class="col-md-9">
                <input type="text" id="client_phone" name="client_phone" class="form-control" value="<?php echo isset($val->client_phone)?$val->client_phone:'';?>" />
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Email</div>
              <div class="col-md-9">
                <input type="text" id="client_email" name="client_email" class="form-control" value="<?php echo isset($val->client_email)?$val->client_email:'';?>" />
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Provinsi</div>
              <div class="col-md-9">
                  <select class="validate[required] form-control select" name="client_region" id="client_region">
                      <option value=""> - pilih provinsi - </option>
                      <?php 
                        $prov = isset($val)?$val->client_region:'';
                        echo option_province($prov);
                      ?>
                  </select>
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Kota/Kabupaten</div>
              <div class="col-md-9">
                  <select class="validate[required] form-control" name="client_location" id="client_location">
                      <option value=""> - pilih kota/kabupaten - </option>
                  </select>
              </div>
            </div>
            
            <div class="row form-group">  
              <div class="col-md-3 control-label">Description</div>
              <div class="col-md-9">
                  <textarea class="form-control" name="client_description" id="client_description" rows="2"><?php echo isset($val)?$val->client_description:'';?></textarea>
              </div>
            </div> 

            <div class="row form-group">  
              <div class="col-md-3 control-label">Status</div>
              <div class="col-md-4">
                <select name="client_status" id="client_status" class="form-control select">
                  <?php
                  foreach ((array)cfg('status_tampil') as $k1 => $v1) {
                      $slc = isset($val)&&$val->client_status==$k1?'selected="selected"':'';
                      echo "<option value='".$k1."' $slc >".$v1."</option>";
                  }
                  ?>
                </select>
              </div>
            </div>

          </div> 

		<?php if(isset($val)) {?>
		<div class="col-md-6">
          
          	<div class="row">
				<div class="panel panel-default">
					<div class="panel-heading">
				        <div class="panel-title-box">
				            <h3>Provider List</h3>
				            <span>Data Provider dari Client</span>
				        </div>                                    
				        <ul class="panel-controls" style="margin-top: 2px;">
				            <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>                                       
				        </ul>
				    </div>
	    
					<div class="panel-body panel-body-table">		
						<div class="table-responsive">
							<table class="table table-hover table-bordered table-striped" id="table-provider">
							<thead>
								<tr>
									<th width="30px">No</th>
									<th>Nama Provider</th>
									<th>Deskripsi</th>
								</tr>
							</thead>
							<tbody id="list-provider">
							<?php if( count($data) > 0 ){
							foreach($data as $r){
							?>
		    	 				<tr id="tr-detail-<?php echo $r->provider_id;?>" style="cursor:pointer;" onclick="CLIENT._showDetail(<?php echo $r->provider_id;?>)">
									<td><?php echo ++$no;?></td>
									<td><?php echo $r->provider_name;?></td>
									<td><?php echo $r->provider_description;?></td>
								</tr>
							<?php } } ?>
							</tbody>
							</table>
						</div>
						<!-- <div class="pull-right">
							</?php echo isset($paging)?$paging:'';?>
						</div> -->
					</div>
					<div class="panel-footer"> 
        				<input type="hidden" name="p-offset" id="p-offset" value="0" />
        				<input type="hidden" name="n-offset" id="n-offset" value="5" />
						<button type="button" id="btn-previous" class="btn btn-primary pull-left" disabled="disabled" onclick="CLIENT._showData(<?php echo isset($val)?$val->client_id:'';?>,$('#p-offset').val());">Previous</button>
						<button type="button" id="btn-next" class="btn btn-primary pull-right" onclick="CLIENT._showData(<?php echo isset($val)?$val->client_id:'';?>,$('#n-offset').val());">Next</button>
					</div>
				</div>
			</div><br />
				
            <h5 class="heading-form">Client Provider Detail</h5>
            <div class="panel panel-default panel-detail" style="opacity:0.5;">
				<div class="panel-body panel-body-table">	
					<div class="table-responsive">
						<table class="table table-bordered">
							<tbody id="list-detail">								
								<tr>
									<td width="40%">Provider</td>
									<td width="5">:</td>
									<td><span id="provider-name"></span></td>
								</tr>
								<tr>
									<td>Deskripsi</td>
									<td>:</td>
									<td><span id="provider-description"></span></td>
								</tr>								
							</tbody>
						</table>
					</div>
				</div>
			</div>

		</div>
		<?php }?>
        </div>
        <br />
        
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
var AJAX_URL = '<?php echo site_url("ajax/data");?>';
var URL_GET_DATA_PROVIDER = '<?php echo $own_links;?>/get_data_provider';
$(document).ready(function(){

    $('#client_region').change(function(){
        get_kota($(this).val(),"");
    });
    <?php if(isset($val)){?>
      get_kota("<?php echo $val->client_region;?>","<?php echo $val->client_location;?>");
    <?php }else{ ?>
      get_kota('','');
    <?php } ?>
    
    $("#table-provider tr").click(function(){
        var id = ['','provider_name','provider_description'], i = 0;
        $(this).find("td").each(function(){
           if(i==1) $('#'+id[i]).val($(this).html());
           if(i==2) $('#'+id[i]).html($(this).html());
            i++;
        });
    });

});

function get_kota(prov,kota){
  $.post(AJAX_URL+"/kota",{prov:prov,kota:kota},function(o){
      $('#client_location').html(o);
  });
}

</script>