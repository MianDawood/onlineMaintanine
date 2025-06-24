<?php include('header.php'); ?>
<script src="https://www.google.com/recaptcha/api.js"></script>
<style>
    .profile-container {
        display: inline-block;
        border: 5px solid #3b82f6; /* Adjust the color to match your preferred border color */
        border-radius: 50%;
        padding: 3px; /* Adds spacing between the border and the image */
        overflow: hidden;
        width: 150px; /* Set a fixed width and height for the circle */
        height: 150px;
    }
    .profile-container img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover; /* Ensures the image covers the container without distortion */
    }
    .styled-input {
        width: 100%; /* Full width */
        padding: 12px 20px; /* Inner padding for comfort */
        font-size: 16px; /* Larger, more prominent text */
        font-weight: bold; /* Make the text more prominent */
        color: #333; /* Text color */
        background-color: #f7f7f7; /* Light background */
        border: 2px solid #3b82f6; /* Border color to make it stand out */
        border-radius: 8px; /* Rounded corners */
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
        outline: none; /* Remove the default outline */
        transition: all 0.3s ease; /* Smooth transition for effects */
    }

    /* Change style on focus */
    .styled-input:focus {
        border-color: #2563eb; /* Darker border on focus */
        background-color: #ffffff; /* Change background color on focus */
        box-shadow: 0px 6px 10px rgba(0, 0, 0, 0.15); /* Stronger shadow */
    }

    .styled-select {
        width: 100%; /* Full width */
        padding: 12px 20px; /* Inner padding */
        font-size: 16px; /* Larger, prominent text */
        font-weight: bold; /* Make text prominent */
        color: #333; /* Text color */
        background-color: #f7f7f7; /* Light background */
        border: 2px solid #3b82f6; /* Blue border color */
        border-radius: 8px; /* Rounded corners */
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        appearance: none; /* Remove default arrow */
        -webkit-appearance: none;
        -moz-appearance: none;
        outline: none; /* Remove default outline */
        background-image: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="black"><path d="M7 10l5 5 5-5z"/></svg>');
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 12px; /* Size of the arrow */
    }

    .styled-select:focus {
        border-color: #2563eb; /* Darker border on focus */
        background-color: #ffffff; /* Change background on focus */
        box-shadow: 0px 6px 10px rgba(0, 0, 0, 0.15); /* Stronger shadow */
    }
</style>


<!-- ********************* login signup form ******************** -->

    <div class="login-signup">
<div class="container login-signup-container" >
     <div class="login-signup-form p-5">
        <div class="row">
        <div class="col-lg-5 ">
            <h2 class="form-title">Log In</h2>
        <div class="mb-3 mt-4">
          <form action="<?php echo base_url('user/login')?>" method="post">
            <input type="text" placeholder="Username" class="styled-input" name="email">
            </div>
            <input type="password" placeholder="Password" class="styled-input" name="pass">
        
<p class="forget-password mt-2">Forgot password?</p>

<button class="form-login-btn">Log In</button>
  </form>
        </div>


<div class="col-lg-2"></div>
        <div class="col-lg-5 " >
            <h2 class="form-title">Sign Up</h2>
            <form action="<?php echo base_url('register-process')?>" method="post">
            <div class="mb-3 mt-4">
                <input type="text" placeholder="First Name" class="styled-input" name="f_name">
                </div>

                <div class="mb-3">
                    <input type="text" placeholder="Last Name" class="styled-input" name="l_name">
                    </div>

                    <div class="mb-3 ">
                        <input type="text" placeholder="Brand Name" class="styled-input"  name="company">
                        </div>

                        <div class="mb-3 ">
                            <input type="text" placeholder="City" class="styled-input"  name="city">
                            </div>

                            <div class="mb-3 ">
                                <input type="number" placeholder="Whatsapp Number" class="styled-input"  name="contact">
                                </div>

                                <div class="mb-3 ">
                                    <input type="email" placeholder="Email" class="styled-input" onkeyup="validateEmail()" name="User_email">
                                    </div>

                                    
                                <div class="mb-3 ">
                                    <input type="password" placeholder="Password" class="styled-input" name="password">
                                    </div>

                                    <div class="mb-3 ">
                                    <input type="password" required id="name" name="name" placeholder="Confirm Password" onkeyup="passwordValid()" class="confirmpass styled-input">
                                    <input type="hidden" class="rq-form-control" name="orderno" value="<?php echo 'FP'.rand() ?>">
                                    </div>
                                    <div class="g-recaptcha brochure__form__captcha" data-sitekey="6Lc6DzkaAAAAAJ84n_lf2DtJTaUNy0P7rfTna_px"></div>

                                    <button class="narkin-btn">Sign Up</button>

                                    </form>
                        </div>
    </div>
    </div>
</div>
</div>
    
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="<?php echo base_url('public/newui/js/myapp.js')?>"></script>
<?php include('footer.php'); ?>