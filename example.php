<?php

require("JWT.php");

define('TOKEN_SECRET', 'AsdfasdfASFD@#45');

$payload = array(
	'user_id' => 123
);

// Creating the token string
$token = JWT::makeToken($payload, TOKEN_SECRET);
$token->setExpiration(0.001); // about 4 seconds
$jwt = $token->build();

echo "<h2>Token String is: </h2>";
echo "<pre>$jwt</pre>";
echo "<hr>";

// Parsing the token string
$token = JWT::loadToken($jwt, TOKEN_SECRET) or die("Token is not valid: $jwt");
$user_id = $token->getValue('user_id');
		
echo "<h2>User ID is: </h2>";
echo "<pre>$user_id</pre>";
echo "<hr>";

echo "<h2>Expiration Test: </h2>";
echo $token->isExpired() ? "Token is expired." : "Token is NOT expired yet.";
echo "<hr>";
sleep(4); // Token expires in 4 seconds
echo $token->isExpired() ? "Token is expired." : "Token is NOT expired yet.";
echo "<hr>";