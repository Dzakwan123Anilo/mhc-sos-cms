<form id="form-validated" class="form-horizontal" action="" method="post" class="input">
	<input type="hidden" name="page" id="page" value="">
	<?php getFormSearch();?>
	<div class="panel panel-default" style="margin-top:-10px;">
	    <div class="panel-heading">
	        <div class="panel-title-box">
	            <h3>Tabel Data <?php echo isset($title)?$title:'';?></h3>
	            <span>Data <?php echo isset($title)?$title:'';?> dari <?php echo cfg('app_name');?></span>
	        </div>                                    
	        <ul class="panel-controls" style="margin-top: 2px;">
	            <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>                                       
	        </ul>
	    </div>
	    <div class="panel-body panel-body-table">

	        <div class="table-responsive">
	            <table class="table table-hover table-bordered table-striped">
	               <thead>
	                <tr>
	                    <th width="30px">No</th>
	                    <th width="50px">Image</th>
	                    <th>Judul Berita</th>
	                    <th>Konten</th>
	                    <th>File</th>                    
	                    <th>Status</th>
	                    <th width="60">Action</th>
	                </tr>
	                </thead>
	               <tbody>
	                <?php if( count($data) > 0 ){
	                    foreach($data as $r){
	                   	 	$image_thumb = get_new_image(array(
								"url" 		=> cfg('upload_path_photo')."/".$r->news_image,
								"url_klub" 	=> cfg('upload_path_photo')."/".$r->news_image,
								"folder"	=> "photo",
								"width"		=> 40,
								"height"	=> 40,
								"site_id"	=> $r->news_site_id
							),true);
							
							$image_big = get_new_image(array(
								"url" 		=> cfg('upload_path_photo')."/".$r->news_image,
								"url_klub" 	=> cfg('upload_path_photo')."/".$r->news_image,
								"folder"	=> "photo",
								"site_id"	=> $r->news_site_id
							),true);
	                ?>
	                        <tr>
	                            <td><?php echo ++$no;?></td>
	                            <td><a href="<?php echo $image_big;?>" title="Image Photo" class="act_modal" rel="700|400"> <img alt="" src="<?php echo $image_thumb;?>" style="height:45px;width:45px" class="img-polaroid"></a></td>                   
	                            <td><?php echo $r->news_title?></td>
	                            <td><?php echo $r->news_content?></td>
	                            <td><a href="javascript:void(0)" onclick="pdf_preview('<?php echo _encrypt($r->news_filename);?>')"><?php echo $r->news_filename?></a></td>
	                            <td><?php echo $r->news_status==1?'<span class="label label-info">Tampil</span>':'<span class="label label-warning">Tidak Tampil</span>';?></td>
	                            <td><?php link_action($links_table_item,"?_id="._encrypt($r->news_id));?></td>
	                        </tr>
	                <?php } } ?>
	                </tbody>
	            </table>
	            </div>
	    </div>
	</div>
	<div class="pull-right" style="text-align: right;">
	    <?php echo $no>0?(isset($start)?("Showing ".($start+1)." to ".$no." of ".$cRec." entries"):""):"";?>
		<?php echo isset($paging)?$paging:'';?>
	</div>
</form>
<script type="text/javascript">
function pdf_preview(filename) {
	var h = screen.height,
		w = screen.width;
	
	window.open("<?php echo $this->own_link."/pdf_preview/";?>?fl="+filename, "PDF Preview", "width=600,height=650,left="+((w-600)/2)+",top="+((h-700)/2));
}

function page(pg) {
	$('#page').val(pg);
	$('#form-validated').submit();
}
</script>
