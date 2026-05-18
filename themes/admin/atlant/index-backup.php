<?php js_validate();?>
<div class="panel panel-default tabs">                            
	<ul class="nav nav-tabs" role="tablist">
	    <li class="active"><a href="#tab-first" role="tab" data-toggle="tab">Verifikasi Data Member</a></li>
	    <li><a href="#tab-second" role="tab" data-toggle="tab">Dashboard Reporting</a></li>
	    <li><a href="#tab-third" role="tab" data-toggle="tab">Panduan</a></li>
	    <li><a href="#tab-fourth" role="tab" data-toggle="tab">Berita</a></li>
	</ul>                            
	<div class="panel-body tab-content">
	    <div class="tab-pane" id="tab-first">
	  		<div class="well">
				<form id="form-validated" action="<?php echo $own_links;?>" class="form-horizontal" method="post"> 
					<div class="row">         
	          			<div class="col-md-12">   
				            <div class="row form-group">  
								<div class="col-md-4 control-label">Nama Client</div>
								<div class="col-md-4">
								    <select class="form-control select" name="verify_clientid" id="verify_clientid">
								        <?php 
	                        				$cli = isset($val)?$val->member_clientid:'';
									        echo option_client($cli);
								        ?>
								    </select>  
								</div>
				            </div> 
				            
				            <div class="row form-group">  
								<div class="col-md-4 control-label">Nomor Kartu</div>
								<div class="col-md-4">        
									<div class="input-group">
		                				<input type="text" id="verify_cardno" name="verify_cardno" class="validate[required,custom[ajaxVerification]] form-control" value="<?php echo isset($val)?$val->member_cardno:'';?>" />
									    <span class="input-group-btn">
					                    	<button type="submit" name="btn_show" id="btn_show" class="btn btn-primary"><i class="fa fa-search"></i></button>
									    </span>
									</div>      
								</div>
				            </div> 
			            </div>
					</div>
				</form>
			</div>
		<?php if(isset($val)) {?>	
			<div class="row" id="member_detail">
	            <h5 class="heading-form">Member Detail</h5>
	          <div class="col-md-6">
	
	            <div class="row form-group">  
	              <div class="col-md-3 control-label">No. Kartu</div>
	              <div class="col-md-9 control-label">: <b><?php echo $val->member_cardno;?></b></div>
	            </div>
	
	            <div class="row form-group">  
	              <div class="col-md-3 control-label">Nama Member</div>
	              <div class="col-md-9 control-label">: <b><?php echo $val->member_name;?></b></div>
	            </div>
	
	            <div class="row form-group">  
	              <div class="col-md-3 control-label">Jenis Kelamin</div>
	              <div class="col-md-9 control-label">: <b><?php echo $val->member_sex=='M'?'Laki-laki':'Perempuan';?></b></div>
	            </div>
	            
	            <div class="row form-group">  
	              <div class="col-md-3 control-label">Tanggal Lahir</div>
	              <div class="col-md-9 control-label">: <b><?php echo myDate($val->member_birthdate,"d M Y",false);?></b></div>
	            </div>
	
	            <div class="row form-group">  
	              <div class="col-md-3 control-label">Perusahaan</div>
	              <div class="col-md-9 control-label">: <b><?php echo $val->client_name;?></b></div>
	            </div>
	            
	          </div> 
	
	          <div class="col-md-6">
	
	            <div class="row form-group">  
	              <div class="col-md-3 control-label">Tipe Relasi</div>
	              <div class="col-md-9 control-label">: <b><?php echo $val->member_relationshiptype;?></b></div>
	            </div>
	
	            <div class="row form-group">  
	              <div class="col-md-3 control-label">Status Karyawan</div>
	              <div class="col-md-9 control-label">: <b><?php echo $val->member_employmentstatus;?></b></div>
	            </div>
	            
	            <div class="row form-group">  
	              <div class="col-md-3 control-label">Status Kartu</div>
	              <div class="col-md-9 control-label">: <b><?php echo $val->member_statuscard;?></b></div>
	            </div>
	            
	            <div class="row form-group">  
	              <div class="col-md-3 control-label">Tanggal Aktifasi</div>
	              <div class="col-md-9 control-label">: <b><?php echo !empty($val->member_activationdate)&&$val->member_activationdate!='0000-00-00'?date('d-m-Y', strtotime($val->member_activationdate)):'';?></b></div>
	            </div>
	            
	          </div>
	
	        </div>
	        <br />
			<div class="row" id="benefit">
	            <h5 class="heading-form">Benefit</h5>
	          <div class="col-md-6">
	
	            <div class="row form-group">  
	              <div class="col-md-3 control-label">Hak Akses Pelayanan</div>
	              <div class="col-md-9 control-label">: <b><?php echo $val->plan_description;?></b></div>
	            </div>
	
	            <div class="row form-group">  
	              <div class="col-md-3 control-label">Dental Limit</div>
	              <div class="col-md-9 control-label">: <b><?php echo $val->member_dentallimit;?></b></div>
	            </div>
	            
	          </div>
	
	        </div>
	        <br />
	        <div class="row" id="member_list">	          
	            <h5 class="heading-form">Family Member List</h5>
			      <div class="panel-body panel-body-table">
			
			        <div class="table-responsive">
			            <table class="table table-hover table-bordered table-striped">
			               <thead>
			                <tr>
			                    <th width="30px">No</th>
			                    <th>Client</th>
			                    <th>No. Kartu</th>
			                    <th>Nama Member</th>
			                    <th>Tipe Relasi</th>
			                    <th>Jenis Kelamin</th>
			                    <th>Tgl. Lahir</th>
			                    <th>Propinsi</th>
			                    <th>Kota/Kabupaten</th>
			                    <th>Hak Akses Pelayanan</th>
			                    <th>Status Karyawan</th>
			                    <th>Status Kartu</th>
			                    <th>Dental Limit</th>
			                </tr>
			                </thead>
			               <tbody>
			                <?php if( count($data) > 0 ){
			                	$no = 0;
			                    foreach($data as $r){
			                ?>
			                        <tr>
			                            <td><?php echo ++$no;?></td>
			                            <td><?php echo $r->client_name;?></td>
			                            <td><?php echo $r->member_cardno;?></td>
			                            <td><?php echo $r->member_name;?></td>
			                            <td><?php echo $r->member_relationshiptype;?></td>
			                            <td><?php echo $r->member_sex=='M'?'Laki-laki':'Perempuan';?></td>
			                            <td><?php echo myDate($r->member_birthdate,"d M Y",false);?></td>
			                            <td><?php echo $r->member_region;?></td>
			                            <td><?php echo $r->member_location;?></td>
			                            <td><?php echo $r->plan_description;?></td>
			                            <td><?php echo $r->member_employmentstatus;?></td>
			                            <td><?php echo $r->member_statuscard;?></td>
			                            <td><?php echo $r->member_dentallimit;?></td>
			                        </tr>
			                <?php } } ?>
			                </tbody>
			            </table>
			            </div>
			    </div>
	        </div>
	        <br /><br />
	        <div class="row" id="form_verify">
	        	<div class="well">
					<form id="form-validated" action="<?php echo $own_links;?>/save_claim" class="form-horizontal" method="post">
						<input type="hidden" id="claim_planid" name="claim_planid" value="<?php echo _encrypt($val->plan_id);?>">
						<input type="hidden" id="claim_memberid" name="claim_memberid" value="<?php echo _encrypt($val->member_id);?>">
						<input type="hidden" id="claim_clientid" name="claim_clientid" value="<?php echo _encrypt($val->client_id);?>">
						<input type="hidden" id="claim_relationshiptype" name="claim_relationshiptype" value="<?php echo _encrypt($val->member_relationshiptype);?>">
						<input type="hidden" id="claim_roomclass" name="claim_roomclass" value="<?php echo _encrypt($val->plan_description);?>"> 
						<div class="row">       
							<div class="col-md-2 control-label">Jenis Layanan Klaim</div>
							<div class="col-md-4">
								<select class="form-control select" name="claim_servicetype" id="claim_servicetype">
									<option value="">- pilih jenis layanan -</option>
								<?php 
									echo option_servicetype('');
								?>
								</select>
							</div>
							<div class="col-md-2">
								<!-- <input type="submit" value="Kirim Klaim" style="margin-right:5px;" name="btn_claim" id="btn_claim" class="btn btn-primary" /> -->
								<input type="button" value="Kirim Klaim" style="margin-right:5px;" name="btn_claim" id="btn_claim" class="btn btn-primary" disabled="disabled" />
							</div>
						</div>
					</form>
				</div>
				<h5 class="heading-form">Klaim Histories</h5>
				<div class="panel-body panel-body-table">		
					<div class="table-responsive">
						<table class="table table-hover table-bordered table-striped" id="thistable">
							<thead>
								<tr>
									<th width="30px">No</th>
									<th>Nama Member</th>
									<th>Tipe Layanan</th>
									<th>Tgl. Claim</th>
									<th>Deskipsi</th>
									<th>Status Claim</th>
									<th width="30px">#</th>
								</tr>
							</thead>
							<tbody>
							<?php 
							if( count($history) > 0 ){
								$no=0;
								foreach($history as $r){
							?>
								<tr>
									<td><?php echo ++$no;?>
										<input type="hidden" id="date_<?php echo $r->claimId;?>" value="<?php echo _encrypt($r->date_claim);?>">
										<input type="hidden" id="planid_<?php echo $r->claimId;?>" value="<?php echo _encrypt($r->plan_id);?>">
										<input type="hidden" id="memberid_<?php echo $r->claimId;?>" value="<?php echo _encrypt($r->member_id);?>">
										<input type="hidden" id="clientid_<?php echo $r->claimId;?>" value="<?php echo _encrypt($r->client_id);?>">
										<input type="hidden" id="relationshiptype_<?php echo $r->claimId;?>" value="<?php echo _encrypt($r->member_relationshiptype);?>">
										<input type="hidden" id="roomclass_<?php echo $r->claimId;?>" value="<?php echo _encrypt($r->plan_description);?>">
										<input type="hidden" id="servicetype_<?php echo $r->claimId;?>" value="<?php echo $r->servicetypeId;?>">
									</td>
									<td nowrap="nowrap"><?php echo $r->member_name;?></td>
									<td nowrap="nowrap"><?php echo $r->service_type;?></td>
									<td nowrap="nowrap"><?php echo myDate($r->date_claim,"d M Y H:i:s",false)?></td>
									<td nowrap="nowrap"><?php echo $r->description;?></td>
									<td><?php echo $r->claim_status;?></td>
									<td><button class="btn btn-condensed btn-primary" data-toggle="dropdown" onclick="re_print('<?php echo $r->claimId;?>')"><i class="glyphicon glyphicon-print"></i></button></td>
								</tr>
							<?php } 
							} ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php }?>
	    </div>
	    <div class="tab-pane active" id="tab-second">
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
							<select class="form-control select" name="actmember_client" id="actmember_client" onchange="getActiveMember($(this).val(),$('#actmember_period').val())">
								<option value="">- all -</option>
							<?php 
								echo option_client('');
							?>
							</select>
						</div>       
						<div class="col-md-2 control-label">Period</div>
						<div class="col-md-4">
							<select class="form-control select" name="actmember_period" id="actmember_period" onchange="getActiveMember($('#actmember_client').val(),$(this).val())">
								<option value="">&nbsp;</option>
								<option value="<?php echo date('n');?>"><?php echo get_month(date('n'))?></option>
								<option value="<?php echo date('n')-1;?>"><?php echo get_month(date('n')-1)?></option>
								<option value="<?php echo date('n')-2;?>"><?php echo get_month(date('n')-2)?></option>
							</select>
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
				                <?php if( count($activemember) > 0 ){
				                	$no = 0; $cname = '';
				                    foreach($activemember as $r){
				                ?>
				                        <tr>
				                            <td><?php echo ++$no;?></td>
				                            <td nowrap="nowrap"><?php echo $cname!=$r->client_name?$r->client_name:'';?></td>
				                            <td nowrap="nowrap"><?php echo $r->showit;?></td>
				                            <td><?php echo $r->main_insured;?></td>
				                            <td><?php echo $r->dependent;?></td>
				                            <td><?php echo $r->main_insured+$r->dependent;?></td>
				                        </tr>
				                <?php $cname = $r->client_name; 
				                    } 
				                } ?>
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
							<select class="form-control select" name="swipeclient_clientid" id="swipeclient_clientid" onchange="getSwipeByClient($(this).val(),$('#actmember_period').val())">
								<option value="">- all -</option>
							<?php 
								echo option_client('');
							?>
							</select>
						</div>       
						<div class="col-md-2 control-label">Period</div>
						<div class="col-md-4">
							<select class="form-control select" name="swipeclient_period" id="swipeclient_period" onchange="getSwipeByClient($('#swipeclient_clientid').val(),$(this).val())">
								<option value="">&nbsp;</option>
								<option value="<?php echo date('n');?>"><?php echo get_month(date('n'))?></option>
								<option value="<?php echo date('n')-1;?>"><?php echo get_month(date('n')-1)?></option>
								<option value="<?php echo date('n')-2;?>"><?php echo get_month(date('n')-2)?></option>
							</select>
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
				                <?php if( count($swipeclient) > 0 ){
				                	$no = 0; $cname = '';
				                    foreach($swipeclient as $r){
				                ?>
				                        <tr>
				                            <td><?php echo ++$no;?></td>
				                            <td nowrap="nowrap"><?php echo $cname!=$r->client_name?$r->client_name:'';?></td>
				                            <td nowrap="nowrap"><?php echo $r->showit;?></td>
				                            <td><?php echo $r->inpatient;?></td>
				                            <td><?php echo $r->outpatient;?></td>
				                            <td><?php echo $r->dental;?></td>
				                            <td><?php echo $r->emergency;?></td>
				                            <td><?php echo $r->inpatient+$r->outpatient+$r->dental+$r->emergency;?></td>
				                        </tr>
				                <?php $cname = $r->client_name; 
				                    } 
				                } ?>
				                </tbody>
				            </table>
			            </div>
				    </div>
				</div>
				<div class="col-md-6">
            		<h5 class="heading-form"># of Members who Swipe the Card More Than Once a Day (In-Patient case)</h5>
            		<div class="row">       
						<div class="col-md-2 control-label">Client Name</div>
						<div class="col-md-4">
							<select class="form-control select" name="swipemore_clientid" id="swipemore_clientid" onchange="getSwipeMore($(this).val(),$('#swipemore_period').val())">
								<option value="">- all -</option>
							<?php 
								echo option_client('');
							?>
							</select>
						</div>       
						<div class="col-md-2 control-label">Period</div>
						<div class="col-md-4">
							<select class="form-control select" name="swipemore_period" id="swipemore_period" onchange="getSwipeMore($('#swipemore_clientid').val(),$(this).val())">
								<option value="">&nbsp;</option>
								<option value="<?php echo date('n');?>"><?php echo get_month(date('n'))?></option>
								<option value="<?php echo date('n')-1;?>"><?php echo get_month(date('n')-1)?></option>
								<option value="<?php echo date('n')-2;?>"><?php echo get_month(date('n')-2)?></option>
							</select>
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
				                    <th nowrap="nowrap"># of Member</th>
				                </tr>
				                </thead>
				               <tbody>
				                <?php if( count($swipemore) > 0 ){
				                	$no = 0; $cname = '';
				                    foreach($swipemore as $r){
				                ?>
				                        <tr>
				                            <td><?php echo ++$no;?></td>
				                            <td nowrap="nowrap"><?php echo $cname!=$r->client_name?$r->client_name:'';?></td>
				                            <td nowrap="nowrap"><?php echo $r->showit;?></td>
				                            <td><?php echo $r->numswip;?></td>
				                        </tr>
				                <?php $cname = $r->client_name;
				                    } 
				                } ?>
				                </tbody>
				            </table>
			            </div>
				    </div>
				</div>
	    	</div> <br />
	    	<div class="row">
				<div class="col-md-6">
            		<h5 class="heading-form"># of Transaction by Top 10 Providers and by Type of Case</h5>
            		<div class="row">       
						<div class="col-md-2 control-label">Provider Name</div>
						<div class="col-md-4">
							<select class="form-control select" name="swipeprovider_providerid" id="swipeprovider_providerid" onchange="getSwipeByProvider($(this).val(),$('#swipeprovider_period').val())">
								<option value="">- all -</option>
							<?php 
								echo option_provider('');
							?>
							</select>
						</div>       
						<div class="col-md-2 control-label">Period</div>
						<div class="col-md-4">
							<select class="form-control select" name="swipeprovider_period" id="swipeprovider_period" onchange="getSwipeByProvider($('#swipeprovider_providerid').val(),$(this).val())">
								<option value="">&nbsp;</option>
								<option value="<?php echo date('n');?>"><?php echo get_month(date('n'))?></option>
								<option value="<?php echo date('n')-1;?>"><?php echo get_month(date('n')-1)?></option>
								<option value="<?php echo date('n')-2;?>"><?php echo get_month(date('n')-2)?></option>
							</select>
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
				                <?php if( count($swipeprovider) > 0 ){
				                	$xno = 0; $pname = '';
				                    foreach($swipeprovider as $r){
				                    	$month = date('n');
				                    	$param = array('providerid' => $r->provider_id, 'flag' => false, 'multi' => 3);		
										$html = ''; $m = 2;
										for($i=0; $i<3; $i++) {
											$param['month'] = $month-$m;
											$data = getSingleNumberSwipeByProvider($param);
											$pname = ''; $no = $xno;
											foreach($data as $s){
												$html .= '<tr>';
												$html .= '	<td>'.++$no.'</td>';
												$html .= '	<td nowrap="nowrap">'.$s->provider_name.'</td>';
												$html .= '	<td nowrap="nowrap">'.$s->showit.'</td>';
												$html .= '	<td>'.$s->inpatient.'</td>';
												$html .= '	<td>'.$s->outpatient.'</td>';
												$html .= '	<td>'.$s->dental.'</td>';
												$html .= '	<td>'.$s->emergency.'</td>';
												$html .= '	<td>'.$s->total.'</td>';
												$html .= '</tr>';
											}
											$xno = $no;
											$m--;
										}
										echo $html; 
				                    } 
				                } ?>
				                </tbody>
				            </table>
			            </div>
				    </div>
				</div>
				<div class="col-md-6">
            		<h5 class="heading-form"># of Users Who Login (Hit) to Web Claim System by Top 10 Providers</h5>
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
				                <?php if( count($hitlogin) > 0 ){
				                	$no = 0; $pname = '';
				                    foreach($hitlogin as $r){
				                ?>
				                        <tr>
				                            <td><?php echo ++$no;?></td>
				                            <td nowrap="nowrap"><?php echo $pname!=$r->provider_name?$r->provider_name:'';?></td>
				                            <td nowrap="nowrap"><?php echo $r->prev;?></td>
				                            <td nowrap="nowrap"><?php echo $r->curr;?></td>
				                            <td nowrap="nowrap"><?php echo $r->today;?></td>
				                        </tr>
				                <?php $pname = $r->provider_name;
				                    } 
				                } ?>
				                </tbody>
				            </table>
			            </div>
				    </div>
				</div>
	    	</div>
	    </div>
	    <div class="tab-pane" id="tab-third">
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
	    <div class="tab-pane" id="tab-fourth">
            <h5 class="heading-form">Berita SOS Managed Health Care</h5>
	        <div class="panel-body panel-body-table">			
		        <div class="table-responsive">
		            <table class="table table-hover table-bordered table-striped" id="news">
		               <thead>
		                <tr>
		                    <th width="30px">No</th>
		                    <th>Subject</th>
		                    <th>Attachement File</th>
		                    <th>Date Upload</th>
		                    <th>Upload By</th>
		                    <th>Last Update</th>
		                </tr>
		                </thead>
		               <tbody>
		                <?php if( count($news) > 0 ){
		                	$no = 0;
		                    foreach($news as $r){
		                ?>
		                        <tr>
		                            <td><?php echo ++$no;?></td>
		                            <td><?php echo $r->news_title;?></td>
		                            <!-- <td></?php echo $r->news_filename==""?"-":"<a href='".base_url()."viewpdf?file=".$r->news_filename."' target='_blank'>".$r->news_filename."</a>";?></td> -->
		                            <td><?php echo $r->news_filename==""?'-':'<a href="javascript:void(0)" onclick="pdf_preview(\''._encrypt($r->news_filename).'\')">'.$r->news_filename.'</a>';?></td>
		                            <td><?php echo myDate($r->time_add);?></td>
		                            <td><?php echo get_user_name($r->user_add);?></td>
		                            <td><?php echo myDate($r->time_update);?></td>
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
	$('#verify_cardno').keyup(function(){
		$('#member_detail, #benefit, #member_list, #form_verify').hide();
	});
	$('#claim_servicetype').change(function(){
		if($(this).val()=="") $('#btn_claim').attr('disabled','disabled');
		else $('#btn_claim').removeAttr('disabled');
	});
	$('#btn_claim').click(function(){
		var planid = $('#claim_planid').val(),
			memberid = $('#claim_memberid').val(),
			clientid = $('#claim_clientid').val(),
			relationtype = $('#claim_relationshiptype').val(),
			roomclass = $('#claim_roomclass').val(),
			servicetype = $('#claim_servicetype').val(),
			h = screen.height,
			w = screen.width;

		window.open("<?php echo $this->own_link."/print_preview/";?>?save=1&pid="+planid+"&mid="+memberid+"&cid="+clientid+"&rt="+relationtype+"&rc="+roomclass+"&st="+servicetype, "Print Preview", "width=400,height=600,left="+((w-400)/2)+",top="+((h-650)/2));
	});
	
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
	$('#tab-first').removeAttr('class').attr('class','tab-pane active');
	$('#tab-second').removeAttr('class').attr('class','tab-pane');
}); 

