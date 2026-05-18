<?php js_validate();?>
<style type="text/css">
  .item-info-indicator{
    text-align:center;border:1px solid #ccc;
    border-radius: 5px;
    margin-left: 5px;
    float: right;
    padding-top: 5px;
    color: #fff;
  }
  .item-info-indicator h2{
    padding:0px;
    margin:0px;
  }
  .item-info-indicator h2,.item-info-indicator h4{
    color: #fff;
  }
</style>
<form id="form-validated" enctype="multipart/form-data" action="<?php echo $own_links;?>/upload" class="form-horizontal" method="post"> 
        <input type="hidden" name="import_id" id="import_id" value="<?php echo isset($val->import_id)?$val->import_id:'';?>" />
        <div class="row">
          <div class="col-md-12">
            <div class="row form-group">  
              <div class="col-md-3 control-label">File Import</div>
              <div class="col-md-4">
                <input type="file" id="import_file" name="import_file" class="validate[required] form-control" value="" />
                <?php if(isset($val) && isset($val->import_file)){?>
                <i class="help-block">File Name : <a href="<?php echo base_url()."assets/collections/file/".$val->import_file;?>"><?php echo $val->import_file;?></a></i>
                <?php } ?>
              </div>
            </div>

            
            <?php if($this->jCfg['user']['is_all']==1){?>
            <div class="row form-group">  
              <div class="col-md-3 control-label">Community</div>
              <div class="col-md-3">
                  <select class="validate[required] form-control" name="import_site_id" id="import_site_id">
                      <option value=""> pilih </option>
                      <?php 
                        $dom = isset($val)?$val->import_site_id:'';
                        echo option_domains($dom);
                      ?>
                  </select>  
              </div>     
            </div>
            <?php } ?>
            
            <div class="row form-group">  
              <div class="col-md-3 control-label"></div>
              <div class="col-md-4">
                  <button type="submit" name="simpan" class="btn btn-primary btn-cons"><i class="icon-ok"></i> Upload</button>
              </div>
            </div>


          </div>  

        </div>

</form>

