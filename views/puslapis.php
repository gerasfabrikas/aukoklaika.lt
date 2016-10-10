<?php

$page_slug = (isset($_GET['pageslug']) ? $_GET['pageslug'] : false);
$page_site = 0;

$pcitem = (isset($_GET['klausimas']) ? $_GET['klausimas'] : 1);

if($page_slug) :
	$pagerow = getRow('pages', "page_slug = '$page_slug' AND page_site = $page_site AND deleted = 0");
	if(!$pagerow) :
		$pagerow = getRow('pages', "page_slug = '$page_slug' AND page_site = 2 AND deleted = 0");
		if(!$pagerow) : err('Puslapis neegzistuoja'); return;
		endif;
	endif;
else : include('home.php'); return;
endif;

if($pagerow) :
	echo '<div class="page">';
	echo '<div class="page-con">';
	if($page_slug != 'duk') {$pgcon_exploded = explode('[PARASTE]', $pagerow['page_content']); echo $pgcon_exploded[0];}
endif;

if($page_slug == 'regionuose') :
	if(isset($_GET['reg']) and $_GET['reg'] < 10) $regionas = $_GET['reg'];
	else $regionas = 9;
	
	echo '<h2>Pagalba visoje Lietuvoje</h2><p>Stipraus savanorių tinklo ir partnerių pagalba, <b>Aukoklaika.lt</b> veikla vyksta visoje Lietuvoje.</p>';
	
	echo '<h2>'.( (isset($_GET['city']) and $_GET['city'] != 'all' and in_array($_GET['city'], $regionsListChildren[$regionas])) ? $citiesList[$_GET['city']] : $regionsList[$regionas]).'</h2>';
	
	
	$table = ((isset($_GET['ptype']) and $_GET['ptype'] == 1) ? 'needy' : 'users');
	$onlyactive = ((isset($_GET['ptype']) and $_GET['ptype'] == 1) ? 'AND deleted = 0' : 'AND user_active = 1 AND user_acctype > 0 AND user_acctype < 3');
	$where = ((isset($_GET['city']) and $_GET['city'] != 'all' and in_array($_GET['city'], $regionsListChildren[$regionas])) ? 'user_city = '.$_GET['city'].' '.$onlyactive : 'user_region = '.$regionas.' '.$onlyactive);
	$data = listData($table, $where);
	if(count($data) > 0) :
	echo '<table class="regtable">';
	foreach($data as $item) :
		echo '<tr>';
		
		echo '<td style="width: 150px;">'.$citiesList[$item['user_city']].'</td>';
		
		echo '<td style="width: 220px;">';
		if($table == 'users') echo '<a href="/kuratorius/'.$item['user_id'].'">';
		if($table == 'needy') echo '<a href="/stokojantysis/'.$item['user_id'].'">';
		if( isset($item['user_person']) and $item['user_person'] == 0 ) echo $item['user_fname'].' '.$item['user_lname'];
		elseif( isset($item['user_person']) and $item['user_person'] == 1 ) echo $item['user_orgname'];
		else echo $item['user_fname'].' '.$item['user_lname'].' '.$item['user_orgname'];
		echo '</a>';
		echo '</td>';
		
		if($table == 'users') echo '<td style="width: 110px;">+'.$item['user_phone'].'</td>';
		
		echo '<td style="width: 250px;">'.($item['user_email'] != '' ? '<a href="mailto:'.$item['user_email'].'">'.$item['user_email'].'</a>' : '—').'</td>';
		
		echo '</tr>';
	endforeach;
	echo '</table>';
	else :
		echo 'Kontaktų sąrašas tuščias';
	endif;
endif;

if($page_slug == 'duk') :
	$pces = array();
	$pcex = explode('#####', $pagerow['page_content']);
	foreach($pcex as $item) :
		$pcest = explode('###', $item);
		if(isset($pcest[0]) and isset($pcest[2])) $pces[$pcest[1]] = array($pcest[0], $pcest[2]); 
	endforeach;
	if(isset($pces[$pcitem][0]) and isset($pces[$pcitem][1])) :
		echo '<h2>'.str_replace('</p>', '', str_replace('<p>', '', $pces[$pcitem][0])).'</h2>';
		echo $pces[$pcitem][1];
	endif;
endif;

if($page_slug == 'kontaktai') : ?>
	<div class="contact-balloon1">
	<span class="contact-white">Marija Šaraitė</span><br>
	Projekto vadovė<br>
	+37067882132<br>
	marija@aukoklaika.lt
	</div>
	<div class="contact-balloon2">
	<span class="contact-white">VšĮ GEROS VALIOS<br>
	PROJEKTAI</span><br>
	NVO Avilys, Gedimino<br>pr. 21, 01103 Vilnius
	</div>
<?php
endif;

