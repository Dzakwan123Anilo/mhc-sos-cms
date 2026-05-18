<?php js_validate();?>
<form id="form-validated" enctype="multipart/form-data" action="<?php echo $own_links;?>/save" class="form-horizontal" method="post"> 
        <input type="hidden" name="fraud_id" id="fraud_id" value="<?php echo isset($val->fraud_id)?$val->fraud_id:'';?>" />
        <div class="row">
          <div class="col-md-12">

            <div class="row form-group">  
              <div class="col-md-1 control-label">Email</div>
              <div class="col-md-6">
              	<input type="text" id="fraud_email" name="fraud_email" class="validate[required] form-control" value="<?php echo isset($val->fraud_email)?$val->fraud_email:'';?>" />
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-1 control-label">Title</div>
              <div class="col-md-6">
                <input type="text" id="fraud_title" name="fraud_title" class="validate[required] form-control" value="<?php echo isset($val->fraud_title)?$val->fraud_title:'';?>" />
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-1 control-label">Data</div>
              <div class="col-md-11">
                <table class="table table-hover table-bordered table-striped" id="thistable">
				<thead>
					<tr>
						<th width="30px">No</th>
						<th>Member ID</th>
						<th>Member Name</th>
						<th>DOB</th>
						<th>Benefit Entitlement</th>
						<th>Date and Time</th>
						<th>Provider Name</th>
						<th>Type of Services</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach($data as $v){?>
					<tr>
						<td><?php echo ++$no;?></td>
						<td nowrap="nowrap"><?php echo $v->member_cardno;?></td>
						<td nowrap="nowrap"><?php echo $v->member_name;?></td>
						<td nowrap="nowrap"><?php echo date('d-m-Y', strtotime($v->date_claim));?></td>
						<td nowrap="nowrap"><?php echo $v->room_class;?></td>
						<td nowrap="nowrap"><?php echo date('d-m-Y H:i:s', strtotime($v->createdDate));?></td>
						<td nowrap="nowrap"><?php echo $v->provider_name;?></td>
						<td nowrap="nowrap"><?php echo $v->service_description;?></td>
					</tr>
				<?php }?>
				</tbody>
				</table>
              </div>
            </div>          
			 
            <div class="row form-group">  
              <div class="col-md-1 control-label">Set Waktu</div>
              <div class="col-md-3">
				<div class="input-group date">
					<input type="text" id="fraud_date" name="fraud_date" class="validate[required] form-control datepicker" value="<?php echo isset($val->fraud_schedule)?date('Y-m-d', strtotime($val->fraud_schedule)):'';?>"/>
					<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
				</div>
              </div>
              <div class="col-md-2">
				<div class="input-group bootstrap-timepicker">
					<input type="text" id="fraud_time" name="fraud_time" class="validate[required] form-control timepicker24" value="<?php echo isset($val->fraud_schedule)?date('H:i:s', strtotime($val->fraud_schedule)):'';?>"/>
                	<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
				</div>
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
var TAGID = 'fraud_email';
</script>