<?php

$site = 1;

if(!isset($_GET['id'])) return;

$id = $_GET['id'];
$pagerow = getRow('users', "user_id = $id");

if($pagerow['user_active'] == 0) {err('Šis profilis panaikintas'); return;} 
if($pagerow['user_acctype'] <= 0) {err('Šis profilis neegzistuoja'); return;} 

// User description

echo '<div class="page">';

echo '<div class="page-con">';
	echo '<div class="ku_img">';
	if(strlen($pagerow['user_thumb']) > 3) getThumbnail('users', $pagerow['user_id']);
	else echo '<img src="/img/nt_ku.png" alt="nothumb" />';
	echo '</div><div class="ku_desc">';
	echo '<h2>'.$pagerow['user_fname'].' '.$pagerow['user_lname'].'</h2>';
	echo '<b>Kuratorius</b>';
	echo '<br>';
	foreach($regionsListChildren as $key => $cities) if(in_array($pagerow['user_city'], $cities)) {echo '<a href="/regionuose?p=puslapis&pageslug=regionuose&reg='.$key.'&city=all&ptype=2">'.$regionsList[$key].'</a> &gt; '; $rgn = $key;}
	if(isset($rgn)) echo '<a href="/regionuose?p=puslapis&pageslug=regionuose&reg='.$rgn.'&city='.$pagerow['user_city'].'&ptype=2">'.$citiesList[$pagerow['user_city']].'</a>';
	echo '<br>El. paštas: <a href="mailto:'.$pagerow['user_email'].'">'.$pagerow['user_email'].'</a>';
	echo '<br>Telefono nr.: ' . $pagerow['user_phone'];
	echo '<br><br>'.$pagerow['user_desc'].'';
	echo '</div>';

echo '</div><div class="page-sid"><div class="sid-back">';

echo '<h2>Kuruojami pagalbos gavėjai</h2><p>Kuratorius rūpinasi šiais pagalbos gavėjais:</p>';

	echo '<ul class="kuruojami_list">';
	foreach( listData(false, false, false, "SELECT * FROM needy LEFT JOIN cats ON needy.user_cat = cats.cat_id WHERE user_parent = $id AND needy.deleted = 0") as $data ) :
		echo '<li><a class="ku_name" href="/stokojantysis/'.$data['user_id'].'">';
		echo $data['user_fname'].' '.$data['user_lname'].' '.$data['user_orgname'];
		echo '</a><span class="ku_city">'.$citiesList[$data['user_city']].'</span></li>';
	endforeach;
	echo '</ul>';

echo '</div></div></div>';

?>