<?php if( isset($data) && count($data)>0 ){?>
<h5 class="heading-form">Verifikasi Data Import</h5>

<div class="well">
  <div class="col-md-2 pull-left">
      <button type="button" id="btn_approve" name="approve" class="btn btn-warning btn-lg"><i class="fa fa-check"></i> Approve</button>
      <i class="help-block">Checklist Item Member</i>
  </div>
  <div class="col-md-2 pull-left">
      <button type="button" id="btn_refresh" name="Refresh"  class="btn btn-info btn-lg"><i class="fa fa-refresh"></i> Refresh</button>
      <i class="help-block">Refresh Data</i>
  </div>
  <div class="col-md-5 pull-right">
      <div class="col-md-3 item-info-indicator btn-primary">
        <h2><?php echo count($data);?></h2>
        <h4>Total</h4>
      </div>
      <div class="col-md-3 item-info-indicator btn-success">
        <h2 id="count_success">0</h2>
        <h4>Success</h4>
      </div>
      <div class="col-md-3 item-info-indicator btn-danger">
        <h2 id="count_gagal">0</h2>
        <h4>Gagal</h4>
      </div>
      
  </div>
  <div style="clear:both;"></div>
</div>

<div class="panel panel-default" style="margin-top:-10px;">
	<div class="panel-body">
		<div class="row">
			<div class="col-md-12">
	            <div class="table-responsive">
            		<table class="table table-hover table-bordered table-striped nowrap">
	                   <thead>
	                    <tr>
	                        <th width="30px">No</th>
	                        <th width="10">
	                          <input type="checkbox" value="1" id="select_all" class="select_all" />
	                        </th>
	                        <th>Client ID</th>
	                        <th>Card No</th>
	                        <th>Member Name</th>
	                        <th>Relation Ship</th>
	                        <th>Gender</th>
	                        <th>Birth Date</th>
	                        <th>Address</th>
	                        <th>Region</th>
	                        <th>Upd.Code</th>
	                        <th>Plan.Type</th>
	                        <th>RoomClass</th>
	                        <th>Dental Unit</th>
	                        <th>Employment Status</th>
	                        <th>Remarks</th>
	                        <th>Group</th>
	                        
	                    </tr>
	                    </thead>
	                   <tbody>
	                    <?php 
	                    $no=0;
	                    if( count($data) > 0 ){
	                        foreach($data as $px=>$r){
	                        $idx = $r['id'];
	                    ?>
	                            <tr id="tr_item_<?php echo $idx;?>" class="tr_item">
	                                <td><?php echo ++$no;?></td>
	                                <td>
	                                <?php if($r['status_import']==0){?>
	                                  <input type="checkbox" name="item_select[<?php echo $idx;?>]" value="<?php echo $idx;?>" class="item_select" id="item_select_<?php echo $idx;?>" />
	                                <?php }else{ echo "&#10004;"; } ?>
	                                </td>
	                                <td><input type="text" class="form-control" id="client_id_<?php echo $idx;?>" name="client_id[<?php echo $idx;?>]" value="<?php echo $r['client_id'];?>" /></td>
	                                <td><input type="text" class="form-control" id="card_number_<?php echo $idx;?>" name="card_number[<?php echo $idx;?>]" value="<?php echo $r['card_number'];?>" /></td>
	                                <td><input type="text" class="form-control" id="card_name_<?php echo $idx;?>" name="card_name[<?php echo $idx;?>]" value="<?php echo $r['name'];?>" /></td>
	                                <td><input type="text" class="form-control" id="relationship_<?php echo $idx;?>" name="relationship[<?php echo $idx;?>]" value="<?php echo $r['relationship'];?>" /></td>
	                                <td><input type="text" class="form-control" id="gender_<?php echo $idx;?>" name="gender[<?php echo $idx;?>]" value="<?php echo $r['gender'];?>" /></td>
	                                <td><input type="text" class="form-control datepicker" id="birthdate_<?php echo $idx;?>" name="birthdate[<?php echo $idx;?>]" value="<?php echo myDate($r['birthdate'],"Y-m-d",false);?>" /></td>
	                                <td><input type="text" class="form-control" id="address_<?php echo $idx;?>" name="address[<?php echo $idx;?>]" value="<?php echo $r['address'];?>" /></td>
	                                <td><input type="text" class="form-control" id="province_<?php echo $idx;?>" name="province[<?php echo $idx;?>]" value="<?php echo $r['province'];?>" /></td>
	                                <td><input type="text" class="form-control" id="code_<?php echo $idx;?>" name="code[<?php echo $idx;?>]" value="<?php echo $r['code'];?>" /></td>
	                                <td><input type="text" class="form-control" id="plan_type_<?php echo $idx;?>" name="plan_type[<?php echo $idx;?>]" value="<?php echo $r['plan_type'];?>" /></td>
	                                <td><input type="text" class="form-control" id="room_class_<?php echo $idx;?>" name="room_class[<?php echo $idx;?>]" value="<?php echo $r['room_class'];?>" /></td>
	                                <td><input type="text" class="form-control" id="dental_unit_<?php echo $idx;?>" name="dental_unit[<?php echo $idx;?>]" value="<?php echo $r['dental_unit'];?>" /></td>
	                                <td><input type="text" class="form-control" id="emp_status_<?php echo $idx;?>" name="emp_status[<?php echo $idx;?>]" value="<?php echo $r['emp_status'];?>" /></td>	
	                                
	                                
	                                <td><input type="text" class="form-control" id="remark_<?php echo $idx;?>" name="remark[<?php echo $idx;?>]" value="<?php echo $r['remark'];?>" /></td>
	                                
	                                <td><input type="text" class="form-control" id="group_<?php echo $idx;?>" name="group[<?php echo $idx;?>]" value="<?php echo $r['group'];?>" /></td>
	                            </tr>
	                            <tr id="tr_item_info_<?php echo $idx;?>" style="display:none;">
	                              <td colspan="16" id="msg_item_<?php echo $idx;?>"></td>
	                            </tr>
	                    <?php } } ?>
	                    </tbody>
	                </table>
				</div>
	        </div>
		</div>
	</div>
</div>  
<?php } ?>
<script type="text/javascript">
  var AJAX_KEY_IMPORT = '<?php echo md5($this->jCfg['user']['id'].$this->jCfg['user']['name']);?>';
  var AJAX_URL_IMPORT = '<?php echo site_url("ajax/Import/import_data");?>';
  var IMPORT_ID = $('#import_id').val();
  var IMPORT_SITE_ID = '';
  var GAGAL = 0;
  var SUKSES = 0;
  <?php if( $this->jCfg['user']['is_all'] == 1 ){?>
      var IMPORT_SITE_ID = $('#import_site_id').val();
  <?php } ?>
</script>

