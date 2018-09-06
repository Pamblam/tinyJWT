
# tinyJWT - [RFC 7519](https://tools.ietf.org/html/rfc7519)

The smallest, simplest possible [JSON Web Token](https://jwt.io/) class. (> 200 lines of PHP). Perfectly safe to use as is or use it as a starting point for your own implementation. IDGAF.

This class uses the `HS256` algo. For simplicity, this is the only one available.

### Examples
There are more examples in the [example.php](https://github.com/Pamblam/tinyJWT/blob/master/example.php) file.

**Create a new token**
```php
define('TOKEN_SECRET', 'farts!@#'); // Password for the token
$payload = array('uid'=>123); // Array containing anything you want
$JWT = JWT::makeToken($payload, TOKEN_SECRET);
$token = $JWT->build(); // Build the token string
```
**Set a token's expiration time (in hours)**
```php
$JWT->setExpiration(6); // Set it to expire in 6 hours
$token = $JWT->build(); // Rebuild the token string
```
**Parse and validate a token**
```php
$JWT = JWT::loadToken($token, TOKEN_SECRET);
if($JWT === false){
    // Token is not valid
}else if($JWT->isExpired()){
    // Token has expired
}
```
**Add (or set) a value to the token's payload**
```php
$JWT->setValue('Favorite Color', 'Green');
$token = $JWT->build(); // Rebuild token string
```
**Get a value of the token's payload**
```php
$color = $JWT->getValue('Favorite Color'); // Green
```
**Get the entire payload array**
```php
$payload = $JWT->getPayload();
```
**Delete a payload item**
```php
$JWT->deleteValue('Favorite Color');
$token = $JWT->build(); // Rebuild token string
```

