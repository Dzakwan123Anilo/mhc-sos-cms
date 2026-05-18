<?php js_validate();?>
<form id="form-validated" enctype="multipart/form-data" action="<?php echo $own_links;?>/save" class="form-horizontal" method="post"> 
        <input type="hidden" name="dental_id" id="dental_id" value="<?php echo isset($val->dental_id)?$val->dental_id:'';?>" />
        <div class="row">
          <div class="col-md-6">
          
            <div class="row form-group">  
              <div class="col-md-3 control-label">Plan Type</div>
              <div class="col-md-9">
                  <select class="validate[required] form-control select" name="dental_plantypeid" id="dental_plantypeid">
                      <option value=""> - pilih plan type - </option>
                      <?php 
                        $plan = isset($val)?$val->dental_plantypeid:'';
                        echo option_plantype($plan);
                      ?>
                  </select>
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Dental Limit</div>
              <div class="col-md-9">
                <input type="text" id="dental_limit" name="dental_limit" class="form-control" value="<?php echo isset($val->dental_limit)?$val->dental_limit:'';?>" />
              </div>
            </div>       

            <div class="row form-group">  
              <div class="col-md-3 control-label">Deskripsi</div>
              <div class="col-md-9">
                <textarea class="form-control" name="dental_description" id="dental_description" rows="2"><?php echo isset($val)?$val->dental_description:'';?></textarea>
              </div>
            </div>             

            <div class="row form-group">  
              <div class="col-md-3 control-label">Status</div>
              <div class="col-md-4">
                <select name="dental_status" id="dental_status" class="form-control select">
                  <?php
                  foreach ((array)cfg('status_tampil') as $k1 => $v1) {
                      $slc = isset($val)&&$val->dental_status==$k1?'selected="selected"':'';
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
