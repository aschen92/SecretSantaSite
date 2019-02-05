<?php

error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);

set_include_path("." . PATH_SEPARATOR . ($UserDir = dirname($_SERVER['DOCUMENT_ROOT'])) . "/pear/php" . PATH_SEPARATOR . get_include_path());
require_once "Mail.php";
//----------------------------------------------------------------------------------
$emailAddressArray = array(
							"Aaron"=>"aaron.schendel@gmail.com",
							"Ryan"=>"ryguybriggs@hotmail.com",
							"Krystal"=>"klbriggs28@gmail.com",
							"Phil"=>"schggs@comcast.net",
							"Chris"=>"cmbaron.cb@gmail.com",
							"Kyle"=>"abraham.skellington@yahoo.com",
							"Caytlyn"=>"cshimelphiney@yahoo.com",
							"Beth"=>"beth4smiles3@gmail.com",
							"Amanda"=>"amandajschuster@gmail.com"
							);

// Shuffle the names up and then assign person n to get a gift for person n-1
$names = array("Aaron","Aaron","Aaron");

shuffle($names);
$namesCount = count($names);

$assignList = array();
for ($i = 0; $i < $namesCount; $i++) {
	if ($i == 0) {
		$tempAssignmentArray = array($names[$i], end($names));
		array_push($assignList, $tempAssignmentArray);
	} else {
		$tempAssignmentArray = array($names[$i], $names[$i-1]);
		array_push($assignList, $tempAssignmentArray);
	}
}
//----------------------------------------------------------------------------------
echo("Before comment<br>");
print_r($assignList);
echo("<br>");
print_r($emailAddressArray);


// Static Email Settings
$host = "ssl://sub4.mail.dreamhost.com";
$username = "aaron@aaronschendel.com";
$password = "Pbkras09?";
$port = "465";
$email_from = "aaron@aaronschendel.com";
$email_address = "aaron@aaronschendel.com";
$email_subject = "Secret Santa 2015 Assignment!" ;
$smtp = Mail::factory('smtp', array ('host' => $host, 'port' => $port, 'auth' => true, 'username' => $username, 'password' => $password));

// Output a master list of assignments to a text file for future reference
$myfile = fopen("secret_santa_masterlist_" . date("m-d-Y-h-i-s") . ".txt", "w") or die("Unable to open file!");
	
foreach ($assignList as $assignment) {
	$to = $emailAddressArray[$assignment[0]];
	$email_body = "This year you will be getting a gift for " . $assignment[1] . "!\n\nAs a reminder: the gift for " . $assignment[1] . " should be $30 and then you will get two $10 gifts for the White Elephant game we play.\n\nMerry Christmas!!!";
	
	$headers = array ('From' => $email_from, 'To' => $to, 'Subject' => $email_subject, 'Reply-To' => $email_address);
	$mail = $smtp->send($to, $headers, $email_body);
	
	$fileRow = $assignment[0] . " has " . $assignment[1] . "\n";
	fwrite($myfile, $fileRow);
}

fclose($myfile);


if (PEAR::isError($mail)) {
echo("<p>" . $mail->getMessage() . "</p>");
} else {
echo("<p>Emails were sent out and the Secret Santa Master List was created!</p>");
}
?>