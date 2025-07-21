<style>
.blink {
  animation: blink-animation 1s steps(2, start) infinite;
  color: red;
  font-weight: bold;
}

@keyframes blink-animation {
  to {
    visibility: hidden;
  }
}
</style>
<div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

      <h1>
        Requests Table

     <small>preview of Requests table</small>

      </h1>

      <ol class="breadcrumb">

        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

        <li><a href="#">Tables</a></li>

        <li class="active">Requests</li>

      </ol>

    </section>

    <!-- Main content -->

    <section class="content">

      <div class="row">

        <div class="col-xs-12">



          <div class="box">

            <div class="box-header no_print">

             
				<!--<button type="button" class="btn btn-default" data-toggle="modal" data-target="#modal-email"> Send Email </button>-->

               

               <!--<button type="button" class="btn btn-default" data-toggle="modal" data-target="#modal-sms"> Send Sms </button>-->

				<div class="modal fade" id="modal-sms">

          <div class="modal-dialog modal-lg">

            <div class="modal-content">

              <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                  <span aria-hidden="true">&times;</span></button>

                <h4 class="modal-title">Send Sms To all requests</h4>

              </div>

              <div class="modal-body">

              

          <div class="row">

          	

             	  <form action="<?= site_url(); ?>Company/Smstorequest" method="post" enctype="multipart/form-data">

               

                <div class="col-md-6">

                 <label>From</label>

			      <div class="form-group">

                   <input type="number" name="from" class="form-control" placeholder="From" required>

                </div>

                </div>

                

                 <div class="col-md-6">

                 <label>To</label>

			      <div class="form-group">

                <input type="number" name="to" class="form-control" placeholder="To" required>

                </div>

                </div>

                

                <div class="col-md-6">

                 <label>Title</label>

			      <div class="form-group">

                  <input type="Text" name="title" class="form-control" placeholder="Title" />

                       

                </div>

                </div>

                 <div class="col-md-6">

                 <label>Message</label>

			      <div class="form-group">

                  <textarea name="message" class="form-control" placeholder="Description"></textarea>

                </div>

                </div>

                

                    

                    <br>

                     <div class="col-md-12">

			  <div class="form-group">

              

                <input type="submit" name="submit" value="Send Message" class="btn btn-info btn-lg" />

                </div>

                </div>



          	</div>

          	

          </div>

        

                      </form>

             	 

              	 

              </div>



            </div>

            <!-- /.modal-content -->

          </div>

            </div>

            <!-- /.box-header -->

            <div class="box-body table-responsive">

            

            

            <table id="example2" class="table table-bordered table-striped">

                <thead>

                <tr>
                    <th>S.No</th>
                    <th style="width: 150px;">Name</th>
                    <th>Phone</th>
                    <th>Company</th>
                    <th>city</th>
                    <!--<th>Investment</th>-->
                    <!--<th>Operation</th>-->
                    <!--<th>Place</th> -->
                    <!--<th>time</th>-->
                    
                    <!--<th>Email</th> -->
                    <th>View Comment</th>
                    <th>Date</th>
                    <th>View</th>
                    <th>Meeting</th>

                </tr>

                </thead>

                <tbody>

                 <?php  // $sno = $requests['num'];

           $sno=1;
                        foreach( $requests as $request ) { 

                    ?>

                <tr>

                  <td><?=  $sno; ?></td>

     <td><?=  $request->e_firstname.'  '.$request->e_lastname ?> </td>
              
              <td><?= $request->e_phonenumber; ?> 

              </td>
              
                <td><?=  $request->co_name; ?> 

              </td>
                <td><?php if( $request->name == ""){
                echo 'No City ';
                
                } else{
                
                echo $request->name;
                }?> 

              </td>
              <!-- <td><?=  $request->total_investment; ?> -->

              <!--</td>-->
              <!--<td><?= $request->operation; ?> -->

              <!--</td>-->
              <!--<td><?= $request->place; ?> -->

              <!--</td>-->
              <!--<td><?=  $request->time ?> -->

              <!--</td>-->
       <!--       <td><a href="mailto:<?php echo $request->e_emailaddress; ?>?subject=Franchise request for <?=  $request->co_name; ?> "><?php echo $request->e_emailaddress; ?></a>-->
       <!--</td>-->
 <td>
 <?php if($request->admin_comment != NULL)
              {
                 echo '<span style="color:red;font-weight:bold;">'.$request->admin_comment.'</span><br>';
              } ?>
 <?php echo $request->req_comment; ?>
</td>
             <td>
            
              <?= date('d-m-y',strtotime($request->e_date)); ?></td>

                 

                        <td><a href="<?php echo base_url('Team/view_sigle_requests'); ?>/<?= $request->e_id?>" target="_blank">
<button type="button" class=" btn btn-info"><i class="fa fa-list"></i>View</button></a>
                        
                        </td>
                        <td>
<?php if($request->meeting_status == 0) { $class='btn btn-danger'; }elseif($request->meeting_status == 1){$class='btn btn-success';} else{ $class='btn-primary';} ?>

<button type="button" class="<?php echo $class;?>"><i class="fa fa-calendar"></i></button>

                   


                        </td>

                        
        



                          

                </tr>

                 <!-- /.modal-comment -->

              

            <!-- /.modal-content -->

          </div>
             
             
            <!-- /.modal-content -->

          </div>

             <?php $sno++ ;} ?>

                </tbody>

                <tfoot>

               <tr>

               <th>S.No</th>
                    <th style="width: 150px;">Name</th>
                    <th>Phone</th>
                    <th>Company</th>
                    <th>city</th>
                    <!--<th>Investment</th>-->
                    <!--<th>Operation</th>-->
                    <!--<th>Place</th> -->
                    <!--<th>time</th>-->
                    
                    <!--<th>Email</th> -->
                    <th>View Comment</th>
                    <th>Date</th>
                    <th>View</th>
                    <th>Meeting</th>

					</tr>

                </tfoot>

              </table>

            

            

            </div>

            <!-- /.box-body -->

          </div>



          <!-- /.box -->



        </div>



      </div>



    </section>



    <!-- /.content -->



  </div>

  

