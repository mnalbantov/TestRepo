<?

// !!! security !!! these values are normally extracted from a database.

$MerchantID = "yourMerchantID";       // via e-mail from computop support
$Password   = "yourPassword";         // via phone from computop support


// initialize input.php

$iAmount     = 11;
$sCurrency   = "EUR";              // default - payment.php
$sOrderDesc  = "your order";
$sUserData   = "your data";

$sthPayGate  = "PHP - Server to Server";
$stdTransID  = "TransID";
$stdAmount   = "Amount";
$stdCurrency = "Currency";
$stdOrder    = "Order description";
$stdUserdata = "Userdata";
$stdInpSend  = "send request";
$stdCCNr     = "CC Number";
$stdCCCVC    = "CVC";
$stdCCExpiry = "CC Expiry";
$stdCCBrand  = "CC Brand";
$stdAccOwner = "Account Owner";
$stdAccNr    = "Account Number";
$stdAccBank  = "Bank";
$stdAccIBAN  = "IBAN";
$stdPayType  = "PayType";

$sPayType[0] = "Creditcard Payment";
$sPayType[1] = "Electronic Direct Debit";

$sHost       = "www.netkauf.de";
$iPort       = 443;
$sURL[0]     = "/paygate/direct.aspx";
$sURL[1]     = "/paygate/edddirect.aspx";

$sendMeth[0] = "with curl";         // require curl support
$sendMeth[1] = "with fsockopen";	// require php 4.3.x


// initialize html.inc.php

$sthInfo = "Information";

?>
