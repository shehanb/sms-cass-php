<?php
//Shehan Bhavan - hSenid Mobile Solutions
//shehanb@hsenidmobile.com
//Dialog Ideamart
require 'alllibs.php';


$APP_ID = "APP_000001";
$PASSWORD = "password";
$EXTERNAL_TRX_ID="123";

$production = false;

if ($production == false) {
    $CASS_SERVER_URL = "http://localhost:7000/caas/direct/debit";
    $SMS_SERVER_URL = "http://localhost:7000/sms/send";
} else {
    $CASS_SERVER_URL = 'https://api.dialog.lk/caas/direct/debit';
    $SMS_SERVER_URL = "https://api.dialog.lk/sms/send";
}

$logger = new Logger();

try {


    $receiver = new SMSReceiver();

    $message = $receiver->getMessage(); // Get the message sent to the app
    $address = $receiver->getAddress(); // Get the phone no from which the message was sent 

    list($keyword, $amount) = explode(" ", $message);

    // Setting up CAAS
    $cass = new DirectDebitSender($CASS_SERVER_URL, $APP_ID, $PASSWORD);
    $sender = new SmsSender($SMS_SERVER_URL, $APP_ID, $PASSWORD);


    try {
        if (isset($amount)) {
            $cass->cass($EXTERNAL_TRX_ID, $address, $amount);
            $sender->sms("Thank you for your generosity, You Have made a donation for ".$amount." Rupees", $address);
        }
    } catch (CassException $ex) {
        $logger->WriteLog($ex);
        $sender->sms("You do not have sufficient credit to make this donation", $address);
    }
} catch (Exception $e) {
    $logger->WriteLog($e);
}
?>