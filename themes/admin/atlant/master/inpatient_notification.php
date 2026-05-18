<?php js_validate();?>
<form id="form-validated" enctype="multipart/form-data" action="<?php echo $own_links;?>/save" class="form-horizontal" method="post"> 
        <input type="hidden" name="inpatient_id" id="inpatient_id" value="<?php echo isset($val->inpatient_id)?$val->inpatient_id:'';?>" />
        <div class="row">
          <div class="col-md-9">

            <div class="row form-group">  
              <div class="col-md-3 control-label">Email</div>
              <div class="col-md-9">
              	<input type="text" class="validate[required] form-control" id="inpatient_email" name="inpatient_email" value="<?php echo isset($val->inpatient_email)?$val->inpatient_email:'';?>" />
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Title</div>
              <div class="col-md-9">
                <input type="text" id="inpatient_title" name="inpatient_title" class="validate[required] form-control" value="<?php echo isset($val->inpatient_title)?$val->inpatient_title:'';?>" />
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Content</div>
              <div class="col-md-9">
                <textarea class="form-control" name="inpatient_description" id="wysiwg_full" rows="2"><?php echo isset($val)?$val->inpatient_description:'';?></textarea>
              </div>
            </div>          
			<!-- 
            <div class="row form-group">  
              <div class="col-md-3 control-label">Status</div>
              <div class="col-md-4">
                <select name="inpatient_status" id="inpatient_status" class="form-control select">
                  </?php
                  foreach ((array)cfg('status_tampil') as $k1 => $v1) {
                      $slc = isset($val)&&$val->inpatient_status==$k1?'selected="selected"':'';
                      echo "<option value='".$k1."' $slc >".$v1."</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
             -->

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
var TAGID = 'inpatient_email';
</script>