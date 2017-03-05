<?php
$usermeta = getCurrentUser();
include('cities.php');

global $login;

if(isset($_POST['login']) and $login->isUserLoggedIn() and CUSER > 0) :
	updateField('users', 'user_lastlogin', date('Y-m-d H:i:s'), 'user_id', CUSER);
	updateField('users', 'user_lastlogin_ip', $_SERVER['REMOTE_ADDR'], 'user_id', CUSER);
endif;

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>aukoklaika.lt</title>
		<link rel="stylesheet" type="text/css" href="/normalize.css" />
		<link rel="stylesheet" type="text/css" href="/style.css?v=1" />
		<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="/chosen.css" />
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/i18n/jquery-ui-i18n.min.js"></script>
		<script src="/chosen.jquery.min.js"></script>
		<script src="/clamp.min.js"></script>
		<script src="/script.js"></script>
		<!--[if lte IE 9]><link rel="stylesheet" type="text/css" href="/styleIE.css" /><![endif]-->

<!-- Facebook Pixel Code -->
<script>
	!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
			n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
		n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
		t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
			document,'script','//connect.facebook.net/en_US/fbevents.js');

	fbq('init', '1064341870245688');
	fbq('track', "PageView");
</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1064341870245688&ev=PageView&noscript=1" /></noscript>
<!-- End Facebook Pixel Code -->


    </head>
    <body>
	<?php

	// Google analytics
	include_once(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "analytics.php");

	?>
	<div id="openModal" class="darbais modalDialog">
		<div>
			<div class="modalhead">Prisijungimas<a href="#close" title="Uždaryti" class="close">&times;</a></div>
			<form method="post" action="/index.php" name="loginform">
					<label>El. paštas arba paskyros vardas</label><input id="user_name" type="text" name="user_name" required /><br>
					<label>Slaptažodis <a href="/password_reset.php">(priminti)</a></label><input id="user_password" type="password" name="user_password" autocomplete="off" required />
					<input type="hidden" id="user_rememberme" name="user_rememberme" value="1" />
					<label></label><input type="submit" name="login" value="Prisijungti" />
					<br><br>Registracija <a href="/register.php">fiziniams</a>, <a href="/register.php?juridinis">juridiniams</a> asmenims
			</form>
		</div>
	</div>

	<div class="header darbais">
		<a href="/"><div class="left">
		</div></a><div class="right">
			<div class="inline auth">

				<?php if (isset($login) and $login->isUserLoggedIn() == true) : ?>
					<div class="inline valign-mid">
						<a title="Paskyros nustatymai" href="/?p=edit">
							<?php echo $_SESSION['user_name']; ?>
							<div class="inline valign-mid gravatar">
								<?php echo (strlen($usermeta['user_thumb']) > 16 ? '<img src="'.$usermeta['user_thumb'].'" onError="this.style.visibility=\'hidden\'" />' : ''); ?>
							</div>
						</a>
						<a title="Atsijungti" href="/index.php?logout"><i class="fa fa-sign-out"></i></a>
					</div>
				<?php else : ?>
					<a class="openModal" href="#openModal">Prisijungti</a>
				<?php endif; ?>

			</div><br>
			<ul class="inline pgmenu">
				<?php $pgmenu = array(
					'apie' => 'Apie',
					'padek' => 'Prisidėk',
					'gauk' => 'Gauk pagalbos',
					'regionuose' => 'Mes regionuose',
					'duk' => 'DUK',
					'kontaktai' => 'Kontaktai',
				);
				$submenu = array(
				'apie' => array('apie' => 'Apie projektą', 'apieorg' => 'Apie organizaciją', 'rezultatai' => 'Rezultatai'),
				'padek' => array('padek' => 'Padėk kitiems', 'tapk' => 'Tapk komandos nariu'),
				'gauk' => array('gauk' => 'Pagalbos gavėjai', 'tapkg' => 'Tapk pagalbos gavėju'),
				);

				if( isset($_GET['pageslug']) and array_key_exists($_GET['pageslug'], $submenu['apie']) ) $father = 'apie';
				elseif( isset($_GET['pageslug']) and array_key_exists($_GET['pageslug'], $submenu['padek']) ) $father = 'padek';
				elseif( isset($_GET['pageslug']) and array_key_exists($_GET['pageslug'], $submenu['gauk']) ) $father = 'gauk';
				elseif(isset($_GET['pageslug'])) $father = $_GET['pageslug'];
				else $father = '';

				foreach($pgmenu as $pgkey => $pgitem) echo '<li><a '.( ((isset($_GET['p']) and isset($_GET['pageslug']) and $_GET['p'] == 'puslapis' and $father == $pgkey) ) ? 'class="current"' : '' ).' href="/'.$pgkey.'">'.$pgitem.'</a></li>';
				?>
			</ul>
			<br>&nbsp;
		</div>
	</div>

	<?php if(isset($_GET['pageslug']) and $_GET['pageslug'] != '' and isset($pgmenu[$father])) : ?>
	<div class="pagebanner darbais">
		<div class="social">
			<a class="fb" target="_blank" href="https://www.facebook.com/aukoktinklas/"><i class="fa fa-facebook"></i></a>
			<a class="tw" target="_blank" href="http://www.linkedin.com/company/pagalbadaiktais-lt"><i class="fa fa-linkedin"></i></a>
		</div>
		<div class="pgtitles">
		<img src="http://aukoklaika.lt/img/p<?php echo $father; ?>.png" alt= "pglogo" />
		<h2><?php echo $pgmenu[$father]; ?></h2>
		</div>
		<div class="submenu">
		<?php
		if( array_key_exists($_GET['pageslug'], $submenu['apie'])
			or array_key_exists($_GET['pageslug'], $submenu['padek'])
			or array_key_exists($_GET['pageslug'], $submenu['gauk'])
		) :
			echo '<ul>';
			foreach($submenu[$father] as $menukey => $menuitem) :
				echo '<li'.($menukey == $_GET['pageslug'] ? ' class="current"' : '').'><a href="/'.$menukey.'">'.$menuitem.'</a></li>';
			endforeach;
			echo '</ul>';
		endif;
		?>
		</div>
	</div>
	<?php elseif(isset($_GET['p']) and ($_GET['p'] == 'kuratorius' or $_GET['p'] == 'stokojantysis' or $_GET['p'] == 'tinklo-atstovas' or $_GET['p'] == 'poreikiai' or $_GET['p'] == 'edit') ) :
	$fumenu = array('kuratorius' => 'Kuratoriai', 'stokojantysis' => 'Pagalbos gavėjai', 'tinklo-atstovas' => 'Tinklo atstovai', 'poreikiai' => 'Poreikiai', 'edit' => 'Mano paskyra');
	?>

	<div class="pagebanner darbais">
		<div class="social">
			<a class="fb" target="_blank" href="https://www.facebook.com/aukoktinklas/"><i class="fa fa-facebook"></i></a>
			<a class="tw" target="_blank" href="http://www.linkedin.com/company/pagalbadaiktais-lt"><i class="fa fa-linkedin"></i></a>
		</div>
		<div class="pgtitles">
		<img src="http://aukoklaika.lt/img/fu_<?php echo $_GET['p']; ?>.png" alt= "pglogo" />
		<h2><?php echo $fumenu[$_GET['p']]; ?></h2>
		</div>
	</div>
	<?php else : ?>
	<div class="banner darbais" style="background-image: url('img/head1.jpg');">
		<div class="social">
			<a class="fb" target="_blank" href="https://www.facebook.com/aukoktinklas/"><i class="fa fa-facebook"></i></a>
			<a class="tw" target="_blank" href="http://www.linkedin.com/company/pagalbadaiktais-lt"><i class="fa fa-linkedin"></i></a>
		</div>
		<div class="counter">Nuo portalo veiklos pradžios atlikta <span class="cnumber"><?php echo countData('needs', 'need_full = 1 AND need_type = 1 AND deleted = 0'); ?></span> gerų darbų</div>
	</div>
	<?php endif; ?>

	<div class="site darbais<?php if( (isset($_GET['pageslug']) and $_GET['pageslug'] != '') or (isset($_GET['p']) and in_array($_GET['p'], array('kuratorius', 'tinklo-atstovas', 'stokojantysis', 'poreikiai', 'edit')) ) ) echo ' pagetemp'; ?>">
