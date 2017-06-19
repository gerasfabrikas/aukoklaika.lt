<?php

$subcatslug = false;
$catslug = (isset($_GET['catslug']) ? $_GET['catslug'] : false);
if(stripos($catslug, '/') !== false) :
	$catsluge = explode('/', $catslug);
	$catslug = $catsluge[0];
	$subcatslug = $catsluge[1];
endif;
$site = 3;
$site2 = 1;

$stokoj = 'Pagalbos gavėjas'; $img1 = 'nt_st.png'; $img2 = 'nt_ku.png'; $img3 = 'nt_po.png'; $padovanota = 'Padėta'; $dovanoti = 'Padėti';
$note = '** Užsiregistravę sistemoje turėsite savo asmeninį profilį su atliktų darbų sąrašu, o padėti internetu bus paprasčiau.';

echo '<div class="page"><div class="page-con">';
if(isset($_GET['needid'])) :

	$need_id = $_GET['needid'];
	$pagerow = getRow('needs', "need_id = $need_id AND need_type = $site2 AND deleted = 0");


	if(!isset($pagerow['deleted']) or $pagerow['deleted'] == 1) {err('Šis poreikis panaikintas</div></div>'); return;}

	// New need description
	$needyrow = getRow('needy', "user_id = ".$pagerow['need_needy']);

	echo '<div class="ku_img ku_lg">';
	echo '<img src="http://aukoklaika.lt/img/'.$img3.'" alt="nothumb" />';
	echo '</div><div class="ku_desc">';
	echo '<h2>'.$pagerow['need_name'].'</h2>';
	$catrow = getRow('cats', "cat_id = ".$pagerow['need_cat']);
	echo '<div class="cat"><b>Kategorija</b> <a href="/?fc='.$pagerow['need_cat'].'&fci=&fs=&s=">'.$catrow['cat_name'].'</a></div>';

	foreach($regionsListChildren as $key => $cities) if(in_array($needyrow['user_city'], $cities)) {echo '<a href="/?fc=&fci=100'.$key.'&fs=&s=">'.$regionsList[$key].'</a> &gt; '; $rgn = $key;}
	if(isset($rgn)) echo '<a href="/?fc=&fci='.$needyrow['user_city'].'&fs=&s=">'.$citiesList[$needyrow['user_city']].'</a>';
	echo '</div>';

	echo '<br><br><div class="desc">'.$pagerow['need_desc'].'</div><br><br>';
	echo '<div class="persons_left">';
		echo '<h2>'.$stokoj.'</h2><br>';
		if(strlen($needyrow['user_thumb']) > 3) getThumbnail('needy', $needyrow['user_id']);
		else echo '<img src="http://aukoklaika.lt/img/'.$img1.'" alt="nothumb" />';
		echo '<div class="thumb_desc">';
		echo '<a href="/stokojantysis/'.$needyrow['user_id'].'">'.$needyrow['user_fname'].' '.$needyrow['user_lname'].' '.$needyrow['user_orgname'].'</a>'.'<br>';
		$catNrow = getRow('cats', "cat_id = ".$needyrow['user_cat']);
		echo '<i>'.$catNrow['cat_name'].'</i>';
		echo '</div>';
	echo '</div><div class="persons_right">';
	$parentrow = getRow('users', "user_id = ".$needyrow['user_parent']);
		echo '<h2>Kuratorius</h2><br>';
		$pagerow2 = getRow('users', "user_id = ".$needyrow['user_parent']);
		echo '<a href="/kuratorius/'.$needyrow['user_parent'].'">';
		if(strlen($pagerow2['user_thumb']) > 3) getThumbnail('users', $pagerow2['user_id']);
		else echo '<img src="http://aukoklaika.lt/img/'.$img2.'" alt="nothumb" />';
		echo '<div class="thumb_desc">';
		echo '<a href="/kuratorius/'.$needyrow['user_parent'].'">'.$parentrow['user_fname'].' '.$parentrow['user_lname'].'</a>';
		echo '</div>';
	echo '</div>';

	echo '</div><div class="page-sid">';

	if($pagerow['need_full'] == 1) {err('Šis poreikis jau patenkintas'); echo '</div></div>'; return;}
	elseif(strtotime($pagerow['need_expires']) < strtotime(date('now'))) {err('Pasibaigė šio poreikio galiojimo laikas'); echo '</div></div>'; return;}

	echo '<div class="sid-back contact-form">';



	echo '<h2>'.$dovanoti.'</h2>';

	if($login->isUserLoggedIn() == false) :

		$regsuc = false;

		if(isset($_POST['make_user'])) :
			$registration = new Registration();

			// show negative messages
			if ($registration->errors) {
				foreach ($registration->errors as $error) {
					 err($error, 'red');
				}
			}

			// show positive messages
			if ($registration->messages) {
				foreach ($registration->messages as $message) {
					echo err($message);
				}
			}

			if($registration->reg_id > 0) :
				updateFieldWhere('needs', 'need_full_user', $registration->reg_id, 'need_id = '.$need_id);
				$regsuc = true;
			endif;

		elseif(isset($_POST['register'])):
			$user_fname = (isset($_POST['user_fname']) ? mysqli_real_escape_string($con, $_POST['user_fname']) : '');
			$user_lname = (isset($_POST['user_lname']) ? mysqli_real_escape_string($con, $_POST['user_lname']) : '');
			$user_address = (isset($_POST['user_address']) ? mysqli_real_escape_string($con, $_POST['user_address']) : '');
			$user_region = (isset($_POST['user_region']) ? mysqli_real_escape_string($con, $_POST['user_region']) : 0);
			$user_city = (isset($_POST['user_city']) ? mysqli_real_escape_string($con, $_POST['user_city']) : 0);
			$user_phone = (isset($_POST['user_phone']) ? mysqli_real_escape_string($con, $_POST['user_phone']) : '');
			$user_email = (isset($_POST['user_email']) ? mysqli_real_escape_string($con, $_POST['user_email']) : '');
			$user_subscribed = (isset($_POST['user_subscribed']) ? mysqli_real_escape_string($con, $_POST['user_subscribed']) : 0);

			if($user_fname == '' or $user_lname == '' or $user_address == '' or $user_phone == '' or $user_email == '') :
				err('Užpildykite visus laukelius');
			elseif(!isset($_POST['ag']) or $_POST['ag'] != '1') :
				err('Būtina sutikti su taisyklėmis');
			elseif(strtolower($_POST['captcha']) != strtolower($_SESSION['captcha']))  :
				err('Apsaugos nuo robotų klaida');
			else :
				$q = "UPDATE needs SET
				user_fname='$user_fname',
				user_lname='$user_lname',
				user_address='$user_address',
				user_region=$user_region,
				user_city=$user_city,
				user_phone='$user_phone',
				user_email='$user_email',
				user_subscribed=$user_subscribed
				WHERE need_id = $need_id";
				mysqli_query($con, $q);
				$regsuc = true;
			endif;
		endif;

		if($regsuc) :
			if (!$_FILES['need_full_photo'] || empty($_FILES['need_full_photo']['name'])) :
			elseif ($_FILES['need_full_photo']["size"] == 0) :
			elseif ($_FILES['need_full_photo']["size"] > 5*1024*1024) :
			elseif (($_FILES['need_full_photo']["type"] != "image/pjpeg") AND ($_FILES['need_full_photo']["type"] != "image/jpeg") AND ($_FILES['need_full_photo']["type"] != "image/png")) :
			elseif (!is_uploaded_file($_FILES['need_full_photo']["tmp_name"])) :
			else :
                $upfilename = date('YmdHis') . str_replace('.', '_', uniqid('_', true)) . '.' . pathinfo($_FILES["need_full_photo"]["name"], PATHINFO_EXTENSION);
				$move = @move_uploaded_file($_FILES['need_full_photo']['tmp_name'], (ROOT_PATH . 'uploads' . DIRECTORY_SEPARATOR . 'needs' . DIRECTORY_SEPARATOR . $upfilename));
				if($move) :
					$url = ROOT_URL . 'uploads/needs/' . $upfilename;
					updateFieldWhere('needs', 'need_full_photo', $url, 'need_id = '.$need_id);
				endif;
			endif;
			$need_full_desc = (isset($_POST['need_full_desc']) ? mysqli_real_escape_string($con, $_POST['need_full_desc']) : '');
			updateFieldWhere('needs', 'need_full', '1', 'need_id = '.$need_id);
			updateFieldWhere('needs', 'need_full_desc', $need_full_desc, 'need_id = '.$need_id);
			updateFieldWhere('needs', 'need_fulldate', date('Y-m-d H:i:s'), 'need_id = '.$need_id);
			err($padovanota); echo '</div></div></div>';
			return;
		endif;

	?>
		<form method="post" action="" name="registerform" enctype="multipart/form-data">
			<p>Užpildykite būtinus pildyti (*) laukus ir kuratorius susisieks su Jumis.</p>
			<label for="need_full_desc">Aprašymas</label>
			<textarea id="need_full_desc" type="text" maxlength="400" name="need_full_desc"></textarea>
			<br>
			<label for="need_full_photo">Nuotrauka</label>
			<input type="file" name="need_full_photo" id="need_full_photo" />
			<br><br>
			<label for="user_fname">Vardas<span class="reqfield">*</span></label>
			<input id="user_fname" type="text" maxlength="32" name="user_fname" required />
			<br>
			<label for="user_lname">Pavardė<span class="reqfield">*</span></label>
			<input id="user_lname" type="text" maxlength="32" name="user_lname" required />
			<br>
			<br>
			<label for="user_address">Adresas<span class="reqfield">*</span></label>
			<input id="user_address" type="text" maxlength="256" name="user_address" required />
			<br>
			<label for="user_region">Apskritis<span class="reqfield">*</span></label>
			<select class="slickSelect" id="user_region" name="user_region">
				<?php foreach($regionsList as $key => $city) echo '<option value="'.$key.'">'.$city.'</option>'; ?>
			</select>
			<br>
			<label for="user_city">Savivaldybė<span class="reqfield">*</span></label>
			<select class="slickSelect" id="user_city" name="user_city"></select>
			<br>
			<br>
			<label for="user_phone">Telefonas<span class="reqfield">*</span></label>
			<input id="user_phone" type="text" maxlength="11" name="user_phone" value="370" required />
			<br>
			<label for="user_email">El. paštas<span class="reqfield">*</span></label>
			<input id="user_email" type="email" name="user_email" required />
			<br>
			<br>
			<label for="user_subscribed">Gauti naujienas</label>
			<input id="user_subscribed" type="checkbox" name="user_subscribed" value="1" />

			<input type="hidden" name="user_person" value="0" />
			<input type="hidden" name="user_orgname" value="" />
			<input type="hidden" name="user_legalstatus" value="" />
			<input type="hidden" name="user_code1" value="" />
			<input type="hidden" name="user_code2" value="" />
			<input type="hidden" name="user_reg" value="" />
			<br>
			<br>
			<label>Sukurti profilį**</label>
			<input id="make_user" type="checkbox" name="make_user" value="0" />
			<br>
			<label for="user_name">Paskyros vardas</label>
			<input id="user_name" type="text" pattern="[a-zA-Z0-9]{2,64}" name="user_name" />
			<br>
			<label for="user_password_new">Slaptažodis</label>
			<input id="user_password_new" type="password" name="user_password_new" pattern=".{6,}" autocomplete="off" />
			<br>
			<label for="user_password_repeat">Pakartokite slaptažodį</label>
			<input id="user_password_repeat" type="password" name="user_password_repeat" pattern=".{6,}" autocomplete="off" />
			<br>
			<input type="hidden" name="user_desc" value="" />
			<input style="display: none;" type="file" name="user_thumb" id="user_thumb" />
			<br>
			<label></label> <img class="cap" src="/tools/showCaptcha.php?v=<?php echo uniqid() ?>" alt="captcha" />
			<br>
			<label>Įveskite kodą, kurį matote paveikslėlyje<span class="reqfield">*</span></label>
			<input type="text" name="captcha" required />
			<br>
			<br>
			<label>Su <a href="/taisykles" target="_blank">taisyklėmis</a> sutinku</label>
			<input id="ag" type="checkbox" name="ag" value="1" />
			<br>
			<p><?php echo $note; ?></p>
			<br>
			<label></label> <input type="submit" name="register" value="<?php echo $dovanoti; ?>" />
		</form><?php
	else : // already registered and signed in user
		if( isset($_POST['fullfill']) ) :
			if (!$_FILES['need_full_photo'] || empty($_FILES['need_full_photo']['name'])) :
			elseif ($_FILES['need_full_photo']["size"] == 0) :
			elseif ($_FILES['need_full_photo']["size"] > 5*1024*1024) :
			elseif (($_FILES['need_full_photo']["type"] != "image/pjpeg") AND ($_FILES['need_full_photo']["type"] != "image/jpeg") AND ($_FILES['need_full_photo']["type"] != "image/png")) :
			elseif (!is_uploaded_file($_FILES['need_full_photo']["tmp_name"])) :
			else :
				$upfilename = date('YmdHis') . str_replace('.', '_', uniqid('_', true)) . '.' . pathinfo($_FILES["need_full_photo"]["name"], PATHINFO_EXTENSION);
				$move = @move_uploaded_file($_FILES['need_full_photo']['tmp_name'], (ROOT_PATH . 'uploads' . DIRECTORY_SEPARATOR . 'needs' . DIRECTORY_SEPARATOR . $upfilename));
				if($move) :
					$url = ROOT_URL . 'uploads/needs/' . $upfilename;
					updateFieldWhere('needs', 'need_full_photo', $url, 'need_id = '.$need_id);
				endif;
			endif;
			$kuratoriaus_laiskas = "
