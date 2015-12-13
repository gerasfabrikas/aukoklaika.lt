<?php

$site = 1;

if(!isset($_GET['id'])) return;

$id = $_GET['id'];
$pagerow = getRow('needy', "user_id = $id");

if(!isset($pagerow['user_id']) or $pagerow['deleted'] == 1) {err('Šis profilis panaikintas'); return;}

// User description

echo '<div class="page">';

echo '<div class="page-con">';
	echo '<div class="ku_img">';
	if(strlen($pagerow['user_thumb']) > 3) getThumbnail('needy', $pagerow['user_id']);
	else echo '<img src="/img/nt_st.png" alt="nothumb" />';
	echo '</div><div class="ku_desc">';
	echo '<h2>'.$pagerow['user_fname'].' '.$pagerow['user_lname'].' '.$pagerow['user_orgname'].'</h2>';
	$pagerow3 = getRow('cats', "cat_id = ".$pagerow['user_cat']);
	echo '<b>Pagalbos gavėjas,</b> '.lcfirst($pagerow3['cat_name']);
	echo '<br>';
	foreach($regionsListChildren as $key => $cities) if(in_array($pagerow['user_city'], $cities)) {echo '<a href="/regionuose?p=puslapis&pageslug=regionuose&reg='.$key.'&city=all&ptype=1">'.$regionsList[$key].'</a> &gt; '; $rgn = $key;}
	if(isset($rgn)) echo '<a href="/regionuose?p=puslapis&pageslug=regionuose&reg='.$rgn.'&city='.$pagerow['user_city'].'&ptype=1">'.$citiesList[$pagerow['user_city']].'</a>';
	echo '<br><br>'.$pagerow['user_desc'].'';
	echo '</div>';
	
	echo '<br><br><br><h2>Ieškoma pagalba</h2>';
	echo '<ul class="poreikiailist smallersq">';
	$data0 = listData(false, false, false, "SELECT * FROM needs LEFT JOIN cats ON needs.need_cat = cats.cat_id WHERE need_needy = $id AND need_type = $site AND needs.deleted = 0 AND need_full = 0 AND need_expires > NOW()");
	foreach( $data0 as $pdata ) :
		echo '<li>';
			echo '<a href="/poreikiai/id/'.$pdata['need_id'].'">';
			echo '<div class="icon" style="background-image: url(/img/c'.$pdata['cat_id'].'.png);"></div>';
			echo '<div class="name">'.$pdata['need_name'].'</div>';
			echo '<div class="city">'.$citiesList[$pdata['user_city']].'</div>';
			echo '</a>';
		echo '</li>';
	endforeach;
	echo '</ul>';
	if(count($data0) == 0) echo 'Sąrašas tuščias';

	echo '<h2>Gauta pagalba</h2>';
	echo '<ul class="poreikiailist smallersq">';
	$data1 = listData(false, false, false, "SELECT * FROM needs LEFT JOIN cats ON needs.need_cat = cats.cat_id WHERE need_needy = $id AND need_type = $site AND needs.deleted = 0 AND need_full = 1");
	foreach( $data1 as $pdata ) :
		echo '<li>';
			echo '<a href="/poreikiai/id/'.$pdata['need_id'].'">';
			echo '<div class="icon" style="background-image: url(/img/c'.$pdata['cat_id'].'.png);"></div>';
			echo '<div class="name">'.$pdata['need_name'].'</div>';
			echo '<div class="city">'.$citiesList[$pdata['user_city']].'</div>';
			echo '</a>';
		echo '</li>';
	endforeach;
	echo '</ul>';
	if(count($data1) == 0) echo 'Sąrašas tuščias';

echo '</div><div class="page-sid"><div class="sid-back">';

echo '<h2>Pagalbos gavėjo kuratorius</h2><p>Norėdami padėti pagalbos gavėjui kreipkitės į šį kuratorių:</p>';
	if($pagerow['user_parent'] > 0) :
		$pagerow2 = getRow('users', "user_id = ".$pagerow['user_parent']);
		echo '<a href="/kuratorius/'.$pagerow['user_parent'].'"><div class="ku_img ku_img_notop">';
		if(strlen($pagerow2['user_thumb']) > 3) getThumbnail('users', $pagerow2['user_id']);
		else echo '<img src="/img/nt_ku.png" alt="nothumb" />';
		echo '</div>';
		echo '<div class="ku_desc ku_desc_short">';
		echo  $pagerow2['user_fname'].' '.$pagerow2['user_lname'];
		echo '<br><span class="city">'.$citiesList[$pagerow2['user_city']].'</span>';
		echo '</div></a>';
	else : err('Stokojantysis neturi jam priskirto kuratoriaus', 'yellow');
	endif;
echo '</div></div></div>';

?>
