<?php
	
	//API Headers
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=UTF-8");
	include_once '../conn/dbh.inc.php' ;

	//Error Display
	error_reporting(E_ALL); ini_set('display_errors', 1);

	$response = array();
	$dateTime = date('Y-m-d H:i:s') ;

	if (($_POST['prs'])&&($_POST['siteName'])&&($_POST['city'])&&($_POST['state'])&&($_POST['country']))
	{
		//Get Data from POST
		$prs = $_POST['prs'];
		$siteName = $_POST['siteName'];
		$siteCity = $_POST['city'].' '.$_POST['cityExt'];
		$siteState = $_POST['state'];
		$siteCountry = $_POST['country'];
		//Generate a new Revision Signature
		$revSign = uniqid();

		//Check if PRS Exists
		$sql = "SELECT * FROM sites WHERE prs='$prs' ; " ;
		$result = mysqli_query($dcConn,$sql);
		$resultCheck=mysqli_num_rows($result);
		if ($resultCheck>0)
		{
			//PRS Exists
			$response['success'] = 0;
		    $response['message'] = "Another hospital with the same PRS exists. Failed to save data. Please consider searching with the existing PRS.";
		}
		else
		{
			//Insert Data
		    $sql = "INSERT INTO sites(prs,site_name,site_city,site_state,site_country) VALUES('$prs','$siteName','$siteCity','$siteState','$siteCountry');" ;
		    $result = mysqli_query($dcConn,$sql);
		    if ($result)
		    {
		        //Create New Revision
		        $sql = "INSERT INTO revisions (rev_sign,prs,rev_id1,rev_id2,rev_desc,rev_author,rev_date,published)
		        		VALUES ('$revSign','$prs',1,1,'Initial Revision','System','$dateTime',1);" ;
		    	$result = mysqli_query($dcConn,$sql);

		    	if ($result==true)
		    	{
		    		//Create New Section Row 63
				$sql = "INSERT INTO sections (rev_sign,section2,section3,section4,section5,section6,section7,section8,section9,section10,section11)VALUES('$revSign','$revSign','$revSign','$revSign','$revSign','$revSign','$revSign','$revSign','$revSign','$revSign','$revSign');" ;
							    	$result = mysqli_query($dcConn,$sql);

			    	if ($result==true)
			    	{
			    		//Success
			    		$response['success'] = 1;
			    		$response['token'] = uniqid();
			    		$response['prs'] = $prs;
			    		$response['siteName'] = 'Document Generator';
				        $response['message'] = "Document created successfully and is now available through search!";
			    	}
			    	else
			    	{
    		    		//Failed to create new section row
    		    		$response['success'] = $sql;
    			        $response['message'] = "Something went wrong (Error: Failed to create new section), please contact administrator with Reference ID:".$revSign ;
			    	}
		    	}
		    	else
		    	{
		    		//Failed to create new revision
		    		$response['success'] = 0;
			        $response['message'] = "Something went wrong (Error: Failed to create new revision), please contact administrator with Reference ID:".$revSign ;
		    	}	
		    }
		    else
		    {
		    	//Fail
		    	$response['success'] = 0;
		        $response['message'] = "Something went wrong. Document could not be created.";
		    }
		}
	}
	else
	{
		$response['success'] = 0;
		$response['message'] = "Please fill out all the details for creating a new document.";
	}
	
		

	//Output Data in JSON
	echo json_encode($response);

?>
