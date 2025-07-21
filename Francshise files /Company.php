<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Company extends CI_Controller {

public function add_property_lead()
{
   
     $result= $this->company_m->add_property_lead();
  
     if($result == true)
     {
         	$this->session->set_flashdata('success', '<h2 class="alert alert-success">Request  Submitted  SuccessFully...</h2>');

            redirect( site_url() . 'Newhome/single_property_details/'.$this->input->post('pid')  );
     }
}



	public function add_brand_meeting($id)

	{

		  if($this->session->userdata('u_id'))

		 {    

			 $userid=$this->session->userdata('u_id');

			 $name= $this->session->userdata('u_name');

		 }

		else

		{   $userid=$this->session->userdata('emp_id');

			$name= $this->session->userdata('emp_name');

		}

		 

		$data=array(

			 'meeting_date'  => $this->input->post('date'),

			 'meeting_time'  => $this->input->post('time'),

			 'meeting_venue'  => $this->input->post('venue'),

			 'meetiing_desc'  => $this->input->post('desc'),

			 'com_contact_person'  => $this->input->post('person'),

			 'emp_id'  => $userid,

			 'emp_name'  => $name,		

		

		);

  

		  $result = $this->company_m->add_brand_meeting($data,$id);

		   

		if($result == true)

		{

	  $companymeeting=$this->company_m->companymeetingsms($data,$this->input->post('com_id'));

	  $adminmeetingsms=$this->company_m->adminmeetingsms($data);

		  $adminmeetingsms=$this->company_m->empmeetingsms($data);

	  $franchisemeeting=$this->company_m->franchisemeetingsms($data);

			echo 'done'; exit;

//			if(!$this->session->userdata('emp_id'))

//			{

//				 redirect( site_url() . 'Admin/view_requests/');

//			}			

//			else

//			{

//				 redirect( site_url() . 'Team/view_requests/');

//				

//			}

		}

			

		

	}

	

	

	public function print_properties( $id )

		{

            

            $result['data'] = $this->company_m->print_properties( $id );

            $this->load->view('company/properties_brand', $result);

            

        }

	

	 public function properties_status_change($b_id, $status) {

            

            $this->company_m->properties_status_change($b_id, $status);

		 if($this->session->userdata('emp_special'))

		 {

			   redirect( site_url() . 'Team/view_properties' );

			 exit;

		 }

		 else

		 {

			 redirect( site_url() . 'Admin/view_proerties' );

			 exit;

		 }

            

            

        }

	

    public function add_properties_comment($id)

	 {

		 

		       $data=array(

			 'p_comment'  => $this->input->post('comment'),

			 'p_commentby'  => $this->input->post('name'),

			 'p_commentdate'  => date('d/m/Y')

	                  	);

	 	

		  $result = $this->company_m->add_properties_comment($data,$id);

		if($result == true)

		{

				if($this->session->userdata('emp_special'))

		 {

			   redirect( site_url() . 'Team/view_properties' );

			 exit;

		 }

		 else

		 {

			 redirect( site_url() . 'Admin/view_brand' );

			 exit;

		 }

		}

			

		

	}

    

   public function print_brand( $id )

		{

            

            $result['data'] = $this->company_m->print_brand( $id );

            $this->load->view('company/print_brand', $result);

            

        }

	

	 public function brand_status_change($b_id, $status) {

            

            $this->company_m->brand_status_change($b_id, $status);

		 if($this->session->userdata('emp_special'))

		 {

			   redirect( site_url() . 'Team/view_brand' );

			 exit;

		 }

		 else

		 {

			 redirect( site_url() . 'Admin/view_brand' );

			 exit;

		 }

            

            

        }

	

    public function add_brand_comment($id)

	 {

		 

		       $data=array(

			 'brand_comment'  => $this->input->post('comment'),

			 'brand_commentby'  => $this->input->post('name'),

			 'brand_commentdate'  => date('d/m/Y')

	                  	);

	 	

		  $result = $this->company_m->add_brand_comment($data,$id);

		if($result == true)

		{

				if($this->session->userdata('emp_special'))

		 {

			   redirect( site_url() . 'Team/view_brand' );

			 exit;

		 }

		 else

		 {

			 redirect( site_url() . 'Admin/view_brand' );

			 exit;

		 }

		}

			

		

	}

	public function cancel_meeting($id,$status)

	{

		 

		$data=array( 'meeting_status'  =>$status);

			

		  $result = $this->company_m->cancel_meeting($data,$id);

		if($result == true)

		{

				if ($this->session->userdata('emp_special'))

			{

				 redirect( site_url() . 'Team/view_meeting/');

			}			

			elseif ($this->session->userdata('u_id'))

			{

				 redirect( site_url() . 'Admin/view_meeting/');

			}

			

		}

			

		

	}

	

	// ---------------------------------------------------------------------

	 public function investorrequests() {



       if (!$this->session->userdata('loggedin'))

        {

           

                redirect( site_url() . 'home/' );

        }

        else{

            $result['categories'] = $this->home_m->get_categories();

            $result['investrequests']   = $this->home_m->get_investrequests();

            

            $this->load->view('include/header_view',$result);

            $this->load->view('company/investorrequest_view',$result);

            $this->load->view('include/footer_view');

}

        }

	

	public function add_show($id)

	{

		 

		$data=array(

			 'show_comment'  => $this->input->post('comment'),

			 'show_city'  => $this->input->post('city'),

			 'u_id'  => $this->session->userdata('emp_id'),

			 'emp_name'  => $this->session->userdata('emp_name'),

			 'e_id'  => $id

			

		

		);

			

		  $result = $this->company_m->add_show($data,$id);

		if($result == true)

		{

				echo "done"; exit;

			

		}

			

		

	}

	

	public function cancl_meeting($id)

	{

		 

		$data=array( 'meeting_status'  =>'2');

			

		  $result = $this->company_m->update_show($data,$id);

		if($result == true)

		{

				echo "done"; exit;

			

		}

			

		

	}

	public function update_show($id)

	{

		 

		$data=array(

			 'show_comment'  => $this->input->post('comment'),

			 'show_city'  => $this->input->post('city'),

			 'u_id'  => $this->session->userdata('emp_id'),

			 'emp_name'  => $this->session->userdata('emp_name'),

			 'e_id'  => $this->input->post('req_id')

			

		

		);

			

		  $result = $this->company_m->update_show($data,$id);

		if($result == true)

		{

				

			

		}

			

		

	}

	

	public function update_meeting($id)

	{

		  if($this->session->userdata('u_id'))

		 {    

			 $userid=$this->session->userdata('u_id');

			 $name= $this->session->userdata('u_name');

		 }

		else

		{   $userid=$this->session->userdata('emp_id');

			$name= $this->session->userdata('emp_name');

		}

		$data=array(

			 'meeting_date'  => $this->input->post('date'),

			 'meeting_time'  => $this->input->post('time'),

			 'meeting_venue'  => $this->input->post('venue'),

			 'meetiing_desc'  => $this->input->post('desc'),

			 'com_id'  => $this->input->post('com_id'),

			 'com_name'  => $this->input->post('company'),

			 'com_contact_person'  => $this->input->post('person'),

			 'franchisephoneno'  => $this->input->post('franchisephoneno'),

			 'franchisee_name'  => $this->input->post('franchise'),

			 'request_id'  => $this->input->post('request'),

			 'emp_id'  => $userid,

			 'emp_name'  => $name,

		

		);

		  $result = $this->company_m->update_meeting($data,$id);

		if($result == true)

		{

		   $companymeeting=$this->company_m->companymeetingsms($data,$this->input->post('com_id'));

		  $adminmeetingsms=$this->company_m->adminmeetingsms($data);

		  $adminmeetingsms=$this->company_m->empmeetingsms($data);

		  $franchisemeeting=$this->company_m->franchisemeetingsms($data);

// print_r($this->session->userdata('emp_name'));
// echo 'EMp_id'; 
// print_r($this->session->userdata('u_name'));
// echo 'Admin_id'; 
//  exit;
if ($this->session->userdata('emp_id') && $this->session->userdata('emp_name') )

			{

				 redirect( site_url() . 'Team/view_meeting/');

			}			

elseif ($this->session->userdata('u_id') && $this->session->userdata('u_name'))

			{

				 redirect( site_url() . 'Admin/view_meeting/');

			}

		}

			

		

	}

	

	

	

	public function add_meeting($id = false)

	{
	    

		  if($this->session->userdata('u_id'))

		 {    

			 $userid=$this->session->userdata('u_id');

			 $name= $this->session->userdata('u_name');

		 }

		elseif($this->session->userdata('emp_id'))

		{   $userid=$this->session->userdata('emp_id');

			$name= $this->session->userdata('emp_name');

		    $companyphone='';

		}

		else

		{

			$userid=$this->input->post('emp_id');

			$name=$this->input->post('emp_name');

			$slug=$this->input->post('com_slug');

			$empphone=$this->input->post('emp_no');

			$companyphone=$this->input->post('no');

			$id='0';

			$condition='daud';

		}

		 

		$data=array(

			 'meeting_date'  => $this->input->post('date'),

			 'meeting_time'  => $this->input->post('time'),

			 'meeting_venue'  => $this->input->post('venue'),

			 'meetiing_desc'  => $this->input->post('desc'),

			 'com_id'  => $this->input->post('com_id'),

			 'com_name'  => $this->input->post('company'),

			 'com_contact_person'  => $this->input->post('person'),

			 'franchisephoneno'  => $this->input->post('franchisephoneno'),

			 'franchisee_name'  => $this->input->post('franchise'),
			 'Email'  => $this->input->post('email'),

			 'company_contact_no'  =>$companyphone,

			 'emp_id'  => $userid,

			 'emp_name'  => $name,

			 'request_id'  => $id,


		);


		  $result = $this->company_m->add_meeting($data,$id);

		   

		if($result == true)

		{
		  

		  $companymeeting=$this->company_m->companymeetingsms($data,$this->input->post('com_id'));

		 $adminmeetingsms=$this->company_m->adminmeetingsms($data);
		$adminmeetingsms=$this->company_m->empmeetingsms($data,$empphone);
		$franchisemeeting=$this->company_m->franchisemeetingsms($data);

			if($this->session->userdata('emp_id'))

			{


 echo 'done'; exit;
				

			}			

			elseif($this->session->userdata('u_id'))

			{
			    
			 //   $this->session->set_flashdata('success', '<h2 class="alert alert-success">Meeting Request  Submitted  SuccessFully</h2>');

				//   redirect( site_url() . 'Admin/view_requests/');
				
			

	       	echo 'done'; exit;	 

				

			}

			elseif($condition == 'daud')

			{

				$this->session->set_flashdata('success', '<h2 class="alert alert-success">Meeting Request  Submitted  SuccessFully</h2>');

				  redirect( site_url() . 'Newhome/single_company_view/'.$slug);

			}

		}

			

		

	}



	 public function investor_more_info_add($slug){

            

                $disableButton="all";

           $result= $this->company_m->investor_more_info_add();

         

			

			if($result != false)

			{

				$this->session->set_flashdata('success', '<span class="alert alert-success">Request  to Investor  Submitted  SuccessFully</span>');

		// $this->company_m->sendEmailsRequestMoreInfo( $slug, $this->input->post('email') );

         // $name=$this->input->post('firstname') . " " . $this->input->post('lastname');

         // $this->company_m->sendEmailscontactinfo( $slug, $this->input->post('email'),$this->input->post('number'),$this->input->post('message'),$name);

       //    $name=$this->input->post('firstname') . " " . $this->input->post('lastname'); 

        // $this->company_m->sendEmailToCompany( $slug, $this->input->post('email'),$name,$this->input->post('number'),$this->input->post('message'));

				

				

            redirect( site_url() . 'ucompany/single_investor_view/' . $slug );

				

				

			}

			else

			{

					$this->session->set_flashdata('success', '<span class="alert alert-danger">'.$this->input->post('email').  ' has Already Requested To This Investor</span>');

            redirect( site_url() . 'ucompany/single_investor_view/' . $slug );

			}

				



        }

         public function add_user_comment($id)

	          {

		 

		       $data=array(

			 'comment'  => $this->input->post('comment'),

			 'comment_by'  => $this->input->post('name'),

	                  	);

	 	

		  $result = $this->company_m->add_user_comment($data,$id);

		if($result == true)

		{

				echo "done"; exit;

			

			

		}

			

		

	}

	

	

	public function add_com_comment($id)

	{

		$data=array(

			 'e_comment'  => $this->input->post('comment'),

			 'comment_by'  => $this->input->post('name'),

		);

		  $result = $this->company_m->add_com_comment($data,$id);

		if($result == true)

		{

			 if($this->session->userdata('emp_special'))

		 {

			   redirect( site_url() . 'Team/view_companies' );

			 exit;

		 }

		 else

		 {

			redirect( site_url() . 'Admin/view_companies' );

			 exit;

		 }
			

		}

			

		

	}

	

	public function add_comment($id)

	{

		 

		$data=array(

			 'meetiing_desc'  => $this->input->post('comment'),

			 'emp_name'  => $this->input->post('name'),

			

		

		);

		  $result = $this->company_m->add_comment($data,$id);

		if($result == true)

		{

				echo "done"; exit;

// 			/*if($this->session->userdata('emp_id'))

// 			{

// 				//redirect( site_url() . 'Team/view_requests' );

// 			}

// 			else

// 			{

// 				redirect( site_url() . 'admin/view_requests' );

// 			}*/

			

		}

			

		

	}
	
	
	
	
	
		public function add_lead_comment($id)

	{
		if ($this->session->userdata('emp_id') && $this->session->userdata('emp_name') )
		{
			$data=array(

				'comment'  => $this->input->post('comment'),
				'emp_name'  => $this->session->userdata('emp_name'),
				'lead_id'  => $id,
				'status'  => 0
		   );
		}
		 else
		 {
			$data=array(

				'comment'  => $this->input->post('comment'),
				'emp_name'  => 'Admin',
				'lead_id'  => $id,
				'status'  => 1
		   );
		 }
		


		  $result = $this->company_m->add_lead_comment($data,$id);

		if($result == true)

		{
	//redirect( site_url() . 'Team/view_sigle_requests/'.$id );
if ($this->session->userdata('emp_id') && $this->session->userdata('emp_name') )
			{
				redirect( site_url() . 'Team/view_sigle_requests/'.$id );
			}
			else
			{
				redirect( site_url() . 'admin/view_sigle_requests/'.$id );

			}

		}

	}


public function delete_comment($comment_id, $request_id)
{
    $this->db->where('comment_id', $comment_id)->delete('lead_comments');
	redirect( site_url() . 'admin/view_sigle_requests/'.$request_id );
}


	public function bulk_delete_comments($request_id) {
		$ids = $this->input->post('comment_ids');
		if (!empty($ids)) {
			$this->db->where_in('comment_id', $ids)->delete('lead_comments');
		}
		redirect('admin/view_sigle_requests/'.$request_id);
	}
	

	

	public function investor_status($id,$status)

	{

		if($status == 0)

		{

			$investor = 1;

		}

		elseif($status == 1)

		{

			$investor = 0;

		}

		 $userdata = $this->session->userdata('userdata');

			$data=array(

			 'investor_status'  =>$investor,

			 'investor_by'  =>$this->session->userdata('u_firstname').$this->session->userdata('u_lastname')

		);

		

		  $result = $this->company_m->investor_status($id,$data);

		if($result == true)

		{

			redirect( site_url() . 'admin/view_requests' );

		}

			

		

	}

	public function requestsms()

	{

		   if (!$this->session->userdata('u_id'))

        {

           

                redirect( site_url() . 'home/' );

        }

		

    	$data['countries']    = $this->home_m->get_countries();

        $result['categories'] = $this->home_m->get_categories();

        $data['companies']    = $this->company_m->get_companies();

       // $data['contacts']=$this->company_m->sms_all_requests();

		

        $this->load->view('include/header_view', $result);

        $this->load->view('Company/requestsms_view',$data);

        $this->load->view('include/footer_view');	

		

	}

	

	public function check()

	{

		$api = new SMILE_API();

		$send = $api->sendto('03349047721','daud testing de');

		 print_r($send); exit;

	}

	

	

	

	 public function Smstorequest()

	{

		 if (!$this->session->userdata('u_id'))

        {

           

                redirect( site_url() . 'uhome/' );

        }

		

		//error_reporting(0);

		$data['contacts']=$this->company_m->sms_all_requests();

		

	$already_sent = array();

		$send_succ = 0;

		

		$api = new SMILE_API();

		

		

		 $allCompanySMS = $this->input->post('title');

          $allCompanySMS .= $this->input->post('message');

         $from         = $this->input->post('from');

            $to           = $this->input->post('to');

            $i            = 1;

		

	//$data['contacts']=array('03339277021','03109707275','03009080516'); 

	      

		foreach($data['contacts'] as $key => $value){

			

	foreach($value as $con =>$no){

	//		echo $no; echo '<br>';

	

 if( $i >= $from && $i <= $to ){

	 

	 	if(in_array($value,$already_sent)) continue;

	 	$send = $api->sendto($no,$allCompanySMS);

	 

	 	if($send == 'Message Sent Successfully!')

		{

			$already_sent[] = $no;

			$send_succ++;

		}

			$data['send'] = $no.": ".$send.',';

			

	 

	 

			}

	$i++;	}

		}

		 $this->session->set_flashdata('success', '<span class="alert alert-success">'.$data['send'].'</span>');         

				redirect( site_url() . 'Admin/view_requests' );	



		

	}

	

	public function companysms()

	{

		   if (!$this->session->userdata('u_id'))

        {

           

                redirect( site_url() . 'uhome/' );

        }

		

    	$data['countries']    = $this->home_m->get_countries();

        $result['categories'] = $this->home_m->get_categories();

        $data['companies']    = $this->company_m->get_companies();

        $data['contacts']=$this->company_m->sms_all_contact_company();

		

        $this->load->view('include/header_view', $result);

        $this->load->view('Company/sms_view',$data);

        $this->load->view('include/footer_view');	

		

	}

	

		

		



    public function Smstocompany()

	{

		 if (!$this->session->userdata('u_id'))

        {

           

                redirect( site_url() . 'uhome/' );

        }

		

		error_reporting(0);

		$data['contacts']=$this->company_m->sms_all_contact_company();

	

		$already_sent = array();

		$send_succ = 0;

		

		$api = new SMILE_API();

		

		

		 $allCompanySMS = $this->input->post('title');

          $allCompanySMS .= $this->input->post('message');

         $from         = $this->input->post('from');

            $to           = $this->input->post('to');

            $i            = 1;

		

	//$data['contacts']=array('03339277021','03109707275','03339277021','03109707275','03339277021','03109707275','03339277021','03109707275','03339277021','03109707275','03339277021','03109707275'); 

	      

		foreach($data['contacts'] as $key => $value){

		foreach($value as $con =>$no){

		

 if( $i >= $from && $i <= $to ){

	 	if(in_array($value,$already_sent)) continue;

	 	$send = $api->sendto($no,$allCompanySMS);

	 //echo $send;

	 	if($send == 'Message Sent Successfully!')

		{

			$already_sent[] = $no;

			

			$send_succ++;

		}

		$data['send'] = $no.": ".$send.',';

			//echo $no; echo '<br>';

	// print_r($data['send']);

	// print_r($already_sent);

			}

	$i++;	}

		}

		

		$this->session->set_flashdata('success', '<span class="alert alert-success">'.$data['send'].'</span>');

				 redirect( site_url() . 'Admin/view_companies' );	



		

	}

    

	

	

        public function index() {

             if (!$this->session->userdata('loggedin'))

        {

           

                redirect( site_url() . 'home/' );

        }

        else

        {

            $data['countries']    = $this->home_m->get_countries();

            $result['categories'] = $this->home_m->get_categories();

            $data['companies']    = $this->company_m->get_companies();

            $data['type']         = 'All';

            $this->load->view('include/header_view', $result);

            $this->load->view('company/company_view',$data);

            $this->load->view('include/footer_view');

        }

        } 

    

        // ---------------------------------------------------------------------

        

        public function company_add() {

        

            $company_id = $this->company_m->company_add();

		//echo $company_id;

		//s	exit;

			if($result != false)

			{

			

            $data       = $this->company_m->company_contact_person( $company_id );

            $image      = $this->company_m->upload_images($company_id);

				$this->session->set_flashdata('success', '<span class="alert alert-danger">company Added Successfully</span>');

            redirect( site_url() . 'company' );

			}

			else

			{

					$this->session->set_flashdata('success', '<span class="alert alert-danger">'.$this->input->post('name').  ' has Already registered</span>');

              redirect( site_url() . 'company' );

			}

        }

        

        // ---------------------------------------------------------------------

        

        public function single_company_view( $slug ) {

            $disableButton="none";

            $data                 = $this->company_m->single_company_view( $slug );

            $result['company']    = $data[0];

            $result['related']    = $this->company_m->related_companies( $slug, $result['company']->co_category_id );

            $result['categories'] = $this->home_m->get_categories();



            $this->load->view('include/header_view',$result);

            $this->load->view('home/single_view');

            $this->load->view('include/footer_view');



        }

        

        // ---------------------------------------------------------------------

        

        public function edit( $id ) {



            $result            = $this->company_m->edit( $id );

            $result['company'] = $result[0];



            $result['categories'] = $this->home_m->get_categories();

            $this->load->view('include/header_view',$result);

            $this->load->view('company/edit_view');

            $this->load->view('include/footer_view');



        }



        // ---------------------------------------------------------------------



        public function editpro( $id ) {



            $result = $this->company_m->editpro( $id );

            redirect( site_url() . 'company' );



        }



        // ---------------------------------------------------------------------

        

        public function delete( $id ) {



            $result = $this->company_m->delete( $id );

            redirect( site_url() . 'company' );



        }

	   // ---------------------------------------------------------------------



        public function company_more_info_add( $slug){
            
 $recaptcha =  $this->input->post('g-recaptcha-response');
  $res =  $this->company_m->reCaptcha($recaptcha);
 
if($res['success'] != 1){
 	$this->session->set_flashdata('success', '<h5  class="alert alert-danger>Prove That you are not a rebot</h5>');
	 redirect( site_url() . 'company/' . $slug );
}


            
            
                $disableButton="all";
			 $result= $this->company_m->company_more_info_add();
			if($result == true)

			{
			   // exit('yes i m in 1 block');
             $results = $this->db
                    ->select('name')
                    ->from('cities')
                    ->where('id', $this->input->post('city'))
                    ->get()
                    ->result_array();
		 $city=	$results[0]['name'];
	// $this->company_m->sendEmailsRequestMoreInfo( $slug, $this->input->post('email') );

          $name=$this->input->post('firstname') . " " . $this->input->post('lastname');
				
// 		$this->company_m->sendsmstoemp

// 				($slug,

// 				 $this->input->post('co_id'),

// 				 $name,$this->input->post('number'),

// 				 $city,

// 				 $this->input->post('email'),

// 				 $this->input->post('message'),

// 				 $this->input->post('co_name')

// 				  );
    
          $this->company_m->sendEmailscontactinfo( $slug, $this->input->post('email'),$this->input->post('number'),$this->input->post('message'),$name,$city );

           $name=$this->input->post('firstname') . " " . $this->input->post('lastname'); 

        // $this->company_m->sendEmailToCompany( $slug, $this->input->post('email'),$name,$this->input->post('number'),$this->input->post('message'),$city);
         
        // $this->company_m->send_email_to_admin( $this->input->post('firstname'),$this->input->post('lastname'),$this->input->post('email'),$this->input->post('number'),$this->input->post('message') );
        
        $num='923009080516';
		   $AdminSMS = '  Company Name : ' .   $this->input->post('co_name');
	
		   $AdminSMS .= '  Franchisee Name : ' .  $name;
		
           $AdminSMS .= '  Mobile  : ' .  $this->input->post('number');
       
		   $AdminSMS .= ' Desired City  : ' . $city;
           
           $AdminSMS.=' E-mail  : ' .$this->input->post('email');
         
        // substr($text,0,10)
        $AdminSMS.=' message : ' . $this->input->post('message');
         
        
        
//         $URL="https://api.whatsapp.com/send?phone='.$num.'&text='.$AdminSMS";
// echo "<script type='text/javascript'>document.location.href='{$URL}';</script>";
// echo '<META HTTP-EQUIV="refresh" content="0;URL=' . $URL . '">';
     

		$this->session->set_flashdata('success', '<h5 class="alert alert-success">Request  Submitted  SuccessFully...</h5>');

            // redirect( site_url() . 'Newhome/single_company_view/' . $slug );
			redirect( site_url() . 'company/' . $slug );

				

				

			}


			elseif($result == '11')

			{
             // exit('yes i m in 2 block');
              
				$this->session->set_flashdata('success', '<span class="alert alert-danger">'.$this->input->post('email'). ' has Already Requested for This Brand ..... Or  Please  Fill The form And try Again... </span>');

            // redirect( site_url() . 'Newhome/single_company_view/' . $slug );
			redirect( site_url() . 'company/' . $slug );

			}
            else
            {
       // exit('yes i m in 0 block');
  	$this->session->set_flashdata('success', '<span class="alert alert-danger">'.' Please  Fill The form And try Again... </span>');

	  redirect( site_url() . 'company/' . $slug );
 }
			

			

				



        }



        // ---------------------------------------------------------------------



        public function requests() {



       if (!$this->session->userdata('loggedin'))

        {

           

                redirect( site_url() . 'home/' );

        }

        else{

            $result['categories'] = $this->home_m->get_categories();

            $result['requests']   = $this->home_m->get_requests();

            

            $this->load->view('include/header_view',$result);

            $this->load->view('company/request_view');

            $this->load->view('include/footer_view');

}

        }



        // ---------------------------------------------------------------------



        public function events() {

                         if (!$this->session->userdata('loggedin'))

        {

           

                redirect( site_url() . 'home/' );

        }

        else{



            $result['categories'] = $this->home_m->get_categories();

            $result['events']     = $this->categories_m->get_events();

            $this->load->view('include/header_view',$result);

            $this->load->view('company/events_view');

            $this->load->view('include/footer_view');

        }

        }



        // ---------------------------------------------------------------------



        public function eventspro() {



            $result = $this->categories_m->eventspro();

			 redirect( site_url() . 'company/events' );



        }



        // ---------------------------------------------------------------------



        public function event_home() {

            

            $result['seo']        = $this->home_m->meta_data_events();

            $result['categories'] = $this->home_m->get_categories();

            $result['events']     = $this->categories_m->get_events();

            $this->load->view('include/header_view', $result);

            $this->load->view('company/event_home_view' );

            $this->load->view('include/footer_view');



        }



        // ---------------------------------------------------------------------



        public function single_event_details() {



            $result['categories']         = $this->home_m->get_categories();

            $result['events']             = $this->categories_m->get_single_events( $this->uri->segment(3) );

            $result['related_events']     = $this->categories_m->get_related_events( $this->uri->segment(3) );

            $this->load->view('include/header_view',$result);

            $this->load->view('company/single_event_details');

            $this->load->view('include/footer_view');



        }



        // ---------------------------------------------------------------------



        public function event_delete( $id ) {



            $result = $this->company_m->event_delete( $id );

            redirect( site_url() . 'company/events' );



        }



        // ---------------------------------------------------------------------



        public function event_edit( $id ) {



            $result            = $this->categories_m->get_single_events( $id );

            $result['events'] = $result[0];

            $result['categories'] = $this->home_m->get_categories();

            $this->load->view('include/header_view',$result);

            $this->load->view('company/event_edit_view');

            $this->load->view('include/footer_view');



        }



        // ---------------------------------------------------------------------



        public function editeventspro( $id ) {



            $result = $this->company_m->editeventspro( $id );

            redirect( site_url() . 'Admin/view_event' );



        }



        // ----------------------------------------------------------------------

        

        public function delete_requests() {

            

            $this->home_m->delete_requests( $this->uri->segment(3) );

            redirect( site_url() . 'Admin/view_event' );

            

        }

        

	

	public function delete_investorrequest() {

            

            $this->home_m->delete_investorrequest($this->uri->segment(3) );

            redirect( site_url() . 'Admin/investorrequests' );

            

        }

        // ----------------------------------------------------------------------

        

        public function send_email_to_all() {

            

            $this->home_m->send_email_to_all();

            redirect( site_url() . 'company/requests' );

            

        }

        

        // ----------------------------------------------------------------------

        

        public function meta_data() {

                if (!$this->session->userdata('loggedin'))

        {

           

                redirect( site_url() . 'home/' );

        }

        else{

            

            $result['categories']                 = $this->home_m->get_categories();

            $result['meta_data_home']             = $this->home_m->meta_data();

            $result['meta_data_directory']        = $this->home_m->meta_data_directory();

            $result['meta_data_events']           = $this->home_m->meta_data_events();

            $result['meta_data_top_companies']    = $this->home_m->meta_data_top_companies();

            $this->load->view('include/header_view',$result);

            $this->load->view('company/meta_data_view');

            $this->load->view('include/footer_view');

            

        }

        }

        

        // ----------------------------------------------------------------------

        

        public function meta_data_add() {

            

            $result = $this->home_m->meta_data_add();

            if($result){

                redirect( site_url() . 'company/meta_data' );

            }

            

        }

        

        // ---------------------------------------------------------------------

        

        public function send_email_to_all_requests() {

            

            $this->home_m->send_email_to_all_requests();

            redirect( site_url() . 'company/requests' );

            

        }

        

        // ---------------------------------------------------------------------

        

        public function status_change($c_id, $status) {

            

            $this->home_m->status_change($c_id, $status);

			 if($this->session->userdata('emp_special'))

		 {

			    redirect( site_url() . 'Team/view_companies' );

			 exit;

		 }

		 else

		 {

			  redirect( site_url() . 'Admin/view_companies' );

			 exit;

		 }

          

            

        }

        

        // ---------------------------------------------------------------------

        

        public function sendEmailToAllCompanies() {



            // upload images

            $image_name = $this->categories_m->insert_image_to_db('image');



            // model function calls and variables

           // $output       = $this->company_m->sendEmailToAllCompanies( $image_name['success'] );

            $contactemail = $this->company_m->get_all_emails_company();

            $from         = $this->input->post('from');

            $to           = $this->input->post('to');

            $i            = 1;

            $subject      = $this->input->post('subject');

            

            // loop for sending emails to all the companies

            foreach( $contactemail as $emails ){



                // checking for the email to be send within range

                if( $i >= $from && $i <= $to ){

                    if( $this->send_email( $emails->con_email, $output, $subject) ) {

                        

                        $this->home_m->insert_sent_email_success( $emails->con_email, $output, $subject );

                        

                    } else {

                        

                        $this->home_m->insert_sent_email_fail( $emails->con_email, $output, $subject );

                        

                    }

                    

                }

                $i++;

            }

            // redirecting it back where it came from

            redirect( site_url() . 'company' );

            

        }

        

        // ---------------------------------------------------------------------



        public function send_email( $to, $output, $subject) {

                        

            // load email library

            $this->load->library('email');



            // prepare email

            $this->email

                ->from('marketing@franchisepk.com', 'Franchise Pakistan.')

                ->to($to)

                ->subject( $subject )

                ->message($output)

                ->set_mailtype('html');



            // send email

            return $this->email->send();

            

        }

        

        // ---------------------------------------------------------------------

        

        public function send_attachment() {

            

            $to = $this->input->post('email');

           // $output = $this->company_m->sendEmailAttachment();

            // load email library

            $this->load->library('email');

            // prepare email

            $this->email

                ->from('info@franchisepk.com', 'Franchise Pakistan.')

                ->to($to)

                ->subject("Thanks for Your subscription Franchise Pakistan")

                ->message($output)

//                ->attach( site_url() . 'public/downloads/FP.pdf')

                ->set_mailtype('html');

            // send email

            $this->email->send();

            redirect( site_url() . 'home/directory' );

            

        }

        

        // ---------------------------------------------------------------------

        

        public function add_company ( $msg = null ) {

            

            $result['categories']  =  $this->home_m->get_categories();

            $this->load->view('include/header_view',$result);

            $this->load->view('company/add_company_view');

            $this->load->view('include/footer_view');

            

        }

        

        // ---------------------------------------------------------------------

        

        public function add_company_pro() {

            

            $result = $this->company_m->add_company_pro();

            

            if( $result ){

                redirect( site_url() . 'company/add_company/success' );

            }

            redirect( site_url() . 'company/add_company/error' );

        }

        

        // ---------------------------------------------------------------------

        

        public function print_slip( $id ) {

            

            $result['data'] = $this->company_m->single_company( $id );

            $this->load->view('company/print_slip', $result);

            

        }

        

        // ---------------------------------------------------------------------

        

        public function active_companies() {

            

            $result['categories'] = $this->home_m->get_categories();

            $data['companies']    = $this->company_m->get_companies_a();

            $data['type']         = 'Active';

            $this->load->view('include/header_view', $result);

            $this->load->view('company/company_view',$data);

            $this->load->view('include/footer_view');

            

        }

        

        // ---------------------------------------------------------------------

        

        public function deactive_companies() {

            

            $result['categories'] = $this->home_m->get_categories();

            $data['companies']    = $this->company_m->get_companies_d();

            $data['type']         = 'DeActive';

            $this->load->view('include/header_view', $result);

            $this->load->view('company/company_view',$data);

            $this->load->view('include/footer_view');

            

        }

        

        // ---------------------------------------------------------------------

        

        public function add_companies() {

            

            $data['countries']    = $this->home_m->get_countries();

            $result['categories'] = $this->home_m->get_categories();

            $this->load->view('include/header_view', $result);

            $this->load->view('company/AddCompanyView',$data);

            $this->load->view('include/footer_view');

            

        }

	public function sendAllResquestMail()
	{
	if($this->input->post('franchise_name') && $this->input->post('franchise_name')  == 'Вопрос')
	{
//$result=$this->company_m->sendAllResquestMail($this->input->post('name'),$this->input->post('email'),$this->input->post('phone'),$this->input->post('comment') );
		if($result == 1)
		{
			$this->session->set_flashdata('success', '<h2 class="alert alert-success">Your  Request  Submitted  SuccessFully We Will Contact you Soon..</h2>');
			 redirect( site_url() . 'contact-us/');
			exit;
		}
		else
		{
			$this->session->set_flashdata('success', '<h2 class="alert alert-success">Sorry System Occurs Try Again..</h2>');
			 redirect( site_url() .'contact-us/');
			exit;
		}
	
	}
	 
	}

	    
 
        // ---------------------------------------------------------------------

        

}
?>