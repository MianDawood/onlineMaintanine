<?php include('header.php'); ?>


<section class="hero-section ">
  <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner">
    <?php foreach ($adverts as $keyy => $value) { ?>
      <div class="carousel-item <?php echo $keyy == 0 ? 'active' : '' ?>">
       <a href="<?php echo $value->a_description; ?>" style="cursor: pointer;"> <img class="d-block w-100" src="<?= base_url() ?>public/user_img/<?= $value->a_img; ?>" alt="<?php echo $value->a_name; ?>"> </a>
      </div>
      <?php  } ?>
    
    </div>
    <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="sr-only">Next</span>
    </a>
  </div>

  <div class="search " style="width: 100%;height: auto;">
<div class="container">
  <div class="row">
   
     <!-- <div class="col-lg-3 mb-2 text-center">
    <div class="dropdown">
      <button type="button" class="btn px-4 my-2 dropdown-toggle" data-toggle="dropdown">
        Dropdown button
      </button>
      <div class="dropdown-menu">
        <a class="dropdown-item" href="#">Link 1</a>
        <a class="dropdown-item" href="#">Link 2</a>
        <a class="dropdown-item" href="#">Link 3</a>
      </div>
    </div>
  </div> -->
  
  <!-- <div class="col-lg-3 mb-2 text-center ">
    <div class="dropdown">
      <button type="button" class="btn px-4  my-2 dropdown-toggle" data-toggle="dropdown">
        Dropdown button
      </button>
      <div class="dropdown-menu">
        <a class="dropdown-item" href="#">Link 1</a>
        <a class="dropdown-item" href="#">Link 2</a>
        <a class="dropdown-item" href="#">Link 3</a>
      </div>
    </div>
  </div> -->

  
  <!-- <div class="col-lg-3 mb-2 text-center">
    <div class="dropdown">
      <button type="button" class="btn px-4 my-2  dropdown-toggle" data-toggle="dropdown">
        Dropdown button
      </button>
      <div class="dropdown-menu">
        <a class="dropdown-item" href="#">Link 1</a>
        <a class="dropdown-item" href="#">Link 2</a>
        <a class="dropdown-item" href="#">Link 3</a>
      </div>
    </div>
  </div> -->

  <div class="col-lg-3 mb-2 text-center">
    <!-- <button class=" search-btn  px-4 my-2 "> Search</button> -->
    </div>

</div>
</div>


  </div>
  </section>




<!-- ************************************* FEATURED-FRANCHISES section *************************** -->

  <section class=" container FEATURED-FRANCHISES">

    <h1 class="heading mt-5">FEATURED FRANCHISES</h1>
    <p class="description">Top Franchise Choices To Consider</p>

    
 <div class="row mt-5 mb-4">

          <?php 
              if(!empty($featured)):
              foreach($featured as $company):
              
          ?>
       <div class="col-lg-4 ">
          <div class="card mb-3">
            <a href="<?= site_url(); ?>company/<?= $company->co_slug;?>" style="cursor: pointer;">
           <div class="card-body feature-card-body">
            <img src="<?= base_url('public/user_img/'.$company->company_images[0]->img_name)?>" height="100%" weight="100%">
            </div>
            <div class="feature-card-footer">
              <span><?php echo $company->co_name; ?></span><?php echo $company->co_slogan; ?>
            </div>
             </a>
          </div>
        </div>
        <?php 
       endforeach;
       endif;
       ?>

  
   
      </div>

      <div class="col-lg-12">
      <div class="view-more mb-3">
        <a href="<?php echo base_url('companies-directory')?>">
      <button style="cursor: pointer;">View More Franchises</button>
      </a>
    </div>
    </div>
  </section>

