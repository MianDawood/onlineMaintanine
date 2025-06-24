 <script>
    function copyTable() {
      var table = document.getElementById("example2");
      var textToCopy = "";

      for (var i = 0; i < table.rows.length; i++) {
        if (table.rows[i].cells[0].innerText !== "View Employee Comment :" && table.rows[i].cells[0].innerText !== "Operations :" && table.rows[i].cells[0].innerText !== "Franchisee Message :") {
          for (var j = 0; j < table.rows[i].cells.length; j++) {
            textToCopy += table.rows[i].cells[j].innerText + "\t";
          }
          textToCopy += "\n";
        }
      }

      navigator.clipboard.writeText(textToCopy).then(function() {
      //  alert("Table copied!");

        var message = encodeURIComponent(textToCopy);
    var phoneNumber = <?php echo $this->session->userdata('emp_phone'); ?>;

var whatsappUrl = "https://api.whatsapp.com/send?text=" + message;
        window.open(whatsappUrl, "_blank");
      }).catch(function(error) {
        console.error("Unable to copy table data: ", error);
      });
    }
  </script>

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

            
                <tr>
                   <th > Name :</th>
                    <th><?=  $requests[0]->e_firstname.'  '.$requests[0]->e_lastname ?></th>
                    </tr>
            
                 
                   <tr>
                            <th>Call Now:</th>
                              <th ><a href="tel:<?= $requests[0]->e_phonenumber; ?>"><?= $requests[0]->e_phonenumber; ?></a></th>
                    </tr>
                    
                      <tr>
                           <th >Brand :</th>
                              <th ><?=  $requests[0]->co_name; ?></th>
                    </tr>
                    
                      <tr>
                           <th >City :</th>
                              <th ><?=  $requests[0]->name; ?></th>
                    </tr>
                     <!-- <tr>
                           <th >Investment :</th>
                              <th ><?=  $requests[0]->total_investment; ?> </th>
                    </tr>
                    
                    <tr>
                           <th >Operation :</th>
                              <th ><?= $requests[0]->operation; ?> </th>
                    </tr>
                    
                    <tr>
                           <th >Place :</th>
                              <th ><?= $requests[0]->place; ?> </th>
                    </tr>
                    
                    
                    <tr>
                           <th >Time :</th>
                              <th ><?=  $requests[0]->time; ?></th>
                    </tr> -->
                    
                    
                    <tr>
                           <th >View Employee Comment :</th>
                              <th ><?php echo $requests[0]->req_comment; ?> </th>
                    </tr>

                    <tr>
                           <th >View Admin Comment :</th>
                              <th ><?php echo $requests[0]->admin_comment; ?> </th>
                    </tr>
                    
                       <tr>
                           <th >Franchisee Message :</th>
                              <th ><?php echo $requests[0]->e_message; ?> </th>
                    </tr>
                    
                    
                     <tr>
                           <th >Send email :</th>
                              <th ><a href="mailto:<?php echo $requests[0]->e_emailaddress; ?>?subject=Franchise request for <?=  $requests[0]->e_emailaddress; ?> "><?php echo $requests[0]->e_emailaddress; ?></a></th>
                    </tr>
                    
                    
                     <tr>
                           <th >Date :</th>
                              <th ><?= date('d-m-y',strtotime($requests[0]->e_date)); ?></th>
                    </tr>
                    
                    
                     <tr>
                           <th >Operations :</th>
                                  
                                  
                       <td >
        <a href="<?= site_url(); ?>company/investor_status/<?= $requests[0]->e_id?>/<?= $requests[0]->investor_status?>"><?php if($request ->investor_status == 0 ){ echo '<button class="btn btn-warning"><i class="fa fa-user"></i></button>';} elseif($requests[0]->investor_status == 1) { echo '<button class=" btn btn-success"><i class="fa fa-check"></i></button>';} ?></a>
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-default<?= $requests[0]->e_id?>"> <i class="fa fa-bars"></i></button>
             

<div class="modal fade" id="modal-default<?= $requests[0]->e_id?>">

<div class="modal-dialog">

            <div class="modal-content">

              <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                  <span aria-hidden="true">&times;</span></button>

                <h4 class="modal-title"><?= $requests[0]->co_name; ?></h4>

              </div>

              <div class="modal-body">

              

          <div class="row">

          	<div class="col-md-12 col-lg-12 col-sm-12">

          			<strong>Franchisee Message</strong>:<br><?= $requests[0]->e_message; ?>
          			<hr>
          		
<!--<label><a onclick="return confirm('Are you sure you want to delete this Company?');" href="<?= site_url(); ?>company/delete_requests/<?= $requests[0]->e_id?>"><button class="btn btn-danger">Delete</button></a></label>-->
				</div>
				</div>	

          	
              </div>



            </div>

            <!-- /.modal-content -->

          </div>

          <!-- /.modal-dialog -->

        </div>

        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-comment<?= $requests[0]->e_id?>"><i class="fa fa-comment"></i></button>
			<?php if($requests[0]->meeting_status == 0) { $class='btn btn-danger'; }elseif($requests[0]->meeting_status == 1){$class='btn btn-success';} else{ $class='btn-primary';} ?>
