<?php js_validate();?>
<form id="form-validated" enctype="multipart/form-data" action="<?php echo $own_links;?>/save" class="form-horizontal" method="post"> 
        <input type="hidden" name="panduan_id" id="panduan_id" value="<?php echo isset($val->panduan_id)?$val->panduan_id:'';?>" />
        <div class="row">
          <div class="col-md-6">

            <div class="row form-group">  
              <div class="col-md-3 control-label">Judul Panduan</div>
              <div class="col-md-9">
                <input type="text" id="panduan_name" name="panduan_name" class="validate[required] form-control" value="<?php echo isset($val->panduan_name)?$val->panduan_name:'';?>" />
              </div>
            </div>
            
            <?php if($this->jCfg['user']['is_all'] == 1){?>
            <div class="row form-group">  
              <div class="col-md-3 control-label">Community</div>
              <div class="col-md-9">
                  <select class="validate[required] form-control select" name="panduan_site_id" id="panduan_site_id">
                      <option value=""> - pilih domain - </option>
                      <?php 
                        $dom = isset($val)?$val->panduan_site_id:'';
                        echo option_domains($dom);
                      ?>
                  </select>  
              </div>   
              
            </div>
            <?php } ?>
            
            <div class="row form-group">  
              <div class="col-md-3 control-label">Deskripsi</div>
              <div class="col-md-9">
                  <textarea class="validate[required] form-control" name="panduan_description" id="panduan_description" rows="3"><?php echo isset($val)?$val->panduan_description:'';?></textarea>
              </div>
            </div>
          </div> 

          <div class="col-md-6">
            
			<div class="row form-group">
              <div class="col-md-3 control-label">PDF File</div>
              <div class="col-md-9">
                  <input type="file" id="panduan_filename" class="fileinput btn-primary" name="panduan_filename" />
                  <?php if( isset($val->panduan_filename) && trim($val->panduan_filename)!="" ){ ?>
                      <a href="<?php echo cfg('upload_path_file')."/".$val->panduan_filename;?>" title="PDF File" class="act_modal">
                        <?php echo $val->panduan_filename;?>
                      </a>
                    <?php } ?>
                    <span class="help-block">Max 10Mb </span>
              </div> 
            </div> 

            <div class="row form-group">  
              <div class="col-md-3 control-label">Status</div>
              <div class="col-md-4">
                <select name="panduan_status" id="panduan_status" class="form-control select">
                  <?php
                  foreach ((array)cfg('status_tampil') as $k1 => $v1) {
                      $slc = isset($val)&&$val->panduan_status==$k1?'selected="selected"':'';
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