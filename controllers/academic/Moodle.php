<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>V6</title>
</head>

<body>

<?php
//load curl.php
require_once('curl.php');

function randomPassword() //according to Moodle password requirements
{
	$part1 = "";
	$part2 = "";
	$part3 = "";
	
    //alphanumeric LOWER
	$alphabet = "abcdefghijklmnopqrstuwxyz";
    $password_created = array(); //remember to declare $pass as an array
    $alphabetLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 3; $i++) 
	{
        $pos = rand(0, $alphabetLength); // rand(int $min , int $max)
        $password_created[] = $alphabet[$pos];
    }
	$part1 = implode($password_created); //turn the array into a string
	//echo"<br/>part1 = $part1";

	//alphanumeric UPPER
	$alphabet = "ABCDEFGHIJKLMNOPQRSTUWXYZ";
    $password_created = array(); //remember to declare $pass as an array
    $alphabetLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 3; $i++) 
	{
        $pos = rand(0, $alphabetLength); // rand(int $min , int $max)
        $password_created[] = $alphabet[$pos];
    }	
	$part2 = implode($password_created); //turn the array into a string
	//echo"<br/>part2 = $part2";

	//alphanumeric NUMBER
	$alphabet = "0123456789";
    $password_created = array(); //remember to declare $pass as an array
    $alphabetLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 2; $i++) 
	{
        $pos = rand(0, $alphabetLength); // rand(int $min , int $max)
        $password_created[] = $alphabet[$pos];
    }	
	$part3 = implode($password_created); //turn the array into a string
	//echo"<br/>part3 = $part3";
	
	$password = $part1 . $part2 . $part3 . "#";
	
	return $password;
}

function getCDate()
{
	$format = "Ymd";
	$fulldate = date($format);	
	//echo"<br/>fulldate = $fulldate";
	return $fulldate;
}

function enrol($user_id, $course_id) 
{
	$role_id = 5; //assign role to be Student
	
	$domainname = 'http://www.yoursite.eu'; //paste your domain here
	$wstoken = '8486ed14f3ghjec8967a0229d0a28zzz'; //here paste your enrol token 
	$wsfunctionname = 'enrol_manual_enrol_users';
	
	$enrolment = array( 'roleid' => $role_id, 'userid' => $user_id, 'courseid' => $course_id );
	$enrolments = array($enrolment);
	$params = array( 'enrolments' => $enrolments );
	
	header('Content-Type: text/plain');
	$serverurl = $domainname . "/webservice/rest/server.php?wstoken=" . $wstoken . "&wsfunction=" . $wsfunctionname;
	$curl = new curl;
	$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
	$resp = $curl->post($serverurl . $restformat, $params);
	print_r($resp);
}

function getUserDetails()
{
	$firstname 	= "TestUser";
	$lastname 	= "TestUser";
	$email 	  	= "TestUser@zzz.gr";
	$city 		= "Thessaloniki";
	$country  	= "EL";
	$description= "ZZZ";
	
	//assign username
	//get first two letters of name and surname
	//$strlength_user = strlen($firstname);
	//$strlength_pass = strlen($lastname);
	$rest_firstname = substr($firstname, 0, 2);
	$rest_lastname  = substr($lastname, 0, 2);
	$part1 = $rest_firstname . $rest_lastname;
	$part1 = strtolower($part1);
	//echo"<br/>part1 = $part1";
	$dt = getCDate();
	$part2 = substr($dt, -4);
	//echo"<br/>part2 = $part2";
	
	$username = $part1 . "." . $part2;
	echo"<br/>Username = $username";
	
	//assign password
	$password = randomPassword();
	echo"<br/>Password = $password";
	
	//call WS core_user_create_user of moodle to store the new user
	$domainname = 'http://www.yoursite.eu';
	$wstoken = 'ed1f6d3ebadg372f95f28cd96bd43zzz'; //here paste your create user token 
	$wsfunctionname = 'core_user_create_users';
	//REST return value
	$restformat = 'xml'; 
	//parameters
	$user1 = new stdClass();
	$user1->username 	= $username;
	$user1->password 	= $password;
	$user1->firstname 	= $firstname;
	$user1->lastname 	= $lastname;
	$user1->email 		= $email;
	$user1->auth 		= 'manual';
	$user1->idnumber 	= 'numberID';
	$user1->lang 		= 'en';
	$user1->city 		= $city;
	$user1->country 	= $country;
	$user1->description = $description;
	
	$users = array($user1);
	$params = array('users' => $users);
	//REST call
 	header('Content-Type: text/plain');
	$serverurl = $domainname . "/webservice/rest/server.php?wstoken=" . $wstoken . "&wsfunction=" . $wsfunctionname;
	$curl = new curl;
	$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
	$resp = $curl->post($serverurl . $restformat, $params);
	print_r($resp);
		
	//get id from $resp
	$xml_tree = new SimpleXMLElement($resp);
	print_r($xml_tree);         
	$value = $xml_tree->MULTIPLE->SINGLE->KEY->VALUE;
	$user_id = intval(sprintf("%s",$value));
	echo"<br/>user_id number = $user_id";
	
	//enrol_manual_enrol_users 
	//for($i = 64; $i < 70; $i++) //where 64,65,66,67,68,69 are the six ids of the six courses of phase 1
	for($i = 64; $i < 65; $i++)
	{
		echo "\nThe user has been successfully enrolled to course " . $i;
		$course_id = $i;
		enrol($user_id, $course_id);
	}	
}

getUserDetails();

?>
</body>
</html>