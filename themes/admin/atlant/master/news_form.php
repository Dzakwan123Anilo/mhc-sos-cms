<?php js_validate();?>
<form id="form-validated" enctype="multipart/form-data" action="<?php echo $own_links;?>/save" class="form-horizontal" method="post"> 
        <input type="hidden" name="news_id" id="news_id" value="<?php echo isset($val->news_id)?$val->news_id:'';?>" />
        <div class="row">
          <div class="col-md-6">

            <div class="row form-group">  
              <div class="col-md-3 control-label">Judul Berita</div>
              <div class="col-md-9">
                <input type="text" id="news_title" name="news_title" class="validate[required] form-control" value="<?php echo isset($val->news_title)?$val->news_title:'';?>" />
              </div>
            </div>
            
            <?php if($this->jCfg['user']['is_all'] == 1){?>
            <div class="row form-group">  
              <div class="col-md-3 control-label">Community</div>
              <div class="col-md-9">
                  <select class="validate[required] form-control select" name="news_site_id" id="news_site_id">
                      <option value=""> - pilih domain - </option>
                      <?php 
                        $dom = isset($val)?$val->news_site_id:'';
                        echo option_domains($dom);
                      ?>
                  </select>  
              </div>   
              
            </div>
            <?php } ?>
            
            <div class="row form-group">  
              <div class="col-md-3 control-label">Konten</div>
              <div class="col-md-9">
                  <textarea class="validate[required] form-control" name="news_content" id="news_content" rows="3"><?php echo isset($val)?$val->news_content:'';?></textarea>
              </div>
            </div>
          </div> 

          <div class="col-md-6">
            
			<div class="row form-group">
              <div class="col-md-3 control-label">Image File</div>
              <div class="col-md-9">
                  <input type="file" id="news_image" class="fileinput btn-primary" name="news_image" />
                  <?php if( isset($val->news_image) && trim($val->news_image)!="" ){
	                            
                        $image_thumb = get_new_image(array(
            				"url" 		=> cfg('upload_path_photo')."/".$val->news_image,
            				"url_klub" 	=> cfg('upload_path_photo')."/".$val->news_image,
            				"folder"	=> "photo",
            				"width"		=> 40,
            				"height"	=> 40,
            				"site_id"	=> $val->news_site_id
            			),true);
            			
            			$image_big = get_new_image(array(
            				"url" 		 => cfg('upload_path_photo')."/".$val->news_image,
            				"url_klub" 	 => cfg('upload_path_photo')."/".$val->news_image,
            				"folder"	 => "photo",
            				"site_id"    => $val->news_site_id
            			),true);
                        
                    ?>
                      <a href="<?php echo $image_big;?>" title="Image File" class="act_modal" rel="700|400">
                        <img alt="" src="<?php echo $image_thumb;?>" style="height:25px;width:25px" class="img-polaroid">
                      </a>
                    <?php } ?>
                    <span class="help-block">Max 500kb (820x320)px </span>
              </div> 
            </div>
            
			<div class="row form-group">
              <div class="col-md-3 control-label">PDF File</div>
              <div class="col-md-9">
                  <input type="file" id="news_filename" class="fileinput btn-primary" name="news_filename" />
                  <?php if( isset($val->news_filename) && trim($val->news_filename)!="" ){ ?>
                      <a href="<?php echo cfg('upload_path_file')."/".$val->news_filename;?>" title="PDF File" class="act_modal">
                        <?php echo $val->news_filename;?>
                      </a>
                    <?php } ?>
                    <span class="help-block">Max 2Mb </span>
              </div> 
            </div> 

            <div class="row form-group">  
              <div class="col-md-3 control-label">Status</div>
              <div class="col-md-4">
                <select name="news_status" id="news_status" class="form-control select">
                  <?php
                  foreach ((array)cfg('status_tampil') as $k1 => $v1) {
                      $slc = isset($val)&&$val->news_status==$k1?'selected="selected"':'';
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