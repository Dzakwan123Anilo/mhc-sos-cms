<div class="row">
	<h5 class="heading-form">Member Detail</h5>
	<div class="col-md-6">
		<div class="row form-group">
			<div class="col-md-4 control-label">No. Kartu</div>
			<div class="col-md-8 control-label">: <b><?php echo $val->member_cardno;?></b></div>
		</div>

		<div class="row form-group">
			<div class="col-md-4 control-label">ID Karyawan</div>
			<div class="col-md-8 control-label">: <b><?php echo $val->member_employeeid;?></b></div>
		</div>
		
		<div class="row form-group">
			<div class="col-md-4 control-label">Tipe Relasi</div>
			<div class="col-md-8 control-label">: <b><?php echo $val->member_relationshiptype;?></b></div>
		</div>
		
		<div class="row form-group">
			<div class="col-md-4 control-label">Nama Member</div>
			<div class="col-md-8 control-label">: <b><?php echo $val->member_name;?></b></div>
		</div>
		
		<div class="row form-group">
			<div class="col-md-4 control-label">Jenis Kelamin</div>
			<div class="col-md-8 control-label">: <b><?php echo $val->member_sex=='M'?'Laki-laki':'Perempuan';?></b></div>
		</div>
		
		<div class="row form-group">
			<div class="col-md-4 control-label">Tanggal Lahir</div>
			<div class="col-md-8 control-label">: <b><?php echo myDate($val->member_birthdate, 'd M Y', false);?></b></div>
		</div>
	</div>
	<div class="col-md-6">		
		<div class="row form-group">
			<div class="col-md-4 control-label">Provinsi</div>
			<div class="col-md-8 control-label">: <b><?php echo $val->member_region;?></b></div>
		</div>
		
		<div class="row form-group">
			<div class="col-md-4 control-label">Kota/Kabupaten</div>
			<div class="col-md-8 control-label">: <b><?php echo $val->member_location;?></b></div>
		</div>
		
		<div class="row form-group">
			<div class="col-md-4 control-label">Perusahaan</div>
			<div class="col-md-8 control-label">: <b><?php echo $val->client_name;?></b></div>
		</div>
		
		<div class="row form-group">
			<div class="col-md-4 control-label">Kelas Perawatan</div>
			<div class="col-md-8 control-label">: <b><?php echo $val->plan_description;?></b></div>
		</div>
		
		<div class="row form-group">
			<div class="col-md-4 control-label">Deskripsi</div>
			<div class="col-md-8 control-label">: <b><?php echo $val->member_description;?></b></div>
		</div>
	</div>