function pdf_preview(filename) {
	var h = screen.height,
		w = screen.width;
	
	window.open("<?php echo $this->own_link."/pdf_preview/";?>?fl="+filename, "PDF Preview", "width=600,height=650,left="+((w-600)/2)+",top="+((h-700)/2));
}
function re_print(idx){
	if (typeof win != "undefined") win.close();
	var date = $('#date_'+idx).val(),
    	planid = $('#planid_'+idx).val(),
		memberid = $('#memberid_'+idx).val(),
		clientid = $('#clientid_'+idx).val(),
		relationtype = $('#relationshiptype_'+idx).val(),
		roomclass = $('#roomclass_'+idx).val(),
		servicetype = $('#servicetype_'+idx).val(),
		h = screen.height,
		w = screen.width;

	win = window.open("<?php echo $this->own_link."/print_preview/";?>?save=0&pid="+planid+"&dt="+date+"&mid="+memberid+"&cid="+clientid+"&rt="+relationtype+"&rc="+roomclass+"&st="+servicetype, "Print Preview", "width=400,height=600,left="+((w-400)/2)+",top="+((h-650)/2));
}
function getActiveMember(clientid,month) {
	$.post(AJAX_URL+"/get_active_member",{clientid:clientid,month:month},function(o){
		$('#dash_two tbody').empty().html(o);
	});
}
function getSwipeByClient(clientid,month) {
	$.post(AJAX_URL+"/get_swipe_by_client",{clientid:clientid,month:month},function(o){
		$('#dash_three tbody').empty().html(o);
	});
}
function getSwipeByProvider(providerid,month) {
	$.post(AJAX_URL+"/get_swipe_by_provider",{providerid:providerid,month:month},function(o){
		$('#dash_four tbody').empty().html(o);
	});
}
function getSwipeMore(clientid,month) {
	$.post(AJAX_URL+"/get_swipe_more",{clientid:clientid,month:month},function(o){
		$('#dash_five tbody').empty().html(o);
	});
}
function getUserLoginByProvider(providerid) {
	$.post(AJAX_URL+"/get_user_login",{providerid:providerid},function(o){
		$('#dash_six tbody').empty().html(o);
	});
}
</script>