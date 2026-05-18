 <form action="" method="post" >
 	<input type="hidden" id="switch_search" name="switch_search" value="<?php echo $switch;?>">
 	<input type="hidden" id="load" name="load" value="<?php echo empty($load)?0:1;?>">
	  <div class="well">
	  		<div class="row" id="standard"<?php echo $switch==2?' style="display:none"':''?>>
	  			
	  			<?php if( count($this->cat_search) > 0 ){?>
	  			<div class="col-md-2">
		  			<select name="colum" class="form-control select" id="colum">
		  				<?php cat_search($this->cat_search);?>
		  			</select>
		  		</div>
	  			<?php }else{ ?> 
	  				<input type="hidden" name="colum" value="" />
	  			<?php } ?>
	  			<div class="col-md-2">
	  				<input type="text" class="form-control" id="keyword" name="keyword" value="<?php echo $this->jCfg['search']['keyword'];?>" placeholder="Keyword" autocomplete="off" />
	  			</div>
	  			
	  			<?php if($this->is_search_date==true){?>
	  			<div class="col-md-2" style="margin-top:0px;">
		  			<div class="input-group">
	                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
	                    <input type="text" id="date_start" name="date_start" class="form-control datepicker" placeholder="Tanggal Awal"  value="<?php echo isset($this->jCfg['search']['date_start'])?$this->jCfg['search']['date_start']:date("Y-m-d");?>" autocomplete="off" />                                            
	                </div>
	            </div>

	            <div class="col-md-2" style="margin-top:0px;">
		  			<div class="input-group">
	                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
	                    <input type="text" id="date_end" name="date_end" class="form-control datepicker" placeholder="Tanggal Akhir" value="<?php echo isset($this->jCfg['search']['date_end'])?$this->jCfg['search']['date_end']:date("Y-m-d");?>" autocomplete="off" />                                            
	                </div>
	            </div>
	            <?php } ?>

				
				<div class="col-md-2" style="margin-top:0px;">
		  			<input type="submit" value="Search!" style="margin-right:5px;" name="btn_search" id="btn_search"  class="btn btn-primary col-md-5" />
		  			<input type="submit" value="Reset!" name="btn_reset" id="btn_reset" class="btn btn-warning col-md-5" />
		  		</div>
		  		<?php
		  			switch($this->prefix_view) { 
		  				case 'member' : 
	  					case 'provider' :
	  					case 'transraw' : 
	  					case 'transclaim' : ?>
				<div class="col-md-2" style="margin:6px 0 0 -20px; font-size:14px;">	
		  			<a href="#" onclick="switch_search(2)">Switch to Advanced Search</a>
	  			</div>
				<?php 	default : '';
		  			} ?>
	  			<!--<div class="col-md-2 pull-right" style="margin-top:-16px;">
	  			  <div class="btn-group pull-right"> <a href="#" data-toggle="dropdown" class="btn btn-success dropdown-toggle btn-demo-space"> <span class="fa fa-download"></span>Download <span class="caret"></span> </a>
                    <ul class="dropdown-menu"> 
                      <li><a href="<?php echo $this->own_link;?>/export_data?type=html" target="_blank"><span class="fa fa-html5"></span> Html</a></li>
                      <li><a href="<?php echo $this->own_link;?>/export_data?type=excel"><span class="fa fa-table"></span> Excel</a></li>
                    </ul>
                  </div>
				</div>-->
	  		</div>
	  		
	  		
	  		<div class="row" id="advanced"<?php echo $switch==1?' style="display:none"':''?>>
	  		<?php
	  			switch($this->prefix_view) { 
	  				case 'member' : 
	  				case 'provider' :
	  				case 'transraw' : 
  					case 'transclaim' :
	  					getAdvancedSearch($this->prefix_view);
	  				default : '';
	  			}
	  		?>
		  		<br />
	  			<div class="row">
					<div class="col-md-2" style="margin-top:0px;">
			  			<input type="submit" value="Search!" style="margin-right:5px;" name="btn_search" id="btn_search"  class="btn btn-primary col-md-5" />
			  			<input type="submit" value="Reset!" name="btn_reset" id="btn_reset" class="btn btn-warning col-md-5" />
			  		</div>
					<div class="col-md-2" style="margin:6px 0 0 -20px; font-size:14px;">	
			  			<a href="#" onclick="switch_search(1)">Switch to Standard Search</a>
		  			</div>
	  			</div>
	  		</div>
	  	
	  </div>

</form>
<script type="text/javascript">
function switch_search(flag) {
	if(flag==1) {
		$('#advanced').hide();
		$('#standard').fadeIn('slow');
	} else {
		$('#standard').hide();
		$('#advanced').fadeIn('slow');
	}	
	$('#switch_search').val(flag);
}
</script>
