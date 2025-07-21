<?php
class Home_m extends CI_Model
{
   
	public function get_investrequests(){
$results= $this->db->select('*')
->from('requestforinvestor')
->join('email_history', 'email_history.e_id = requestforinvestor.e_id')
->get()
 ->result();
        return $results;
    }
	
	public function payment_sitting(){
$results= $this->db->select('*')
->from('payment_settings')
->limit(1)
->get()
 ->result_array();

        return $results;
        
	
    }
	 
	 
	public function featured_franchise()
	{
		$results = $this->db
                    ->select('co_id,co_category_id,co_name,co_slug')
                    ->from('companies')
                  ->where('home_page',1)
                    ->where('co_status', 1)
	                 ->order_by('co_id','desc')
                 	 ->limit(18)
                    ->get()
                    ->result();
        $array = [];
        foreach( $results as $result ){
            
            $array[$result->co_id] = $result;
                                        
            
            $array[$result->co_id]->company_images   = $this->db->select('img_name')
                                                              ->order_by('img_id','desc')
                                                              ->where('img_type',1)  
                                                        ->where('fkco_id',$result->co_id)
				                                        //  -> limit(1)
                                                              ->get('images')
                                                              ->result();
        }
        return $array;
	
	}
	public function cities()
	{
	return	$this->db->select('co_city')
                                     ->from('companies')
                                     ->group_by('co_city')
		                              ->where('co_city !=', '0')
		                               ->where('co_city !=', '#')
                                     ->get()
                                     ->result();
	}
	public function ranges()
	{
		 return $this->db->select('co_total_investment')
                                     ->from('companies')
			                         ->where('co_total_investment >',1000000)
			                        ->order_by("co_total_investment","desc")
                                     ->group_by('co_total_investment')                                  
                                     ->get()
                                     ->result(); 
	}
	
	public function stories($limit = NULL)
	{
		 return $this->db->select('*')
                                     ->from('successstory')
                                     ->where('story_status',1)
                                     ->order_by('story_id','desc')
                                     ->limit($limit)
                                     ->get()
                                     ->result();
	}


	
	public function get_all_companies_home_search() {    
                   // echo $this->input->post('categories');
                   if( $this->input->post('cat') != '' ){
                    $conditionOne=   $this->db->where('co_category_id', $this->input->post('cat'));
                   }else
				   {
                     
                     $conditionOne='';
                   }
		if( $this->input->post('city') != null )
				   {
             $conditionTwo=   $this->db->like( 'co_city', $this->input->post('city') );
                   }
		else
		{
			$conditionTwo='';
		};
		 if($this->input->post('range') != null)
		 {
			 $conditionThree=$this->db->where('co_cash_required<=', $this->input->post('range') );
		 }
		else
		{
			$conditionThree='';
		}
                    
                   $this->db->select('*');
                   $this->db->from('companies');
                   $conditionOne;
		           $conditionTwo;
		           $conditionThree;
                   $this->db->order_by('companies.co_id','desc')
                   ->where('co_status', 1);
        $query   = $this->db->get();
        $results  = $query->result();
		//echo $this->db->last_query();exit;
        $array = [];
        foreach( $results as $result ){
            
            $array[$result->co_id] = $result;
                                        
            $array[$result->co_id]->company_contacts = $this->db
                                                              ->where('con_company_id',$result->co_id)
                                                              ->get('company_contact_person')
                                                              ->result();
            $array[$result->co_id]->company_images   = $this->db
                                                              ->order_by('img_id','desc')
                                                              ->where('img_type',1)  
                                                              ->where('fkco_id',$result->co_id)
                                                              ->get('images')
                                                              ->result();
        }

        if( isset( $category ) ){
          $array['company_name'] = $category->c_name;
        }
		//print_r($array);
		//exit;
        return $array;

    }

	
	public function testimonials()
	{
	return	$this->db->select('*')
                                       ->from('testamonials')
                                       ->order_by('t_id','desc')
                                       ->get()
                                       ->result();
	}
	 public function articles()
	 {
		return $this->db->select('*')
                                       ->from('articles')
                                       ->order_by('id','desc')
                                       ->limit(4)
                                       ->get()
                                       ->result();
	 }
	public function newfranchise()
	{
		
                          $results = $this->db
                    ->select('*')
                    ->from('companies')
                    ->where('co_status', 1)
	                 ->order_by('co_id','desc')
                    ->get()
                    ->result();
        $array = [];
        foreach( $results as $result ){
            
            $array[$result->co_id] = $result;
                                        
            
            $array[$result->co_id]->company_images   = $this->db->select('img_name')
                                                              ->order_by('img_id','desc')
                                                              ->where('img_type',1)  
                                                        ->where('fkco_id',$result->co_id)
				                                        //  -> limit(1)
                                                              ->get('images')
                                                              ->result();
        }
       return $array;
	}
	 public function delete_investorrequest( $r_id ){
        
     return    $this->db
                       ->where('req_id', $r_id ) 
                       ->delete('requestforinvestor');    
		
    }
    // -------------------------------------------------------------------------
     public function successstory(){

        $results = $this->db
                    ->select('*')
                    ->from('successstory')
                    ->where('story_status', 0)
                    ->get()
                    ->result();
       
        
        return $results;

    }
    
    
    public function get_categories(){
        
        return $this->db
                ->select('c_id,c_name,c_slug,c_image,c_place_home')
                ->get('category')
                ->result();
    }
    
