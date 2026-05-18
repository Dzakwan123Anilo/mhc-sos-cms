<?php js_validate();?>
<form id="form-validated" enctype="multipart/form-data" action="<?php echo $own_links;?>/save" class="form-horizontal" method="post"> 
        <input type="hidden" name="member_id" id="member_id" value="<?php echo isset($val->member_id)?$val->member_id:'';?>" />
        <div class="row">
            
          <div class="col-md-5">
			<h5 class="heading-form">Member Detail</h5>
			<table class="table table-bordered">
				<tr>
					<td>Client</td>
					<td width="5">:</td>
					<td><?php echo isset($val->client_name)?$val->client_name:'-';?></td>
				</tr>
				<tr>
					<td>No. Kartu</td>
					<td width="5">:</td>
					<td><?php echo isset($val->member_cardno)?$val->member_cardno:'-';?></td>
				</tr>
				<tr>
					<td>Tipe Relasi</td>
					<td width="5">:</td>
					<td><?php echo isset($val->member_relationshiptype)?$val->member_relationshiptype:'-';?></td>
				</tr>
				<tr>
					<td>Nama Member</td>
					<td width="5">:</td>
					<td><?php echo isset($val->member_name)?$val->member_name:'-';?></td>
				</tr>
				<tr>
					<td>Jenis Kelamin</td>
					<td width="5">:</td>
					<td><?php echo isset($val->member_sex)?($val->member_sex=='M'?'Laki-laki':'Perempuan'):'-';?></td>
				</tr>
				<tr>
					<td>Tanggal Lahir</td>
					<td width="5">:</td>
					<td><?php echo isset($val->member_birthdate)?date('d-m-Y', strtotime($val->member_birthdate)):'-';?></td>
				</tr>
				<tr>
					<td>Alamat</td>
					<td width="5">:</td>
					<td><?php echo isset($val->member_address)?$val->member_address:'-';?></td>
				</tr>
				<tr>
					<td>Telp</td>
					<td width="5">:</td>
					<td><?php echo isset($val->member_phone)?$val->member_phone:'-';?></td>
				</tr>
				<tr>
					<td>Email</td>
					<td width="5">:</td>
					<td><?php echo isset($val->member_email)?$val->member_email:'-';?></td>
				</tr>
				<tr>
					<td>Provinsi</td>
					<td width="5">:</td>
					<td><?php echo isset($val->member_region)?$val->member_region:'-';?></td>
				</tr>
				<tr>
					<td>Kota/Kabupaten</td>
					<td width="5">:</td>
					<td><?php echo isset($val->member_location)?$val->member_location:'-';?></td>
				</tr>
				<tr>
					<td>Deskripsi</td>
					<td width="5">:</td>
					<td><?php echo isset($val->member_description)?$val->member_description:'-';?></td>
				</tr>
				<tr>
					<td>Kelas Perawatan</td>
					<td width="5">:</td>
					<td><?php echo isset($val->member_plantype)?$val->member_plantype:'-';?></td>
				</tr>
				<tr>
					<td>Status Karyawan</td>
					<td width="5">:</td>
					<td><?php echo isset($val->member_employmentstatus)?$val->member_employmentstatus:'-';?></td>
				</tr>
				<tr>
					<td>Status Kartu</td>
					<td width="5">:</td>
					<td><?php echo isset($val->member_statuscard)?$val->member_statuscard:'-';?></td>
				</tr>
				<tr>
					<td>Tanggal Aktifasi</td>
					<td width="5">:</td>
					<td><?php echo isset($val->member_activationdate)&&!empty($val->member_activationdate)&&$val->member_activationdate!='0000-00-00'?date('d-m-Y', strtotime($val->member_activationdate)):'-';?></td>
				</tr>
				<tr>
					<td>Dental Limit</td>
					<td width="5">:</td>
					<td><?php echo isset($val->member_dentallimit)?$val->member_dentallimit:'-';?></td>
				</tr>
			</table>      
            
          </div> 

          <div class="col-md-7">
            <h5 class="heading-form">Assign Group</h5>
            <?php if($this->jCfg['user']['is_all']==1){?>
            <div class="row form-group">  
              <div class="col-md-3 control-label">Website</div>
              <div class="col-md-7">
                  <select class="validate[required] form-control" name="member_site_id" id="member_site_id">
                      <?php 
                        $dom = isset($val)?$val->member_site_id:'';
                        echo option_domains($dom);
                      ?>
                  </select>  
              </div>     
            </div>
            <?php } ?>

            <div class="row form-group">  
              <div class="col-md-3 control-label">Group</div>
              <div class="col-md-9">
                  <select class="validate[required] form-control select" multiple name="group_assign[]" id="group_assign">
                      <?php foreach ((array)$group_member as $k1 => $v1) {
                        $slc = count($group_assign)>0&&in_array($v1->providergroupId, $group_assign)?'selected="selected"':'';
                        echo "<option value='".$v1->providergroupId."' ".$slc.">".$v1->provider_group."</option>";
                      }?>
                  </select>  
                  <i class="help-block">Assign group to member</i>
              </div>
            </div>

            <div class="row form-group">  
              <div class="col-md-3 control-label"></div>
              <div class="col-md-9">
                <button type="submit" name="simpan" class="btn btn-primary btn-cons"><i class="icon-ok"></i> Save</button>

              </div>
            </div>
            
            
          </div>

        </div>
        <br />
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
                            <th>Provinsi</th>
                            <th>Kota/Kabupaten</th>
                        </tr>
                        </thead>
                       <tbody>
                        <?php if( count($data) > 0 ){
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
                                </tr>
                        <?php } } ?>
                        </tbody>
                    </table>
                    </div>
            </div>
        </div>

</form>