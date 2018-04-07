<?php

//20180407 SPC
//super quick and dirty - add cards to specific restyaboard from txt file
//works with restyaboard base url: /v1 , api version: 1.0.0 

set_time_limit(180);			
//Change below settings to specific instances
$host = "http://192.168.0.15";	
$board_id = 5;				
$list_id = 225;	
$user = "user";
$pass = "restya";
$textfile = "textfile.txt";	//add path		

//get guest access token
$url1 = $host."/api/v1/oauth.json";
$response1 = json_decode(CallAPI("GET", $url1), TRUE);
$token1 =  $response1['access_token'];

//login using guest access token + user + pass
$url2 = $host."/api/v1/users/login.json?token=".$token1;
$data2 = json_encode(array(
		'email' => $user,		
		'password' => $pass	
	));
$response2 = json_decode(CallAPI("POST", $url2, $data2), TRUE);
$token2 = $response2['access_token'];

//using access token with write authority, 
//for each row in file, add card to board

$url3 = $host."/api/v1/boards/5/lists/227/cards.json?token=".$token2;
$file = new SPLFileObject($textfile);
$count = 0;

foreach($file as $line) {
	
	$data3 = json_encode(array(
            'board_id' => $board_id, 	
            'list_id' => $list_id, 	
            'name' => $line,
			'position' => 0
        ));
    $response3 = json_decode(CallAPI("POST", $url3, $data3), TRUE);
	$count++;
}

//assuming all were added successfully - TODO: check actual response 
echo "Added ".$count." cards.";


//CURL CALL API	
function CallAPI($method, $url, $data = false)
{
	
    $curl = curl_init($url);

    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
			curl_setopt( $curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default: //get
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }


    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}


?>