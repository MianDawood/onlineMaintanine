<?php include('header.php'); ?>

<!-- ************************************* FEATURED-FRANCHISES section *************************** -->

    
  <section class=" container FEATURED-FRANCHISES">
   

    <!-- <h1 class="heading mt-5">FEATURED FRANCHISES</h1> -->
    <p class="description">Find a Perfect Property</p>
  
    
 <div class="row mt-5 mb-4">

 <?php 
      
				
				foreach( $property as $propert ) {
                    ?>  
      <div class="col-lg-4">
      <div class="card malls-card mb-3">
        <img class="card-img-top" src="<?= base_url(); ?>public/user_img/<?= $propert->pimage;?>" height="50%" width="100%" alt="Card image cap">
        <div class="card-body malls-card-body">
          <h5 class="card-title malls-card-title"><?= substr($propert->pName,0, 18); ?></h5>
          <!-- <p class="malls-card-text "> <span>Size</span> <?= substr($propert->pSize,0,50); ?></p> -->
          <p class="malls-card-text pl-3"> <span>City</span> <?= substr($propert->Pcity,0,50); ?></p>
          <!-- <p class="malls-card-text pl-3"> <span>Address</span> <?= substr($propert->pFloor,0,150); ?></p> -->
          <p class="malls-card-text">I <?= substr($propert->pMessage,0,500); ?></p>
          <button href="<?= site_url(); ?>newhome/single_property_details/<?= $propert->p_id;?>" class="read-more" style="cursor: pointer;">Read More</button>
        </div>
      </div>
    </div>
    <?php 
      }
       ?>

 
      

      

     </div>
  </div>
  </section>

  <!-- ******************************** footer ****************************************** -->

  <?php include('footer.php'); ?>