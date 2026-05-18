d<?php getFormSearch();?>
<div class="panel panel-default" style="margin-top:-10px;">
    <div class="panel-heading">
        <div class="panel-title-box">
            <h3>Tabel Data <?php echo isset($title)?$title:'';?></h3>
            <span>Data <?php echo isset($title)?$title:'';?> dari <?php echo cfg('app_name');?></span>
        </div>                                    
        <ul class="panel-controls" style="margin-top: 2px;">
            <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
            <li><a href="<?php echo current_url();?>" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>                                       
        </ul>
    </div>
    <div class="panel-body panel-body-table">

        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped">
               <thead>
                <tr>
                    <th width="30px">No</th>
                    <th width="30">Icon</th>
                    <th>Name</th>
                    <th>Description</th>
                    <?php if($this->jCfg['user']['is_all']==1){?>
                    <th>Community</th>
                    <?php } ?>
                    <th width="50">Status</th>
                    <th width="80">Action</th>
                </tr>
                </thead>
               <tbody>
                <?php if( count($data) > 0 ){
                    foreach($data as $r){
                ?>
                        <tr>
                            <td><?php echo ++$no;?></td>
                            <td>
                                <?php if( isset($r->badge_icon) && trim($r->badge_icon)!="" ){
                                
                                $image_thumb = get_new_image(array(
                                    "url"       => cfg('upload_path_badge')."/".$r->badge_icon,
                                    "url_klub"  => cfg('upload_path_badge')."/".$r->badge_icon,
                                    "folder"    => "setting",
                                    "width"     => 40,
                                    "height"    => 40,
                                    "site_id"   => $r->badge_site_id
                                ),true);
                                
                                $image_big = get_new_image(array(
                                    "url"       => cfg('upload_path_badge')."/".$r->badge_icon,
                                    "url_klub"  => cfg('upload_path_badge')."/".$r->badge_icon,
                                    "folder"    => "setting",
                                    "width"     => 500,
                                    "height"    => 400,
                                    "site_id"   => $r->badge_site_id
                                ),true);
                                
                            ?>
                              <a href="<?php echo $image_big;?>" title="Image Photo" class="act_modal" rel="700|400">
                                <img alt="" src="<?php echo $image_thumb;?>" style="height:25px;width:25px" class="img-polaroid">
                              </a>
                            <?php } ?>
                            
                                
                            </td>
                            <td><?php echo $r->badge_name;?></td>
                            <td><?php echo $r->badge_desc;?></td>
                            <?php if($this->jCfg['user']['is_all']==1){?>
                            <td><?php echo $r->site_name?></td>
                            <?php } ?>
                            <td><?php echo $r->badge_status==1?'<span class="label label-info">Tampil</span>':'<span class="label label-warning">Tidak</span>';?></td>
                            <td><?php link_action($links_table_item,"?_id="._encrypt($r->badge_id));?></td>
                        </tr>
                <?php } } ?>
                </tbody>
            </table>
            </div>
    </div>
</div>

<div class="pull-right">
            <?php echo isset($paging)?$paging:'';?>
</div>
