<?php

require("JWT.php");

echo "<h3>Create a new token</h3>";
define('TOKEN_SECRET', 'farts!@#');
$payload = array('uid'=>123);
$JWT = JWT::makeToken($payload, TOKEN_SECRET);
$token = $JWT->build();
echo "<textarea>$token</textarea><br>";

echo "<h3>Set a token's expiration time (in hours)</h3>";
$token = $JWT->setExpiration(6)->build();
echo "<textarea>$token</textarea><br>";

echo "<h3>Parse and validate a token</h3>";
$JWT = JWT::loadToken($token, TOKEN_SECRET) or die('Token is not valid.');
if($JWT->isExpired()) die('token has expired');
echo "Token is valid<br>";

echo "<h3>Add (or set) a value to the token's payload</h3>";
$token = $JWT->setValue('Favorite Color', 'Green')->build(); // Rebuild token string
echo "<textarea>$token</textarea><br>";

echo "<h3>Get a value of the token's payload</h3>";
$color = $JWT->getValue('Favorite Color'); // Green
echo "$color<br>";

echo "<h3>Get the entire payload array</h3>";
$payload = $JWT->getPayload();
echo "<pre>"; print_r($payload); echo "</pre>";

echo "<h3>Delete a payload item</h3>";
$token = $JWT->deleteValue('Favorite Color');
$payload = $JWT->getPayload();
echo "<pre>"; print_r($payload); echo "</pre>";