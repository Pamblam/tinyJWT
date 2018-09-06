<?php
/**
 * JSON Web Tokens (RFC 7519)
 * The smallest, simplest possible JWT class.
 * https://github.com/Pamblam/tinyJWT
 * WTFPL
 */

class JWT{
	
	/**
	 * A payload array
	 * @var array
	 */
	private $payload;
	
	/**
	 * The secret used for encryption
	 * @var string 
	 */
	private $secret;
	
	/***************************************************************************
	 *** Constructors
	 **************************************************************************/
	
	/**
	 * Make a new token
	 * @param assoc array $payload
	 * @param string $secret
	 * @return \JWT
	 */
	public static function makeToken($payload, $secret){
		return new JWT($payload, $secret);
	}
	
	/**
	 * Get a JWT instance from a jwt string (or False if not a valid token)
	 * @param string $token
	 * @param string $secret
	 * @return \JWT|boolean
	 */
	public static function loadToken($token, $secret){
		if(!self::isTokenValid($token, $secret)) return false;
		$tokenParts = explode('.', $token);
		if(count($tokenParts) !== 3) return false;
		$payload = self::decode($tokenParts[1]);
		return new JWT($payload, $secret);
	}
	
	/***************************************************************************
	 *** Methods
	 **************************************************************************/
	
	/**
	 * Generate the token string 
	 * @return string
	 */
	public function build(){
		$header = self::encode(array('typ' => 'JWT', 'alg' => 'HS256'));
		$payload = self::encode($this->payload);
		$signature = self::getSignature($payload, $this->secret);
		return $header.".".$payload.".".$signature;
	}
	
	/**
	 * Set the number of hours until this ticket expires
	 * @param number $hours
	 * @return \JWT
	 */
	public function setExpiration($hours){
		$expIn = 60 * 60 * $hours;
		$this->setValue('expire_ts', time()+$expIn);
		return $this;
	}
	
	/**
	 * Determine if the current token has expired
	 * @return boolean
	 */
	public function isExpired(){
		$exp_ts = $this->getValue('expire_ts');
		if($exp_ts === null) return false;
		return $exp_ts <= time();
	}
	
	/**
	 * Get the entire payload array from the JWT instance
	 * @return array
	 */
	public function getPayload(){
		return $this->payload;
	}
	
	/**
	 * Get the value of a specific payload property or null if not defined
	 * @param string $prop
	 * @return mixed|null
	 */
	public function getValue($prop){
		return isset($this->payload[$prop]) ? $this->payload[$prop] : null;
	}
	
	/**
	 * Set the value of a specific payload property
	 * @param string $prop
	 * @param mixed $value
	 * @return \JWT
	 */
	public function setValue($prop, $value){
		$this->payload[$prop] = $value;
		return $this;
	}
	
	/**
	 * Delete the value of a specific payload property
	 * @param string $prop
	 * @return \JWT
	 */
	public function deleteValue($prop){
		unset($this->payload[$prop]);
		return $this;
	}
	
	/***************************************************************************
	 *** Helpers
	 **************************************************************************/
	
	/**
	 * Is the $token valid?
	 * @param string $token
	 * @param string $secret
	 * @return boolean
	 */
	public static function isTokenValid($token, $secret){
		$tokenParts = explode('.', $token);
		if(count($tokenParts) !== 3) return false;
		$signature = $tokenParts[2];
		if(!hash_equals($signature, self::getSignature($tokenParts[1], $secret))) return false;
		return true;
	}
	
	/***************************************************************************
	 *** Private methods
	 **************************************************************************/
	
	private function __construct($payload, $secret){
		$this->payload = $payload;
		$this->secret = $secret;
	}
	
	private static function getSignature($payload, $secret){
		$header = self::encode(array('typ' => 'JWT', 'alg' => 'HS256'));
		$signature = self::encrypt($header.".".$payload, $secret);
		return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($signature));
	}
	
	private static function encode($value){
		$value = json_encode($value);
		$value = base64_encode($value);
		return str_replace(array('+', '/', '='), array('-', '_', ''), $value);
	}
	
	private static function decode($value){
		$value = str_replace(array('-', '_', ''), array('+', '/', '='), $value);
		$value = base64_decode($value);
		return json_decode($value, true);
	}
	
	private static function encrypt($string, $secret){
		$secret_iv = md5($secret);
		$output = false;
		$encrypt_method = "AES-256-CBC";
		$key = hash('sha256', $secret);
		$iv = substr(hash('sha256', $secret_iv ), 0, 16);
		return base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
	}
	
	// Not used, here for posterity
	private static function decrypt($string, $secret){
		$secret_iv = md5($secret);
		$output = false;
		$encrypt_method = "AES-256-CBC";
		$key = hash('sha256', $secret);
		$iv = substr(hash('sha256', $secret_iv), 0, 16);
		return openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
	}
	
}
