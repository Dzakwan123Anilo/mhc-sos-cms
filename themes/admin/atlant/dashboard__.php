<?php js_validate();?>
<div class="panel panel-default tabs">                            
	<ul class="nav nav-tabs" role="tablist">
	    <li><a href="<?php echo site_url('meme/me')?>" role="tab">Verifikasi Data Member</a></li>
	    <?php if( $this->jCfg['user']['is_all']==1 ){ ?>
	    <li class="active"><a href="javascript:void(0)" role="tab">Dashboard Reporting</a></li>
	    <?php } ?>
	    <li><a href="<?php echo site_url('meme/me/guide')?>" role="tab">Panduan</a></li>
	    <li><a href="<?php echo site_url('meme/me/news')?>" role="tab">Berita</a></li>
	</ul>                            
	<div class="panel-body tab-content">
	    <div class="tab-pane active">
			<div class="alert alert-warning" id="div_info_msg" style="padding:10px;margin:-10px 0px 15px 0px;display: none; ">
				<p id="info_export">Silahkan tunggu sedang memproses <b id="jumlah_progress">0</b> dari <b id="total_progress">0</b> data</p>
			</div>
	    	<div class="row">
				<div class="col-md-6">
            		<h5 class="heading-form">Chart Transaksi & History Transaksi</h5>       
            		<div class="row" style="height: 180px;">
						<div id="transaction-chart" style="height: 180px;"></div>
				    </div>
				</div>
				<div class="col-md-6">
            		<h5 class="heading-form"># of Active Members by Client</h5>       
            		<div class="row">       
						<div class="col-md-2 control-label">Client Name</div>
						<div class="col-md-4">
							<select class="form-control select" name="actmember_clientid" id="actmember_clientid" onchange="getActiveMember($(this).val(),$('#actmember_month').val(),$('#actmember_year').val())">
								<option value="">- all -</option>
							<?php 
								echo option_client('');
							?>
							</select>
						</div>       
						<div class="col-md-1 control-label">Period</div>
						<div class="col-md-5">       
	            			<div class="row">       
								<div class="col-md-7">
									<select class="form-control select" name="actmember_month" id="actmember_month" onchange="getActiveMember($('#actmember_clientid').val(),$(this).val(),$('#actmember_year').val())">
										<option value="">&nbsp;</option>
										<option value="<?php echo date('n');?>"><?php echo get_month(date('n'))?></option>
										<option value="<?php echo (date('n')-1==0?12:date('n')-1);?>"><?php echo (date('n')-1==0?get_month(12):get_month(date('n')-1))?></option>
										<option value="<?php echo (date('n')-2<=0?11:date('n')-2);?>"><?php echo (date('n')-2<=0?get_month(11):get_month(date('n')-2))?></option>
									</select>
								</div>
								<div class="col-md-5">
									<select class="form-control select" name="actmember_year" id="actmember_year" onchange="getActiveMember($('#actmember_clientid').val(),$('#actmember_month').val(),$(this).val())">
									<?php for($y=date('Y')-2;$y<(date('Y')+9);$y++) {
										$selected = $y==date('Y')?' selected="selected"':'';?>
										<option value="<?php echo $y?>"<?php echo $selected?>><?php echo $y?></option>
									<?php }?>
									</select>
								</div>
							</div>
						</div>
					</div><br />
					<div class="panel-body panel-body-table" style="height: 130px; overflow-y: scroll;">			
				        <div class="table-responsive">
				            <table class="table table-hover table-bordered table-striped" id="dash_two">
				               <thead>
				                <tr>
				                    <th width="30px">No</th>
				                    <th>Client Name</th>
				                    <th>Period</th>
				                    <th nowrap="nowrap">Main Insured</th>
				                    <th>Dependents</th>
				                    <th>Total</th>
				                </tr>
				                </thead>
				               <tbody>
				                </tbody>
				            </table>
			            </div>
				    </div>
				</div>
	    	</div> <br />
	    	<div class="row">
				<div class="col-md-6">
            		<h5 class="heading-form"># of Transaction by Client and by Type of Case</h5>
            		<div class="row">     
						<div class="col-md-2 control-label">Client Name</div>
						<div class="col-md-4">
							<select class="form-control select" name="swipeclient_clientid" id="swipeclient_clientid" onchange="getSwipeByClient($(this).val(),$('#swipeclient_month').val(),$('#swipeclient_year').val())">
								<option value="">- all -</option>
							<?php 
								echo option_client('');
							?>
							</select>
						</div>            
						<div class="col-md-1 control-label">Period</div>
						<div class="col-md-5">       
	            			<div class="row">       
								<div class="col-md-7">
									<select class="form-control select" name="swipeclient_month" id="swipeclient_month" onchange="getSwipeByClient($('#swipeclient_clientid').val(),$(this).val(),$('#swipeclient_year').val())">
										<option value="">&nbsp;</option>
										<option value="<?php echo date('n');?>"><?php echo get_month(date('n'))?></option>
										<option value="<?php echo (date('n')-1==0?12:date('n')-1);?>"><?php echo (date('n')-1==0?get_month(12):get_month(date('n')-1))?></option>
										<option value="<?php echo (date('n')-2<=0?11:date('n')-2);?>"><?php echo (date('n')-2<=0?get_month(11):get_month(date('n')-2))?></option>
									</select>
								</div>
								<div class="col-md-5">
									<select class="form-control select" name="swipeclient_year" id="swipeclient_year" onchange="getSwipeByClient($('#swipeclient_clientid').val(),$('#swipeclient_month').val(),$(this).val())">
									<?php for($y=date('Y')-2;$y<(date('Y')+9);$y++) {
										$selected = $y==date('Y')?' selected="selected"':'';?>
										<option value="<?php echo $y?>"<?php echo $selected?>><?php echo $y?></option>
									<?php }?>
									</select>
								</div>
							</div>
						</div>
					</div><br />
					<div class="panel-body panel-body-table" style="height: 150px; overflow-y: scroll;">			
				        <div class="table-responsive">
				            <table class="table table-hover table-bordered table-striped" id="dash_three">
				               <thead>
				                <tr>
				                    <th width="30px">No</th>
				                    <th>Client Name</th>
				                    <th>Period</th>
				                    <th nowrap="nowrap">In-Patient</th>
				                    <th nowrap="nowrap">Out-Patient</th>
				                    <th>Dental</th>
				                    <th>Emergency</th>
				                    <th>Total</th>
				                </tr>
				                </thead>
				               <tbody>
				                </tbody>
				            </table>
			            </div>
				    </div>
				</div>
				<div class="col-md-6">
            		<h5 class="heading-form"># of Members who Swipe the Card More Than Once a Day</h5>
            		<div class="row">       
						<div class="col-md-2 control-label">Client Name</div>
						<div class="col-md-4">
							<select class="form-control select" name="swipemore_clientid" id="swipemore_clientid" onchange="getSwipeMore($(this).val(),$('#swipemore_month').val(),$('#swipemore_year').val())">
								<option value="">- all -</option>
							<?php 
								echo option_client('');
							?>
							</select>
						</div>  
						<div class="col-md-1 control-label">Period</div>
						<div class="col-md-5">       
	            			<div class="row">       
								<div class="col-md-7">
									<select class="form-control select" name="swipemore_month" id="swipemore_month" onchange="getSwipeMore($('#swipemore_clientid').val(),$(this).val(),$('#swipemore_year').val())">
										<option value="">&nbsp;</option>
										<option value="<?php echo date('n');?>"><?php echo get_month(date('n'))?></option>
										<option value="<?php echo (date('n')-1==0?12:date('n')-1);?>"><?php echo (date('n')-1==0?get_month(12):get_month(date('n')-1))?></option>
										<option value="<?php echo (date('n')-2<=0?11:date('n')-2);?>"><?php echo (date('n')-2<=0?get_month(11):get_month(date('n')-2))?></option>
									</select>
								</div>
								<div class="col-md-5">
									<select class="form-control select" name="swipemore_year" id="swipemore_year" onchange="getSwipeMore($('#swipemore_clientid').val(),$('#swipemore_month').val(),$(this).val())">
									<?php for($y=date('Y')-2;$y<(date('Y')+9);$y++) {
										$selected = $y==date('Y')?' selected="selected"':'';?>
										<option value="<?php echo $y?>"<?php echo $selected?>><?php echo $y?></option>
									<?php }?>
									</select>
								</div>
							</div>
						</div>       
					</div><br />
					<div class="panel-body panel-body-table" style="height: 150px; overflow-y: scroll;">			
				        <div class="table-responsive">
				            <table class="table table-hover table-bordered table-striped" id="dash_five">
				               <thead>
				                <tr>
				                    <th width="30px">No</th>
				                    <th>Client Name</th>
				                    <th>Period</th>
				                    <th nowrap="nowrap">In-Patient</th>
				                    <th nowrap="nowrap">Out-Patient</th>
				                    <th>Dental</th>
				                    <th>Emergency</th>
				                    <th>Total</th>
				                </tr>
				                </thead>
				               <tbody>
				                </tbody>
				            </table>
			            </div>
				    </div>
				</div>
	    	</div> <br />
	    	<div class="row">
				<div class="col-md-6">
            		<h5 class="heading-form"># of Transaction Providers and by Type of Case</h5>
            		<div class="row">       
						<div class="col-md-2 control-label">Provider Name</div>
						<div class="col-md-4">
							<select class="form-control select" name="swipeprovider_providerid" id="swipeprovider_providerid" onchange="getSwipeByProvider($(this).val(),$('#swipeprovider_month').val(),$('#swipeprovider_year').val())">
								<option value="">- all -</option>
							<?php 
								echo option_provider('');
							?>
							</select>
						</div>  
						<div class="col-md-1 control-label">Period</div>
						<div class="col-md-5">       
	            			<div class="row">       
								<div class="col-md-7">
									<select class="form-control select" name="swipeprovider_month" id="swipeprovider_month" onchange="getSwipeByProvider($('#swipeprovider_providerid').val(),$(this).val(),$('#swipeprovider_year').val())">
										<option value="">&nbsp;</option>
										<option value="<?php echo date('n');?>"><?php echo get_month(date('n'))?></option>
										<option value="<?php echo (date('n')-1==0?12:date('n')-1);?>"><?php echo (date('n')-1==0?get_month(12):get_month(date('n')-1))?></option>
										<option value="<?php echo (date('n')-2<=0?11:date('n')-2);?>"><?php echo (date('n')-2<=0?get_month(11):get_month(date('n')-2))?></option>
									</select>
								</div>
								<div class="col-md-5">
									<select class="form-control select" name="swipeprovider_year" id="swipeprovider_year" onchange="getSwipeByProvider($('#swipeprovider_providerid').val(),$('#swipeprovider_month').val(),$(this).val())">
									<?php for($y=date('Y')-2;$y<(date('Y')+9);$y++) {
										$selected = $y==date('Y')?' selected="selected"':'';?>
										<option value="<?php echo $y?>"<?php echo $selected?>><?php echo $y?></option>
									<?php }?>
									</select>
								</div>
							</div>
						</div>
					</div><br />
					<div class="panel-body panel-body-table" style="height: 200px; overflow-y: scroll;">			
				        <div class="table-responsive">
				            <table class="table table-hover table-bordered table-striped" id="dash_four">
				               <thead>
				                <tr>
				                    <th width="30px">No</th>
				                    <th>Provider Name</th>
				                    <th>Period</th>
				                    <th nowrap="nowrap">In-Patient</th>
				                    <th nowrap="nowrap">Out-Patient</th>
				                    <th>Dental</th>
				                    <th>Emergency</th>
				                    <th>Total</th>
				                </tr>
				                </thead>
				               <tbody>
				                </tbody>
				            </table>
			            </div>
				    </div>
				</div>
				<div class="col-md-6">
            		<h5 class="heading-form"># of Users Who Login (Hit) to Web Claim System by Providers</h5>
            		<div class="row">       
						<div class="col-md-2 control-label">Provider Name</div>
						<div class="col-md-4">
							<select class="form-control select" name="login_providerid" id="login_providerid" onchange="getUserLoginByProvider($(this).val())">
								<option value="">- all -</option>
							<?php 
								echo option_provider('');
							?>
							</select>
						</div>     
					</div><br />
					<div class="panel-body panel-body-table" style="height: 200px; overflow-y: scroll;">			
				        <div class="table-responsive">
				            <table class="table table-hover table-bordered table-striped" id="dash_six">
				               <thead>
				                <tr>
				                    <th width="30px">No</th>
				                    <th>Provider Name</th>
				                    <th>Prev. Mont</th>
				                    <th>Curr. Mont</th>
				                    <th>Today</th>
				                </tr>
				                </thead>
				               <tbody>
				                </tbody>
				            </table>
			            </div>
				    </div>
				</div>
	    	</div>
	    </div>
	</div>