    // -------------------------------------------------------------------------
     public function investors(){

 return  $results = $this->db
                    ->select('*')
                    ->from('email_history')
                    ->where('investor_status', 1)
	                ->order_by('e_id', 'desc')
                    ->get()
                    ->result();
        
            
        }
    public function insertnews()
    {
        $array = [

            'title'  => $this->input->post('title'),
            'desc'   => $this->input->post('desc'),
            'date'   => date('Y-M-d')
        ];
        $this->db->insert('news' , $array);
        return 1;
    }

    public function getnews()
    {
        return $this->db->get('news')->result();  
    }
    
    public function editnews($id)
    {
        $result = $this->db
                            ->where('id', $id)
                            ->get('news')
                            ->row();
        return $result; 
    }

    public function editpro($id)
    {
        $data = $this->input->post();
        $this->db
                ->where('id', $id)
                ->update('news', $data); 
        return 1;  
    }

    public function deletenews( $id ){

        return $this->db
                       ->where('id', $id ) 
                       ->delete('news');
    }
    
    public function user_status_change($u_id, $status) {      
      $this->db
                ->where('u_id', $u_id)
                ->update('login', ['status' => $status] );    
    }


//    public function login_pro(){
//                $this->db->where( 'u_email', $this->input->post('email') );  
//                $this->db->where( 'u_password',  $this->input->post('password')  );
//                $this->db->where( 'u_type', 'admin');  
//                  //$this->db->where( 'u_password', hash('sha256', $this->input->post('password') ) );  
//        $result = $this->db->get( 'login' )->row();
//        if( !empty( $result ) ){
//            $this->session->set_userdata( [ 'userdata' => $result ] );
//            return 1;
//        }else{
//            return 0;
//        }
//        
//    }

//    public function ulogin_pro(){
//                  $this->db->where( 'u_email', $this->input->post('email') );  
//                  $this->db->where( 'u_password',  $this->input->post('password')  );
//                  $this->db->where( 'u_type', 'user');  
//                  $this->db->where( 'status', '1');  
//                  $this->db->where( 'payment_status', '2');  
//                 
//                  //$this->db->where( 'u_password', hash('sha256', $this->input->post('password') ) );  
//        $result = $this->db->get( 'login' )->row();
//        if( !empty( $result ) ){
//            $this->session->set_userdata( [ 'userdata' => $result ] );
//            return 1;
//        }else{
//            return 0;
//        }
//        
//    }
//    
    // -------------------------------------------------------------------------
    
    public function get_categories_view(){
        
        return $this->db
                ->get('category')
                ->result();
    }
    
    // -------------------------------------------------------------------------
    
    public function get_countries(){
        
        return $this->db
                ->get('countries')
                ->result();
        
    }
    
    // -------------------------------------------------------------------------
    
    public function fetch_province( $id ){
        
        return $this->db
                ->where( 'country_id' , $id )
                ->get( 'states' )
                ->result();
    }
    
    // -------------------------------------------------------------------------
    
    public function fetch_city( $id ){
        
        return $this->db
                ->where( 'state_id' , $id )
                ->get( 'cities' )
                ->result();
    }
    
    // -------------------------------------------------------------------------
    
