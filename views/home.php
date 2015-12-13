<?php
// FACEBOOK SETUP
$fbsite = 1;
$fbid = '283520278441';
$fbsender = 'Pagalbadaiktais.lt';

// Filters
$filterCat = ( (isset($_GET['fc']) and is_numeric($_GET['fc']) and $_GET['fc'] > 0) ? 'AND (needs.need_cat = '.$_GET['fc'].' OR needs.need_subcat = '.$_GET['fc'].')' : '' );
if(isset($_GET['fci']) and is_numeric($_GET['fci'])) :
	if($_GET['fci'] > 999) :
		$region = str_replace('100', '', $_GET['fci']);
		$cities = (array_key_exists($region, $regionsListChildren) ? $regionsListChildren[$region] : array());
		foreach($cities as $ctkey => $cityid) $cities[$ctkey]= 'needy.user_city = '.$cityid;
		$cities = implode(' OR ', $cities);
		$filterCity = 'AND ('.$cities.')';
	else :
		$filterCity = 'AND needy.user_city = '.$_GET['fci'];
	endif;
else : $filterCity = '';
endif;
$filterStok = ( (isset($_GET['fs']) and is_numeric($_GET['fs']) ) ? 'AND needy.user_cat = '.$_GET['fs'] : '' );
$srch = ( (isset($_GET['s']) and $_GET['s'] != '' ) ? "AND need_name LIKE '%".$_GET['s']."%'" : '' );
?>
<!-- POREIKIAI -->
<div class="inline poreikiai">

<form class="filter_form" action="" method="GET">
<span class="label">Pasirink</span>
<select data-placeholder="Kategorija" class="slickSelect" name="fc">
<option></option>
<option value="">Visos kategorijos</option>
<?php
	foreach(listData('cats', 'cat_type = 3') as $item) :
		echo '<option '.((isset($_GET['fc']) and $_GET['fc'] == $item['cat_id']) ? 'selected="selected"' : '').' value = "'.$item['cat_id'].'">'.$item['cat_name'].'</option>';
	endforeach;
?>
</select>

<select data-placeholder="Teritorija" class="slickSelect" name="fci">
<option></option>
<option value="">Visa Lietuva</option>
<?php
	foreach($regionsListChildren as $regkey => $children) :
		echo '<optgroup label="'.$regionsList[$regkey].'">';
		echo '<option '.((isset($_GET['fci']) and is_numeric($_GET['fci']) and $_GET['fci'] == '100'.$regkey) ? 'selected="selected"' : '').' value = "100'.$regkey.'">Visa '.$regionsList[$regkey].'</option>';
		foreach($children as $ckey) :
			echo '<option '.((isset($_GET['fci']) and is_numeric($_GET['fci']) and $_GET['fci'] == $ckey) ? 'selected="selected"' : '').' value = "'.$ckey.'">'.$citiesList[$ckey].'</option>';
		endforeach;
		echo '</optgroup>';
	endforeach;
?>
</select>

<select data-placeholder="Stokojantysis" class="slickSelect" name="fs">
<option></option>
<option value="">Visi stokojantieji</option>
<?php
	foreach(listData('cats', 'cat_type = 1 OR cat_type = 2') as $item) :
		echo '<option '.((isset($_GET['fs']) and $_GET['fs'] == $item['cat_id']) ? 'selected="selected"' : '').' value = "'.$item['cat_id'].'">'.$item['cat_name'].'</option>';
	endforeach;
?>
</select>
<input class="search" type="text" name="s" value="<?php echo (isset($_GET['s']) ? $_GET['s'] : ''); ?>" />
<input type="submit" value="Rodyti" />
</form>

<ul class="poreikiailist">
<?php
	$where = "SELECT need_id, need_name, cat_name, cat_id, user_city, need_regdate, a.deleted AS deleted FROM (SELECT need_id, need_name, cat_name, cat_id, need_type, need_needy, need_regdate, needs.deleted AS deleted FROM needs INNER JOIN cats ON needs.need_cat = cats.cat_id WHERE needs.need_type = $fbsite AND needs.need_full=0 AND needs.need_expires > NOW() AND needs.deleted = 0 $filterCat) a INNER JOIN needy ON a.need_needy = needy.user_id WHERE a.need_type = $fbsite $filterCity $filterStok $srch ORDER BY need_id DESC";
	$c = 0;
	if(pageNum() != 0) :
	foreach(listData(false, false, pageNum(), $where, 15) as $pdata) :
		echo '<li>';
			echo '<a href="/poreikiai/id/'.$pdata['need_id'].'">';
			echo '<div class="icon" style="background-image: url(/img/c'.$pdata['cat_id'].'.png);"></div>';
			echo '<div class="name">'.$pdata['need_name'].'</div>';
			echo '<div class="city">'.$citiesList[$pdata['user_city']].'</div>';
			echo '</a>';
		echo '</li>';
		$c++;
	endforeach;
	endif;
	
	if($c == 0) err('Nerastas nė vienas poreikis');
?>
</ul>
<?php pagination(countData(false, false, $where), 15); ?>

<!-- SIDEBAR -->
</div><div class="inline sidebar">
	<a href="https://www.aukok.lt/projektai/pagalbadaiktais-lt" target="_blank"><div class="sidehead remti"><i class="fa fa-plus"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paremk projektą&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-plus"></i></div></a>
	<div class="facebookNews">
		<div class="sidehead"><i class="fa fa-facebook-square fa-lg"></i> Naujienos</div>
		<?php

		$fbdata = getRow('fbcache', "id = $fbsite AND updated > DATE_SUB(NOW(), INTERVAL 30 MINUTE) ");
		if($fbdata) : $fblist = $fbdata['content'];
		else :
            require (ROOT_PATH . "libraries" . DIRECTORY_SEPARATOR . "facebook" . DIRECTORY_SEPARATOR . "facebook.php");
			$facebook = new Facebook(array(
				'appId'  => '1465543123659209',
				'secret' => '24983a3c450565771723dc19486b9edc',
			));

			$pageFeed = $facebook->api($fbid . '/feed');

			$i = 0; $fblist = '';
			foreach($pageFeed['data'] as $feed) :
				if(isset($feed['message']) and isset($feed['from']['name']) and $feed['from']['name'] == $fbsender) :
					$i++;
					$message = (isset($feed['picture']) ? '<img src="'.$feed['picture'].'" />' : '').'<div id="clamp'.$i.'">'.$feed['message'].'</div>';
					if(isset($feed['link'])) : $message = $message.'<a target="_blank" href="'.$feed['link'].'"><div class="angles"><i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i></div></a>';
					endif;
					$fblist .= '<li>'.$message.'</li>';
				endif;
				if($i > 2) break;
			endforeach;
			mysqli_query($con, "UPDATE fbcache SET content = '$fblist', updated = NOW() WHERE id = $fbsite");
		endif;

		echo '<ul class="facebookFeed">';
			echo $fblist;
		echo '</ul>';
		?>
	</div>
    <a href="http://www.pagalbadaiktais.lt/" target="_blank"><div class="sidehead brothersite">&nbsp;</div></a><?php

    $draugaipg = getRow('pages', "page_slug = 'draugai' AND page_type = 0 AND deleted = 0");
    if(!empty($draugaipg['page_content'])) {
        ?>
        <div class="draugai">
            <div class="sidehead">Draugai</div>
            <div class="wrap">
                <?= $draugaipg['page_content']; ?>
            </div>
        </div>
        <?php
    }
    ?>
</div>
