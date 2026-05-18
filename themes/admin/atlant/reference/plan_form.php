<?php js_validate();?>
<form id="form-validated" enctype="multipart/form-data" action="<?php echo $own_links;?>/save" class="form-horizontal" method="post"> 
        <input type="hidden" name="plan_id" id="plan_id" value="<?php echo isset($val->plan_id)?$val->plan_id:'';?>" />
        <div class="row">
          <div class="col-md-6">
              
            <div class="row form-group">  
              <div class="col-md-3 control-label">Nama Client</div>
              <div class="col-md-9">
                  <select class="validate[required] form-control select" name="plan_clientid" id="plan_clientid">
                      <option value=""> - pilih client - </option>
                      <?php 
                        $cli = isset($val)?$val->plan_clientid:'';
                        echo option_client($cli);
                      ?>
                  </select>  
              </div>
            </div>	

            <div class="row form-group">  
              <div class="col-md-3 control-label">Plan Type</div>
              <div class="col-md-9">
                <input type="text" id="plan_type" name="plan_type" class="form-control" value="<?php echo isset($val->plan_type)?$val->plan_type:'';?>" />
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Deskripsi</div>
              <div class="col-md-9">
                <textarea class="form-control" name="plan_description" id="plan_description" rows="2"><?php echo isset($val)?$val->plan_description:'';?></textarea>
              </div>
            </div>          

            <div class="row form-group">  
              <div class="col-md-3 control-label">Status</div>
              <div class="col-md-4">
                <select name="plan_status" id="plan_status" class="form-control select">
                  <?php
                  foreach ((array)cfg('status_tampil') as $k1 => $v1) {
                      $slc = isset($val)&&$val->plan_status==$k1?'selected="selected"':'';
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
