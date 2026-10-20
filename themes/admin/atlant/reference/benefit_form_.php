<?php js_validate();?>
<form id="form-validated" enctype="multipart/form-data" action="<?php echo $own_links;?>/save" class="form-horizontal" method="post"> 
        <input type="hidden" name="benefit_id" id="benefit_id" value="<?php echo isset($val->benefit_id)?$val->benefit_id:'';?>" />
        <div class="row">
          <div class="col-md-9">

            <div class="row form-group">  
              <div class="col-md-3 control-label">Tipe Pelayanan</div>
              <div class="col-md-9">
                  <select class="validate[required] form-control select" name="benefit_serviceid" id="benefit_serviceid">
                      <option value=""> - pilih tipe pelayanan - </option>
                      <?php 
                        $serv = isset($val)?$val->benefit_serviceid:'';
                        echo option_servicetype($serv);
                      ?>
                  </select>
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Kelas Perawatan</div>
              <div class="col-md-9">
                  <select class="validate[required] form-control select" name="benefit_planid" id="benefit_planid">
                      <option value=""> - pilih kelas perawatan - </option>
                      <?php 
                        $plan = isset($val)?$val->benefit_planid:'';
                        echo option_plantype($plan);
                      ?>
                  </select>
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Benefit</div>
              <div class="col-md-9">
                <textarea class="form-control" name="benefit_description" id="wysiwg_full" rows="2"><?php echo isset($val)?$val->benefit_description:'';?></textarea>
              </div>
            </div>          

            <div class="row form-group">  
              <div class="col-md-3 control-label">Status</div>
              <div class="col-md-4">
                <select name="benefit_status" id="benefit_status" class="form-control select">
                  <?php
                  foreach ((array)cfg('status_tampil') as $k1 => $v1) {
                      $slc = isset($val)&&$val->benefit_status==$k1?'selected="selected"':'';
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