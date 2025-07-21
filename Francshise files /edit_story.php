<div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

      <h1>

        Story

        <small>Preview</small>

      </h1>

      <ol class="breadcrumb">

        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

        <li><a href="#">pages</a></li>

        <li class="active">Add Story</li>

      </ol>

    </section>

    <!-- Main content -->

    <section class="content">

      <div class="row">

     

        <!-- left column -->

        <div class="col-md-12">

        <?php if($msg = $this->session->flashdata('msg')) { ?>

    

     <div class="alert alert-success alert-dismissible" role="alert">

           <strong><?php echo $msg ?></strong> 

                    </div>

                    <?php } ?>

    <?php if($msg = $this->session->flashdata('failed')) { ?>

     <div class="alert alert-danger alert-dismissible" role="alert">

           <strong><?php echo $msg ?></strong> 

                    </div>

                    <?php } ?>

          <!-- general form elements -->

          <div class="box box-primary">

            <div class="box-header with-border">

              <h3 class="box-title">Add New Story Here: </h3>

            </div>

            <!-- /.box-header -->

            <!-- form start -->

              <form action="<?= site_url(); ?>Testimonials/addstorypro" method="post" enctype="multipart/form-data">
                  <div class="row">
                    <div class="col-md-6">
						<div class="form-group">
							<label>Name</label>
                        <input type="Text" name="name" class="form-control" placeholder="Name" />
						</div>
                        
                    </div>
                    <div class="col-md-6">
						<div class="form-group">
							 <label>Brand</label>
                      <input type="text" name="brand" class="form-control" placeholder="Brand" />
						</div>
                     
                    </div>
                    
                    <div class="col-md-6">
						<div class="form-group">
							   <label>City</label>
                        <input type="Text" name="city" class="form-control" placeholder="City" />
						</div>
                     
                    </div>
                    <div class="col-md-6">
						<div class="form-group">
							<label>Upload An Image</label>
                      <input type="file" name="img" class="form-control" />
						</div>
                      
                    </div>
                    <div class="col-md-12">
						<div class="form-group">
							<label>Desc</label>
							 <textarea name="description" class="form-control" placeholder="Description"></textarea>
						</div>
                       
                    </div>
                    <div class="col-md-12">
						<div class="form-group">
							 <button type="submit" class="btn btn-primary fluid border-radius">Submit Story </button>
						</div>
                     
                    </div>
                  </div>
                </form>

          </div>

          <!-- /.box -->

        </div>

      </div>

      <!-- /.row -->

    </section>

    <!-- /.content -->

  </div>