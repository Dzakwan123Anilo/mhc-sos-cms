<?php js_validate();?>
<form id="form-validated" enctype="multipart/form-data" action="<?php echo $own_links;?>/save" class="form-horizontal" method="post"> 
        <input type="hidden" name="site_id" id="site_id" value="<?php echo isset($val->site_id)?$val->site_id:'';?>" />
        <input type="hidden" name="template_id" id="template_id" value="<?php echo isset($val2->template_id)?$val2->template_id:'';?>" />

        <div class="row">
          <div class="col-md-6">

            <div class="row form-group">  
              <div class="col-md-3 control-label">Nama Klub</div>
              <div class="col-md-9">
                <input type="text" id="site_name" name="site_name" class="validate[required] form-control" value="<?php echo isset($val->site_name)?$val->site_name:'';?>" />
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Slogan Klub</div>
              <div class="col-md-9">
                <textarea id="site_slogan" name="site_slogan" class="validate[required] form-control"><?php echo isset($val->site_slogan)?$val->site_slogan:'';?></textarea>
              </div>
            </div>
            
            <div class="row form-group">  
              <div class="col-md-3 control-label">Tanggal Aktif</div>
              <div class="col-md-4">
                 <div class="input-group">
                      <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                      <input type="text" id="site_start_date" name="site_start_date" class="validate[required] datepicker form-control" value="<?php echo isset($val->site_start_date)?$val->site_start_date:date("Y-m-d");?>" />
                 </div>
                 <div class="help-block">Tanggal Mulai</div>
              </div>
              <div class="col-md-4">
                 <div class="input-group">
                      <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                      <input type="text" id="site_end_date" name="site_end_date" class="validate[required] datepicker form-control" value="<?php echo isset($val->site_end_date)?$val->site_end_date:date("Y-m-d");?>" />
                 </div>
                 <div class="help-block">Tanggal Akhir</div>
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">URL</div>
              <div class="col-md-9">
                <input type="text" id="site_url" name="site_url" class="validate[required] form-control" value="<?php echo isset($val->site_url)?$val->site_url:'';?>" />contoh isi : hondaweb.com
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Contact Person</div>
              <div class="col-md-9">
                <input type="text" id="site_cp_name" name="site_cp_name" class="validate[required] form-control" value="<?php echo isset($val->site_cp_name)?$val->site_cp_name:'';?>" />
                <span class="help-block">Nama</span>
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label"></div>
              <div class="col-md-9">
                <input type="text" id="site_cp_phone" name="site_cp_phone" class="validate[required] form-control" value="<?php echo isset($val->site_cp_phone)?$val->site_cp_phone:'';?>" />
                <span class="help-block">Telp</span>
              </div>
            </div>
            
            <div class="row form-group">  
              <div class="col-md-3 control-label">Klub Type</div>
              <div class="col-md-3">
                <select name="site_type" id="site_type" class="form-control select">
                  <?php
                  foreach ((array)cfg('site_type') as $k1 => $v1) {
                      $slc = isset($val)&&$val->site_type==$k1?'selected="selected"':'';
                      echo "<option value='".$k1."' $slc >".$v1."</option>";
                  }
                  ?>
                </select>
              </div>
            </div>

            <div class="row form-group" id="div_parent" style="display:none;">  
              <div class="col-md-3 control-label">Parent Site</div>
              <div class="col-md-5">
                <select name="site_parent" id="site_parent" class="form-control select">
                  <option value=""> No Parent </option>
                  <?php
                  foreach ((array)get_site(1) as $k1 => $v1) {
                      $slc = isset($val)&&$val->site_parent==$v1->site_id?'selected="selected"':'';
                      echo "<option value='".$v1->site_id."' $slc >".$v1->site_name."</option>";
                  }
                  ?>
                </select>
              </div>
            </div>


            <div class="row form-group">  
              <div class="col-md-3 control-label">Status</div>
              <div class="col-md-3">
                <select name="site_status" id="site_status" class="form-control select">
                  <?php
                  foreach ((array)cfg('status_data') as $k1 => $v1) {
                      $slc = isset($val)&&$val->site_status==$k1?'selected="selected"':'';
                      echo "<option value='".$k1."' $slc >".$v1."</option>";
                  }
                  ?>
                </select>
              </div>
            </div>

            <div class="row form-group">
              <div class="col-md-3 control-label">Logo</div>
              <div class="col-md-9">
                  <input type="file" id="site_logo" class="fileinput btn-primary" name="site_logo" />
                  <?php if( isset($val->site_logo) && trim($val->site_logo)!="" ){
	                            
                    $image_thumb = get_new_image(array(
          						"url" 		=> cfg('upload_path_klub')."/".$val->site_logo,
          						"folder"	=> "site",
          						"width"		=> 40,
          						"height"	=> 40
          					),true);
          					
          					$image_big = get_new_image(array(
          						"url" 		=> cfg('upload_path_klub')."/".$val->site_logo,
          						"folder"	=> "site",
          						"width"		=> 500,
          						"height"	=> 400
          					),true);
                    
                  ?>
                  <a href="<?php echo $image_big;?>" title="Image Photo" class="act_modal" rel="700|400">
                    <img alt="" src="<?php echo $image_thumb;?>" style="height:25px;width:25px" class="img-polaroid">
                  </a>
                <?php } ?>
                <span class="help-block">Max 500kb</span>
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
                    $slc = isset($val)&&$val2->template_name==$k1?'selected="selected"':'';
                    echo "<option value='".$k1."' $slc >".$v1."</option>";
                  }

                  ?>
                </select>

              </div>
            </div>

            <div class="row form-group">
              <div class="col-md-3 control-label">Bg Head</div>
              <div class="col-md-4">
                  <input type="file" id="template_bg_head" class="fileinput btn-primary" name="template_bg_head" />
                  <?php if( isset($val2->template_bg_head) && trim($val2->template_bg_head)!="" ){
                        $image_thumb = get_new_image(array(
                          "url"     => cfg('upload_path_template')."/".$val2->template_bg_head,
                          "folder"  => "site",
                          "width"   => 40,
                          "height"  => 40
                        ),true);
                        
                        $image_big = get_new_image(array(
                          "url"     => cfg('upload_path_template')."/".$val2->template_bg_head,
                          "folder"  => "site",
                          "width"   => 600,
                          "height"  => 400
                        ),true);
                    ?>
                  <a href="<?php echo $image_big;?>" title="Image Photo" class="act_modal" rel="700|400">
                    <img alt="" src="<?php echo $image_thumb;?>" style="height:25px;width:25px" class="img-polaroid">
                  </a>
                  <?php } ?>
                  <span class="help-block"><a href="#" title="Max 500kb template : klub1(985x283)px, klub2(1123x650)px ">aturan file ?</a></span>
              </div> 
              <div class="col-md-4">
                    <label class="check"><input name="template_bg_head_show" type="checkbox" id="template_bg_head_show" <?php echo (isset($val2->template_bg_head_show)&& $val2->template_bg_head_show=="1")?'checked="checked"':'';?> class="icheckbox" /> Tampilkan </label>
              </div>
            </div>

            <div class="row form-group">
              <div class="col-md-3 control-label">Background</div>
              <div class="col-md-4">
                  <input type="file" id="template_bg" class="fileinput btn-primary" name="template_bg" />
                  <?php if( isset($val2->template_bg) && trim($val2->template_bg)!="" ){

                        $image_thumb = get_new_image(array(
                          "url"     => cfg('upload_path_template')."/".$val2->template_bg,
                          "folder"  => "site",
                          "width"   => 40,
                          "height"  => 40
                        ),true);
                        
                        $image_big = get_new_image(array(
                          "url"     => cfg('upload_path_template')."/".$val2->template_bg,
                          "folder"  => "site",
                          "width"   => 600,
                          "height"  => 400
                        ),true);

                    ?>
                  <a href="<?php echo $image_big;?>" title="Image Photo" class="act_modal" rel="700|400">
                    <img alt="" src="<?php echo $image_thumb;?>" style="height:25px;width:25px" class="img-polaroid">
                  </a>
                  <?php } ?>
                  <span class="help-block">Max 500kb</span>
              </div> 
              <div class="col-md-4">
                    <label class="check"><input name="template_bg_show" type="checkbox" id="template_bg_show" <?php echo (isset($val2->template_bg_show)&& $val2->template_bg_show=="1")?'checked="checked"':'';?> class="icheckbox" /> Tampilkan </label>
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Warna</div>
              <div class="col-md-4">
                  <div class="input-group color" data-color="#fff" data-color-format="hex" id="colorpicker">
                      <input type="text" id="template_color" style="background-color:#fff;color:#111;" name="template_color" value="<?php echo isset($val2->template_color)?$val2->template_color:'';?>" class="form-control" readonly/>
                      <span class="input-group-addon"><i style="background-color: rgb(255, 146, 180)"></i></span>
                  </div>
                  <span class="help-block">Klik di kotak</span>
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
  $(document).ready(function(){
    show_parent();
    $('#site_type').change(function(event) {
      show_parent();
    });
  });
  function show_parent(){
    if( $('#site_type').val() == 1 ){
      $('#div_parent').fadeOut();
    }else{
      $('#div_parent').fadeIn();
    }
  }
</script>