    public function get_companies(){
        
        $results = $this->db
                    ->select('*')
                    ->from('companies')
                    ->join('category','category.c_id=companies.co_category_id')
                    ->where('companies.type_of_company', 1)
                    ->where('co_status', 1)
                    ->get()
                    ->result();
        $array = [];
        foreach( $results as $result ){
            
            $array[$result->co_id] = $result;
                                        
            $array[$result->co_id]->company_contacts = $this->db
                                                              ->where('con_company_id',$result->co_id)
                                                              ->get('company_contact_person')
                                                              ->result();
            $array[$result->co_id]->company_images   = $this->db
//                                                              ->where('img_type',1)  
                                                              ->where('fkco_id',$result->co_id)
                                                              ->get('images')
                                                              ->result();
            
        }
        
        return $results;
        
    }
    
    // -------------------------------------------------------------------------
    
    public function home_search(){

        if( isset( $_POST['categories'] ) ){

            $this->db->where('co_category_id',$this->intput->post('categories') );
            
        }

        return $this->db
                        ->get('companies')
                        ->result();

    }

    // -------------------------------------------------------------------------

    public function directory($search = false){
if($search != false)
{
	$results = $this->db
                    ->select('*')
                    ->from('companies')
                    ->where('co_status', 1)
		           ->like( 'co_name', $search )
		            ->order_by("co_id", "desc")
                    ->get()
                    ->result();
	//echo $this->db->last_query(); exit;
}
	else
	{
		$results = $this->db
                    ->select('*')
                    ->from('companies')
                    ->where('co_status', 1)
                     ->order_by("co_id", "desc")
                    ->get()
                    ->result();
	}
        
        $array = [];
        foreach( $results as $result ){
            
            $array[$result->co_id] = $result;
                                        
            $array[$result->co_id]->company_contacts = $this->db
                                                              ->where('con_company_id',$result->co_id)
                                                              ->get('company_contact_person')
                                                              ->result();
            $array[$result->co_id]->company_images   = $this->db
                                                              ->order_by('img_id','desc')
                                                              ->where('img_type',1)  
                                                              ->where('fkco_id',$result->co_id)
                                                              ->get('images')
                                                              ->result();
            
        }
        
        return $results;

    }
    
	
	
	 public function international(){

        $results = $this->db
                    ->select('*')
                    ->from('companies')
                    ->where('co_status', 1)
                    ->where('co_country_id != ', 'pakistan')
                    ->get()
                    ->result();
		//echo  $this->db->last_query();
		// exit;
        $array = [];
        foreach( $results as $result ){
            
            $array[$result->co_id] = $result;
                                        
            $array[$result->co_id]->company_contacts = $this->db
                                                              ->where('con_company_id',$result->co_id)
                                                              ->get('company_contact_person')
                                                              ->result();
            $array[$result->co_id]->company_images   = $this->db
                                                              ->order_by('img_id','desc')
                                                              ->where('img_type',1)  
                                                              ->where('fkco_id',$result->co_id)
                                                              ->get('images')
                                                              ->result();
            
        }
        
        return $results;

    }
    
    // -------------------------------------------------------------------------

    public function top_10_companies(){

        $results = $this->db
                    ->select('*')
                    ->where('top_10_companies',1)
                    ->from('companies')
                    ->where('co_status', 1)
                    ->get()
                    ->result();
        $array = [];
        foreach( $results as $result ){
            
            $array[$result->co_id] = $result;
                                        
            $array[$result->co_id]->company_contacts = $this->db
                                                              ->where('con_company_id',$result->co_id)
                                                              ->get('company_contact_person')
                                                              ->result();
            $array[$result->co_id]->company_images   = $this->db 
                                                              ->order_by('img_id','desc')
                                                              ->where('img_type',1) 
                                                              ->where('fkco_id',$result->co_id)
                                                              ->get('images')
                                                              ->result();
            
        }
        
        return $results;

    }

    // -------------------------------------------------------------------------

    public function get_featured_franchises(){

        $query = $this->db
                        ->select('*')
                        ->from('images')
                        ->where('images.img_type', 1)
                        ->get()
                        ->result();
        return $query;

    }

    // -------------------------------------------------------------------------

