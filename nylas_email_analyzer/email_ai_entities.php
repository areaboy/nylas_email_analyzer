<style>


.cssxv{
background:#800000;color:white;padding:6px;border:none;border-radius:25%;
}


.cssxv:hover{
background: black;
color:white;

}

</style>
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


if($expertai_username ==''){
echo "<div  style='background:red;color:white;padding:10px;border:none;'>Please Ask Admin to Set Expert.AI Username at <b>settings.php</b> File</div><br>";
exit();

}

if($expertai_password ==''){
echo "<div  style='background:red;color:white;padding:10px;border:none;'>Please Ask Admin to Set Expert.AI Password  at <b>settings.php</b> File</div><br>";
exit();

}


$message= strip_tags($_POST['msg_text']);
$access_token= strip_tags($_POST['expertai_token']);


// Make API Call to Expert.AI Text Entites Analysis

$text_post= $message;


$url1 = 'https://nlapi.expert.ai/v2/analyze/standard/en/entities';

$ch1 = curl_init($url1);
$data1 = array(
    'text' => $text_post
);
$payload1 = json_encode(array("document" => $data1));
curl_setopt($ch1, CURLOPT_POSTFIELDS, $payload1);
curl_setopt($ch1, CURLOPT_HTTPHEADER, array("Authorization: Bearer $access_token", 'Content-Type:application/json'));
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch1);




$http_status = curl_getinfo($ch1, CURLINFO_HTTP_CODE);

// catch error message before closing
if (curl_errno($ch1)) {
   // echo $error_msg = curl_error($ch1);
}

curl_close($ch1);

if($http_status==200){

echo "<div style='color:white;background:green;padding:10px;'>Email Text Entities Successfully Analyzed. See Below</div>";

}
else {
echo "<div style='color:white;background:red;padding:10px;'>There is an Issue Making Entities API Call to ExpertAI. Please Check Internet Connections</div>";
exit();

}   


$json = json_decode($output, true);


echo "<div class='row'>
<div class='col-sm-1'></div> 
<div class='col-sm-10 well'>
<h4> Email Text Entities Analysis</h4>";


foreach($json['data']['entities'] as $row2){

$value = $row2["lemma"];
echo "<span class='cssxv'>$value</span><br><br>";

}
echo "</div>
<div class='col-sm-1'></div> 
</div>";





}
else{
echo "<div id='' style='background:red;color:white;padding:10px;border:none;'>
Direct Page Access not Allowed<br></div>";
}


?>