<!-- ******************************** malls section ********************************* -->


  <section class=" container malls">

    <h1 class="heading mt-5 py-5">MALLS</h1>
  <div class="row mt-4">

  <?php 
              if(!empty($property)):
              foreach($property as $propert):
             
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
       endforeach;
       endif;
       ?>

  </div>
  <div class="col-lg-12" style="margin-bottom: 20px;">
      <div class="view-more">
        <a href="<?php echo base_url('view-property-list')?>" style="cursor: pointer;">
      <button style="cursor: pointer;">View More</button>      <br><br>
      </a>

    </div>
    </div>

  </section>


  <!-- ********************************************* franchise call ************************************************ -->


  <!-- <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img class="d-block w-100" src="images/franchisecall.png" alt="First slide">
      </div>
      <div class="carousel-item">
        <img class="d-block w-100" src="images/franchisecall.png" alt="Second slide">
      </div>
      <div class="carousel-item">
        <img class="d-block w-100" src="images/franchisecall.png" alt="Third slide">
      </div>
    </div>
    <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="sr-only">Next</span>
    </a>
  </div> -->

<!-- ********************** swiper **************************-->


<div class="container mt-5 mb-5">
  <div class="row franchise-card-row">
  <div class="swiper mySwiper">
    <div class="swiper-wrapper">
    <?php foreach ($stories as $key => $value) {?> 
      <div class="swiper-slide"> 
        <div class="card franchisepk-card" >
        <div class="card-body">
          <h2 class="card-title franchisepk-card-title">Franchise<span>pk.</span>com</h2>  
          
            <ul class="breadcrumbs">
              <li class="breadcrumbs-item"><a href="#" style="cursor: pointer;">BRANDS</a></li>
              <p class="breadcrumbs-item">|</p>
              <li class="breadcrumbs-item"><a href="#" style="cursor: pointer;">INVESTERS</a></li>
              <p class="breadcrumbs-item">|</p>
              <li class="breadcrumbs-item" aria-current="page" style="cursor: pointer;">MALLS</li>
            </ul>
         
            <p class="dash-line" >----------------------------------------------------------</p>
    
          <h2 class="signed">MOU SIGNED</h2>
          <p class="dancing-script">between</p>
         <p class="dancing-script">  <?php echo $value->story_brand ?></p>
          <img src="<?= base_url() ?>public/user_img/<?=  $value->story_image; ?>" class="franchise-card-logo">
    
          <p class="dancing-script" > And </p>
          <h3><span class="dancing-script"><?php echo $value->story_name ?></span></h3>
          <p><?php echo $value->story_city ?></p>
          <p class="dancing-script">Through</p>
          <h2 class="font-weight-bold">FRANCHISE <span>PAKISTAN</span></h2>
          <p class="font-weight-bold"><?php echo $value->story_desc; ?></p>
          <span> <p><?php echo $value->story_date; ?></p></span>
        </div>
      </div></div>
      <?php } ?>
   
  </div>
  <div class="swiper-pagination"></div>
</div>
</div>
</div>


<!-- ************************** news and articles ************************************ -->
<!-- <section class="  container new & articles">

  
  <h1 class="heading mt-5"> NEWS & ARTICLES</h1>
  <p class="text-center mt-3 news-subtitle ">Follow the most remarkable events in franchising with us! The latest companies news, success stories, franchise feature articles, announcements of upcoming events and useful information on franchising</p>
  <div class="row mt-5">
    <div class="col-lg-4 mb-3">
      <div class="card news-card" >
        <img class="card-img-top" src="images/news.png" alt="Card image cap">
        <div class="card-body news-card-body">
        
          <p class="card-text  news-card-text">Building Brand Awareness for Franchises</p>
          <a href="#"><u  class="all-articles">All Articles</u></a>
         
        </div>
      </div>
    </div>

    <div class="col-lg-4 mb-3">
      <div class="card news-card" >
        <img class="card-img-top" src="images/news.png" alt="Card image cap">
        <div class="card-body news-card-body">
          <p class="card-text news-card-text">6.7.2023</p>
          <p class="card-text  news-card-text">New Realities if franchises after the pandemic</p>
         
        </div>
      </div>
    </div>

    <div class="col-lg-4 mb-3">
      <div class="card news-card" >
        <img class="card-img-top" src="images/news.png" alt="Card image cap">
        <div class="card-body news-card-body">
          <p class="card-text news-card-text">6.7.2023</p>
          <p class="card-text  news-card-text">New Realities if franchises after the pandemic</p>
          </div>
      </div>
    </div>


  </div>