<p>Sveiki!</p>

<p>Jūsų kuruojamajam asmeniui siūlomas jo geidžiamas darbas.</p>

<p>Kuruojamas asmuo, kuris gaus šią dovaną - ".$needyrow['user_fname']." ".$needyrow['user_lname']." ".$needyrow['user_orgname']."</p>

<p>Dovana – " . htmlspecialchars($pagerow['need_name'], ENT_QUOTES, CHARSET)."</p>

<p>" . htmlspecialchars((isset($pagerow['need_dsc']) ? $pagerow['need_dsc'] : ''), ENT_QUOTES, CHARSET)."</p>

<p>Dovanotojas:</p>
<p>" . htmlspecialchars($_SESSION['user_fname'], ENT_QUOTES, CHARSET) . " " . htmlspecialchars($_SESSION['user_lname'], ENT_QUOTES, CHARSET) . "<br/>
" . htmlspecialchars($citiesList[$_SESSION['user_city']], ENT_QUOTES, CHARSET) . ", " . htmlspecialchars($regionList[$_SESSION['user_region']], ENT_QUOTES, CHARSET) . "<br/>
Adresas: " . htmlspecialchars($_SESSION['user_address'], ENT_QUOTES, CHARSET) . "<br/>
Telefonas: +" . htmlspecialchars($_SESSION['user_phone'], ENT_QUOTES, CHARSET) . "<br/>
El. Paštas: " . htmlspecialchars($_SESSION['user_email'], ENT_QUOTES, CHARSET) . "
</p>
<p>Dovanos aprašymas: " . htmlspecialchars((isset($_POST['need_full_desc']) ? $_POST['need_full_desc'] : '-'), ENT_QUOTES, CHARSET) . "</p>
<p>Prašome susisiekti su dovanotoju ir sutarti dėl dovanos perdavimo.</p>
<br />
<p>
Su pagarba,<br />
Aukoklaika.lt administracija<br />
http://aukoklaika.lt<br />
</p>
";
			$dovanotojo_laiskas = "