</div>   
<script type="text/javascript">
var URL_EXPORT_DATA_ACTIVE_MEMBER = '<?php echo $own_links;?>/export_data_active_member';
var URL_EXPORT_DATA_SWIPE_BY_CLIENT = '<?php echo $own_links;?>/export_data_swipe_by_number';
var URL_EXPORT_DATA_SWIPE_MORE = '<?php echo $own_links;?>/export_data_swipe_more';
var URL_EXPORT_DATA_SWIPE_BY_PROVIDER = '<?php echo $own_links;?>/export_data_swipe_by_provider';
var URL_EXPORT_DATA_USER_LOGIN = '<?php echo $own_links;?>/export_data_user_login';
var AJAX_URL = '<?php echo site_url("ajax/data");?>';
$(document).ready(function(){
    $('#panel-content-wrap').removeClass('panel');
	$('#panel-content-wrap').css('padding','0');
    $('#border-header').css('border','none');
	$('.heading-form').css('fontSize','16px');
	dt = $.ajax({type:'GET', url:AJAX_URL+'/get_transactions/',async:false}).responseText,
	data = JSON.parse(dt);
	var morrisCharts = function() {
	    Morris.Bar({
	        element: 'transaction-chart',
	        data: data,
	        xkey: 0,
	        ykeys: [1,2,3],
	        labels: ['Week', 'Month', 'Year'],
	        barColors: ['#953202', '#4B7407', '#23788B']
	    });
	}();
	getActiveMember('','','<?php echo date('Y')?>');
	getSwipeByClient('','','<?php echo date('Y')?>');
	getSwipeByProvider('','','<?php echo date('Y')?>');
	getSwipeMore('','','<?php echo date('Y')?>');
	getUserLoginByProvider('');
}); 