<button type="button" class="<?php echo $class;?>" data-toggle="modal" data-target="#modal-meeting<?= $requests[0]->e_id?>"><i class="fa fa-calendar"></i></button>
<button id="copy-table-btn" class="btn btn-success" onclick="copyTable()">Whatsapp</button>
					
                 <!-- /.modal-comment -->

                <div class="modal fade" id="modal-comment<?= $requests[0]->e_id?>">

          <div class="modal-dialog">

            <div class="modal-content">

              <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                  <span aria-hidden="true">&times;</span></button>

                <h4 class="modal-title"></h4>

              </div>

              <div class="modal-body">

              

          <div class="row">

          	

              	 <!--<form action="javascript:;" id="com<?= $requests[0]->e_id?>" onSubmit="submit_comment('<?= $requests[0]->e_id?>');" method="post">-->
               <form action="<?php echo site_url(); ?>company/add_lead_comment/<?= $requests[0]->e_id; ?>" method="post" >

                 <div class="col-md-12">

                 <label> Employee Comment</label>

			      <div class="form-group">

                    <textarea name="comment" class="form-control"  cols="50" rows="3"><?php echo $requests[0]->req_comment; ?> </textarea>

                </div>

                </div>

                <br>

                <div class="col-md-12">

<label>Admin Comment</label>

<div class="form-group">

   <textarea name="admin_comment" class="form-control"  cols="50" rows="3"><?php echo $requests[0]->admin_comment; ?> </textarea>

</div>

</div>

<br>


               <div class="col-md-12">

			  <div class="form-group">

                <label>Name</label>

   <input type="text" name="name" required placeholder="Enter Must your Name" value="<?=  $requests[0]->comment_req_by; ?>" class="form-control"/>

                </div>

                </div>	

                    <div class="col-md-12">

			  <div class="form-group">

                 <label><?= $requests[0]->comment_date; ?></label>

                </div>

                </div>

                    <br>

                     <div class="col-md-12">

			  <div class="form-group">

                 <input type="hidden" name="add" value="comment"/>



                <input type="submit" name="submit" value="Save" class="btn btn-info" />

                </div>

                </div>



          	</div>

          	

          </div>

        

                      </form>

              </div>



            </div>

            <!-- /.modal-content -->

          </div>
             
             
             
             <div class="modal fade" id="modal-meeting<?= $requests[0]->e_id?>">

          <div class="modal-dialog">

            <div class="modal-content">

              <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                  <span aria-hidden="true">&times;</span></button>

                <h4 class="modal-title"></h4>

              </div>

              <div class="modal-body">

              

          <div class="row">

          	
<!--<form action="<?php echo site_url(); ?>company/add_meeting/<?= $requests[0]->e_id?>" method="post">            -->
          	<form action="javascript:;" id="meet<?= $requests[0]->e_id?>" onSubmit="sub_meeting('<?= $requests[0]->e_id?>');" method="post">

          <div class="col-md-6">

          <div class="form-group">

            <label>Date</label>

   <input type="date" name="date" required class="form-control"/>

                </div>

                </div>
                 
                  <div class="col-md-6">

			  <div class="form-group">

                <label>Time</label>

   <input type="time" name="time" required class="form-control"/>

                </div>

                </div>
                 
                  <div class="col-md-6">

          <div class="form-group">

            <label>Franchisee</label>

   <input type="text" name="franchise" required class="form-control" value="<?= $requests[0]->e_firstname.' '.$requests[0]->e_lastname ?>"/>

                </div>

                </div>
                 
                  <div class="col-md-6">

			  <div class="form-group">

                <label>Company</label>

   <input type="text" name="company" required class="form-control" value="<?= $requests[0]->co_name; ?>"/>
                <input type="hidden" name="com_id" required class="form-control" value="<?= $requests[0]->co_id; ?>" />

                </div>

                </div>
      <div class="col-md-12">

			  <div class="form-group">

                <label>Contact person</label>

   <input type="text" name="person" required class="form-control"/>

                </div>

                </div>
                 
                 <div class="col-md-12">

			  <div class="form-group">

                <label>Venue</label>

   <input type="text" name="venue" required class="form-control"/>

                </div>

                </div>

                 <div class="col-md-12">

                 <label>Description</label>

			      <div class="form-group">

                    <textarea name="desc" class="form-control"  cols="50" rows="3"> </textarea>

                </div>

                </div>

                <br>

             

                     <div class="col-md-12">

			  <div class="form-group">

                 <input type="hidden" name="add" value="meeting"/>
 <input type="hidden" name="franchisephoneno" value="<?php echo $requests[0]->e_phonenumber ?>">


                <input type="submit" name="submit" value="Save" class="btn btn-info" />

                </div>

                </div>



          	</div>

          	

          </div>

        

                      </form>

              </div>



            </div>

            <!-- /.modal-content -->

          </div>
</td>

</tr>
                                  
                                  
                </tbody>

            

              </table>

            

            <button id="copy-table-btn" onclick="copyTable()">Copy Table</button>

            </div>

            <!-- /.box-body -->

          </div>



          <!-- /.box -->



        </div>



      </div>
   
                    

                

              


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
					url : '<?php echo site_url(); ?>company/add_lead_comment/'+ida,
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

