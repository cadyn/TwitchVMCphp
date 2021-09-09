<?php
require __DIR__ . '/vendor/autoload.php';
$configs = include('config.php');

$helixGuzzleClient = new \TwitchApi\HelixGuzzleClient($configs['CLIENT_ID']);
$twitchApi = new \TwitchApi\TwitchApi($helixGuzzleClient, $configs['CLIENT_ID'], $configs['CLIENT_SECRET']);
$oauth = $twitchApi->getOauthApi();

$user_refresh_token = $_GET['refresh_token'];

try {
    $token = $oauth->refreshToken($user_refresh_token, $twitch_scopes ?? '');
    $data = json_decode($token->getBody()->getContents());

    header('Content-type:application/json');
    echo json_encode($data);
} catch (Exception $e) {
    echo $e;
}

?>