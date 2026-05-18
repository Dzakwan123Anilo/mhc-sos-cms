<div class="row">
	<div class="col-md-6">		
		<div class="row form-group">
			<div class="col-md-3 control-label">Nama Provider</div>
			<div class="col-md-9">
				<input type="text" id="provider_name" name="provider_name" class="form-control" value="<?php echo $this->jCfg['provider_search']['name'];?>" />
			</div>
		</div>
		
		<div class="row form-group">
			<div class="col-md-3 control-label">Tipe Provider</div>
			<div class="col-md-9">
				<select class="form-control select" name="provider_type" id="provider_type">
					<option value=""> - pilih tipe provider - </option>
					<?php 
						$pt = isset($this->jCfg['provider_search']['type'])?$this->jCfg['provider_search']['type']:'';
						echo option_providertype($pt);
					?>
				</select>
			</div>
		</div>
	</div>
	
	<div class="col-md-6">		
		<div class="row form-group">
			<div class="col-md-3 control-label">Provinsi</div>
			<div class="col-md-9">
				<select class="form-control select" name="provider_region" id="provider_region">
					<option value=""> - pilih provinsi - </option>
					<?php 
						$prov = isset($this->jCfg['provider_search']['region'])?$this->jCfg['provider_search']['region']:'';
						echo option_province($prov);
					?>
				</select>
			</div>
		</div>
		
		<div class="row form-group">
			<div class="col-md-3 control-label">Kota/Kabupaten</div>
			<div class="col-md-9">
				<select class="validate[required] form-control" name="provider_location" id="provider_location">
					<option value=""> - pilih kota/kabupaten - </option>
				</select>
			</div>
		</div>				
	</div>
</div>
<script type="text/javascript">

var AJAX_URL = '<?php echo site_url("ajax/data");?>';
$(document).ready(function(){

	$('#provider_region').change(function(){
		get_kota($(this).val(),"");
	});
	
	get_kota('<?php echo isset($this->jCfg['provider_search']['region'])?$this->jCfg['provider_search']['region']:''?>','<?php echo isset($this->jCfg['provider_search']['location'])?$this->jCfg['provider_search']['location']:''?>');

});

function get_kota(prov,kota){
	$.post(AJAX_URL+"/kota",{prov:prov,kota:kota},function(o){
		$('#provider_location').html(o);
	});
}

</script>