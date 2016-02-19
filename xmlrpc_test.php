<?php

// static vars
define('SERVHOST', 'localhost');
define('SERVPORT', 80);
define('TESTUSER', 'ca_service');
define('TESTPASS', 'password');

function call_service($host, $port, $request, $http_headers) {

  $fp = fsockopen($host, $port, $errno, $errstr);

  if (!fputs($fp, $http_headers, strlen($http_headers))) {
    $errstr = 'Write error';
    return 0;
  }

  // separate the headers from the content
  // loop until the end of the header
  do {
			$ret_header .= fgets ( $fp, 128 );
	} while ( strpos ( $ret_header, "\r\n\r\n" ) === false );

  // loop until the end of the contents
  $contents = '';
  while (!feof($fp)) {
    $contents .= fgets($fp);
  }

  fclose($fp);

  return array($ret_header, $contents);

}

// return an http header string
// e.g - return "POST /drupal/services/ca HTTP/1.0\nUser_Agent: My client\nHost: " . $host . "\nContent-Type: text/xml\nCookie: " . $session_cookie . "\nContent-Length: " . strlen($request) . "\n\n" . $request ."\n";
function get_header_str($url, $host, $request, $session_cookie = false) {
  $header_str = "POST /drupal-7-sandbox/services HTTP/1.0\n";
  $header_str .= "User_Agent: My client\nHost: " . $host;
  $header_str .= "\nContent-Type: text/xml\n";
  if ($session_cookie) {
    $header_str .= "Cookie: " . $session_cookie . "\n";
  }
  $header_str .= "Content-Length: " . strlen($request) . "\n\n" . $request ."\n";

  return $header_str;
}

///////////////////////////////////////////////////////
// try login

echo '<p>Try login</p>';

// get xmlrpc formated request
$request = xmlrpc_encode_request('user.login', array(TESTUSER, TESTPASS));
$http_headers = get_header_str(SERVHOST, SERVPORT, $request);

// call the service
$response = call_service(SERVHOST, SERVPORT, $request, $http_headers);

$xml = new SimpleXMLElement($response[1]);

// extract the session name and id
$s_name = $xml->params->param->value->struct->member[1]->value->string;
$s_id = $xml->params->param->value->struct->member[0]->value->string;
$session_cookie = $s_name .'='. $s_id;

print_r($response);
// echo 'cookie is : ' . $session_cookie . '<br /><br />';

///////////////////////////////////////////////////////
// try call method (using authentication)

echo '<p>Try sending data</p>';

$request = xmlrpc_encode_request('ca.importData', 'blah blah blah');

$http_headers = get_header_str(SERVHOST, SERVPORT, $request, $session_cookie);

$response = call_service(SERVHOST, SERVPORT, $request, $http_headers);

echo $response[0] . '<br />';
echo $response[1] . '<br /><br />';

///////////////////////////////////////////////////////
// try logout

echo '<p>Try logout</p>';

$request = xmlrpc_encode_request('user.logout', true);

$http_headers = get_header_str(SERVHOST, SERVPORT, $request, $session_cookie);

$response = call_service(SERVHOST, SERVPORT, $request, $http_headers);
echo $response[0] . '<br />';

///////////////////////////////////////////////////////
#// try call method (using authentication) after logout

echo '<p>Try send data after logout</p>';

$request = xmlrpc_encode_request('ca.importData', '4 5 6');

$http_headers = get_header_str(SERVHOST, SERVPORT, $request, $session_cookie);

$response = call_service(SERVHOST, SERVPORT, $request, $http_headers);

echo $response[0] . '<br />';
echo $response[1];
