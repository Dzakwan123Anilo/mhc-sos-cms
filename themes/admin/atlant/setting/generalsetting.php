<?php js_validate();?>
<style type="text/css">
    .form-control{
        background-color: #f1f1f1;
        border-color: #fff;
    }
</style>
<form id="form-validated" enctype="multipart/form-data" action="<?php echo $own_links;?>/save" class="form-horizontal" method="post"> 
        <input type="hidden" name="site_id" id="site_id" value="<?php echo isset($val->site_id)?$val->site_id:'';?>" />
        <input type="hidden" name="template_id" id="template_id" value="<?php echo isset($template->template_id)?$template->template_id:'';?>" />

        <div class="row">
          <div class="col-md-6">
            
            <h5 class="heading-form">Main Info</h5>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Community Name</div>
              <div class="col-md-9">
                <input type="text" id="site_name" name="site_name" class="validate[required] form-control" value="<?php echo isset($val->site_name)?$val->site_name:'';?>" />
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Slogan</div>
              <div class="col-md-9">
              <input type="text" id="site_slogan" name="site_slogan" class="validate[required] form-control" value="<?php echo isset($val->site_slogan)?$val->site_slogan:'';?>" />
              </div>
            </div>
            
            <div class="row form-group">  
              <div class="col-md-3 control-label">Description</div>
              <div class="col-md-9">
                <textarea id="site_description" rows="6" name="site_description" class="validate[required] form-control"><?php echo isset($val->site_description)?$val->site_description:'';?></textarea>
              </div>
            </div>
            
            <div class="row form-group">
              <div class="col-md-3 control-label">Maskot</div>
              <div class="col-md-9">
                  <?php if( isset($val->site_maskot) && trim($val->site_maskot)!="" ){
                              
                    $image_thumb = get_new_image(array(
                      "url"     => cfg('upload_path_klub')."/".$val->site_maskot,
                      "folder"  => "site",
                      "width"   => 300,
                      "height"  => 150
                    ),true);
                    
                    $image_big = get_new_image(array(
                      "url"     => cfg('upload_path_klub')."/".$val->site_maskot,
                      "folder"  => "site"
                      //"width"   => 300,
                      //"height"  => 200
                    ),true);
                    
                  ?>
                  <a href="<?php echo $image_big;?>" title="Image Photo" class="act_modal" rel="700|400">
                    <img alt="" src="<?php echo $image_thumb;?>" class="img-polaroid">
                  </a>
                <?php } ?>
              </div> 
            </div>

            <div class="row form-group">
              <div class="col-md-3 control-label">Logo</div>
              <div class="col-md-9">
                  <?php if( isset($val->site_logo) && trim($val->site_logo)!="" ){
                            
                    
                    $image_big = get_new_image(array(
                      "url"     => cfg('upload_path_klub')."/".$val->site_logo,
                      "folder"  => "site"
                    ),true);
                    
                  ?>
                  <a href="<?php echo $image_big;?>" title="Image Photo" class="act_modal" rel="700|400">
                    <img alt="" src="<?php echo $image_big;?>" height="50" class="img-polaroid">
                  </a>
                <?php } ?>
              </div> 
            </div>

            <h5 class="heading-form">Contact Person</h5>
            <div class="row form-group">  
              <div class="col-md-3 control-label">Contact Person</div>
              <div class="col-md-9">
                <input type="text" id="site_cp_name" name="site_cp_name" class="validate[required] form-control" value="<?php echo isset($val->site_cp_name)?$val->site_cp_name:'';?>" />
                <span class="help-block">Name</span>
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label"></div>
              <div class="col-md-4">
                <input type="text" id="site_cp_phone" name="site_cp_phone" class="validate[required] form-control" value="<?php echo isset($val->site_cp_phone)?$val->site_cp_phone:'';?>" />
                <span class="help-block">Telp</span>
              </div>
              <div class="col-md-5">
                <input type="text" id="site_cp_email" name="site_cp_email" class="validate[required] form-control" value="<?php echo isset($val->site_cp_email)?$val->site_cp_email:'';?>" />
                <span class="help-block">Email</span>
              </div>
            </div>
            
            <div class="row form-group">  
              <div class="col-md-3 control-label"></div>
              <div class="col-md-9">
                <textarea id="site_cp_address" name="site_cp_address" class="validate[required] form-control"><?php echo isset($val->site_cp_address)?$val->site_cp_address:'';?></textarea>
                <span class="help-block">Address</span>

              </div>
            </div>
            
            <h5 class="heading-form">Google Setting</h5>
            
            <div class="row form-group">  
              <div class="col-md-3 control-label">Meta Title</div>
              <div class="col-md-9">
                <input type="text" id="site_meta_title" name="site_meta_title" class="validate[required] form-control" value="<?php echo isset($val->site_meta_title)?$val->site_meta_title:'';?>" />
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Meta Keyword</div>
              <div class="col-md-9">
                <input type="text" id="site_meta_keyword" name="site_meta_keyword" class="validate[required] form-control" value="<?php echo isset($val->site_meta_keyword)?$val->site_meta_keyword:'';?>" />
              </div>
            </div>
            
            <div class="row form-group">  
              <div class="col-md-3 control-label">Meta Description</div>
              <div class="col-md-9">
                <textarea id="site_meta_description" name="site_meta_description" class="validate[required] form-control"><?php echo isset($val->site_meta_description)?$val->site_meta_description:'';?></textarea>

              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">GA Script</div>
              <div class="col-md-9">
                <textarea id="site_ga" name="site_ga" rows="5" class="form-control"><?php echo isset($val->site_ga)?$val->site_ga:'';?></textarea>
              </div>
            </div>
          </div> 

          <div class="col-md-6">
            <h5 class="heading-form">Template Setting</h5>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Template</div>
              <div class="col-md-9">

                <select class="validate[required] form-control select" name="template_name" id="template_name">
                  <?php
                  $tmp = cfg('template');
                  foreach ((array)$tmp as $k1 => $v1) {
                    $slc = isset($val)&&$template->template_name==$k1?'selected="selected"':'';
                    echo "<option value='".$k1."' $slc >".$v1."</option>";
                  }

                  ?>
                </select>

              </div>
            </div>

            <div class="row form-group">
              <div class="col-md-3 control-label">Bg Head</div>
              <div class="col-md-4">
                  <?php if( isset($template->template_bg_head) && trim($template->template_bg_head)!="" ){
                        
                        $image_big = get_new_image(array(
                          "url"     => cfg('upload_path_template')."/".$template->template_bg_head,
                          "folder"  => "site"
                        ),true);
                    ?>
                  <a href="<?php echo $image_big;?>" title="Image Photo" class="act_modal" rel="700|400">
                    <img alt="" src="<?php echo $image_big;?>" style="height:100px;" class="img-polaroid">
                  </a>
                  <?php } ?>
              </div> 
              <div class="col-md-4">
                    <label class="check"><input name="template_bg_head_show" type="checkbox" id="template_bg_head_show" <?php echo (isset($template->template_bg_head_show)&& $template->template_bg_head_show=="1")?'checked="checked"':'';?> class="icheckbox" /> Tampilkan </label>
              </div>
            </div>

            <div class="row form-group">
              <div class="col-md-3 control-label">Background</div>
              <div class="col-md-4">
                  <?php if( isset($template->template_bg) && trim($template->template_bg)!="" ){
                        
                        $image_big = get_new_image(array(
                          "url"     => cfg('upload_path_template')."/".$template->template_bg,
                          "folder"  => "site"
                        ),true);

                    ?>
                  <a href="<?php echo $image_big;?>" title="Image Photo" class="act_modal" rel="700|400">
                    <img alt="" src="<?php echo $image_big;?>" style="height:100px;" class="img-polaroid">
                  </a>
                  <?php } ?>
              </div> 
              <div class="col-md-4">
                    <label class="check"><input name="template_bg_show" type="checkbox" id="template_bg_show" <?php echo (isset($template->template_bg_show)&& $template->template_bg_show=="1")?'checked="checked"':'';?> class="icheckbox" /> Tampilkan </label>
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Color</div>
              <div class="col-md-4">
                  <div class="input-group color" data-color="#fff" data-color-format="hex" id="colorpicker">
                      <input type="text" id="template_color" style="background-color:#fff;color:#111;" name="template_color" value="<?php echo isset($template->template_color)?$template->template_color:'';?>" class="form-control" readonly/>
                      <span class="input-group-addon"><i style="background-color: rgb(255, 146, 180)"></i></span>
                  </div>
                  <span class="help-block">Klik di kotak</span>
              </div>
            </div>

          
          <h5 class="heading-form">Social Media Setting</h5>
          <?php foreach ((array)cfg('sosmed') as $p => $q) { ?>
            
            <div class="row form-group">  
              <div class="col-md-3 control-label">Social Media <?php echo $q['name'];?></div>
              <div class="col-md-9">
                <input type="text" id="sosmed_<?php echo $q['key'];?>" name="sosmed_item[<?php echo $q['key'];?>]" class="form-control" value="<?php echo isset($sosmed)&&isset($sosmed[$q['key']])?$sosmed[$q['key']]:'';?>" />
              </div>
            </div>


          <?php }?>

          </div>       
        </div>
        <br />
        
        <div class="panel-footer">
          <div class="pull-left">
            <button type="button" onclick="document.location='<?php echo $own_links;?>'" class="btn btn-white btn-cons">Cancel</button>
          </div>
          <div class="pull-right">
            <button type="button" onclick="document.location='<?php echo $own_links;?>/edit'" class="btn btn-success btn-cons"> <i class="fa fa-pencil"></i>EDIT</button>
          </div>

        </div>

</form>

