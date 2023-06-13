<?php
	
	//API Headers
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=UTF-8");
	include_once '../conn/dbh.inc.php' ;

	//Error Display
	error_reporting(E_ALL); ini_set('display_errors', 1);

	//Response Array
	$response = array();
	$dateTime = date('Y-m-d H:i:s') ;

	if(($_GET['prs'])&&($_GET['token']))
	{
		//Get PRS and Token
		$prs = $_GET['prs'];
		$token = $_GET['token'];

		//Check if Revision table already updated
		$sql = "SELECT *
				FROM revisions
				WHERE rev_sign='$token';" ;
		$result = mysqli_query($dcConn,$sql);
		$resultCheck = mysqli_num_rows($result) ;

		if($resultCheck<1)
		{
		    //Create New Revision
		    $sql = "INSERT INTO revisions (rev_sign,prs,rev_id1,rev_id2,rev_desc,rev_author,rev_date,published)
		    		VALUES ('$token','$prs',1,1,'New Unpublished Revision','System','$dateTime',0);" ;
			$result = mysqli_query($dcConn,$sql);

			if ($result==true)
			{
			    //Retrieve latest published revision
			    $sql = "SELECT *
			    		FROM revisions
			    		WHERE prs='$prs' AND published=1
			    		ORDER BY rev_date DESC;" ;
				$result = mysqli_query($dcConn,$sql);
				$resultCheck = mysqli_num_rows($result) ;

				if($resultCheck>0)
				{
					$row = mysqli_fetch_row($result);
					$revSign = $row[0];

				    //Retrieve latest published section row
				    $sql = "SELECT *
				    		FROM sections
				    		WHERE rev_sign='$revSign';" ;
					$result = mysqli_query($dcConn,$sql);
					$resultCheck = mysqli_num_rows($result) ;

					if ($resultCheck>0)
					{
						$row = mysqli_fetch_row($result);

			    		//Create New Section Row
				        $sql1 = "INSERT INTO sections (rev_sign,section2,section3,section4,section5,section6,section7,section8,section9,section10,section11)VALUES ('$token','$row[1]','$row[2]','$row[3]','$row[4]','$row[5]','$row[6]','$row[7]','$row[8]','$row[9]','$row[10]')";
				    	$result1 = mysqli_query($dcConn,$sql1);

				    	$response['status'] = 1;
				    	$response['message'] = "You're now editing this document" ;
					}
					else
					{
						$response['status'] = 0;
						$response['message'] = "Something went wrong (Error: Failed to retrieve latest section details), please contact administrator." ;
					}
				}
				else
				{
					$response['status'] = 0;
					$response['message'] = "Something went wrong (Error: Failed to retrieve latest revision), please contact administrator." ;
				}
			}
			else
			{
				//Failed to create new revision
				$response['status'] = 0;
		        $response['message'] = "Something went wrong (Error: Failed to create new revision), please contact administrator with Reference ID:".$token ;
		        $response['res'] = $sql;
			}
		}
		else
		{
			//Do Nothing
			$response['status'] = 1;
			$response['message'] = "You're now editing this document" ;
		}
	}
	else
	{
		$response['status'] = 0;
		$response['message'] = "Something went wrong, please contact administrator before editing.";
	}		

	//Output Data in JSON
	echo json_encode($response);

?>
