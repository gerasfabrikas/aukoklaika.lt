<?php

$site = 1;

if(!isset($_GET['id'])) return;

$id = $_GET['id'];
$pagerow = getRow('users', "user_id = $id");

if($pagerow['user_active'] == 0) {err('Šis profilis panaikintas'); return;} 
if($pagerow['user_acctype'] <= 1) {err('Šis profilis neegzistuoja'); return;} 

echo '<div class="poreikis_left inline">';

// User description

echo '<div class="wrap">';
	getThumbnail('user', $id);
	echo '<div class="thumb_desc wide">';
	echo '<h2>'.$pagerow['user_fname'].' '.$pagerow['user_lname'].'</h2>';
	echo '<b>Tinklo atstovas</b>';
	echo '<br>Gyvena '.$citiesList[$pagerow['user_city']];
	echo '<br>Telefonas +'.$pagerow['user_phone'];
	echo '<div class="desc">'.$pagerow['user_desc'].'</div>';
	echo '<div class="desc">Profilis sukurtas '.$pagerow['user_registration_datetime'].'</div>';
	echo '</div>';
	
echo '</div>';
// User description end

echo '</div><!-- poreikis_left -->';

echo '<div class="poreikis_right inline">';

echo '<h2>Tinklo atstovui priskirti kuratoriai</h2>';

	echo '<ul class="user_list">';
	foreach( listData(false, false, false, "SELECT * FROM users WHERE user_active = 1 AND user_acctype = 1 AND user_parent = $id") as $data ) :
		echo '<li><a href="/kuratorius/'.$data['user_id'].'">';
		getThumbnail('user', $data['user_id']);
		echo '<div class="thumb_desc wide">';
		echo $data['user_fname'].' '.$data['user_lname'];
		echo '</div>';
		echo '</a></li>';
	endforeach;
	echo '</ul>';

echo '</div><!-- poreikis_right -->';

?>