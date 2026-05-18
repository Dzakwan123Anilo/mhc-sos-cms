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
							<select class="form-control select" name="actmember_clientid" id="actmember_clientid" onchange="getActiveMember()">
								<option value="">- all -</option>
							<?php 
								echo option_client('');
							?>
							</select>
						</div>       
						<div class="col-md-1 control-label">Period</div>
						<div class="col-md-5">
              <div class="input-group input-daterange">
                  <input type="text" id="actmember_my_from" name="actmember_my_from" class="form-control" placeholder="month-year"/>
                  <div class="input-group-addon">to</div>
                  <input type="text" id="actmember_my_to" name="actmember_my_to" class="form-control" placeholder="month-year"/>
              </div>
	            			<!--<div class="row">       
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
							</div>-->
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
							<select class="form-control select" name="swipeclient_clientid" id="swipeclient_clientid" onchange="getSwipeByClient()">
								<option value="">- all -</option>
							<?php 
								echo option_client('');
							?>
							</select>
						</div>            
						<div class="col-md-1 control-label">Period</div>
						<div class="col-md-5">
              <div class="input-group input-daterange">
                  <input type="text" id="swipeclient_my_from" name="swipeclient_my_from" class="form-control" placeholder="month-year"/>
                  <div class="input-group-addon">to</div>
                  <input type="text" id="swipeclient_my_to" name="swipeclient_my_to" class="form-control" placeholder="month-year"/>
              </div>
	            			<!--<div class="row">       
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
						--></div>
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
							<select class="form-control select" name="swipemore_clientid" id="swipemore_clientid" onchange="getSwipeMore()">
								<option value="">- all -</option>
							<?php 
								echo option_client('');
							?>
							</select>
						</div>  
						<div class="col-md-1 control-label">Period</div>
						<div class="col-md-5"> 
              <div class="input-group input-daterange">
                  <input type="text" id="swipemore_my_from" name="swipemore_my_from" class="form-control" placeholder="month-year"/>
                  <div class="input-group-addon">to</div>
                  <input type="text" id="swipemore_my_to" name="swipemore_my_to" class="form-control" placeholder="month-year"/>
              </div>
	            			<!--<div class="row">       
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
						--></div>       
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
							<select class="form-control select" name="swipeprovider_providerid" id="swipeprovider_providerid" onchange="getSwipeByProvider()">
								<option value="">- all -</option>
							<?php 
								echo option_provider('');
							?>
							</select>
						</div>  
						<div class="col-md-1 control-label">Period</div>
						<div class="col-md-5">      
              <div class="input-group input-daterange">
                  <input type="text" id="swipeprovider_my_from" name="swipeprovider_my_from" class="form-control" placeholder="month-year"/>
                  <div class="input-group-addon">to</div>
                  <input type="text" id="swipeprovider_my_to" name="swipeprovider_my_to" class="form-control" placeholder="month-year"/>
              </div>
	            			<!--<div class="row">       
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
						--></div>
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

var getActiveMemberState = true;
var getSwipeByClientState = true;
var getSwipeByProviderState = true;
var getSwipeMoreState = true;
var getUserLoginByProviderState = true;

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
	getActiveMember();
	getSwipeByClient();
	getSwipeByProvider();
	getSwipeMore();
	getUserLoginByProvider('');
});

function toNormalDate(date) {
  const [month, year] = date.split('-');

  return year + '-' + month;
}

$('#actmember_my_from, #actmember_my_to').change(function() {
  const actmember_my_from = $('#actmember_my_from').val();
  const actmember_my_to = $('#actmember_my_to').val();

  if (!actmember_my_from || !actmember_my_to) return false;

  if (toNormalDate(actmember_my_from) > toNormalDate(actmember_my_to)) {
    $('#actmember_my_from').val('');
    $('#actmember_my_to').val('');

    $('#actmember_my_from').change();
    $('#actmember_my_to').change();
    return alert('Start Date tidak boleh melebihi End Date');
  }

  getActiveMember();
})

$('#swipeclient_my_from, #swipeclient_my_to').change(function() {
  const swipeclient_my_from = $('#swipeclient_my_from').val();
  const swipeclient_my_to = $('#swipeclient_my_to').val();

  if (!swipeclient_my_from || !swipeclient_my_to) return false;

  if (toNormalDate(swipeclient_my_from) > toNormalDate(swipeclient_my_to)) {
    $('#swipeclient_my_from').val('');
    $('#swipeclient_my_to').val('');

    $('#swipeclient_my_from').change();
    $('#swipeclient_my_to').change();
    return alert('Start Date tidak boleh melebihi End Date');
  }
  
  return getSwipeByClient();
})

$('#swipemore_my_from, #swipemore_my_to').change(function() {
  const swipemore_my_from = $('#swipemore_my_from').val();
  const swipemore_my_to = $('#swipemore_my_to').val();

  if (!swipemore_my_from || !swipemore_my_to) return false;

  if (toNormalDate(swipemore_my_from) > toNormalDate(swipemore_my_to)) {
    $('#swipemore_my_from').val('');
    $('#swipemore_my_to').val('');

    $('#swipemore_my_from').change();
    $('#swipemore_my_to').change();
    return alert('Start Date tidak boleh melebihi End Date');
  }

  getSwipeMore();
})

