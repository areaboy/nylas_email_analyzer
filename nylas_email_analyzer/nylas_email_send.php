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


if($expertai_username ==''){
echo "<div  style='background:red;color:white;padding:10px;border:none;'>Please Ask Admin to Set Expert.AI Username at <b>settings.php</b> File</div><br>";
exit();

}

if($expertai_password ==''){
echo "<div  style='background:red;color:white;padding:10px;border:none;'>Please Ask Admin to Set Expert.AI Password  at <b>settings.php</b> File</div><br>";
exit();

}

$fullname= strip_tags($_POST['fullname']);
$email= strip_tags($_POST['email']);
$subject= strip_tags($_POST['subject']);
$message= trim($_POST['message']);
$sentiments= strip_tags($_POST['sentiments']);
$keyphrases= strip_tags($_POST['keyphrases']);
$entity= strip_tags($_POST['entity']);


$data_param= '{
  "subject": "'.$subject.'",
  "to": [
    {
      "email": "'.$admin_email.'",
      "name": "'.$admin_name.'"
    }
  ],
  "from": [
    {
      "name": "'.$fullname.'",
      "email": "'.$email.'"
    }
  ],
  "reply_to": [
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

$mx_error = $json["message"];
if($mx_error != ''){
echo "<div style='background:red;color:white;padding:10px;border:none;'>API Error Message: $mx_error</div><br>";
exit();
}

// start ajax call



if($account_id != ''){

echo "<div style='background:green;color:white;padding:10px;border:none;'> Email Successfully Sent Via Nylas Rest API</div><br>";



// Make API Call to Expert.AI to Generate Access Token

$url = 'https://developer.expert.ai/oauth2/token';
$ch = curl_init($url);

$uname =$expertai_username;
$upass  =$expertai_password;
$data = array(
    'username' => $uname,
    'password' => $upass
);
$payload = json_encode(array($data));
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);
$access_token = $result;





// Email Text  Summary and Keyphrases Analysis starts Here
if($keyphrases == 'yes'){

echo "
<script>
$(document).ready(function(){

var msg_text  = '$message';
var expertai_token = '$access_token';


if(msg_text==''){
alert('Message Cannot be Empty');

}

else{

$('#loader_keyphrases').fadeIn(400).html('<br><div style=color:black;background:#ddd;padding:10px;><img src=loader.gif style=font-size:20px> &nbsp;Please Wait!. Summarizing Email Message and Displaying Keyword Phrases via Expert.AI</div>');
var datasend = {msg_text:msg_text,expertai_token:expertai_token};


$.ajax({
			
			type:'POST',
			url:'email_ai_keyphrases.php',
			data:datasend,
                        crossDomain: true,
			cache:false,
			success:function(msg){

                        $('#loader_keyphrases').hide();
				//$('#result_keyphrases').fadeIn('slow').prepend(msg);
$('#result_keyphrases').html(msg);



			
			}
			
		});
		
		}
		
					
	});


</script>




<br>
<div class='well'>
<div id='loader_keyphrases'></div>
<div id='result_keyphrases'></div>
</div>

";

}


// Email Text Summary and Keyphrases  Analysis ends









// Email Text  Entity Analysis starts Here
if($entity == 'yes'){

echo "
<script>
$(document).ready(function(){

var msg_text  = '$message';
var expertai_token = '$access_token';


if(msg_text==''){
alert('Message Cannot be Empty');

}

else{

$('#loader_entity').fadeIn(400).html('<br><div style=color:black;background:#ddd;padding:10px;><img src=loader.gif style=font-size:20px> &nbsp;Please Wait!. Email Messages Entity Analysis via Expert.AI</div>');
var datasend = {msg_text:msg_text,expertai_token:expertai_token};


$.ajax({
			
			type:'POST',
			url:'email_ai_entities.php',
			data:datasend,
                        crossDomain: true,
			cache:false,
			success:function(msg){

                        $('#loader_entity').hide();
				//$('#result_entity').fadeIn('slow').prepend(msg);
$('#result_entity').html(msg);



			
			}
			
		});
		
		}
		
					
	});


</script>




<br>
<div class='well'>
<div id='loader_entity'></div>
<div id='result_entity'></div>
</div>

";

}


// Email Text Entity  Analysis ends










// Email Text  Sentiments Analysis starts Here
if($sentiments == 'yes'){

echo "
<script>
$(document).ready(function(){

var msg_text  = '$message';
var expertai_token = '$access_token';


if(msg_text==''){
alert('Message Cannot be Empty');

}

else{

$('#loader_sentiments').fadeIn(400).html('<br><div style=color:black;background:#ddd;padding:10px;><img src=loader.gif style=font-size:20px> &nbsp;Please Wait! .Analyzing Email Message for Sentimentss via Expert.AI</div>');
var datasend = {msg_text:msg_text,expertai_token:expertai_token};


$.ajax({
			
			type:'POST',
			url:'email_ai_sentiments.php',
			data:datasend,
                        crossDomain: true,
			cache:false,
			success:function(msg){

                        $('#loader_sentiments').hide();
				//$('#result_sentiments').fadeIn('slow').prepend(msg);
$('#result_sentiments').html(msg);



			
			}
			
		});
		
		}
		
					
	});


</script>




<br>
<div class='well'>
<div id='loader_sentiments'></div>
<div id='result_sentiments'></div>
</div>

";

}


// Email Text  Sentiments Analysis ends








//Send Automated Email starts 

if($automated_email == '1'){

echo "
<script>
$(document).ready(function(){

var sender_fullname  = '$fullname';
var sender_email = '$email';


if(sender_fullname==''){
alert('Email Sender Cannot be Empty');

}

else{

$('#loader_em').fadeIn(400).html('<br><div style=color:black;background:#ddd;padding:10px;><img src=loader.gif style=font-size:20px> &nbsp;Please Wait!. Sending Automated Email Response to User via Nylas API</div>');
var datasend = {sender_fullname:sender_fullname,sender_email:sender_email};


$.ajax({
			
			type:'POST',
			url:'nylas_automated_email_response.php',
			data:datasend,
                        crossDomain: true,
			cache:false,
			success:function(msg){

                        $('#loader_em').hide();
				//$('#result_em').fadeIn('slow').prepend(msg);
$('#result_em').html(msg);



			
			}
			
		});
		
		}
		
					
	});


</script>




<br>
<div class='well'>
<div id='loader_em'></div>
<div id='result_em'></div>
</div>

";

}


// Send Automated Email ends 





exit();

}








// end ajax call




}
else{
echo "<div id='' style='background:red;color:white;padding:10px;border:none;'>
Direct Page Access not Allowed<br></div>";
}


?>