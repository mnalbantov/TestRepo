<?php

namespace Payment\Gateway\Computop\S2S;

// required constants

include('./includes/function.inc.php');


// read data (for form method="POST")

$TransID   = $HTTP_POST_VARS["TransID"];
$Amount    = $HTTP_POST_VARS["Amount"];
$Currency  = $HTTP_POST_VARS["Currency"];
$OrderDesc = $HTTP_POST_VARS["OrderDesc"];
$UserData  = $HTTP_POST_VARS["UserData"];
$PayType   = $HTTP_POST_VARS["PayType"];
$Method    = $HTTP_POST_VARS["sMethod"];

switch ($PayType){

    case $sPayType[0]:
         $CCNr     = $HTTP_POST_VARS["CCNr"];
         $CCCVC    = $HTTP_POST_VARS["CCCVC"];
         $CCExpiry = $HTTP_POST_VARS["CCExpiry"];
         $CCBrand  = $HTTP_POST_VARS["CCBrand"];
         $CCNr     = "&CCNr="      .$CCNr;
         $CCCVC    = "&CCCVC="     .$CCCVC;
         $CCExpiry = "&CCExpiry="  .$CCExpiry;
         $CCBrand  = "&CCBrand="   .$CCBrand;
         $plain    = $CCNr.$CCCVC.$CCExpiry.$CCBrand;
         $URL      = $sURL[0];
         break;
         
    case $sPayType[1]:
         $AccOwner = $HTTP_POST_VARS["AccOwner"];
         $AccNr    = $HTTP_POST_VARS["AccNr"];
         $AccBank  = $HTTP_POST_VARS["AccBank"];
         $AccIBAN  = $HTTP_POST_VARS["AccIBAN"];
         $AccOwner = "&AccOwner="  .$AccOwner;
         $AccNr    = "&AccNr="     .$AccNr;
         $AccBank  = "&AccBank="   .$AccBank;
         $AccIBAN  = "&AccIBAN="   .$AccIBAN;
         $plain    = $AccOwner.$AccNr.$AccBank.$AccIBAN;
         $URL      = $sURL[1];
         break;
}


// optional values

$Currency = trim($Currency);

if($Currency == ""){
    $Currency = "&Currency=$sCurrency";

} else {
    $Currency = "&Currency=".$Currency;
}

$UserData = "&UserData=".$UserData;
  #$Capture = "&Capture=AUTO";


// formatting data to transmit - required

$ReqId     = "&ReqID="     .$TransID;
$TransID   = "&TransID="   .$TransID;
$Amount    = "&Amount="    .$Amount;
$OrderDesc = "&OrderDesc=" .$OrderDesc;



// build MerchantID, Len and Data (encrypted)

$plaintext = "MerchantID=".$MerchantID.$TransID.$Amount.$Currency.$UserData.$OrderDesc.$Capture.$plain.$ReqId;
$Len        = strlen($plaintext);  // Length of the plain text string


// encryption

$myPayGate = new ctPayGate;
$Data      = $myPayGate->ctEncrypt($plaintext, $Len, $Password);
$data      = "MerchantID=$MerchantID&Len=$Len&Data=$Data";

#echo "result: ".$result;

// connect to server

switch ($Method){

    case $sendMeth[0]:
         include('curlpost.php');
         break;

    case $sendMeth[1]:
		 
         include('socketpost.php');
         break;
}
 

// decrypt the data string

$rs        = split ('&', $result);
$Len       = $myPayGate->ctSplit($rs, '=', 'Len');
$Data      = $myPayGate->ctSplit($rs, '=', 'Data');
$plaintext = $myPayGate->ctDecrypt($Data, $Len, $Password);

#echo $plaintext . "<p>";

// prepare information string

$a      = split ('&', $plaintext);
$info  .= $myPayGate->ctSplit($a, '=');
$Status = $myPayGate->ctSplit($a, '=', 'Status');


// checking transmitted decrypted status

$realstatus = $myPayGate->ctRealstatus($Status);


// html output

require('./includes/html.inc.php');

?>