<p>Sveiki!</p>
<p>Dėkojame Jums už tai, kad norite padėti!</p>
<p>Jūsų kontaktiniai duomenys (el. pašto adresas ir telefono numeris) buvo perduoti asmens, kuriam norite padėti, kuratoriui.</p>
<p>Dovanojamą darbą Jūs galėsite perduoti stokojančiam asmeniui pats arba susitarti su kuratoriumi dėl dovanojamo darbo perdavimo.</p>

<br />

<p>Kuratoriaus kontaktiniai duomenys:</p>
<p>
".$parentrow['user_fname']." ".$parentrow['user_lname']."<br/>
".$citiesList[$parentrow['user_city']].", ".$regionsList[$parentrow['user_region']]."<br/>
+".$parentrow['user_phone']."<br/>
".$parentrow['user_email']."<br/>
</p>
  
<p>Nuoširdžiai Jūsų, <br />http://aukoklaika.lt kolektyvas</p>
<br/><br/>
 
<p>Jūs šį laišką gavote todėl, kad interneto tinklalapyje www.aukoklaika.lt 
išreiškėte norą padėti skurstantiems Lietuvos žmonėms, padovanodami reikalingą 
darbą, ir įvedėte savo el. pašto adresą. Jei Jūs to nedarėte, vadinasi, kažkas 
iš Jūsų pažįstamų negražiai pajuokavo. Tokiu atveju prašome pranešti el. paštu: info@aukoklaika.lt </p>
			";
			if(myMail($_SESSION['user_email'], 'Aukoklaika.lt dėkoja!', $dovanotojo_laiskas)
			   && myMail($parentrow['user_email'], 'Aukoklaika.lt dovanojamas darbas', $kuratoriaus_laiskas)){
				$need_full_desc = (isset($_POST['need_full_desc']) ? mysqli_real_escape_string($con, $_POST['need_full_desc']) : '');
				updateFieldWhere('needs', 'need_full', '1', 'need_id = '.$need_id);
				updateFieldWhere('needs', 'need_full_desc', $need_full_desc, 'need_id = '.$need_id);
				updateFieldWhere('needs', 'need_full_user', CUSER, 'need_id = '.$need_id);
				updateFieldWhere('needs', 'need_fulldate', date('Y-m-d H:i:s'), 'need_id = '.$need_id);
				err($padovanota); echo '</div></div></div>';
                        } else { err('Klaida siunčiant žinutę', 'red'); }
			return;
		endif; ?>
			<form method="post" action="" name="registerform" enctype="multipart/form-data">
				<label for="need_full_desc">Aprašymas</label>
				<textarea id="need_full_desc" type="text" maxlength="400" name="need_full_desc"></textarea>
				<br>
				<label for="need_full_photo">Nuotrauka</label>
				<input type="file" name="need_full_photo" id="need_full_photo" />
				<br>
				<label></label> <input type="submit" name="fullfill" value="<?php echo $dovanoti; ?>" />
			</form>
		<?php
	endif;

	echo '</div></div></div>';
	return;
else :
	err('Puslapis neegzistuoja</div></div>');
endif;


?>
