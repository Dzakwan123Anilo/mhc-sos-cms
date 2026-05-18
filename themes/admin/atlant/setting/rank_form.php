<?php js_validate();?>
<form id="form-validated" enctype="multipart/form-data" action="<?php echo $own_links;?>/save" class="form-horizontal" method="post"> 
        <input type="hidden" name="rank_id" id="rank_id" value="<?php echo isset($val->rank_id)?$val->rank_id:'';?>" />
        <div class="row">
          <div class="col-md-11">

            <div class="row form-group">  
              <div class="col-md-3 control-label">Name</div>
              <div class="col-md-8">
                <input type="text" id="rank_name" name="rank_name" class="validate[required] form-control" value="<?php echo isset($val->rank_name)?$val->rank_name:'';?>" />
              </div>
            </div>
            
             <?php if($this->jCfg['user']['is_all']){?>
             <div class="row form-group">  
              <div class="col-md-3 control-label">Domain</div>
              <div class="col-md-7">
                  <select class="validate[required] form-control select" name="rank_site_id" id="rank_site_id">
                      <option value=""> - pilih domain - </option>
                      <?php 
                        $dom = isset($val)?$val->rank_site_id:'';
                        echo option_domains($dom);
                      ?>
                  </select>  
              </div>
            </div>
            <?php } ?>
            
            <div class="row form-group">  
              <div class="col-md-3 control-label">Parameter</div>
              <div class="col-md-3">
                <input type="text" id="rank_min_post" name="rank_min_post" class="validate[required] form-control" value="<?php echo isset($val->rank_min_post)?$val->rank_min_post:'';?>" />
                <i class="help-block">Min Post</i>
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label"></div>
              <div class="col-md-3">
                <input type="text" id="rank_min_likes" name="rank_min_likes" class="validate[required] form-control" value="<?php echo isset($val->rank_min_likes)?$val->rank_min_likes:'';?>" />
                <i class="help-block">Min Likes</i>
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label"></div>
              <div class="col-md-3">
                <input type="text" id="rank_min_minutes" name="rank_min_minutes" class="validate[required] form-control" value="<?php echo isset($val->rank_min_minutes)?$val->rank_min_minutes:'';?>" />
                <i class="help-block">Min Time On Forum ( Minutes )</i>
              </div>
            </div>

            <div class="row form-group">
              <div class="col-md-3 control-label">Icon</div>
              <div class="col-md-3">
                  <input type="file" id="rank_icon" class="fileinput btn-primary" name="rank_icon" />
                  <?php if( isset($val->rank_icon) && trim($val->rank_icon)!="" ){
                                
                        $image_thumb = get_new_image(array(
                                        "url"       => cfg('upload_path_rank')."/".$val->rank_icon,
                                        "url_klub"  => cfg('upload_path_rank')."/".$val->rank_icon,
                                        "folder"    => "video",
                                        "width"     => 40,
                                        "height"    => 40,
                                        "site_id"   => $val->rank_site_id
                                    ),true);
                                    
                                    $image_big = get_new_image(array(
                                        "url"       => cfg('upload_path_rank')."/".$val->rank_icon,
                                        "url_klub"  => cfg('upload_path_rank')."/".$val->rank_icon,
                                        "folder"    => "video",
                                        "width"     => 500,
                                        "height"    => 400,
                                        "site_id"  => $val->rank_site_id
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
                <select name="rank_status" id="rank_status" class="form-control select">
                  <?php
                  foreach ((array)cfg('status_tampil') as $k1 => $v1) {
                      $slc = isset($val)&&$val->rank_status==$k1?'selected="selected"':'';
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