<div class="modal fade" id="modal-email">

          <div class="modal-dialog modal-lg">

            <div class="modal-content">

              <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                  <span aria-hidden="true">&times;</span></button>

                <h4 class="modal-title">Send Email To all requests</h4>

              </div>

              <div class="modal-body">

              

          <div class="row">

          	

             	  <form action="<?= site_url(); ?>home/sendEmailtorequests/" method="post" enctype="multipart/form-data">

               

                 <div class="col-md-6">

                 <label>Subject</label>

			      <div class="form-group">

                  <input type="Text" name="subject" class="form-control" placeholder="Email Subject" required>

                       

                </div>

                </div>

                

                <div class="col-md-6">

                 <label>Heading</label>

			      <div class="form-group">

                 <input type="Text" name="heading" class="form-control" placeholder="Heading" required>

                </div>

                </div>

                

                 <div class="col-md-6">

                 <label>From</label>

			      <div class="form-group">

                 <input type="number" name="from" class="form-control" placeholder="From" required>

                </div>

                </div>

                 <div class="col-md-6">

                 <label>To</label>

			      <div class="form-group">

                  <input type="number" name="to" class="form-control" placeholder="To" required>

                </div>

                </div>

                 

                 <div class="col-md-12">

                 <label>Descripation</label>

			      <div class="form-group">

                 <textarea name="description" class="form-control" placeholder="Description if you need to shift the out to next line just add <br> there" required ></textarea>

                 <input type="hidden" value="1" name="counter" id="counter" />

                </div>

                </div>

                

                <br>

               <div class="col-md-5">

			  <div class="form-group">

                <label>Button link</label>

  <input type="Text" name="buttonlink[1]" class="form-control" placeholder="Button 1 Link like https://www.franchisepk.com/" required>

                </div>

                </div>

                   

                   	<div class="col-md-5">

			  <div class="form-group">

                <label>Button Text</label>

  <input type="Text" name="buttontext[1]" class="form-control" placeholder="Button 1 Text" required>

                </div>

                </div> 

                   

                   	

                   		<div class="col-md-2">

			  <div class="form-group">

                <label>&nbsp;</label><br>

 <a id="add" class="btn btn-primary">Add button <i class="heavy_plus_sign"></i></a>

                </div>

                </div>

                  <div id="tobeadded" class="col-md-12"></div> 

                   

                   	<div class="col-md-6">

			  <div class="form-group">

                <label>Image</label>

  <input type="file" name="image" class="form-control" placeholder="upload image" required>

                </div>

                </div> 

                   

                   	

                   		<div class="col-md-6">

			  <div class="form-group">

                <label>Image Link</label>

  <input type="Text" name="image_link" class="form-control" placeholder="Link The Image"

                </div>

                </div>	

                    <div class="col-md-12">

			  <div class="form-group">

                 <label></label>

                </div>

                </div>

                    <br>

                     <div class="col-md-12">

			  <div class="form-group">

            

                <input type="submit" name="submit" value="Send Message" class="btn btn-info btn-lg" />

                </div>

                </div>



          	</div>

          	

          </div>

        

                      </form>

             	 

              	 

              </div>



            </div>

            <!-- /.modal-content -->

          </div>
          
         <script type="text/javascript">
			 function submit_comment(ida)
			 {
				 //alert($('#com'+ida).serialize()); return;
				$.ajax({
					method : 'POST',
					url : '<?php echo site_url(); ?>company/add_comment/'+ida,
					data : $('#com'+ida).serialize(),
					success: function(output)
					{
						if(output == 'done')
							{
								$('#modal-comment'+ida).modal('toggle');
							}
					}
				});
			 }
			 
			 function sub_meeting(id)
			 {
				
				 var form_data = $('#meet'+id).serialize();
				 //alert(form_data);
				 $.ajax({
					method : 'POST',
					url : '<?php echo site_url(); ?>company/add_meeting/'+id,
					data : form_data,
					success: function(output)
					{
						//alert(output);
						if(output == 'done')
							{
								$('#modal-meeting'+id).modal('toggle');
							}
					},
					 error:function(e)
					 {
						 console.log(e);
						 alert('error');
					 } 
				 });
			 }
			 
			 /*function submit_meeting(id)
			 {
				 
				 alert($('#meet'+id).serialize()); return;
				 //exit;
				$.ajax({
					method : 'POST',
					url : '<?php //echo site_url(); ?>company/add_meeting/'+id,
					data : $('#meet'+id).serialize(),
					alert($data);
					success: function(output)
					{
						if(output == 'done')
							{
								$('#modal-meeting'+id).modal('toggle');
							}
					}
				});
			 }*/
</script>