$('#swipeprovider_my_from, #swipeprovider_my_to').change(function() {
  const swipeprovider_my_from = $('#swipeprovider_my_from').val();
  const swipeprovider_my_to = $('#swipeprovider_my_to').val();

  if (!swipeprovider_my_from || !swipeprovider_my_to) return false;

  if (toNormalDate(swipeprovider_my_from) > toNormalDate(swipeprovider_my_to)) {
    $('#swipeprovider_my_from').val('');
    $('#swipeprovider_my_to').val('');

    $('#swipeprovider_my_from').change();
    $('#swipeprovider_my_to').change();
    return alert('Start Date tidak boleh melebihi End Date');
  }

  console.log('TEST');

  getSwipeByProvider();
})

function getActiveMember() {
  let clientid =  '';
  let periodFrom = '<?php echo date('n-Y')?>';
  let periodTo = '<?php echo date('n-Y')?>';

  const actmember_clientid = $('#actmember_clientid').val();
  const actmember_my_from = $('#actmember_my_from').val();
  const actmember_my_to = $('#actmember_my_to').val();

  if (actmember_clientid) {
    clientid = actmember_clientid;
  }

  if (actmember_my_from && actmember_my_to) {
    periodFrom = actmember_my_from;
    periodTo = actmember_my_to;
  }

  if (!getActiveMemberState) return false;
  getActiveMemberState = false;
 
  $('#dash_two tbody').html('<tr><td colspan="6" style="text-align: center">loading...</td></tr>');
	$.post(AJAX_URL+"/get_active_member",{clientid:clientid,periodFrom:periodFrom,periodTo:periodTo},function(o){
		$('#dash_two tbody').html(o);
    getActiveMemberState = true;
	});
}
function getSwipeByClient() {
    let clientid =  '';
    let periodFrom = '<?php echo date('n-Y')?>';
    let periodTo = '<?php echo date('n-Y')?>';

    const swipeclient_clientid = $('#swipeclient_clientid').val();
    const swipeclient_my_from = $('#swipeclient_my_from').val();
    const swipeclient_my_to = $('#swipeclient_my_to').val();

    if (swipeclient_clientid) {
      clientid = swipeclient_clientid;
    }

    if (swipeclient_my_from && swipeclient_my_to) {
      periodFrom = swipeclient_my_from;
      periodTo = swipeclient_my_to;
    }

    if (!getSwipeByClientState) return false;
    getSwipeByClientState = false;

    $('#dash_three tbody').html('<tr><td colspan="8" style="text-align: center">loading...</td></tr>');
	$.post(AJAX_URL+"/get_swipe_by_client",{clientid:clientid,periodFrom:periodFrom,periodTo:periodTo},function(o){
		$('#dash_three tbody').html(o);
    getSwipeByClientState = true;
	});
}

function getSwipeMore() {
  let clientid =  '';
  let periodFrom = '<?php echo date('n-Y')?>';
  let periodTo = '<?php echo date('n-Y')?>';

  const swipemore_clientid = $('#swipemore_clientid').val();
  const swipemore_my_from = $('#swipemore_my_from').val();
  const swipemore_my_to = $('#swipemore_my_to').val();

  if (swipemore_clientid) {
    clientid = swipemore_clientid;
  }

  if (swipemore_my_from && swipemore_my_to) {
    periodFrom = swipemore_my_from;
    periodTo = swipemore_my_to;
  }

  if (!getSwipeMoreState) return false;
  getSwipeMoreState = false;

  $('#dash_five tbody').html('<tr><td colspan="8" style="text-align: center">loading...</td></tr>');
	$.post(AJAX_URL+"/get_swipe_more",{clientid:clientid,periodFrom:periodFrom,periodTo:periodTo},function(o){
		$('#dash_five tbody').empty().html(o);
    getSwipeMoreState = true;
	});
}
function getSwipeByProvider() {
  let providerid =  '';
  let periodFrom = '<?php echo date('n-Y')?>';
  let periodTo = '<?php echo date('n-Y')?>';

  const swipeprovider_providerid = $('#swipeprovider_providerid').val();
  const swipeprovider_my_from = $('#swipeprovider_my_from').val();
  const swipeprovider_my_to = $('#swipeprovider_my_to').val();

  if (swipeprovider_providerid) {
    providerid = swipeprovider_providerid;
  }

  if (swipeprovider_my_from && swipeprovider_my_to) {
    periodFrom = swipeprovider_my_from;
    periodTo = swipeprovider_my_to;
  }

  if (!getSwipeByProviderState) return false;
  getSwipeByProviderState = false;

  $('#dash_four tbody').html('<tr><td colspan="8" style="text-align: center">loading...</td></tr>');
	$.post(AJAX_URL+"/get_swipe_by_provider",{providerid:providerid,periodFrom:periodFrom,periodTo:periodTo},function(o){
		$('#dash_four tbody').empty().html(o);
    getSwipeByProviderState = true;
	});
}
function getUserLoginByProvider(providerid) {
    if (!getUserLoginByProviderState) return false;
    getUserLoginByProviderState = false;

    $('#dash_six tbody').html('<tr><td colspan="5" style="text-align: center">loading...</td></tr>');
	$.post(AJAX_URL+"/get_user_login",{providerid:providerid},function(o){
		$('#dash_six tbody').empty().html(o);
    getUserLoginByProviderState = true;
	});
}
</script>