    public function get_countries_search(){

        return $this->db
//                        ->or_where( 'state_id' , 2723 )
//                        ->or_where( 'state_id' , 2724 )
//                        ->or_where( 'state_id' , 2725 )
//                        ->or_where( 'state_id' , 2726 )
//                        ->or_where( 'state_id' , 2727 )
//                        ->or_where( 'state_id' , 2728 )
                       ->where( 'state_id' , 2726 )
			           //  where->('state_id',2729)
                        ->get( 'cities' )
                        ->result();

    }
public function get_requests()
{
     $result= $this->db->select('email_history.*,companies.co_name,cities.name')
                       ->join('companies','email_history.e_co_id=companies.co_id') 
                     ->join('cities','email_history.e_city=cities.id','left') 
                   ->order_by("email_history.e_id", "desc")
                 //  ->where('e_id <',537)
		->limit('1500')
                       ->get('email_history')
                       ->result();
//        $result=$this->db
//                       ->join('companies','email_history.e_co_id=companies.co_id') 
//                   //->order_by("email_history.e_id", "desc")
//		//	->limit('10')
//                       ->get('email_history')
//                       ->result();
	return $result;
	
        
    }
    
    
    
    
    public function get_single_requests($id)
{
     $result= $this->db->select('email_history.*,companies.co_name,cities.name')
                       ->join('companies','email_history.e_co_id=companies.co_id') 
                     ->join('cities','email_history.e_city=cities.id','left') 
                     ->where('e_id',$id)
                   ->order_by("email_history.e_id", "desc")
                       ->get('email_history')
                       ->result();

	            return $result;
  
    }


    public function get_lead_comments($id)
    {
        $result = $this->db
        ->select('*')
        ->from('lead_comments')
        ->where('lead_id',$id)
       ->order_by('comment_id','desc')
        ->get()
        ->result();
    
        return $result;
      
        }
    


    // -------------------------------------------------------------------------
    
     public function get_user_requests(){
        $userdata['userdata'] = $this->session->userdata('userdata'); 
        return $this->db->join('companies','email_history.e_co_id=companies.co_id')
                  ->join('login','login.u_id=companies.uid') 
                   ->where('uid' , $userdata['userdata']->u_id)
                   ->get('email_history')
                   ->result();
		
    //     // return mysql_query("SELECT * FROM `login` JOIN `companies` ON login.u_id=companies.uid JOIN email_history ON email_history.e_co_id=companies.co_id");
        
     }
	
