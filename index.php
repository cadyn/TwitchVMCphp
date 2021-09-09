<?php
require __DIR__ . '/vendor/autoload.php';
$configs = include('config.php');

$helixGuzzleClient = new \TwitchApi\HelixGuzzleClient($configs['CLIENT_ID']);
$twitchApi = new \TwitchApi\TwitchApi($helixGuzzleClient, $configs['CLIENT_ID'], $configs['CLIENT_SECRET']);
$oauth = $twitchApi->getOauthApi();

// Get the code from URI
$code = $_GET['code'];

// Get the current URL, we'll use this to redirect them back to exactly where they came from
$currentUri = explode('?', 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])[0];

if ($code == '') {
    // Generate the Oauth Uri
    try{
        $oauthUri = $oauth->getAuthUrl($currentUri, 'code', $configs['TWITCH_SCOPES']);
    } catch (Exception $e) {
        echo $e;
    }
    // Redirect them as there was no auth code
    header("Location: {$oauthUri}");
} else {
    try {
        $token = $oauth->getUserAccessToken($code, $currentUri);
        // It is a good practice to check the status code when they've responded, this really is optional though
        if ($token->getStatusCode() == 200) {
            // Below is the returned token data
            $data = json_decode($token->getBody()->getContents());

            // Your bearer token
            $twitch_access_token = $data->access_token ?? null;
            $twitch_refresh_token = $data->refresh_token ?? null;
            //echo $twitch_access_token.'<br>'.$twitch_refresh_token;
            header('Content-type:application/json');
            echo json_encode($data);
            //echo $twitch_refresh_token;
        } else {
            echo $token;
        }
    } catch (Exception $e) {
        echo $e;
    }
}
?>