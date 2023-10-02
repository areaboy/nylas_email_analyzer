<?php
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
// temporarly extend time limit
set_time_limit(300);
error_reporting(0);

if (isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST") {

//include('data6rst.php');
include('settings.php');

if($nylas_accesstoken ==''){
echo "<div  style='background:red;color:white;padding:10px;border:none;'>Please Ask Admin to Set Nylas API Access Token  at <b>settings.php</b> File</div><br>";
exit();

}



if($admin_name ==''){
echo "<div  style='background:red;color:white;padding:10px;border:none;'>Please Ask Admin to Set Site Admin Name at <b>settings.php</b> File</div><br>";
exit();

}


if($admin_email ==''){
echo "<div  style='background:red;color:white;padding:10px;border:none;'>Please Ask Admin to Set Site Admin Email at <b>settings.php</b> File</div><br>";
exit();

}



$fullname= strip_tags($_POST['sender_fullname']);
$email= strip_tags($_POST['sender_email']);
$subject= $automated_email_subject;
$message= "$fullname, $automated_email_message";


 $data_param= '{
  "subject": "'.$subject.'",
  "to": [
    {
      "email": "'.$email.'",
      "name": "'.$fullname.'"
    }
  ],
  "from": [
    {
      "name": "'.$admin_name.'",
      "email": "'.$admin_email.'"
    }
  ],
  "body": "'.$message.'"
}';


$url ="https://api.nylas.com/send";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', "Authorization: Bearer $nylas_accesstoken"));  
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_param);
//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
 $output = curl_exec($ch); 


$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// catch error message before closing
if (curl_errno($ch)) {
    //echo $error_msg = curl_error($ch);
}

curl_close($ch); 


$json = json_decode($output, true);
$account_id = $json["account_id"];
$id = $json["id"];

if($account_id != ''){
echo "<br><div style='background:green;color:white;padding:10px;border:none;'>Automated Email Response Successfully Sent Via Nylas Rest API</div><br>";
//echo "<script'>alert('Automated Email Response Successfully Sent Via Nylas Rest API');</script>";

}



}
else{
echo "<div id='' style='background:red;color:white;padding:10px;border:none;'>
Direct Page Access not Allowed<br></div>";
}


?>