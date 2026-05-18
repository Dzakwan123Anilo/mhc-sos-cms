
			<div class="row"> 
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
	              <div class="col-md-9 control-label">: <b><?php echo jenkel($val->member_sex);?></b></div>
	            </div>
	            
	            <div class="row form-group">  
	              <div class="col-md-3 control-label">Tanggal Lahir</div>
	              <div class="col-md-9 control-label">: <b><?php echo myDate($val->member_birthdate,"d M Y",false);?></b></div>
	            </div>
	            
	            <div class="row form-group">  
	              <div class="col-md-3 control-label">Alamat</div>
	              <div class="col-md-9 control-label">: <b><?php echo $val->member_address;?></b></div>
	            </div>
	
	            <div class="row form-group">  
	              <div class="col-md-3 control-label">Telp</div>
	              <div class="col-md-9 control-label">: <b><?php echo $val->member_phone;?></b></div>
	            </div>
	            
	          </div> 
	
	          <div class="col-md-6">
	
	            <div class="row form-group">  
	              <div class="col-md-3 control-label">Perusahaan</div>
	              <div class="col-md-9 control-label">: <b><?php echo $val->client_name;?></b></div>
	            </div>
	
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
	              <div class="col-md-3 control-label">Kelas Perawatan</div>
	              <div class="col-md-9 control-label">: <b><?php echo $val->plan_description;?></b></div>
	            </div>
	
	            <div class="row form-group">  
	              <div class="col-md-3 control-label">Dental Limit</div>
	              <div class="col-md-9 control-label">: <b><?php echo $val->member_dentallimit;?></b></div>
	            </div>
	            
	          </div>
	
	        </div>
        <br />
        <div class="row">
          
				<br />
		      <div class="panel panel-default" style="margin-top:-10px;">
			    <div class="panel-heading">
			        <div class="panel-title-box">
			            <h3>Family Member List</h3>
			            <span>Data Family Member</span>
			        </div>                                    
			        <ul class="panel-controls" style="margin-top: 2px;">
			            <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>                                       
			        </ul>           
			    </div>
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
		                    <th>Kelas Perawatan</th>
		                    <th>Status Karyawan</th>
		                    <th>Status Kartu</th>
		                    <th>Dental Limit</th>
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
        </div>