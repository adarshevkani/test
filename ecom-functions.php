<?php

add_action( 'rest_api_init', function () {
	register_rest_route( 'api/v1', 'signupUserDetailsEcommerce/user/', array(
	  'methods' => 'POST',
	  'callback' => 'insertEcommerceUserDetails'	  
	) );
  } );

  function insertEcommerceUserDetails($data)
  {
    
	global $wpdb ,$ecommerce_table ,$post_data;
	$returnArray = array();
	if($_POST){
		// Post data
		$post_data 				= $_POST;
		$user_email 			= stripcslashes($_POST['user_email']);
		$user_first_name 		= stripcslashes($_POST['user_first_name']);
		$user_last_name 		= stripcslashes($_POST['user_last_name']);
		$user_business_phone 	= stripcslashes($_POST['user_business_phone']);
		$user_company_name 		= stripcslashes($_POST['user_company_name']);
		$user_website 			= stripcslashes($_POST['user_website']);
		$user_industry 			= stripcslashes($_POST['user_industry']);
		$user_company_size 		= stripcslashes($_POST['user_company_size']);
		$user_country_or_region = stripcslashes($_POST['user_country_or_region']);
		// tables
		$ecommerce_table 		= $wpdb->prefix.'ecommerce_records';
		$restaurant_table 		= $wpdb->prefix.'ls_trial_signup_data';
		$baseUrl				='https://dev-panacea.emvigotech.co.uk/index.php';
		$siteUrl				= get_site_url();
		$is_email_exist_in_ecommerce = $wpdb->get_results("SELECT * FROM `cfwp_ecommerce_records` WHERE `CompanyEmail` = '$user_email' ");

		if($is_email_exist_in_ecommerce) {
			$returnArray ['status'] 	= "0";
			$returnArray ['message'] 	= "Company Already Exists!";
		}
		else{

			$ecommerce_data = array(
				'platform' 		=> $user_industry, 
				'CompanyName' 	=> $user_company_name,
				'FirstName' 	=> $user_first_name,
				'LastName' 		=> $user_last_name,
				'CompanyEmail' 	=> $user_email,
				'business_phone'=> $user_business_phone,
				'website_url'	=> $user_website
			);
		
			$wpdb->insert( 'cfwp_ecommerce_records', $ecommerce_data); 
			$ecom_row_incert_id = $wpdb->insert_id;

		}
		if($ecom_row_incert_id){
			$company_id 		= $user_company_name;
			$normalised_name	= createNormalisedName($company_id);
			$username  = $normalised_name;
			$arrContextOptions	= array(
				"ssl"				=>array(
				"verify_peer"		=>false,
				"verify_peer_name"	=>false,
				),
			);  
// UNCOMMENT			
			// $apiKeyURL=$baseUrl.'/user/create_aws_gateway_key_using_sdk?token=TyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9&key='.$normalised_name;
			// $apiData=file_get_contents($apiKeyURL, false, stream_context_create($arrContextOptions));
			// $apiDataResponse = json_decode( $apiData, true);
			// $aws_api_key=$apiDataResponse['api_key'];
			// $aws_api_key_id=$apiDataResponse['api_key_id'];
// UNCOMMENT	
			$aws_api_key_id = '3eg4p49wja';
			$aws_api_key    ='wEho4UWZjY6ds25y3Ryac35fiVy7Ov0w3M1rc2oj';
			$srapisecret	= generateKey();
			$srapikey		= generateKey();
			$srapitoken 	= base64_encode($srapikey.':'.$srapisecret);

			$postcode 		= "";
			$first_name 	= $user_first_name;
			$last_name 		= $user_last_name ;
			$email 			= $user_email;
			$country  		= $user_country_or_region;
			// $company_epos = $epos_provider = 'epos now';
			$password = wp_generate_password( 8, false ); //generate random password with letter and numbers
			$api_data = array(
					'company'  			=>   $user_company_name, //company name
					'email'    			=>   $email,
					'first_name'    	=>   $first_name,
					'last_name'     	=>   $last_name,
					'phone'         	=>   $user_business_phone,
					'tree_nums'     	=>   0,
					'password'      	=>   $password,
					'postcode'      	=>   $postcode,
					'form_type'     	=>  'non-ls-signp-form',
					// 'company_epos'  	=>   $epos_provider,
					'normalised_name'	=>$normalised_name,
					'csr_url' 			=>'https://sustainably.run/'.$normalised_name,
					'srapikey'			=>$srapikey,
					'srapisecret'		=>$srapisecret,
					'srapitoken'		=>$srapitoken,
					'aws_api_key'		=>$aws_api_key,
					'aws_api_key_id'	=>$aws_api_key_id,
					// 'guid'			=>$post_data['guid'],				
					// 'app_location_name'=>$post_data['AppLocationName'],
					// 'app_location_id'=>$post_data['AppLocationId'],
					// 'if_is_epos_now'	=>1,
					'transaction_email'	=>'Yes',
					);					
// UNCOMMENT					
			// $trial_record 		    = http_build_query($api_data);               //to add automatic initiative partner on panacea
			// $url					= $baseUrl.'/user/add_initiative_partner_from_api/?'.$trial_record;
			// $panaceaApiResponse 	= file_get_contents($url, false, stream_context_create($arrContextOptions)); 	
			// $panaceaApiResponseArray= json_decode($panaceaApiResponse, true);
			// $panacea_partner_id		= $panaceaApiResponseArray['panacea_partner_id'];
// UNCOMMENT			
			$panacea_partner_id = 60226;
			$if_is_parent		= $panaceaApiResponseArray['if_is_parent'];
			$userdata 			= array(
				'user_login'  	=>  $username,
				'user_pass'   	=>  $password,
				'first_name'  	=>  $first_name,
				'last_name'   	=>  $last_name,
				'user_email'	=>	$email,
				//'user_email'   	=>  $email,
				'user_pass'  	=>  $password,
				'user_url'    	=>  $siteUrl,//'https://carbonfreedining.org',
				'role'   	  	=>  'ecommerce'
			); 

			$exists = email_exists($user_email);
			if ( $exists ){
				$user_id = $exists;
			}
			else{
				$user_id = wp_insert_user( $userdata );     //create new wordpress user	
			}
			if($user_id){
				$users_tbl = $wpdb->prefix.'users';
				$wpdb->query($wpdb->prepare("UPDATE $users_tbl SET user_email='$email' WHERE ID=$user_id"));
				$wpdb->update( $ecommerce_table, array(
					'cfd_id'			=> $user_id,
					'normalised_name'	=>$normalised_name,
					'srapikey'			=>$srapikey,
					'srapisecret'		=>$srapisecret,
					'srapitoken'		=>$srapitoken,
					'aws_api_key'		=>$aws_api_key,
					'aws_api_key_id'	=>$aws_api_key_id ),
					array(
					 'id' 				=> $ecom_row_incert_id
					));
				
				$data = array(
					'first_name' 		 => $first_name, 
					'last_name' 		 => $last_name,
					'user_id' 			 => $user_id,
					'panacea_id' 		 => $panacea_partner_id,
					'email' 			 => $email, //company email
					'postcode' 			 => $postcode,
					'country' 			 => $user_country_or_region,
					'company_name' 		 => $user_company_name, //company name
					'phone_number' 		 => $user_business_phone, 
					'restaurant_phone'   => $user_business_phone,
					'non_lightspeed'     => '1',
					'normalised_name'	 =>$normalised_name,
					'srapikey'			 =>$srapikey,
					'srapisecret'		 =>$srapisecret,
					'srapitoken'	     =>$srapitoken,
					'aws_api_key'		 =>$aws_api_key,
					'aws_api_key_id'	 =>$aws_api_key_id,
				);
				$wpdb->insert( $restaurant_table, $data);
				$restaurant_table_id 	 = $wpdb->insert_id;
				$staff_activation_key 	 = md5( microtime() . rand() );
				update_user_meta( $user_id, 'staff_activation_key', $staff_activation_key);

				$login_url = $siteUrl.'/autologin.php?email='.$email.'&pkey='.$staff_activation_key;
				$company_res_name = stripslashes($user_company_name);
				if($company_res_name !=""){
					$getcompany_id = $wpdb->get_row( $wpdb->prepare("SELECT * FROM cfwp_hubspot_companies_list WHERE `company_name` = %s", $company_res_name));
					if(!empty($getcompany_id)){
						$companyId = $getcompany_id->company_id;
					}else{
						$company_arr = array(
								'properties' => array(
								array(
									'name' => 'name',
									'value' => stripslashes($company_res_name)
								),
								// array(
								// 	'name' => 'domain',
								// 	'value' => $email
								// ),
								// array(
								// 	'name' => 'company_address',
								// 	'value' => stripslashes($restaurant_address)
								// ),
								array(
									'name' => 'industry',
									'value' => 'ECOMMERCE'
								),
								// array(
								// 	'name' => 'epos_provider',
								// 	'value' => stripslashes($company_epos)
								// ),
								// array(
								// 	'name' => 'epos_provider_not_listed_answer',
								// 	'value' => stripslashes($epos_provider)     
								// ),
								// array(
								// 	'name' => 'sustainably_run_restaurant_',
								// 	'value' => 'Yes'     
								// ),
							));				
						if($if_is_parent){
							$comp_response = cfd_create_hubspot_company( $company_arr );    // to create hubspot company in the hubspot
							$res_comp  		= json_decode($comp_response, true);
							$companyId 		= $res_comp['companyId'];
							$company_name 	= $res_comp['properties']['name']['value'];
							if(isset($companyId)){		
								$cmp_data = array(
									'company_id'     =>  $companyId , 
									'company_name'   =>  $company_name
								);
								$wpdb->insert('cfwp_hubspot_companies_list', $cmp_data);          //insert data in table
							}
						}
					}											

				}
				if($if_is_parent){
					// to get contact ID of the existing user using email
					$emailresponse = get_hubspot_contact_using_email($email);	
					$email_response = json_decode($emailresponse, true);	 				
					if(isset($email_response['vid'])){
							$contact_id = $email_response['vid'];				
							$arr = array(
								'properties' => array(
									array(
										'property' => 'certification_url',
										'value' => $login_url
									),
									array(
										'property' => 'direct_login_link',
										'value' => $login_url               
									),
									array(
										'property' => 'partner_dashboard_password',
										'value' => $password                
									),
									array(
										'property' => 'partner_dashboard_username',
										'value' => $email               
									),
									array(
										'property' => 'country',
										'value' => $user_country_or_region
									),
									array(
										'property' => 'restaurant_manager_owner',
										'value' =>   $first_name.' '.$last_name     
									),
									array(
										'property' => 'position',
										'value' =>  'Manager'                
									),
									array(
										'property' => 'company',
										'value' =>  stripslashes($user_company_name)      
									),
									// array(
									// 	'property' => 'leadin_restaurant_name_bc6561da211d0d58325ab6608f22c535',
									// 	'value' =>  stripslashes($post_data['CompanyName'])       
									// ),
								)
							);
							$update_contact = to_update_hubspot_contact_properties( $arr, $contact_id );
					}else{	
						$arr = array(
							'properties' => array(
								array(
									'property' => 'email',
									'value' => $email
								),
								array(
									'property' => 'firstname',
									'value' => $first_name
								),
								array(
									'property' => 'lastname',
									'value' => $last_name
								),
								array(
									'property' => 'country',
									'value' => $user_country_or_region
								),
								array(
									'property' => 'phone',
									'value' =>  $user_business_phone
								),	
								array(
									'property' => 'hubspot_owner_id',
									'value' => "32668037"               
								),
								array(
									'property' => 'certification_url',
									'value' => $login_url                
								),
								array(
									'property' => 'direct_login_link',
									'value' => $login_url                
								),
								array(
									'property' => 'partner_dashboard_password',
									'value' => $password                
								),
								array(
									'property' => 'partner_dashboard_username',
									'value' => $email                
								),
								array(
									'property' => 'restaurant_manager_owner',
									'value' =>   $first_name.' '.$last_name      
								),
								array(
									'property' => 'position',
									'value' =>  'Manager'                
								),
								array(
									'property' => 'company',
									'value' =>  stripslashes($user_company_name)       
								),
																	
							)
						);
						$response_data  = cfd_create_hubspot_contact_using_api( $arr );	
						// echo '<pre>'; print_r($response_data); echo '</pre>';
						// exit;
						//to save the contactid for delete record
						if(isset($response_data->vid)){    
							$contact_id  = $response_data->vid;
						}	
					}
					//to add contact to company
					$add_contact_res =  to_add_hscontact_to_hs_company( $companyId, $contact_id );
					$res = json_decode($add_contact_res, true);
					if(isset($res['companyId'])){	
						$companyId = $res['companyId'];			
						/* to associate both ambassador and staff members */
						$associate_ids = $contact_id;
						$associate_ids = explode(",",$associate_ids);
						$arr = array(
							'associations' => array(
									'associatedCompanyIds' => array($companyId),
									'associatedVids' => $associate_ids
									),
								'properties' => array(
									array(
										'name' => 'dealname',
										'value' => $company_res_name
									),
									array(
										'name' => 'dealstage',
										'value' => '89f97f76-4727-4dce-93eb-0f413fa023ed'           //lead form
									),
									array(
										'name' => 'pipeline',
										'value' => 'default'
									), 
									array(
										'name' => 'hubspot_owner_id',
										'value' => '32668037'                      
									)
								)
							);	
								/* To create Deal */
						$deal_response = cfd_create_hubspot_deal($arr);
						$deal_response = json_decode($deal_response, true);
						if(isset($deal_response['dealId']) && $restaurant_table_id!=""){
							$wpdb->query("UPDATE `cfwp_ls_trial_signup_data` SET hsdeal_id='".$deal_response['dealId']."',hscontact_id='".$contact_id."',hs_company_id='".$companyId."' WHERE id='".$restaurant_table_id."'");
							$update_data = array(
								'hs_company_id'  =>   $companyId,
								'hs_deal_id'  	 =>   $deal_response['dealId'],
								'partner_id'  	 =>   $panacea_partner_id
								);
							$update_record = http_build_query($update_data);               //to update initiative partner on panacea
							$update_url =$baseUrl.'/user/update_initiative_partner_from_api/?'.$update_record;
							$update_api_record = file_get_contents($update_url, false, stream_context_create($arrContextOptions)); 
						} 				

					}
				}

				$wpdb->update( $ecommerce_table, array( 'processed'=> 1 ),array( 'id' => $ecom_row_incert_id ));
					$response_arr = array(	
								'user_status' 	 		=> 'active',
								'status' 	 		=> '200',
								'companyname' 		=> $user_company_name,
								'companyemail'		=> $email,
								'apitoken' 			=> $aws_api_key,
								'cfd_id' 	 		=> $user_id,
								'normalised_name'	=> $normalised_name,
								'csr_url'			=> 'https://sustainably.run/'.$normalised_name,
								'srapikey'			=> $srapikey,
								'srapisecret'		=> $srapisecret,
								'srapitoken'		=> $srapitoken,
								'aws_api_key'		=> $aws_api_key
								// 'receipt_messages'  => $receipt_msg_data
							);
						
					$update_epos   =  update_epos_middleware_user_webhook($response_arr, "Create");       // call ePosnow middleware webhook
					echo json_encode($response_arr);
					exit;	
			}
		
			
		}

	}
	else{
		$returnArray['status'] 	= '0';
		$returnArray['message'] = 'Something went wrong';
	}
	die(json_encode($returnArray));
  }

  function createNormalisedName($name){
	global $wpdb, $ecommerce_table , $post_data;
	$returnString='';
	$lowecaseString=strtolower($name);
	$trimString=trim($lowecaseString);
	$replaceSpace=str_replace(' ', '-', $trimString);
	$specialRemoved=preg_replace('/[^A-Za-z0-9\-]/', '', $replaceSpace);
	$returnString=$specialRemoved;	
	$rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $ecommerce_table WHERE CompanyName = '$name'");
	
	if($rowcount==1){
		return $returnString;
	}else{
		return $returnString.'-'.$rowcount;		
	}
}

function generateKey(){
	$n=24;
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
	$randomString = ''; 	  
	for ($i = 0; $i < $n; $i++) { 
		$index = rand(0, strlen($characters) - 1); 
		$randomString .= $characters[$index]; 
	} 	  
	return $randomString; 
}