<?php js_validate();?>
<form id="form-validated" enctype="multipart/form-data" action="<?php echo $own_links;?>/save" class="form-horizontal" method="post"> 
        <input type="hidden" name="provider_id" id="provider_id" value="<?php echo isset($val->provider_id)?$val->provider_id:'';?>" />
        <div class="row">
          <div class="col-md-6">
            <!-- 
            </?php if($this->jCfg['user']['is_all'] == 1){?>
            <div class="row form-group">  
              <div class="col-md-3 control-label">Community</div>
              <div class="col-md-9">
                  <select class="validate[required] form-control select" name="provider_site_id" id="provider_site_id">
                      <option value=""> - pilih domain - </option>
                      </?php 
                        $dom = isset($val)?$val->provider_site_id:'';
                        echo option_domains($dom);
                      ?>
                  </select>  
              </div>
            </div>
            </?php } ?>
                        
            <div class="row form-group">  
              <div class="col-md-3 control-label">Nama Client</div>
              <div class="col-md-9">
                  <select class="validate[required] form-control select" name="provider_clientid" id="provider_clientid">
                      <option value=""> - pilih client - </option>
                      </?php 
                        $cli = isset($val)?$val->provider_clientid:'';
                        echo option_client($cli);
                      ?>
                  </select>  
              </div>
            </div>
			
			<div class="row form-group">  
              <div class="col-md-3 control-label">Group</div>
              <div class="col-md-9">
                <select name="providergroupId[]" id="providergroupId" multiple class="form-control select">
                  </?php
                  foreach ((array)get_provider_group() as $km => $vm) {
                     $slc = count($group_assign)>0&&in_array($vm->providergroupId, $group_assign)?'selected="selected"':'';
                        echo "<option value='".$vm->providergroupId."' ".$slc.">".$vm->provider_group."</option>";
                  }
                  ?>
                </select>
              </div>
            </div>			
			-->
            
            <div class="row form-group">  
              <div class="col-md-3 control-label">Kode Provider</div>
              <div class="col-md-9">
                <input type="text" id="provider_code" name="provider_code" class="form-control" value="<?php echo isset($val->provider_code)?$val->provider_code:'';?>" />
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Nama Provider</div>
              <div class="col-md-9">
                <input type="text" id="provider_name" name="provider_name" class="form-control" value="<?php echo isset($val->provider_name)?$val->provider_name:'';?>" />
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Nama Provider EDC</div>
              <div class="col-md-9">
                <input type="text" id="provider_name_edc" name="provider_name_edc" class="form-control" value="<?php echo isset($val->provider_name_edc)?$val->provider_name_edc:'';?>" />
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Tipe Provider</div>
              <div class="col-md-9">
                  <select class="validate[required] form-control select" name="provider_type" id="provider_type">
                      <option value=""> - pilih tipe provider - </option>
                      <?php 
                        $type = isset($val)?$val->provider_type:'';
                        echo option_providertype($type);
                      ?>
                  </select>  
              </div>
            </div>
            
            <div class="row form-group">  
              <div class="col-md-3 control-label">Alamat</div>
              <div class="col-md-9">
                  <textarea class="form-control" name="provider_address" id="provider_address" rows="2"><?php echo isset($val)?$val->provider_address:'';?></textarea>
              </div>
            </div> 

            <div class="row form-group">  
              <div class="col-md-3 control-label">Status</div>
              <div class="col-md-9">
                <select name="provider_status" id="provider_status" class="form-control select">
                  <?php
                  foreach ((array)cfg('status_tampil') as $k1 => $v1) {
                      $slc = isset($val)&&$val->provider_status==$k1?'selected="selected"':'';
                      echo "<option value='".$k1."' $slc >".$v1."</option>";
                  }
                  ?>
                </select>
              </div>
            </div>

          </div> 

          <div class="col-md-6">

            <div class="row form-group">  
              <div class="col-md-3 control-label">Provinsi</div>
              <div class="col-md-9">
                  <select class="validate[required] form-control select" name="provider_region" id="provider_region">
                      <option value=""> - pilih provinsi - </option>
                      <?php 
                        $prov = isset($val)?$val->provider_region:'';
                        echo option_province($prov);
                      ?>
                  </select>
                <!-- <input type="text" id="provider_region" name="provider_region" class="form-control" value="</?php echo isset($val->provider_region)?$val->provider_region:'';?>" /> -->
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Kota/Kabupaten</div>
              <div class="col-md-9">
                  <select class="validate[required] form-control" name="provider_location" id="provider_location">
                      <option value=""> - pilih kota/kabupaten - </option>
                  </select>
                <!-- <input type="text" id="provider_location" name="provider_location" class="form-control" value="</?php echo isset($val->provider_location)?$val->provider_location:'';?>" /> -->
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Telepon</div>
              <div class="col-md-9">
                <input type="text" id="provider_phone" name="provider_phone" class="form-control" value="<?php echo isset($val->provider_phone)?$val->provider_phone:'';?>" />
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Email</div>
              <div class="col-md-9">
                <input type="text" id="provider_email" name="provider_email" class="form-control" value="<?php echo isset($val->provider_email)?$val->provider_email:'';?>" />
              </div>
            </div>
            
            <div class="row form-group">  
              <div class="col-md-3 control-label">Description</div>
              <div class="col-md-9">
                  <textarea class="form-control" name="provider_description" id="provider_description" rows="2"><?php echo isset($val)?$val->provider_description:'';?></textarea>
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

var AJAX_URL = '<?php echo site_url("ajax/data");?>';
$(document).ready(function(){

    $('#provider_region').change(function(){
        get_kota($(this).val(),"");
    });
    <?php if(isset($val)){?>
      get_kota("<?php echo $val->provider_region;?>","<?php echo $val->provider_location;?>");
    <?php }else{ ?>
      get_kota('','');
    <?php } ?>

});

function get_kota(prov,kota){
  $.post(AJAX_URL+"/kota",{prov:prov,kota:kota},function(o){
      $('#provider_location').html(o);
  });
}

</script>
