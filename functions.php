<?php
require_once('config/config.php');

// Web core class
require_once('classes' . DIRECTORY_SEPARATOR . 'Core.php');


$con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (mysqli_connect_errno($con)) {echo "Failed to connect to MySQL: " . mysqli_connect_error();}
mysqli_set_charset($con, "utf8");

function err($text, $color = 'yellow', $class = '') {
	echo '<div class="err '.$color.' '.$class.'">'.$text.'</div>';
}

function redirect($time = 0, $url = false) {
	echo '<meta http-equiv="refresh" content="'.$time.';'.($url ? 'URL='.$url : NULL).'">';
}

function getCurrentUser() {
	global $login;
	global $con;
	if (isset($login) and $login->isUserLoggedIn() == true and !empty($_SESSION['user_name'])) :
		$row = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM users WHERE user_name = '".$_SESSION['user_name']."'"));
		return $row;
	endif;
	return false;
}
$user = getCurrentUser();
define("CUSER", $user['user_id']);

function isAction($action) {
	if(isset($_GET['veiksmas']) && $_GET['veiksmas'] == $action) return true;
	return false;
}

function page() {
	if(isset($_GET['p'])) return $_GET['p'];
	return 'home';
}

function getAction() {
	if(isset($_GET['action'])) return $_GET['action'];
	return false;
}

function getParam() {
	if(isset($_GET['param'])) return $_GET['param'];
	return false;
}

function isHome() {
	if(!isset($_GET['p'])) return true;
	return false;
}

function doLevelsMatch($level) {
	$id = CUSER;
	global $con;
	if($id) :
		$q = "SELECT user_acctype FROM users WHERE user_id = $id";
		$res = mysqli_query($con, $q);
		if($res) :
			$res = mysqli_fetch_array($res);
			if($res['user_acctype'] == $level) return true;
		endif;
	endif;
	return false;
}

function isSponsor()	{return doLevelsMatch(0);}
function isManager()	{return doLevelsMatch(1);}
function isGridManager()	{return doLevelsMatch(2);}
function isAdmin()	{return doLevelsMatch(3);}


function updateField($table, $field, $post, $wherefield, $wherenum) {
	global $con;
	$q = "UPDATE $table SET $field = '$post' WHERE $wherefield = $wherenum";
	mysqli_query($con, $q);
}

function updateFieldWhere($table, $field, $post, $where) {
	global $con;
	$q = "UPDATE $table SET $field = '$post' WHERE $where";
	mysqli_query($con, $q);
}

