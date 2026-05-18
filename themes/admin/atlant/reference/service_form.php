<?php js_validate();?>
<form id="form-validated" enctype="multipart/form-data" action="<?php echo $own_links;?>/save" class="form-horizontal" method="post"> 
        <input type="hidden" name="service_id" id="service_id" value="<?php echo isset($val->service_id)?$val->service_id:'';?>" />
        <div class="row">
          <div class="col-md-9">
              
            <div class="row form-group">  
              <div class="col-md-3 control-label">Nama Client</div>
              <div class="col-md-9">
                  <select class="validate[required] form-control select" name="service_clientid" id="service_clientid">
                      <option value=""> - pilih client - </option>
                      <?php 
                        $cli = isset($val)?$val->service_clientid:'';
                        echo option_client($cli);
                      ?>
                  </select>  
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Tipe Pelayanan</div>
              <div class="col-md-9">
                <input type="text" id="service_type" name="service_type" class="form-control" value="<?php echo isset($val->service_type)?$val->service_type:'';?>" />
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Deskripsi</div>
              <div class="col-md-9">
                <textarea class="form-control" name="service_description" id="service_description" rows="2"><?php echo isset($val)?$val->service_description:'';?></textarea>
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Urutan</div>
              <div class="col-md-1">
                <input type="text" id="service_number" name="service_number" class="form-control" value="<?php echo isset($val->service_number)?$val->service_number:'';?>" />
              </div>
            </div>          

            <div class="row form-group">  
              <div class="col-md-3 control-label">Status</div>
              <div class="col-md-4">
                <select name="service_status" id="service_status" class="form-control select">
                  <?php
                  foreach ((array)cfg('status_tampil') as $k1 => $v1) {
                      $slc = isset($val)&&$val->service_status==$k1?'selected="selected"':'';
                      echo "<option value='".$k1."' $slc >".$v1."</option>";
                  }
                  ?>
                </select>
              </div>
            </div>

          </div>
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
$(document).ready(function(){
	$( 'textarea#wysiwg_full' ).ckeditor({
		filebrowserUploadUrl: '/upload_ckeditor.php'
	});
}); 
</script>