</div>
<br /><br />
<div class="row">
	<div class="col-md-6">
		<h5 class="heading-form">Claim History List</h5>
		<div class="panel-body panel-body-table">		
			<div class="table-responsive">
				<table class="table table-hover table-bordered table-striped" id="thistable">
				<thead>
					<tr>
						<th width="30px">No</th>
						<th>Tgl. Claim</th>
						<th>Provider</th>
						<th>Tipe Layanan</th>
						<th>Nama Member</th>
						<th>No. Kartu</th>
						<th>ID Karyawan</th>
						<th>Tipe Relasi</th>
						<th>Kelas Kamar</th>
						<th>Status Claim</th>
					</tr>
				</thead>
				<tbody>
				<?php if( count($data) > 0 ){
					$no=0;
				foreach($data as $r){
				?>
					<tr style="cursor: pointer;">
						<input type="hidden" id="claim_id" name="claim_id" value="<?php echo _encrypt($r->claimId);?>">
						<td><?php echo ++$no;?>
							<input type="hidden" id="date" name="date" value="<?php echo _encrypt($r->date_claim);?>">
							<input type="hidden" id="planid" name="planid" value="<?php echo _encrypt($r->plan_id);?>">
							<input type="hidden" id="memberid" name="memberid" value="<?php echo _encrypt($r->member_id);?>">
							<input type="hidden" id="clientid" name="clientid" value="<?php echo _encrypt($r->client_id);?>">
							<input type="hidden" id="relationshiptype" name="relationshiptype" value="<?php echo _encrypt($r->member_relationshiptype);?>">
							<input type="hidden" id="roomclass" name="roomclass" value="<?php echo _encrypt($r->plan_description);?>">
							<input type="hidden" id="servicetype" name="servicetype" value="<?php echo $r->servicetypeId;?>">
						</td>
						<td nowrap="nowrap"><?php echo !empty($r->date_claim)?myDate($r->date_claim,"d M Y H:i:s",false):''?></td>
						<td><?php echo $r->provider_name;?></td>
						<td nowrap="nowrap"><?php echo $r->service_type;?></td>
						<td nowrap="nowrap"><?php echo $r->member_name;?></td>
						<td><?php echo $r->member_cardno;?></td>
						<td><?php echo $r->member_employeeid;?></td>
						<td><?php echo $r->relationship_type;?></td>
						<td nowrap="nowrap"><?php echo $r->room_class;?></td>
						<td><?php echo $r->claim_status;?></td>
					</tr>
				<?php } } ?>
				</tbody>
				</table>
			</div>
			<div class="pull-right">
				<?php echo isset($paging)?$paging:'';?>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<h5 class="heading-form">Informasi Claim</h5>
		<div class="row">	                  
			<input type="hidden" id="claim_date" name="claim_date" value="">    
			<input type="hidden" id="claim_planid" name="claim_planid" value="">
			<input type="hidden" id="claim_memberid" name="claim_memberid" value="">
			<input type="hidden" id="claim_clientid" name="claim_clientid" value="">
			<input type="hidden" id="claim_relationshiptype" name="claim_relationshiptype" value="">
			<input type="hidden" id="claim_roomclass" name="claim_roomclass" value="">
			<input type="hidden" id="claim_servicetype" name="claim_servicetype" value="">
			               
			<div class="btn-group pull-right">
			    <button class="btn btn-primary" data-toggle="dropdown" id="print-preview" disabled="disabled"><i class="fa fa-bars"></i> Print Struk</button>
			</div>   
		</div><br />
		<div class="row">
			<div class="col-md-6">				
				<div class="row form-group">
					<div class="col-md-4 control-label">Provider</div>
					<div class="col-md-8 control-label">: <b id="provider"></b></div>
				</div>
		
				<div class="row form-group">
					<div class="col-md-4 control-label">Tgl. Claim</div>
					<div class="col-md-8 control-label">: <b id="dclaim"></b></div>
				</div>
								
				<div class="row form-group">
					<div class="col-md-4 control-label">Tipe Layanan</div>
					<div class="col-md-8 control-label">: <b id="stype"></b></div>
				</div>
				
				<div class="row form-group">
					<div class="col-md-4 control-label">Nama Client</div>
					<div class="col-md-8 control-label">: <b id="client"></b></div>
				</div>
				
				<div class="row form-group">
					<div class="col-md-4 control-label">Nama</div>
					<div class="col-md-8 control-label">: <b id="name"></b></div>
				</div>
						
				<div class="row form-group">
					<div class="col-md-4 control-label">No. Kartu</div>
					<div class="col-md-8 control-label">: <b id="cardno"></b></div>
				</div>
			</div>
			<div class="col-md-6">				
				<div class="row form-group">
					<div class="col-md-4 control-label">Tgl. Lahir</div>
					<div class="col-md-8 control-label">: <b id="dbirth"></b></div>
				</div>
				
				<div class="row form-group">
					<div class="col-md-4 control-label">Jenis Kelamin</div>
					<div class="col-md-8 control-label">: <b id="sex"></b></div>
				</div>
				
				<div class="row form-group">
					<div class="col-md-4 control-label">Kelas Perawatan</div>
					<div class="col-md-8 control-label">: <b id="plan"></b></div>
				</div>
				
				<div class="row form-group">
					<div class="col-md-4 control-label">Dental Limit</div>
					<div class="col-md-8 control-label">: <b id="dental"></b></div>
				</div>
				
				<div class="row form-group">
					<div class="col-md-4 control-label">Deskripsi</div>
					<div class="col-md-8 control-label">: <b id="desc"></b></div>
				</div>
			</div>
		</div>
	</div>
</div>
<br /><br />
<div class="row">
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
						<th>Kelas Perawatan</th>
						<th>Status Karyawan</th>
						<th>Status Kartu</th>
						<th>Dental Limit</th>
					</tr>
				</thead>
				<tbody>
				<?php if( count($family) > 0 ){
					$no = 0;
					foreach($family as $r){
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
<script type="text/javascript">
$(document).ready(function(){
    $("#thistable tr").click(function(){
        var id = ['provider','dclaim','stype','client','name','cardno','dbirth','sex','plan','dental','desc'], 
	        idabj = ['a','b','c','d','e','f','g','h','i','j','k'], 
	        inputid = ['claim_date','claim_planid','claim_memberid','claim_clientid','claim_relationshiptype','claim_roomclass','claim_servicetype'], 
	    	ci = $.ajax({type:'GET', url:'<?php echo $this->own_link."/fill_information/?cid="?>'+$(this).find("input").val(),async:false}).responseText,
			claiminf = JSON.parse(ci);

        if(claiminf.status == 1) {
	        for(var i=0; i<id.length; i++) {
	        	$('#'+id[i]).html(claiminf.data[idabj[i]]);
	        }
	    	
	        $(this).find("td").each(function(){
	            var j = 0;
	            $(this).find("input").each(function(){
	                $('#'+inputid[j]).val($(this).val());
	                j++;
	            });
	        });
	        $('#print-preview').removeAttr('disabled');
        }
    });
    $('#print-preview').click(function(){
    	var date = $('#claim_date').val(),
	    	planid = $('#claim_planid').val(),
			memberid = $('#claim_memberid').val(),
    		clientid = $('#claim_clientid').val(),
    		relationtype = $('#claim_relationshiptype').val(),
    		roomclass = $('#claim_roomclass').val(),
    		servicetype = $('#claim_servicetype').val(),
    		h = screen.height,
    		w = screen.width;

    	window.open("<?php echo $this->own_link."/print_preview/";?>?save=0&pid="+planid+"&dt="+date+"&mid="+memberid+"&cid="+clientid+"&rt="+relationtype+"&rc="+roomclass+"&st="+servicetype, "Print Preview", "width=400,height=600,left="+((w-400)/2)+",top="+((h-650)/2));
    });
});
</script>