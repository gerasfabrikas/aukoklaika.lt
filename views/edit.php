<div class="page"><div class="page-con">

<?php
echo '<h2>Suteikta pagalba</h2>';
echo '<ul class="poreikiailist smallersq">';
$data0 = listData(false, false, false, "SELECT * FROM needs LEFT JOIN cats ON needs.need_cat = cats.cat_id WHERE need_full_user = ".CUSER." AND need_type = 1 AND needs.deleted = 0");
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
?>

</div><div class="page-sid">

<div class="sid-back">
<h2>Keisti slaptažodį</h2>
<!-- edit form for user's password / this form uses the HTML5 attribute "required" -->
<form method="post" action="" name="user_edit_form_password">
	<label for="user_password_old"><?php echo $phplogin_lang['Old password']; ?><span class="reqfield">*</span></label><input id="user_password_old" type="password" name="user_password_old" autocomplete="off" required />
	<br>
	<label for="user_password_new"><?php echo $phplogin_lang['New password']; ?><span class="reqfield">*</span></label><input id="user_password_new" type="password" name="user_password_new" autocomplete="off" required />
	<br>
	<label for="user_password_repeat"><?php echo $phplogin_lang['Repeat new password']; ?><span class="reqfield">*</span></label><input id="user_password_repeat" type="password" name="user_password_repeat" autocomplete="off" required />

	<br>
	<label></label><input type="submit" name="user_edit_submit_password" value="<?php echo $phplogin_lang['Change password']; ?>" />
</form>
</div>
<br>
<div class="sid-back">
<h2>Keisti profilio nuotrauką</h2>
<?php

$options = array(
	'fields' => array(
		'user_thumb'			=> array( 'inputtype' => 'photo' ),
	),
);
updateUsermeta($options, CUSER, 'users', 'user_id');
echo '</div><br><div class="sid-back"><h2>Keisti profilio duomenis</h2>';

if($usermeta['user_person'] == 0) :
	$options = array(
		'fields' => array(
			'user_fname'		=> array('Vardas', 'required' => true),
			'user_lname'		=> array('Pavardė', 'required' => true),
		),
	);
elseif($usermeta['user_person'] == 1) :
    $Core = new Core();
    $legalStatuses = $Core->get('legalStatuses');

	$options = array(
		'fields' => array(
			'user_orgname'		=> array('Pavadinimas', 'required' => true),
			'user_legalstatus'	=> array('Teisinis statusas', 'inputtype' => 'drop', 'drops' => $legalStatuses, 'required' => true),
			'user_code1'		=> array('Įmonės kodas', 'len' => 9, 'required' => true),
			'user_code2'		=> array('PVM mokėtojo kodas', 'len' => 14),
			'user_reg'			=> array('Registras', 'len' => 128),
		),
	);
endif;

$options['fields']['user_address'] = 	array('Adresas', 'required' => true);
$options['fields']['user_city2'] = 		array('Savivaldybė', 'inputtype' => 'drop', 'drops' => $citiesList, 'required' => true);
$options['fields']['user_phone'] = 		array('Telefonas', 'len' => 11, 'required' => true);
$options['fields']['user_desc'] = 		array( ($usermeta['user_person'] == 0 ? 'Apie save' : 'Apie organizaciją') , 'inputtype' => 'textarea');
$options['fields']['user_subscribed'] = array( 'Naujienų prenumerata' , 'inputtype' => 'bool' );

updateUsermeta($options, CUSER, 'users', 'user_id');

?>
</div>
</div></div>