function getRow($table, $where) {
	global $con;
	$q = "SELECT * FROM $table WHERE $where";
	$getfi = mysqli_query($con, $q);
	if($getfi) :
		$row = mysqli_fetch_array($getfi);
		if(!empty($row)) :
			return $row;
		endif;
	endif;
	return false;
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function thumb($source, $destination, $new_w = 60, $new_h = 60, $ext) {
	$cropfile=$source;
	$ext = strtolower($ext);
	if($ext == 'jpg' or $ext == 'jpeg') $source_img = @imagecreatefromjpeg($cropfile);
	if($ext == 'png') $source_img = @imagecreatefrompng($cropfile);

	if (!$source_img) {
		echo "could not create image handle";
		exit(0);
	}

	$orig_w = imagesx($source_img);
	$orig_h = imagesy($source_img);

	$w_ratio = ($new_w / $orig_w);
	$h_ratio = ($new_h / $orig_h);
	if ($orig_w > $orig_h ) {//landscape from here new
		$crop_w = round($orig_w * $h_ratio);
		$crop_h = $new_h;
		$src_x = ceil( ( $orig_w - $orig_h ) / 2 );
		$src_y = 0;
	} elseif ($orig_w < $orig_h ) {//portrait
		$crop_h = round($orig_h * $w_ratio);
		$crop_w = $new_w;
		$src_x = 0;
		$src_y = ceil( ( $orig_h - $orig_w ) / 2 );
	} else {//square
		$crop_w = $new_w;
		$crop_h = $new_h;
		$src_x = 0;
		$src_y = 0;
	}

	$dest_img = imagecreatetruecolor($new_w,$new_h);
	imagecopyresampled($dest_img, $source_img, 0 , 0 , $src_x, $src_y, $crop_w, $crop_h, $orig_w, $orig_h); //till here
	if($ext == 'jpg' or $ext == 'jpeg') {
		if(imagejpeg($dest_img, $destination)) {
			imagedestroy($dest_img);
			imagedestroy($source_img);
			return true;
		}
		else {return false;}
	}
	if($ext == 'png') {
		if(imagepng($dest_img, $destination)) {
			imagedestroy($dest_img);
			imagedestroy($source_img);
			return true;
		}
		else {return false;}
	}
	return false;
}

function getRegChildren($region) {
	global $regionsListChildren, $citiesList;
	$regionChildren = array();
	foreach($regionsListChildren[$region] as $children) :
		$regionChildren[$children] = $citiesList[$children];
	endforeach;
	return $regionChildren;
}

function updateUsermeta($options, $user, $table, $flname) {
	global $con;

	if(isset($_POST['updateUsermeta'])) :
		foreach($_POST as $key => $data) :
			if(array_key_exists ($key, $options['fields'])) :
				if($data != '') :
					if($key == 'user_password_hash') : $data = password_hash($data, PASSWORD_DEFAULT, array('cost' => 10)); endif;
					updateField($table, $key, $data, $flname, $user);
					if($key == 'user_city2') :
						include('views/cities.php');
						updateField($table, 'user_city', $data, $flname, $user);
						foreach($regionsListChildren as $keyr => $cities) if(in_array($data, $cities))
						updateField($table, 'user_region', $keyr, $flname, $user);
					endif;
				endif;
			endif;
		endforeach;
		//redirect();
	endif;

	$usermeta = getRow($table, "$flname = '$user'");

	echo '<form action="" method="post" enctype="multipart/form-data">';
		foreach($options['fields'] as $keyn => $fi) :

			if( $keyn == 'user_city2' ) $usermeta['user_city2'] = $usermeta['user_city'];

			if(isset($fi['required']) and $fi['required'] == true) :
				$spanreq = '<span class="reqfield">*</span>';
				$req = 'required';
			else :
				$spanreq = '';
				$req = '';
			endif;

			echo '<label>' . (isset($fi[0]) ? $fi[0] : '') . $spanreq . '</label>';
			if(isset($fi['inputtype']) and $fi['inputtype'] == 'radio') :
				foreach($fi['radios'] as $keyradio => $radioname) :
					echo '<input type ="radio" name="'.$keyn.'" value="'.$keyradio.'" '.(($keyradio == $usermeta[$keyn]) ? 'checked="checked"' : '').' /> '.$radioname.'&nbsp;&nbsp;';
				endforeach;

			elseif(isset($fi['inputtype']) and $fi['inputtype'] == 'number') :
				echo '<input type="number" min="'.$fi['min'].'" max="'.$fi['max'].'" name="'.$keyn.'" value="'.$usermeta[$keyn].'" '.$req.' />';

			elseif(isset($fi['inputtype']) and $fi['inputtype'] == 'drop') :
				echo '<select name="'.$keyn.'">';
					foreach($fi['drops'] as $keyn2 => $drop) :
						echo '<option '.(($keyn2 == $usermeta[$keyn]) ? 'selected="selected"' : '').' value="'.$keyn2.'">'.$drop.'</option>';
					endforeach;
				echo '</select>';

			elseif(isset($fi['inputtype']) and $fi['inputtype'] == 'bool') :
				$checked = ($usermeta[$keyn] == 1 ? 'checked="checked"' : '');

				echo '<input type="hidden" value="0" name="'.$keyn.'">';
				echo '<input '.$checked.' type="checkbox" value="1" name="'.$keyn.'">';

			elseif(isset($fi['inputtype']) and $fi['inputtype'] == 'photo') :
                if(isset($_FILES[$keyn]) && !$_FILES[$keyn]['error'] && !empty($_FILES[$keyn]['tmp_name'])) {
                    // take care of uploads
                    $Core = new Core();
                    $basenameToRemove = pathinfo($usermeta['user_thumb'], PATHINFO_BASENAME);
                    $result = $Core->uploadFile($usermeta['user_id'], $basenameToRemove, ROOT_URL);
                    if(!is_array($result)) {
                        $errExpl = $Core->get('uploadFileWriteToFilesystemErrors');
                        $uperr = $errExpl[$result];
                    }
                    $usermeta = getRow($table, "$flname = '$user'");
                }

                if(isset($usermeta['user_thumb']) && (strlen($usermeta['user_thumb']) > 3)) { // if this is not code, if this is an link to image...
                    echo '<img src="' . $usermeta['user_thumb'] . '" alt="Nuotrauka" onError="this.style.visibility=\'hidden\'" />';
                }

				echo '<input type="file" name="'.$keyn.'" />';
				if(isset($uperr)) : err($uperr, 'red', 'normal'); endif;

			elseif(isset($fi['inputtype']) and $fi['inputtype'] == 'textarea') :
				echo '<textarea name="'.$keyn.'">'.$usermeta[$keyn].'</textarea>';
			elseif(isset($fi['inputtype']) and $fi['inputtype'] == 'pass') :
				echo '<input '.((isset($fi['class'])) ? 'class="'.$fi['class'].'"' : '').' type="text" name="'.$keyn.'" '.((isset($fi['len'])) ? 'maxlength="'.$fi['len'].'"' : '').' value="" />';
			else :
				echo '<input '.((isset($fi['class'])) ? 'class="'.$fi['class'].'"' : '').' type="text" name="'.$keyn.'" '.((isset($fi['len'])) ? 'maxlength="'.$fi['len'].'"' : '').' value="'.$usermeta[$keyn].'" '.$req.' />';
			endif;
			echo '<br>';
		endforeach;

		echo '<label class="noprint"></label><input type="submit" name="updateUsermeta" value="Išsaugoti" />';

	echo '</form>';
}


// Admin functions

function listData($what, $where, $page = false, $advanced = false, $pp = 10) {
	global $con;

	if($page) :
		$page = ($page - 1) * $pp;
		$limit = 'LIMIT '.$page.','.$pp;
	else :
		$limit = '';
	endif;

	$ret = array();
	if($advanced) $q = $advanced." ".$limit;
	else $q = "SELECT * FROM $what WHERE $where $limit";
	$res = mysqli_query($con, $q);
	if($res) while($row = mysqli_fetch_array($res)) $ret[] = $row;
	return $ret;
}

function countData($what, $where, $advanced = false) {
	global $con;
	$ret = array();
	if($advanced) $q = $advanced;
	else $q = "SELECT * FROM $what WHERE $where";
	$res = mysqli_query($con, $q);
	if($res) return mysqli_num_rows($res);
	return 0;
}

function getSort($default, $defaultadv = false) {
	global $getsort, $getorder;
	if(!isset($_GET['rikiuoti']) && !isset($_GET['tvarka']) && $defaultadv) :
		$getsort = $defaultadv;
		$getorder = '';
	else :
		$getsort = (isset($_GET['rikiuoti']) ? $_GET['rikiuoti'] : $default);
		$gettvarka = (isset($_GET['tvarka']) ? $_GET['tvarka'] : '');
		switch($gettvarka) :
			case 'asc' : $getorder = ' ASC'; break;
			case 'desc' : $getorder = ' DESC'; break;
			default : $getorder = ' DESC';
		endswitch;
	endif;
}

function getCurrentLink() {
	return '?p='.page().(isset($_GET['subp']) ? '&subp='.$_GET['subp'] : '').(isset($_GET['rikiuoti']) ? '&rikiuoti='.$_GET['rikiuoti'] : '').(isset($_GET['tvarka']) ? '&tvarka='.$_GET['tvarka'] : '').(psl() ? '&page='.psl(): '');
}

function pageNum() {
	if(isset($_GET['page'])) return $_GET['page'];
	else return 1;
}

function pagination($count, $perpage = 10) {
	if($count > $perpage) :

		if(isset($_POST['smsearch']) or isset($_GET['s'])) : $getsm = '&s='.(!empty($_POST['smsearch']) ? $_POST['smsearch'] : (!empty($_GET['s']) ? $_GET['s'] : NULL));
		else : $getsm = '';
		endif;

		$pages = ceil($count/$perpage);
		$adj = 3;
		$pbl = $pages - 1;

		$pag = '<div class="pagination-div">Puslapis <i><strong>'.pageNum().'</strong></i> iš <i>'.$pages.'</i></div>';
		$pag .= '<ul class="pagination">';


		$parameters = preg_replace("/([?&])page=\w+(&|$)/", "$2", $_SERVER['QUERY_STRING']);
		$parameters = preg_replace('/([?&])s=[^&]+(&|$)/','$2', $parameters);
		$parameters = str_replace('p=puslapis&pageslug=','', $parameters);

		if ($pages < 7 + ($adj * 2))
		{
			for ($i = 1; $i <= $pages; $i++) :
				if($i == pageNum()) $class = ' class="current" '; else $class = '';

				$pag .= '<li '.$class.' style="margin: 5px 2px;"><a href="/index.php?'.$parameters.$getsm.'&page='.$i.'">'.$i.'</a></li>';
			endfor;
		}
		else if ($pages > 5 + ($adj * 2))
		{
			if (pageNum() < 1 + ($adj * 2))
			{
				for ($i = 1; $i < 4 + ($adj * 2); $i++) :
					if($i == pageNum()) $class = ' class="current" '; else $class = '';

					$pag .= '<li '.$class.' style="margin: 5px 2px;"><a href="/index.php?'.$parameters.$getsm.'&page='.$i.'">'.$i.'</a></li>';
				endfor;

				$pag .= '<li style="margin: 5px 2px;" class="dots"><a>...</a></li>';
				$pag .= '<li style="margin: 5px 2px;"><a href="/index.php?'.$parameters.$getsm.'&page='.$pbl.'">'.$pbl.'</a></li>';
				$pag .= '<li style="margin: 5px 2px;"><a href="/index.php?'.$parameters.$getsm.'&page='.$pages.'">'.$pages.'</a></li>';
			}
			else if ($pages - ($adj * 2) > pageNum() && pageNum() > ($adj * 2))
			{
				$pag .= '<li style="margin: 5px 2px;"><a href="/index.php?'.$parameters.$getsm.'&page=1">1</a></li>';
				$pag .= '<li style="margin: 5px 2px;"><a href="/index.php?'.$parameters.$getsm.'&page=2">2</a></li>';
				$pag .= '<li style="margin: 5px 2px;" class="dots"><a>...</a></li>';

				for ($i = pageNum() - $adj; $i <= pageNum() + $adj; $i++) :
					if($i == pageNum()) $class = ' class="current" '; else $class = '';

					$pag .= '<li '.$class.' style="margin: 5px 2px;"><a href="/index.php?'.$parameters.$getsm.'&page='.$i.'">'.$i.'</a></li>';
				endfor;

				$pag .= '<li style="margin: 5px 2px;" class="dots"><a>...</a></li>';
				$pag .= '<li style="margin: 5px 2px;"><a href="/index.php?'.$parameters.$getsm.'&page='.$pbl.'">'.$pbl.'</a></li>';
				$pag .= '<li style="margin: 5px 2px;"><a href="/index.php?'.$parameters.$getsm.'&page='.$pages.'">'.$pages.'</a></li>';
			}
			else
			{
				$pag .= '<li style="margin: 5px 2px;"><a href="/index.php?'.$parameters.$getsm.'&page=1">1</a></li>';
				$pag .= '<li style="margin: 5px 2px;"><a href="/index.php?'.$parameters.$getsm.'&page=2">2</a></li>';
				$pag .= '<li style="margin: 5px 2px;" class="dots"><a>...</a></li>';

				for ($i = $pages - (2 + ($adj * 2)); $i <= $pages; $i++) :
					if($i == pageNum()) $class = ' class="current" '; else $class = '';

					$pag .= '<li '.$class.' style="margin: 5px 2px;"><a href="/index.php?'.$parameters.$getsm.'&page='.$i.'">'.$i.'</a></li>';
				endfor;
			}
		}

		$pag .= '</ul>';

		echo $pag;
	endif;


	return false;
}

function pagination2($count, $perpage = 10, $rew) {
	if($count > $perpage) :

		if(isset($_POST['smsearch']) or isset($_GET['s'])) : $getsm = '&s='.(!empty($_POST['smsearch']) ? $_POST['smsearch'] : (!empty($_GET['s']) ? $_GET['s'] : NULL));
		else : $getsm = '';
		endif;

		$pages = ceil($count/$perpage);
		$adj = 3;
		$pbl = $pages - 1;

		$pag = '<div class="pagination-div">Puslapis <i><strong>'.pageNum().'</strong></i> iš <i>'.$pages.'</i></div>';
		$pag .= '<ul class="pagination">';


		$parameters = preg_replace("/([?&])page=\w+(&|$)/", "$2", $_SERVER['QUERY_STRING']);
		$parameters = preg_replace('/([?&])s=[^&]+(&|$)/','$2', $parameters);

		if ($pages < 7 + ($adj * 2))
		{
			for ($i = 1; $i <= $pages; $i++) :
				if($i == pageNum()) $class = ' class="current" '; else $class = '';

				$pag .= '<li '.$class.' style="margin: 5px 2px;"><a href="/'.$rew.'/'.$i.'">'.$i.'</a></li>';
			endfor;
		}
		else if ($pages > 5 + ($adj * 2))
		{
			if (pageNum() < 1 + ($adj * 2))
			{
				for ($i = 1; $i < 4 + ($adj * 2); $i++) :
					if($i == pageNum()) $class = ' class="current" '; else $class = '';

					$pag .= '<li '.$class.' style="margin: 5px 2px;"><a href="/'.$rew.'/'.$i.'">'.$i.'</a></li>';
				endfor;

				$pag .= '<li style="margin: 5px 2px;" class="dots"><a>...</a></li>';
				$pag .= '<li style="margin: 5px 2px;"><a href="/'.$rew.'/'.$pbl.'">'.$pbl.'</a></li>';
				$pag .= '<li style="margin: 5px 2px;"><a href="/'.$rew.'/'.$pages.'">'.$pages.'</a></li>';
			}
			else if ($pages - ($adj * 2) > pageNum() && pageNum() > ($adj * 2))
			{
				$pag .= '<li style="margin: 5px 2px;"><a href="/'.$rew.'/1">1</a></li>';
				$pag .= '<li style="margin: 5px 2px;"><a href="/'.$rew.'/2">2</a></li>';
				$pag .= '<li style="margin: 5px 2px;" class="dots"><a>...</a></li>';

				for ($i = pageNum() - $adj; $i <= pageNum() + $adj; $i++) :
					if($i == pageNum()) $class = ' class="current" '; else $class = '';

					$pag .= '<li '.$class.' style="margin: 5px 2px;"><a href="/'.$rew.'/'.$i.'">'.$i.'</a></li>';
				endfor;

				$pag .= '<li style="margin: 5px 2px;" class="dots"><a>...</a></li>';
				$pag .= '<li style="margin: 5px 2px;"><a href="/'.$rew.'/'.$pbl.'">'.$pbl.'</a></li>';
				$pag .= '<li style="margin: 5px 2px;"><a href="/'.$rew.'/'.$pages.'">'.$pages.'</a></li>';
			}
			else
			{
				$pag .= '<li style="margin: 5px 2px;"><a href="/'.$rew.'/1">1</a></li>';
				$pag .= '<li style="margin: 5px 2px;"><a href="/'.$rew.'/2">2</a></li>';
				$pag .= '<li style="margin: 5px 2px;" class="dots"><a>...</a></li>';

				for ($i = $pages - (2 + ($adj * 2)); $i <= $pages; $i++) :
					if($i == pageNum()) $class = ' class="current" '; else $class = '';

					$pag .= '<li '.$class.' style="margin: 5px 2px;"><a href="/'.$rew.'/'.$i.'">'.$i.'</a></li>';
				endfor;
			}
		}

		$pag .= '</ul>';

		echo $pag;
	endif;


	return false;
}

function prettyslug($url) {
   $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
   $url = trim($url, "-");
   $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
   $url = strtolower($url);
   $url = preg_replace('~[^-a-z0-9_]+~', '', $url);
   return $url;
}

function getThumbnail($table, $userid) {
	$row = getRow($table, "user_id = $userid");
	echo '<div class="sitethumb"';
	if($row and strlen($row['user_thumb']) > 5) echo ' style="background-image: url('."'".$row['user_thumb']."'".')"';
	echo '></div>';
}

function getField($table, $field, $idfield, $id) {
	global $con;
	$q = "SELECT $field FROM $table WHERE $idfield = $id";
	$getfi = mysqli_query($con, $q);
	if($getfi) $row = mysqli_fetch_array($getfi);
	if(!empty($row)) :
		if(isset($row[$field])) return $row[$field];
	endif;
	return false;
}

function getManagerParent($user) {
	$acctype = getField('users', 'user_acctype', 'user_id', $user);
	if($acctype == 1) :
		$parent = getField('users', 'user_parent', 'user_id', $user);
		if($parent) : return $parent; endif;
	elseif($acctype == 2 or $acctype == 3) : return $user;
	endif;
	return 0;
}

// Mail

function myMail($to, $subject, $message, $from = 'noreply@aukokdaiktus.lt', $fromName = 'aukokdaiktus.lt') {

    $mail = new PHPMailer;

    // please look into the config/config.php for much more info on how to use this!
    // use SMTP or use mail()
    if (EMAIL_USE_SMTP) {
        // Set mailer to use SMTP
        $mail->IsSMTP();
        //useful for debugging, shows full SMTP errors
        //$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
        // Enable SMTP authentication
        $mail->SMTPAuth = EMAIL_SMTP_AUTH;
        // Enable encryption, usually SSL/TLS
        if (defined(EMAIL_SMTP_ENCRYPTION)) {
            $mail->SMTPSecure = EMAIL_SMTP_ENCRYPTION;
        }
        // Specify host server
        $mail->Host = EMAIL_SMTP_HOST;
        $mail->Username = EMAIL_SMTP_USERNAME;
        $mail->Password = EMAIL_SMTP_PASSWORD;
        $mail->Port = EMAIL_SMTP_PORT;
    } else {
        $mail->IsMail();
    }

    $mail->From = $from;
    $mail->FromName = $fromName;
    $mail->AddAddress($to);
    $mail->Subject = $subject;

    // Sender - Must be set, because it is required as security flag or so..
    $mail->set('Sender', $from);

    // Encoding
    $mail->set('CharSet', CHARSET);

    // the link to your register.php, please set this value in config/email_verification.php
    $mail->Body = $message;

    if(!$mail->Send()) {
        //$this->errors[] = 'Mail not sent' . $mail->ErrorInfo;
        return false;
    } else {
        return true;
    }
}

?>
