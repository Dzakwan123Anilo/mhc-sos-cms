<?php js_validate();?>
<form id="form-validated" enctype="multipart/form-data" action="<?php echo $own_links;?>/save" class="form-horizontal" method="post"> 
        <input type="hidden" name="badge_id" id="badge_id" value="<?php echo isset($val->badge_id)?$val->badge_id:'';?>" />
        <div class="row">
          <div class="col-md-11">

            <div class="row form-group">  
              <div class="col-md-3 control-label">Name</div>
              <div class="col-md-8">
                <input type="text" id="badge_name" name="badge_name" class="validate[required] form-control" value="<?php echo isset($val->badge_name)?$val->badge_name:'';?>" />
              </div>
            </div>

             <div class="row form-group">  
              <div class="col-md-3 control-label">Description</div>
              <div class="col-md-8">
                <textarea id="badge_desc" rows="3" name="badge_desc" rows="3" class="validate[required] form-control"><?php echo isset($val->badge_desc)?$val->badge_desc:'';?></textarea>
              </div>
            </div>
            
             <?php if($this->jCfg['user']['is_all']){?>
             <div class="row form-group">  
              <div class="col-md-3 control-label">Domain</div>
              <div class="col-md-7">
                  <select class="validate[required] form-control select" name="badge_site_id" id="badge_site_id">
                      <option value=""> - pilih domain - </option>
                      <?php 
                        $dom = isset($val)?$val->badge_site_id:'';
                        echo option_domains($dom);
                      ?>
                  </select>  
              </div>
            </div>
            <?php } ?>
            

            <div class="row form-group">
              <div class="col-md-3 control-label">Icon</div>
              <div class="col-md-3">
                  <input type="file" id="badge_icon" class="fileinput btn-primary" name="badge_icon" />
                  <?php if( isset($val->badge_icon) && trim($val->badge_icon)!="" ){
                                
                        $image_thumb = get_new_image(array(
                                        "url"       => cfg('upload_path_badge')."/".$val->badge_icon,
                                        "url_klub"  => cfg('upload_path_badge')."/".$val->badge_icon,
                                        "folder"    => "video",
                                        "width"     => 40,
                                        "height"    => 40,
                                        "site_id"   => $val->badge_site_id
                                    ),true);
                                    
                                    $image_big = get_new_image(array(
                                        "url"       => cfg('upload_path_badge')."/".$val->badge_icon,
                                        "url_klub"  => cfg('upload_path_badge')."/".$val->badge_icon,
                                        "folder"    => "video",
                                        "width"     => 500,
                                        "height"    => 400,
                                        "site_id"  => $val->badge_site_id
                                    ),true);
                        
                    ?>
                      <a href="<?php echo $image_big;?>" title="Image Photo" class="act_modal" rel="700|400">
                        <img alt="" src="<?php echo $image_thumb;?>" style="height:25px;width:25px" class="img-polaroid">
                      </a>
                    <?php } ?>
                    <span class="help-block">Max 500kb</span>
              </div> 
            </div>       

            <div class="row form-group">  
              <div class="col-md-3 control-label">Status</div>
              <div class="col-md-2">
                <select name="badge_status" id="badge_status" class="form-control select">
                  <?php
                  foreach ((array)cfg('status_tampil') as $k1 => $v1) {
                      $slc = isset($val)&&$val->badge_status==$k1?'selected="selected"':'';
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