<div class="all-news-btn">
  <button class="all-news mt-4 mb-4 align-right" >All News</button>
</div>
</section> -->
 
<!-- **************** review ************************* -->

<div class="container">
  <div class="row review-row">


<div class="col-lg-6 mb-3">
    <div class="card text-center">
      <div class="review-card-header">
        <h1>Franchisee Reviews</h1>
      </div>
      <div class="review-card-body">
        <p class="review-card-text mt-5">Show more Franchisee
          Reviews</p>
          <button class="review-card-btn mt-4 mb-4">All reviews</button>
      </div>
    </div>
    </div>
      

    
<div class="col-lg-6">
  <div class="card text-center">
    <div class="review-card-header">
      <h1>Franchisers Reviews</h1>
    </div>
    <div class="review-card-body">
      <p class="review-card-text mt-5">Show more Franchiser
        Reviews</p>
        <button class="review-card-btn mt-4 mb-4">All reviews</button>
    </div>
  </div>
  </div>
  </div>
  </div>
    
<!-- ****************** help ******************************* -->

<section class="help">

  <div class="container">
    <div class="row mt-5">
      <div class="col-lg-6">
        <h3 class="help-title">
        Discover Your Ideal Franchise with Franchise Pakistan
        </h3>

        <p class="help-description">
        Franchise Pakistan is committed to assisting aspiring entrepreneurs in finding the best franchise business opportunities across the nation. With access to over 100 top franchises throughout Pakistan, we are your gateway to launching a successful venture.
How Franchisepk.com Helps You Find the Right Franchise
Comprehensive Industry Coverage At Franchisepk.com, we offer a wide range of franchise opportunities across various sectors:
<br>
<h3 class="help-title mt-3">Opportunities across various sectors:</h3>
<p class="help-description">
<br>
• Food and Beverage
<br>
• Retail
<br>
• Education
<br>
• Healthcare
<br>
• Cleaning Services
<br>
• Delivery Services
    </p>
        </p>

        <h3 class="help-title mt-3">
          User-Friendly Search Tools
        </h3>
        <p class="help-description">
        Navigating through our extensive franchise listings is easy! Our user-friendly search tools allow you to:
Explore detailed franchise profiles Access essential information about investment requirements Understand candidate criteria for each franchise
This makes evaluating and selecting the best franchise opportunity simpler than ever.
        </p>
      </div>

<div class="col-lg-6">
<iframe width="560" height="500" src="https://www.youtube.com/embed/HIyjk_WpTzU?si=6RKFlTxBvEKbslTG" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
</div>

    </div>
    <div class="row">
      <div class="col-lg-12">
      <h3 class="help-title mt-3">
          User-Friendly Search Tools
        </h3>
  
      <p class="help-description">Franchise Pakistan team of dedicated Business Development Officers (BDOs) is here to support you every step of the way. They actively engage with leads generated for the brands we represent, ensuring:
Seamless communication between potential investors and franchise brands
Assistance in securing the perfect franchise deal</p>
    </div>
  </div>

  <div class="row">
      <div class="col-lg-12">
      <h3 class="help-title mt-3">
      Turn Your Entrepreneurial Dreams into Reality
        </h3>
  
      <p class="help-description">Franchisepk.com, finding your ideal franchise has never been easier. Let us help you embark on your entrepreneurial journey today!</p>
    </div>
  </div>
  </div>
</section>


<?php include('footer.php'); ?>