if($page_slug == 'rezultatai') :
	// map
	function regCounter($site, $region, $full) {
		global $regionsListChildren;
		$cities = (array_key_exists($region, $regionsListChildren) ? $regionsListChildren[$region] : array());
		foreach($cities as $ctkey => $cityid) $cities[$ctkey]= 'needy.user_city = '.$cityid; 
		$cities = implode(' OR ', $cities);
		$filterCity = 'AND ('.$cities.')';
		$site = $site + 1;
		if($full == 0) $expires = 'AND needs.need_expires > NOW()'; else $expires = '';
		$q = "SELECT needs.need_id FROM needs INNER JOIN needy ON needs.need_needy = needy.user_id WHERE need_type = $site AND needs.need_full = $full AND needs.deleted = 0 $expires $filterCity";
		return countData(false, false, $q);
		return 0;
	}

	$map11 = regCounter($page_site, 8, 0);
	$map12 = regCounter($page_site, 8, 1);
	$map21 = regCounter($page_site, 9, 0);
	$map22 = regCounter($page_site, 9, 1);
	$map31 = regCounter($page_site, 1, 0);
	$map32 = regCounter($page_site, 1, 1);
	$map41 = regCounter($page_site, 3, 0);
	$map42 = regCounter($page_site, 3, 1);
	$map51 = regCounter($page_site, 2, 0);
	$map52 = regCounter($page_site, 2, 1);
	$map61 = regCounter($page_site, 7, 0);
	$map62 = regCounter($page_site, 7, 1);
	$map71 = regCounter($page_site, 5, 0);
	$map72 = regCounter($page_site, 5, 1);
	$map81 = regCounter($page_site, 4, 0);
	$map82 = regCounter($page_site, 4, 1);
	$map91 = regCounter($page_site, 6, 0);
	$map92 = regCounter($page_site, 6, 1);
	$map101 = regCounter($page_site, 0, 0);
	$map102 = regCounter($page_site, 0, 1);
	echo '<div class="mapr">';

    $ltMap = ROOT_PATH . 'img' . DIRECTORY_SEPARATOR . 'ltmap2.svg';
    if(is_file($ltMap)) {
        include($ltMap);
    }

	echo '<div class="legend"><div class="leg1">Darbų poreikiai apskrityse</div><div class="leg2">Atlikti darbai apskrityse</div></div>';
	echo '</div>';
	// end map
	$site = $page_site+1;
	$digitl = countData("needs", "need_type = $site AND needs.need_full = 0 AND deleted = 0 AND needs.need_expires > NOW()");
	$digitr = countData("needs", "need_type = $site AND needs.need_full = 1 AND deleted = 0");
	$digit = $digitl + $digitr;
	$per1 = round($digitr/($digit)*100);
	
	$pr1 = ($per1 <= 50 ? round($per1*360/100) : 180);
	$pr2 = ($per1 > 50 ? round(($per1-50)*360/100) : 0);
	?><br><hr><br>
	<table class="reztable" width="100%">
		<tr>
			<td class="rb im1" width="227"><div class="imlabel"><div class="digit"><?php echo $digitr; ?></div>Atlikti<br>darbai</div></td>
			<td class="rb im2" width="246"><div class="imlabel"><div class="digit"><?php echo $digitl; ?></div>Darbų<br>poreikiai</div></td>
			<td width="227">
			<style>
			#pieSlice6 {-webkit-transform:rotate(180deg); -moz-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(50deg);}
			#pieSlice5 .pie {-webkit-transform:rotate(<?php echo $pr1; ?>deg); -moz-transform:rotate(<?php echo $pr1; ?>deg); -o-transform:rotate(<?php echo $pr1; ?>deg); transform:rotate(<?php echo $pr1; ?>deg);}
			#pieSlice6 .pie {-webkit-transform:rotate(<?php echo $pr2; ?>deg); -moz-transform:rotate(<?php echo $pr2; ?>deg); -o-transform:rotate(<?php echo $pr2; ?>deg); transform:rotate(<?php echo $pr2; ?>deg);}
			</style>
			<div class="pieContainer">
				<div class="pieHole"><div class="lab1"><div class="digit"><?php echo $per1; ?><small> %</small></div>Atlikti darbai</div><hr><div class="lab2">Darbų poreikiai<div class="digit"><?php echo (100 - $per1); ?><small> %</small></div></div></div>
				<div class="pieBackground slice2"></div>
				<div id="pieSlice5" class="hold"><div class="pie slice1"></div></div>
				<div id="pieSlice6" class="hold"><div class="pie slice1"></div></div>
			</div>
			</td>
		</tr>
	</table>
	<?php
endif;

if($pagerow) :
	echo '</div><div class="page-sid">';
	include('sidebar.php');
	echo '</div>';
	echo '</div>';
endif;

?>