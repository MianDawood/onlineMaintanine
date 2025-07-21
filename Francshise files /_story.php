<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Story Table
        <small>preview of Story table</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Tables</a></li>
        <li class="active">Story</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">View All Story:</h3>
              
              
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
              <table class="table table-hover" id="example1">
               <thead>
               	<tr>
                  <th>S.No</th>
                      <th>Title</th>
                      <th>Brand</th>
					  <th>City </th>
                      <th>Desc</th>
                      <th>Image</th>
                      <th>Status</th>
                    
                      <th>Edit</th>
                      <th>Delete</th>
                      
                </tr>
               </thead>
                <tbody>
                    <?php
                        $i = 1;
                        foreach($stories as $story ){
                    ?>
                    <tr>
                      <td><?= $i; ?></td>
                      <td><?= $story->story_name; ?></td>
                      <td><?= $story->story_brand; ?></td>
                      <td><?= $story->story_city; ?></td>
                 
                      <td><?= $story->story_desc; ?></td>
                      <td><img style="width: 50px;height:50px;" src="<?= base_url() ?>public/user_img/<?= $story->story_image;?>"></td>

                        <?php

                        if ( $story->story_status == 1 ) {

                            ?>

                            <td><a href="<?= site_url(); ?>Testimonials/changestatus/<?= $story->story_id; ?>/0" class="btn btn-price"><button class="btn btn-success"><i class="fa fa-check"></i></button></a></td>

                            <?php

                        } else {

                            ?>

                            <td><a href="<?= site_url(); ?>Testimonials/changestatus/<?= $story->story_id; ?>/1" class="btn btn-price"><button class="btn btn-warning"><i class="fa fa-times"></i></button></a></td>

                            <?php

                        }

                      ?>                      

                      

<td>
    <a href="<?= site_url(); ?>admin/editstory/<?= $story->story_id; ?>">
        <i class="fa fa-edit"></i>
    </a>
</td>


                  <td>
                          <a href="<?= site_url(); ?>testimonials/deltestory/<?= $story->story_id; ?>" onclick="return confirm('Are you sure you want to delete this story?');" >
                              <i class="fa fa-trash"></i>
                          </a>
                      </td>

                    </tr>
                    <?php
                        $i++;
                        }
                    ?>
                    </tbody>
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
  