<?php js_validate();?>
<form id="form-validated" enctype="multipart/form-data" action="<?php echo $own_links;?>/save" class="form-horizontal" method="post"> 
        <input type="hidden" name="group_id" id="group_id" value="<?php echo isset($val->group_id)?$val->group_id:'';?>" />
        <div class="row">
          <div class="col-md-6">
            <h5 class="heading-form">Group Info</h5>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Nama Kategori</div>
              <div class="col-md-5">
                <input type="text" id="group_name" name="group_name" class="validate[required] form-control" value="<?php echo isset($val->group_name)?$val->group_name:'';?>" />
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Deskripsi</div>
              <div class="col-md-7">
                <input type="text" id="group_desc" name="group_desc" class="validate[required] form-control" value="<?php echo isset($val->group_desc)?$val->group_desc:'';?>" />
              </div>
            </div>

            
            <?php if($this->jCfg['user']['is_all']==1){?>
            <div class="row form-group">  
              <div class="col-md-3 control-label">Community</div>
              <div class="col-md-7">
                  <select class="validate[required] form-control" name="group_site_id" id="group_site_id">
                      <option value=""> pilih </option>
                      <?php 
                        $dom = isset($val)?$val->group_site_id:'';
                        echo option_domains($dom);
                      ?>
                  </select>  
              </div>     
            </div>
            <?php } ?>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Status</div>
              <div class="col-md-3">
                <select name="group_status" id="group_status" class="form-control select">
                  <?php
                  foreach ((array)cfg('status_tampil') as $k1 => $v1) {
                      $slc = isset($val)&&$val->group_status==$k1?'selected="selected"':'';
                      echo "<option value='".$k1."' $slc >".$v1."</option>";
                  }
                  ?>
                </select>
              </div>
            </div>


          </div>  


          <div class="col-md-6">
            <h5 class="heading-form">Member Provider</h5>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Provider</div>
              <div class="col-md-9">
                  <select class="validate[required] form-control select" multiple name="provider_assigns[]" id="provider_assigns">
                      <?php foreach ((array)$provider_data as $k1 => $v1) {
                        $slc = count($provider_assigns)>0&&in_array($v1->provider_id, $provider_assigns)?'selected="selected"':'';
                        echo "<option value='".$v1->provider_id."' ".$slc.">".$v1->provider_name."</option>";
                      }?>
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