	public function get_emp_requests($userid){

    //city wise Query//////
// $this->db->select('email_history.*, companies.co_name, companies.co_office_number, cities.name, companies.co_id');
// $this->db->join('emp_city', 'email_history.e_city = emp_city.id');
// $this->db->join('companies', 'email_history.e_co_id = companies.co_id');
// $this->db->join('cities', 'cities.id = emp_city.id');
// $this->db->where('emp_city.u_id', $userid);
// $this->db->order_by('email_history.e_id', 'desc');
// $this->db->limit(1500);
// $result = $this->db->get('email_history')->result();
// return $result;



    //Brand  wise Query//////
// $this->db->select('email_history.*, cities.name, companies.co_name, companies.co_office_number, companies.co_id');
// $this->db->join('cities', 'cities.id = email_history.e_city','left');
// $this->db->join('emp_city', 'email_history.e_co_id = emp_city.id');
// $this->db->join('companies', 'email_history.e_co_id = companies.co_id');
// $this->db->where('emp_city.u_id', $userid);
// $this->db->order_by('email_history.e_id');
// $result = $this->db->get('email_history')->result();
// return $result;

// assigned lead query


$this->db->select('
email_history.*,
cities.name,
companies.co_name,
companies.co_office_number,
companies.co_id
');
$this->db->from('email_history');
$this->db->join('cities', 'cities.id = email_history.e_city', 'left');
$this->db->join('emp_city', 'email_history.e_co_id = emp_city.id');
$this->db->join('companies', 'email_history.e_co_id = companies.co_id');

// ✅ Begin grouped WHERE
$this->db->group_start();
// 1. Assigned to employee
$this->db->where('email_history.employee_id', $userid);

// 2. OR unassigned (meaning employee_id=0) but city-mapped
$this->db->or_group_start();
    $this->db->where('email_history.employee_id', 0);
    $this->db->where('emp_city.u_id', $userid);
$this->db->group_end();
$this->db->group_end();

// ✅ Order
$this->db->order_by('email_history.e_id');

// ✅ Get
$query = $this->db->get();
return $query->result();







// All Query ////
//  $result= $this->db->select('email_history.*,companies.co_name,cities.name')
//                       ->join('companies','email_history.e_co_id=companies.co_id') 
//                      ->join('cities','email_history.e_city=cities.id','left') 
//                   ->order_by("email_history.e_id", "desc")
//                  //  ->where('e_id <',537)
// 	                   ->limit('1500')
//                       ->get('email_history')
//                       ->result();
                       
//                       return $result;
		//exit;
		
//		SELECT * FROM `emp_city` INNER join email_history on emp_city.id= email_history.e_city
//INNER join cities on emp_city.id=cities.id
//INNER join companies on email_history.e_co_id=companies.co_id WHERE emp_city.u_id='217'
//$this->db->where('login.u_type','employee');

     }
//	SELECT * from emp_company INNER join companies on emp_company.co_id=companies.co_id INNER join 
//email_history ON email_history.e_co_id=companies.co_id INNER JOIN  login ON login.u_id=emp_company.u_id
    
//	public function get_emp_requests($userid){
//       //$userdata['userdata'] = $this->session->userdata('userdata'); 
//		$this->db->select('*');
//		$this->db->join('companies','companies.co_id=emp_company.co_id');
//       $this->db->join('email_history','email_history.e_co_id=companies.co_id');
//       $this->db->join('login','login.u_id=emp_company.u_id');
//	$this->db->where('emp_company.u_id',$userid);
////$this->db->where('login.u_type','employee');
//
////$this->db->where('emp_company.ecom_status',0);
//return  $result=$this->db->get('emp_company')->result();
////return $result=$this->db->get('email_history')->result();
//		//echo $this->db->last_query();
//		//exit;
//    //     // return mysql_query("SELECT * FROM `login` JOIN `companies` ON login.u_id=companies.uid JOIN email_history ON email_history.e_co_id=companies.co_id");
//        
//     }
    // -------------------------------------------------------------------------
    
    public function delete_requests( $r_id ){
        
        return $this->db
                       ->where('e_id', $r_id ) 
                       ->delete('email_history');        
    }
    
    // -------------------------------------------------------------------------
    
    public function meta_data_add() {
        
        
        $result_g = $this->db
                            ->where('m_p_name', $this->input->post('page_name') )
                            ->get( 'meta_data' )
                            ->result();
        if( $result_g ){
        
            $result = $this->db->update( 'meta_data',[
                                        'm_keywords'       => $this->input->post('keyword'),
                                        'm_description'    => $this->input->post('meta_description')
                            ],[ 'm_p_name' => $this->input->post('page_name')]
                        );
            return 1;
        }
        
        $this->db->insert( 'meta_data',[
                                    'm_keywords'       => $this->input->post('keyword'),
                                    'm_description'    => $this->input->post('meta_description'),
                                    'm_p_name'         => $this->input->post('page_name')
                        ]
                    );
        return 1;
        
    }
    
    // -------------------------------------------------------------------------
    
    public function meta_data(){
        
        $result = $this->db
                            ->where('m_p_name','Home')
                            ->get('meta_data')
                            ->result();
        if($result){
            return $result[0];
        }
        return 0;
    }
    
    // -------------------------------------------------------------------------
    public function meta_data_directory(){
       
        $result = $this->db
                            ->where('m_p_name','Directory')
                            ->get('meta_data')
                            ->result();
        if($result){
            return $result[0];
        }
        return 0;
    }
    
    // -------------------------------------------------------------------------
    
    public function meta_data_events(){
       
        $result = $this->db
                            ->where('m_p_name','Event')
                            ->get('meta_data')
                            ->result();
        if($result){
            return $result[0];
        }
        return 0;
    }
    
    // -------------------------------------------------------------------------
    
    public function meta_data_top_companies(){
       
        $result = $this->db
                            ->where('m_p_name','Top Companies')
                            ->get('meta_data')
                            ->result();
        if($result){
            return $result[0];
        }
        return 0;
    }
    
    // -------------------------------------------------------------------------
    
    public function send_email_to_all_requests(){
        
        $result = $this->db
                            ->select('*')
                            ->from('email_history')
                            ->get()
                            ->result();
        
        foreach( $result as $contact ){
            
            mail( $contact->e_emailaddress, $this->input->post('subject'), $this->input->post('message') );
            
        }
        
        return 1;
        
    }
    
    // -------------------------------------------------------------------------
    
    public function status_change ($c_id, $status) {
        
        $this->db
                ->where('co_id', $c_id)
                ->update('companies', ['co_status' => $status] );
        
    }
    
    // -------------------------------------------------------------------------
    
    
    
     public function get_adverts_center(){
        
       return $result = $this->db
                        ->where('a_status', 1)
                         ->where('center_slider', 1)
                        ->get('advertisements')
                        ->result();
//     $result = [];
    //   $i = 1   foreach( $outputs as $output  ){
//          foreach ($outputs as $output1){
//              switch ( $i ){
//                  case 1:
//                      if( !array_key_exists($output1->a_id, $result) && $output1->a_large != 1 ){
//                        $result[$output1->a_id] = $output1;
//                        $i++;
//                      }
//                      
//                      break;
//                  case 2:
//                      if( !array_key_exists($output1->a_id, $result) && $output1->a_large != 0 ){
//                        $result[$output1->a_id] = $output1;
//                        $i++;
//                      }
//                      
//                      break;
//                  case 3:
//                      if( !array_key_exists($output1->a_id, $result) && $output1->a_large != 1 ){
//                        $result[$output1->a_id] = $output1;
//                        $i++;
//                      }
//                      break;
//                  case 4:
//                      if( !array_key_exists($output1->a_id, $result) && $output1->a_large != 1 ){
//                        $result[$output1->a_id] = $output1;
//                        $i++;
//                      }
//                      break;
//                  case 5:
//                      if( !array_key_exists($output1->a_id, $result) && $output1->a_large != 1 ){
//                        $result[$output1->a_id] = $output1;
//                        $i++;
//                      }
//                      break;
//                  case 6:
//                      if( !array_key_exists($output1->a_id, $result) && $output1->a_large != 0 ){
//                        $result[$output1->a_id] = $output1;
//                        $i++;
//                      }
//                      break;
//                  case 7:
//                      if( !array_key_exists($output1->a_id, $result) && $output1->a_large != 1 ){
//                        $result[$output1->a_id] = $output1;
//                        $i++;
//                      }
//                      break;
//              }
//              
//          }
//       }

    }
    
    
    
     public function get_adverts(){
        
       return $result = $this->db
                        ->where('a_status', 1)
                        ->where('center_slider', 0)
                        ->get('advertisements')
                        ->result();
//     $result = [];
    //   $i = 1   foreach( $outputs as $output  ){
//          foreach ($outputs as $output1){
//              switch ( $i ){
//                  case 1:
//                      if( !array_key_exists($output1->a_id, $result) && $output1->a_large != 1 ){
//                        $result[$output1->a_id] = $output1;
//                        $i++;
//                      }
//                      
//                      break;
//                  case 2:
//                      if( !array_key_exists($output1->a_id, $result) && $output1->a_large != 0 ){
//                        $result[$output1->a_id] = $output1;
//                        $i++;
//                      }
//                      
//                      break;
//                  case 3:
//                      if( !array_key_exists($output1->a_id, $result) && $output1->a_large != 1 ){
//                        $result[$output1->a_id] = $output1;
//                        $i++;
//                      }
//                      break;
//                  case 4:
//                      if( !array_key_exists($output1->a_id, $result) && $output1->a_large != 1 ){
//                        $result[$output1->a_id] = $output1;
//                        $i++;
//                      }
//                      break;
//                  case 5:
//                      if( !array_key_exists($output1->a_id, $result) && $output1->a_large != 1 ){
//                        $result[$output1->a_id] = $output1;
//                        $i++;
//                      }
//                      break;
//                  case 6:
//                      if( !array_key_exists($output1->a_id, $result) && $output1->a_large != 0 ){
//                        $result[$output1->a_id] = $output1;
//                        $i++;
//                      }
//                      break;
//                  case 7:
//                      if( !array_key_exists($output1->a_id, $result) && $output1->a_large != 1 ){
//                        $result[$output1->a_id] = $output1;
//                        $i++;
//                      }
//                      break;
//              }
//              
//          }
//       }

    }
    
    // -------------------------------------------------------------------------
    
    public function register_pro()
    {
        if( $_FILES['image']['size'] > 0 )
        {
            $image_name = $this->categories_m->insert_image_to_db('image');
        }
        else
        {
            $image_name['success'] = '';
        }

        $array = [
           'u_firstname'  => $this->input->post('f_name'),
           'u_lastname'   => $this->input->post('l_name'),
           'u_image'      => $image_name['success'],
           'u_bio'        => $this->input->post('bio'),
           'u_email'      => $this->input->post('email'),
           'u_contact'    => $this->input->post('contact'),
           'u_password'   =>  $this->input->post('password'),
           'u_date'       => date('d-M-Y'),
           'u_time'       => date('H:i:s'),
           'u_type'       => $this->input->post('type'), 
           'a_company'    => $this->input->post('a_company'),
           'a_category'   => $this->input->post('a_category'),
           'a_request'    => $this->input->post('a_request'),
           'a_event'      => $this->input->post('a_event'),
           'a_seo'        => $this->input->post('a_seo'),
           'a_jobs'       => $this->input->post('a_jobs'),
           'a_resale'     => $this->input->post('a_resale'),
           'a_property'   => $this->input->post('a_property'),
           'a_ads'        => $this->input->post('a_ads'),
           'a_ads'        => $this->input->post('a_ads'),
        ];
        return $this->db->insert( 'login', $array );
    }

    public function uregister_pro($array)
		
    {
     
    return  $this->db->insert( 'login', $array );
	

    }    
    
    // -------------------------------------------------------------------------
    
    private function hash_password($password){
        return password_hash($password, PASSWORD_BCRYPT);
     }
    
     // ------------------------------------------------------------------------
     
     public function subscribe(){
         
         $email = $this->input->post('email');
         
         $data = $this->db
                        ->where('s_email', $email)
                        ->get('subscribed')
                        ->row();
         
         $array = [
           's_email'  => $email
         ];
         
         if( empty( $data ) ){
            $result = $this->db->insert( 'subscribed', $array );
         } else {
             $result = 0;
         }
         
         if( $result ){
             return 1;
         } else {
             return 0;
         }
         
     }
     
     // ------------------------------------------------------------------------
     // Disable due to server blockage
    //  public function sendemail(){
         
    //     // set subject variable 
    //     $subject = 'Subject : A Person has subscribed to our website';
    //     // set $message variable for sending it to admin        
    //     $message  = $subject;
    //     $message .= '\n Email : ' . $this->input->post('email');
        
    //     // load email library
    //     $this->load->library('email');

    //     // prepare email
    //     $this->email
    //                 ->from('marketing@franchisepk.com', 'Franchise Pakistan.')
    //                 //->to('mjks4545@gmail.com')
    //                 ->to('info@franchisepk.com')
    //                 ->subject($subject)
    //                 ->message($message)
    //                 ->set_mailtype('html');

    //     // send email
    //     if( $this->email->send() ){
    //         return 1;
    //     } else {
    //         return 0;
    //     }
         
    //  }
     
     // ------------------------------------------------------------------------
     
     public function get_subs(){
         
         return $this->db
                        ->get('subscribed')
                        ->result();
         
     }
     
     // ------------------------------------------------------------------------
     
     public function insert_sent_email_success ( $emails, $output, $subject ){
         
         $array = [
             'm_email'       => $emails,
             'm_subject'     => $subject,
             'm_message'     => $output,
             'm_status'      => 1
         ];
         return $this->db->insert( 'mail_box', $array );
         
     }
     
     // ------------------------------------------------------------------------
     
     public function insert_sent_email_fail ( $emails, $output, $subject ){
         
         $array = [
             'm_email'       => $emails,
             'm_subject'     => $subject,
             'm_message'     => $output,
             'm_status'      => 0
         ];
         return $this->db->insert( 'mail_box', $array );
         
     }
     
     // ------------------------------------------------------------------------

    public function delete( $id ) {

        return $this->db
                        ->where('s_id', $id)
                        ->delete('subscribed');

    }
    
    public function get_users()
    {
      return $this->db->where( 'u_type', 'user')
		    // ->where('status <','2')
			->order_by("u_id", "desc")
			->get('login')->result();    
    }

	 public function get_Active_employee() {
        return $this->db->where( 'u_type', 'employee')
			->where( 'status', 1)
			->order_by("u_id", "desc")
			->get('login')->result();   
    }
     // ------------------------------------------------------------------------
     
}