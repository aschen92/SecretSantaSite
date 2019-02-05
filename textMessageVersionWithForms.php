<?php

// Require the bundled autoload file - the path may need to change
// based on where you downloaded and unzipped the SDK
require __DIR__ . '/twilio-php-master/Twilio/autoload.php';

// Use the REST API Client to make requests to the Twilio REST API
use Twilio\Rest\Client;

$inputRowCount = htmlspecialchars($_POST["inputRowCount"]);

// Populate the email address array and names array by looping through the rows of input from the form
$emailAddressArray = array();
$names = array();

for ($i = 1; $i < $inputRowCount + 1; $i++) {
    $currParticipantName = htmlspecialchars($_POST["participantName".$i]);
    array_push($names, $currParticipantName);
    
    $currParticipantEmailAddress = htmlspecialchars($_POST["participantEmailAddress".$i]);
    
    $tempEmailAddressPair = array($currParticipantName, $currParticipantEmailAddress);
    $emailAddressArray[$currParticipantName] = $currParticipantEmailAddress;
}

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

//-----------------

// Your Account SID and Auth Token from twilio.com/console
$account_sid = '[redacted]'; 
$auth_token = '[redacted]'; 
$client = new Client($account_sid, $auth_token); 

// Output a master list of assignments to a text file for future reference
$currTimeStamp = date("m-d-Y-h-i-s");
$myfile = fopen("secret_santa_masterlist_" . date("m-d-Y-h-i-s") . ".txt", "w") or die("Unable to open file!");
	
foreach ($assignList as $assignment) {
    //echo $emailAddressArray[$assignment[0]];
    $dollarLimit = preg_replace("/[^0-9]*/", "", $_POST["dollarLimit"]);
    try {
        $messages = $client->messages->create($emailAddressArray[$assignment[0]], array( 
                'From' => "+16124402289",  
                'Body' => "This year you will be getting a Secret Santa gift for " . $assignment[1] . "!\n\nThe dollar limit is $" . $dollarLimit . ". Merry Christmas!",
        ));
    }
    catch(Exception $e) {
        echo "Sorry, that is not a valid number";
        echo "Sorry, a problem occurred. It could be that some fields were left blank or at least one of the phone numbers were not valid. <a href='http://www.secretsanta.aaronschendel.com'>Please try again!</a>";
        //throw $e;
        return;
    }
	
	$fileRow = $assignment[0] . " has " . $assignment[1] . "\n";
	fwrite($myfile, $fileRow);
}

fclose($myfile);
                              
echo("
<p>Text messages were sent out and the Secret Santa Master List was created!</p>

<p><a href='secret_santa_masterlist_".$currTimeStamp.".txt'>Here is the Secret Santa Master List!</a> Right click on the link to save it as a file (in case you don't want to ruin the surprise but still want a backup!</p>");              
                              
?>                             