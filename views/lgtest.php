<?php
// @todo: remove this errors part...
if(isset($_SERVER['APP_ENV']) && (strtolower($_SERVER['APP_ENV']) == 'dev')) {
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors','on');
} else {
    error_reporting(0);
    ini_set('display_errors',false);
}

require ROOT_PATH . "libraries" . DIRECTORY_SEPARATOR . "facebook" . DIRECTORY_SEPARATOR . "facebook.php";
$facebook = new Facebook(array(
	'appId'  => '1465543123659209',
	'secret' => '24983a3c450565771723dc19486b9edc',
      'cookie' => true,
));

// See if there is a user from a cookie
$user = $facebook->getUser();
//var_dump($user);
if ($user) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me');
$logoutUrl = $facebook->getLogoutUrl();
  } catch (FacebookApiException $e) {
    echo '<pre>'.htmlspecialchars(print_r($e, true)).'</pre>';
    $user = null;
$loginUrl = $facebook->getLoginUrl(array(
   'next'=>'http://aukoklaika.lt/views/lgtest.php'
));
  }
}
$loginUrl = $facebook->getLoginUrl(array(
   'next'=>'http://aukoklaika.lt/views/lgtest.php'
));
?>

<?php if ($user) { ?>
    Your user profile is
    <?php print htmlspecialchars(print_r($user_profile, true)) ?>
    <a href="<?php echo $logoutUrl; ?>">Logout</a>
<?php } else { ?>
    <a href="<?php echo $loginUrl; ?>">Login with Facebook</a>
<?php } ?>