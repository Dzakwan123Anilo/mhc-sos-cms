<?php js_validate();?>
<form id="form-validated" enctype="multipart/form-data" action="<?php echo $own_links;?>/save" class="form-horizontal" method="post"> 
        <input type="hidden" name="kab_id" id="kab_id" value="<?php echo isset($val->kab_id)?$val->kab_id:'';?>" />
        <div class="row">
          <div class="col-md-6">
          
            <div class="row form-group">  
              <div class="col-md-3 control-label">Provinsi</div>
              <div class="col-md-9">
                  <select class="validate[required] form-control select" name="kab_propinsi_id" id="kab_propinsi_id">
                      <option value=""> - pilih provinsi - </option>
                      <?php 
                        $prov = isset($val)?$val->kab_propinsi_id:'';
                        echo option_propinsi($prov);
                      ?>
                  </select>
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Kota/Kabupaten</div>
              <div class="col-md-9">
                <input type="text" id="kab_nama" name="kab_nama" class="form-control" value="<?php echo isset($val->kab_nama)?$val->kab_nama:'';?>" />
              </div>
            </div>          

            <div class="row form-group">  
              <div class="col-md-3 control-label">Status</div>
              <div class="col-md-4">
                <select name="kab_status" id="kab_status" class="form-control select">
                  <?php
                  foreach ((array)cfg('status_tampil') as $k1 => $v1) {
                      $slc = isset($val)&&$val->kab_status==$k1?'selected="selected"':'';
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
