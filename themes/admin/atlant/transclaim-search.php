<div class="row">
	<div class="col-md-6">
	
		<div class="row form-group">
			<div class="col-md-3 control-label">Nama Client</div>
			<div class="col-md-9">
				<?php 
					$cli = isset($this->jCfg['transraw_search']['clientid'])?$this->jCfg['transraw_search']['clientid']:'';
				?>
				<select class="form-control select" name="transclaim_clientid" id="transclaim_clientid">
					<option value=""> - pilih client - </option>
					<?php 
						echo option_client($cli);
					?>
				</select>
			</div>
		</div>
		
		<div class="row form-group">
			<div class="col-md-3 control-label">Tipe Layanan</div>
			<div class="col-md-9">
				<?php 
					$sid = isset($this->jCfg['transraw_search']['serviceid'])?$this->jCfg['transraw_search']['serviceid']:'';
				?>
				<select class="form-control select" name="transclaim_serviceid" id="transclaim_serviceid">
					<option value=""> - pilih tipe layanan - </option>
				</select>
			</div>
		</div>
			
		<div class="row form-group">
			<div class="col-md-3 control-label">Nama Member</div>
			<div class="col-md-9">
				<input type="text" id="transclaim_name" name="transclaim_name" class="form-control" value="<?php echo ($this->jCfg['transraw_search']['name'] ?? '');?>" />
			</div>
		</div>
		
		<div class="row form-group">
			<div class="col-md-3 control-label">Tanggal Klaim</div>
			<div class="col-md-4">
				<div class="input-group">
					<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
					<input type="text" id="transclaim_datestart" name="transclaim_datestart" class="form-control datepicker" placeholder="Tanggal Awal"  value="<?php echo ($this->jCfg['transraw_search']['date_start'] ?? '');?>" autocomplete="off" />                                            
				</div>
			</div>
	
			<div class="col-md-5">
				<div class="input-group">
					<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
					<input type="text" id="transclaim_dateend" name="transclaim_dateend" class="form-control datepicker" placeholder="Tanggal Akhir" value="<?php echo ($this->jCfg['transraw_search']['date_end'] ?? '');?>" autocomplete="off" />                                            
				</div>
			</div>
		</div>
				
	</div>
	<div class="col-md-6">
					
		<div class="row form-group">
			<div class="col-md-3 control-label">Nama Provider</div>
			<div class="col-md-9">
				<select class="form-control select" name="transclaim_providerid" id="transclaim_providerid">
					<option value=""> - pilih provider - </option>
					<?php 
						$pli = isset($this->jCfg['transraw_search']['providerid'])?$this->jCfg['transraw_search']['providerid']:'';
						echo option_provider($pli);
					?>
				</select>
			</div>
		</div>
		
		<div class="row form-group">
			<div class="col-md-3 control-label">Kota/Kabupaten</div>
			<div class="col-md-9">
				<select class="form-control select" name="transclaim_location" id="transclaim_location">
					<option value=""> - pilih kota/kabupaten - </option>
					<?php 
						$city = isset($this->jCfg['transraw_search']['location'])?$this->jCfg['transraw_search']['location']:'';
						echo option_kab($city);
					?>
				</select>
			</div>
		</div>
		
		<div class="row form-group">
			<div class="col-md-3 control-label">No. Kartu</div>
			<div class="col-md-9">
				<input type="text" id="transclaim_cardno" name="transclaim_cardno" class="form-control" value="<?php echo ($this->jCfg['transraw_search']['cardno'] ?? '');?>" />
			</div>
		</div>
		
		<div class="row form-group">
			<div class="col-md-3 control-label">Tipe Relasi</div>
			<div class="col-md-9">
				<select class="form-control select" name="transclaim_relationshiptype" id="transclaim_relationshiptype">
					<option value=""> - pilih tipe relasi - </option>
					<?php 
					$relate = isset($this->jCfg['transraw_search']['relationship'])?$this->jCfg['transraw_search']['relationship']:'';
					echo option_relationshiptype_on_search($relate);
					?>
				</select>
			</div>
		</div>				
	</div>
</div>
<script type="text/javascript">
var GET_SERVICE_TYPE = '<?php echo $own_links;?>/get_service_type';
var AJAX_URL = '<?php echo site_url("ajax/data");?>';
var CID = '<?php echo $cli;?>';
var SID = '<?php echo $sid;?>';
</script>
