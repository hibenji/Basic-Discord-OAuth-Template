<?php
session_start();

$config = include('config.php');
$OAUTH2_CLIENT_ID = $config["OAUTH2_CLIENT_ID"];
$OAUTH2_CLIENT_SECRET = $config["OAUTH2_CLIENT_SECRET"];
$redirect_uri = $config["redirect_uri"];

define('OAUTH2_CLIENT_ID', $OAUTH2_CLIENT_ID);
define('OAUTH2_CLIENT_SECRET', $OAUTH2_CLIENT_SECRET);
define('REDIRECT_URI', $redirect_uri);

$authorizeURL = 'https://discord.com/api/oauth2/authorize';
$tokenURL = 'https://discord.com/api/oauth2/token';
$apiURLBase = 'https://discord.com/api/users/@me';
$revokeURL = 'https://discord.com/api/oauth2/token/revoke';

if(get('action') == 'logout') {
    logout($revokeURL, array(
        'token' => session('access_token'),
        'token_type_hint' => 'access_token',
        'client_id' => OAUTH2_CLIENT_ID,
        'client_secret' => OAUTH2_CLIENT_SECRET,
      ));
    unset($_SESSION['access_token']);
    header('Location: ' . $_SERVER['PHP_SELF']);
    die();
}

// Start the login process by sending the user to Discord's authorization page
if(get('action') == 'login') {

  $params = array(
    'client_id' => OAUTH2_CLIENT_ID,
    'redirect_uri' => REDIRECT_URI,
    'response_type' => 'code',
    'scope' => 'identify email'
  );
  // Redirect the user to Discord's authorization page
  header('Location: https://discord.com/api/oauth2/authorize' . '?' . http_build_query($params));
  die();
}

if(get('code')) {
  $token = apiRequest($tokenURL, array(
    "grant_type" => "authorization_code",
    'client_id' => OAUTH2_CLIENT_ID,
    'client_secret' => OAUTH2_CLIENT_SECRET,
    'redirect_uri' => REDIRECT_URI,
    'code' => get('code')
  ));
  $logout_token = $token->access_token;
  $_SESSION['access_token'] = $token->access_token;
  header('Location: ' . $_SERVER['PHP_SELF']);
}


if(get('action') == 'logout') {
    $params = array(
      'access_token' => $logout_token
    );
    header('Location: https://discord.com/api/oauth2/token/revoke' . '?' . http_build_query($params));
    die();
  }
  
  function apiRequest($url, $post=FALSE, $headers=array()) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($ch);

    if($post)
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

    $headers[] = 'Accept: application/json';
  
    if(session('access_token'))
      $headers[] = 'Authorization: Bearer ' . session('access_token');
  
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  
    $response = curl_exec($ch);
    return json_decode($response);
  }
  
  function logout($url, $data=array()) {
      $ch = curl_init($url);
      curl_setopt_array($ch, array(
          CURLOPT_POST => TRUE,
          CURLOPT_RETURNTRANSFER => TRUE,
          CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
          CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
          CURLOPT_POSTFIELDS => http_build_query($data),
      ));
      $response = curl_exec($ch);
      return json_decode($response);
  }
  
  function get($key, $default=NULL) {
    return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
  }
  
  function session($key, $default=NULL) {
    return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
  }

?>