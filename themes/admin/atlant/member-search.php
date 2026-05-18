<div class="row">
	<div class="col-md-6">
		<div class="row form-group">
			<div class="col-md-3 control-label">Client</div>
			<div class="col-md-9">
				<select class="form-control select" name="member_clientid" id="member_clientid">
					<option value=""> - pilih client - </option>
					<?php 
						$cli = isset($this->jCfg['member_search']['clientid'])?$this->jCfg['member_search']['clientid']:'';
						echo option_client($cli);
					?>
				</select>
			</div>
		</div>
		
		<div class="row form-group">
			<div class="col-md-3 control-label">No. Kartu</div>
			<div class="col-md-9">
				<input type="text" id="member_cardno" name="member_cardno" class="form-control" value="<?php echo $this->jCfg['member_search']['cardno'];?>" />
			</div>
		</div>
		
		<div class="row form-group">
			<div class="col-md-3 control-label">Nama Member</div>
			<div class="col-md-9">
				<input type="text" id="member_name" name="member_name" class="form-control" value="<?php echo $this->jCfg['member_search']['name'];?>" />
			</div>
		</div>
		
		<div class="row form-group">
			<div class="col-md-3 control-label">Tipe Relasi</div>
			<div class="col-md-9">
				<select class="form-control select" name="member_relationshiptype" id="member_relationshiptype">
					<option value=""> - pilih tipe relasi - </option>
					<?php 
					$relate = isset($this->jCfg['member_search']['relationship'])?$this->jCfg['member_search']['relationship']:'';
					echo option_relationshiptype_on_search($relate);
					?>
				</select>
			</div>
		</div>
		
		<!-- div class="row form-group">
			<div class="col-md-3 control-label">Jenis Kelamin</div>
			<div class="col-md-9">
				<select class="form-control select" name="member_sex" id="member_sex">
					<option value=""> - pilih jenis kelamin - </option>
					<option value="M"<?php echo $this->jCfg['member_search']['sex']=='M'?' selected="selected"':''?>> Laki-laki </option>
					<option value="F"<?php echo $this->jCfg['member_search']['sex']=='F'?' selected="selected"':''?>> Perempuan </option>
				</select>
			</div>
		</div -->
		
		<div class="row form-group">
			<div class="col-md-3 control-label">Tanggal Lahir</div>
			<div class="col-md-9">
				<div class="input-group">
					<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
					<input type="text" id="member_birthdate" name="member_birthdate" class="form-control datepicker" placeholder="Tanggal Lahir"value="<?php echo !empty($this->jCfg['member_search']['birthdate'])?date('Y-m-d', strtotime($this->jCfg['member_search']['birthdate'])):'';?>" />
				</div>
			</div>
		</div>
		
		<!-- div class="row form-group">
			<div class="col-md-3 control-label">Telp</div>
			<div class="col-md-9">
				<input type="text" id="member_phone" name="member_phone" class="form-control" value="</?php echo $this->jCfg['member_search']['phone'];?>" />
			</div>
		</div -->
	</div>
	
	<div class="col-md-6">		
		<!-- div class="row form-group">
			<div class="col-md-3 control-label">Email</div>
			<div class="col-md-9">
				<input type="text" id="member_email" name="member_email" class="form-control" value="</?php echo $this->jCfg['member_search']['email'];?>" />
			</div>
		</div -->
		
		<div class="row form-group">
			<div class="col-md-3 control-label">Provinsi</div>
			<div class="col-md-9">
				<select class="form-control select" name="member_region" id="member_region">
					<option value=""> - pilih provinsi - </option>
					<?php 
						$prov = isset($this->jCfg['member_search']['region'])?$this->jCfg['member_search']['region']:'';
						echo option_province($prov);
					?>
				</select>
			</div>
		</div>
		
		<div class="row form-group">
			<div class="col-md-3 control-label">Kota/Kabupaten</div>
			<div class="col-md-9">
				<select class="validate[required] form-control" name="member_location" id="member_location">
					<option value=""> - pilih kota/kabupaten - </option>
				</select>
			</div>
		</div>
		
		<div class="row form-group">
			<div class="col-md-3 control-label">Kelas Perawatan</div>
			<div class="col-md-9">
				<select class="form-control select" name="member_plantype" id="member_plantype">
					<option value=""> - pilih kelas perawatan - </option>
					<?php 
						$plan = isset($this->jCfg['member_search']['plantype'])?$this->jCfg['member_search']['plantype']:'';
						echo option_plantype($plan);
					?>
				</select>
			</div>
		</div>
		
		<div class="row form-group">
			<div class="col-md-3 control-label">Status Karyawan</div>
			<div class="col-md-9">
				<select class="form-control select" name="member_employmentstatus" id="member_employmentstatus">
					<option value=""> - pilih status karyawan - </option>
					<?php 
						$kar = isset($this->jCfg['member_search']['employment'])?$this->jCfg['member_search']['employment']:'';
						echo option_employmentstatus($kar);
					?>
				</select>
			</div>
		</div>
		
		<div class="row form-group">
			<div class="col-md-3 control-label">Status Kartu</div>
			<div class="col-md-9">
				<select class="form-control select" name="member_statuscard" id="member_statuscard">
					<option value=""> - pilih status kartu - </option>
					<option value="Active"<?php echo $this->jCfg['member_search']['cardstatus']=='Active'?' selected="selected"':''?>> Active </option>
					<option value="InActive"<?php echo $this->jCfg['member_search']['cardstatus']=='InActive'?' selected="selected"':''?>> InActive </option>
				</select>
			</div>
		</div>				
	</div>
</div>
<script type="text/javascript">
var AJAX_URL = '<?php echo site_url("ajax/data");?>';
$(document).ready(function(){

	$('#member_region').change(function(){
		get_kota($(this).val(),"");
	});
	
	get_kota('<?php echo isset($this->jCfg['member_search']['region'])?$this->jCfg['member_search']['region']:''?>','<?php echo isset($this->jCfg['member_search']['location'])?$this->jCfg['member_search']['location']:''?>');

});

function get_kota(prov,kota){
	$.post(AJAX_URL+"/kota",{prov:prov,kota:kota},function(o){
	$('#member_location').html(o);
	});
}

</script>