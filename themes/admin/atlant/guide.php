<?php js_validate();?>
<div class="panel panel-default tabs">                            
	<ul class="nav nav-tabs" role="tablist">
	    <li><a href="<?php echo site_url()?>" role="tab">Verifikasi Data Member</a></li>
	    <?php if( $this->jCfg['user']['is_all']==1 ){ ?>
        <?php /*<li><a href="<?php echo site_url('meme/me/dashboard')?>" role="tab">Dashboard Reporting</a></li>*/ ?>
	    <?php } ?>
	    <li class="active"><a href="javascript:void(0)" role="tab">Panduan</a></li>
	    <li><a href="<?php echo site_url('meme/me/news')?>" role="tab">Berita</a></li>
	</ul>                            
	<div class="panel-body tab-content">
	    <div class="tab-pane active">
            <h5 class="heading-form">Panduan SOS Managed Health Care</h5>
	        <div class="panel-body panel-body-table">			
		        <div class="table-responsive">
		            <table class="table table-hover table-bordered table-striped" id="guidance">
		               <thead>
		                <tr>
		                    <th width="30px">No</th>
		                    <th>Subject</th>
		                    <th>Attachement File</th>
		                    <th>Date Upload</th>
		                    <th>Upload By</th>
		                    <th>Last Update</th>
                        <?php if( $this->jCfg['user']['is_all'] == 1 ){ ?>
		                    <th>Updated By</th>
		                <?php }?>
		                </tr>
		                </thead>
		               <tbody>
		                <?php if( count($guide) > 0 ){
		                	$no = 0;
		                    foreach($guide as $r){
		                ?>
		                        <tr>
		                            <td><?php echo ++$no;?></td>
		                            <td><?php echo $r->panduan_name;?></td>
		                            <!-- <td></?php echo $r->panduan_filename==""?"-":"<a href='".base_url()."viewpdf?file=".$r->panduan_filename."' target='_blank'>".$r->panduan_filename."</a>";?></td> -->
		                            <td><?php echo $r->panduan_filename==""?'-':'<a href="javascript:void(0)" onclick="pdf_preview(\''._encrypt($r->panduan_filename).'\')">'.$r->panduan_filename.'</a>';?></td>
		                            <td><?php echo myDate($r->time_add);?></td>
		                            <td><?php echo get_user_name($r->user_add);?></td>
		                            <td><?php echo myDate($r->time_update);?></td>
		                        <?php if( $this->jCfg['user']['is_all'] == 1 ){ ?>
		                            <td><?php echo get_user_name($r->user_update);?></td>
		                        <?php }?>
		                        </tr>
		                <?php } } ?>
		                </tbody>
		            </table>
	            </div>
		    </div>
	    </div>
	</div>
</div>   
<script type="text/javascript">
var AJAX_URL = '<?php echo site_url("ajax/data");?>';
$(document).ready(function(){
    $('#panel-content-wrap').removeClass('panel');
	$('#panel-content-wrap').css('padding','0');
    $('#border-header').css('border','none');
	$('.heading-form').css('fontSize','16px');
}); 

function pdf_preview(filename) {
	var h = screen.height,
		w = screen.width;
	
	window.open("<?php echo $this->own_link."/pdf_preview/";?>?fl="+filename, "PDF Preview", "width=600,height=650,left="+((w-600)/2)+",top="+((h-700)/2));
}
</script>