function getActiveMember(clientid,month,year) {
    $('#dash_two tbody').html('<tr><td colspan="6" style="text-align: center">loading...</td></tr>');
	$.post(AJAX_URL+"/get_active_member",{clientid:clientid,month:month,year:year},function(o){
		$('#dash_two tbody').html(o);
	});
}
function getSwipeByClient(clientid,month,year) {
    $('#dash_three tbody').html('<tr><td colspan="8" style="text-align: center">loading...</td></tr>');
	$.post(AJAX_URL+"/get_swipe_by_client",{clientid:clientid,month:month,year:year},function(o){
		$('#dash_three tbody').html(o);
	});
}
function getSwipeMore(clientid,month,year) {
    $('#dash_five tbody').html('<tr><td colspan="8" style="text-align: center">loading...</td></tr>');
	$.post(AJAX_URL+"/get_swipe_more",{clientid:clientid,month:month,year:year},function(o){
		$('#dash_five tbody').empty().html(o);
	});
}
function getSwipeByProvider(providerid,month,year) {
    $('#dash_four tbody').html('<tr><td colspan="8" style="text-align: center">loading...</td></tr>');
	$.post(AJAX_URL+"/get_swipe_by_provider",{providerid:providerid,month:month,year:year},function(o){
		$('#dash_four tbody').empty().html(o);
	});
}
function getUserLoginByProvider(providerid) {
    $('#dash_six tbody').html('<tr><td colspan="5" style="text-align: center">loading...</td></tr>');
	$.post(AJAX_URL+"/get_user_login",{providerid:providerid},function(o){
		$('#dash_six tbody').empty().html(o);
	});
}
</script>