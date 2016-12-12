<?php


if($page_slug == 'regionuose') :
	echo '<div class="sid-back">';
	echo '<div class="map-form">';
	if(isset($_GET['reg']) and $_GET['reg'] < 10) $regionas = $_GET['reg'];
	else $regionas = 9;
	if(isset($regionas)) echo '<style>.darbais .me'. $regionas.' {fill: #fff;}</style>';

    $ltMap = ROOT_PATH . 'img' . DIRECTORY_SEPARATOR . 'ltmap.svg';
    if(is_file($ltMap)) {
        include($ltMap);
    }

		if(isset($regionas)) : ?>
		<form action="" method="GET" id="region-form">
			<input type="hidden" name="p" value="puslapis" />
			<input type="hidden" name="pageslug" value="regionuose" />
            <select name="reg" data-placeholder="Apskritis" class="slickSelect" onchange="document.getElementById('region-form').submit()">
                <?php foreach($regionsList as $key => $region): ?>
                    <option <?php echo ($regionas == $key ? 'selected="selected"' : '') ?> value="<?php echo $key ?>">
                        <?php echo $region ?>
                    </option>
                <? endforeach; ?>
            </select>

            <select name="city" data-placeholder="Savivaldybė" class="slickSelect" onchange="document.getElementById('region-form').submit()">
                <option value="all">Visos savivaldybės</option>
                <?php foreach($regionsListChildren[$regionas] as $cityid) echo '<option '.((isset($_GET['city']) and $_GET['city'] == $cityid) ? 'selected="selected"' : '').' value="'.$cityid.'">'.$citiesList[$cityid].'</option>'; ?>
            </select>

            <select name="ptype" class="slickSelect" onchange="document.getElementById('region-form').submit()" style="margin-top:5px;">
                <option <?php echo ((isset($_GET['ptype']) and $_GET['ptype'] == 2) ? 'selected="selected"' : ''); ?> value="2">Kuratoriai</option>
                <option <?php echo ((isset($_GET['ptype']) and $_GET['ptype'] == 1) ? 'selected="selected"' : ''); ?> value="1">Stokojantieji</option>
            </select>
		</form>		
		<?php endif;
	echo '</div></div>';
endif;

if($page_slug == 'duk') :
	echo '<div class="sid-back"><h3 style="margin-top: 20px;">DUK</h3>';
	echo '<ul class="klausimai">';
	foreach($pces as $ikey => $item) :
		if(isset($item[0]) and isset($item[1])) :
			echo '<li'.($ikey == $pcitem ? ' class="current"' : '').'><a href="/duk?klausimas='.$ikey.'">'.str_replace('</p>', '', str_replace('<p>', '', $item[0])).'</a></li>';
		endif;
	endforeach;
	echo '</ul></div>';
endif;

if($page_slug == 'kontaktai' or $page_slug == 'tapkg' or $page_slug == 'gauk' or $page_slug == 'tapk') :
	if($page_slug == 'kontaktai') echo '<div class="sid-back contact-form"><h2>Greitoji žinutė</h2>';
	if($page_slug == 'tapkg' or $page_slug == 'gauk') echo '<div class="sid-back contact-form"><h2>Registruokis sistemoje dabar</h2>';
	if($page_slug == 'tapk') echo '<div class="sid-back contact-form"><h2>Parašyk mums</h2>';
	echo '<p>Turi klausimų? Žinai atsakymus, kurių nežinome mes? Susisiek su mumis ir bendraukime.</p>';
	if(isset($_POST['code']) and (strtoupper($_POST["code"]) == $_SESSION['captcha'])) :
		if(isset($_POST['name']) and isset($_POST['elp']) and isset($_POST['desc'])) :
			if($_POST['name'] != '' and $_POST['elp'] !='' and $_POST['desc'] != '') :
				if(myMail(EMAIL_DEFAULT_TO, $_POST['name'].' ('.$citiesList[$_POST['sav']].') nori '.$_POST['nor'], $_POST['desc'], $from = $_POST['elp']))
				err('Jūsų žinutė išsiųsta', 'green');
				else err('Klaida siunčiant žinutę', 'red');
			else : err('Užpildykite visus laukelius', 'red');
			endif;
		else : err('Užpildykite visus laukelius', 'red');
		endif;
	elseif(isset($_POST['code'])) : err('Neteisingas apsaugos nuo robotų kodas', 'red');
	endif;
?>
	<form action="" method="post">
		<label>Aš noriu<span class="reqfield">*</span></label><select name="nor" class="slickSelect">
			<option value="gauti pagalbos">Gauti pagalbos</option>
			<option value="savanoriauti" >Savanoriauti</option>
			<option value="kita" >Kita</option>
		</select><br>
		<label>Savivaldybė<span class="reqfield">*</span></label><select name="sav" class="slickSelect"><?php foreach($citiesList as $ckey => $city) echo '<option value="'.$ckey.'">'.$city.'</option>'; ?></select><br>
		<label>Vardas ir pavardė<span class="reqfield">*</span></label><input type="text" name="name" value="" /><br>
		<label>El. paštas<span class="reqfield">*</span></label><input type="text" name="elp" value="" /><br>
		<label>Žinutė<span class="reqfield">*</span></label><textarea name="desc"></textarea><br>
		<label></label><img class="cap" src="tools/showCaptcha.php" /><br>
		<label>Įveskite tekstą, kurį matote paveikslėlyje<span class="reqfield">*</span></label><input type="text" name="code" value="" /><br>
		<label></label><input type="submit" value="Siųsti" />		
	</form>
	</div>
<?php
endif;

if($page_slug == 'padek') : ?>
	<div class="sid-back"><h2>Kodėl gera padėti kitiems?</h2>
	<p>Auokoklaika.lt – tai <span style="color: #fff;">gerumo tinklas visoje Lietuvoje</span>, internetinis gerų darbų ir informavimo apie juos centras.</p>

	<p>Auokoklaika.lt  – tai interneto <span style="color: #fff;">vartai, jungiantys galinčius suteikti pagalbą</span> ir tuos, kuriems ši pagalba yra labai reikalinga.</p>

	<p>Auokoklaika.lt <span style="color: #fff;">niekada neužsidaro</span>. Jis registruoja Jūsų gerus darbus 24 valandas per parą, 7 dienas per savaitę.</p>

	<p>Kasdien čia užregistruojami nauji dalyviai, kuriems labai reikalinga pagalba, kasdien čia apsilanko norintys padėti, o jau atlikę gerą darbą apsilanko ir vėl, nes <span style="color: #fff;">gera žinoti, kad gali padėti sunkiau gyvenantiems</span>.</p>
 
	<p>Kiekvienam norinčiam pasidalinti gerumu padedant Auokoklaika.lt suteikia šią galimybę!</p>
	</div>
<?php
endif;

if($page_slug == 'apie') : ?>
	<div class="sid-back"><h2>Auokoklaika.lt – tai</h2>
		<p><span style="color: #fff;">įrankis</span> nevyriausybinėms organizacijoms efektyviai ieškoti savanorių pagal konkrečius poreikius;</p>
		<p>naujas <span style="color: #fff;">iššūkis</span> valstybiniam sektoriui prisitraukti pagalbos savanoriškais darbais iš atsakingų piliečių;</p>
		<p><span style="color: #fff;">galimybė</span> sunkiau gyvenantiems žmonėms ieškoti pagalbos darbuose, kurių patys negeba atlikti ar neturi tam galimybių.</p>
	</div>
	<div class="sid-back" style="margin-top: 20px; width: 160px; display: inline-block; vertical-align: top; background-position:170px top;"><h2>Vizija</h2>
	Bendruomeniška visuomenė, kurioje esame organizacijų partneriai ir piliečių patarėjai, priimant tinkamus geros valios sprendimus
	</div><div class="sid-back" style="margin-top: 20px; margin-left: 20px; width: 160px; display: inline-block; vertical-align: top; background-position:170px top;"><h2>Misija</h2>
	Skatinti bendruomeniškumą, kuriant pagalbos sunkiau gyvenantiems žmonėms tinklą<br>&nbsp;
	</div>
<?php
endif;

if($page_slug == 'rezultatai') :
	$digitl = countData("needy", "deleted = 0 AND user_person = 0");
	$digitr = countData("needy", "deleted = 0 AND user_person = 1");
	$digit = $digitl + $digitr;
	$per1 = round($digitr/($digit)*100);
	
	$pr1 = ($per1 <= 50 ? round($per1*360/100) : 180);
	$pr2 = ($per1 > 50 ? round(($per1-50)*360/100) : 0);
?>
<div class="sid-back">
	<style>
	#pieSlice2 {-webkit-transform:rotate(180deg); -moz-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(50deg);}
	#pieSlice1 .pie {-webkit-transform:rotate(<?php echo $pr1; ?>deg); -moz-transform:rotate(<?php echo $pr1; ?>deg); -o-transform:rotate(<?php echo $pr1; ?>deg); transform:rotate(<?php echo $pr1; ?>deg);}
	#pieSlice2 .pie {-webkit-transform:rotate(<?php echo $pr2; ?>deg); -moz-transform:rotate(<?php echo $pr2; ?>deg); -o-transform:rotate(<?php echo $pr2; ?>deg); transform:rotate(<?php echo $pr2; ?>deg);}
	</style>
	<div class="pieLabel left"><div class="digit"><?php echo $digitl; ?></div>fiziniai asmenys</div><div class="pieContainer">
		<div class="pieHole"><div class="digit"><?php echo $digit; ?></div>pagalbos gavėjai</div>
		<div class="pieBackground slice2"></div>
		<div id="pieSlice1" class="hold"><div class="pie slice1"></div></div>
		<div id="pieSlice2" class="hold"><div class="pie slice1"></div></div>
	</div><div class="pieLabel right"><div class="digit"><?php echo $digitr; ?></div>juridiniai asmenys</div>
	<?php
	$digitl = countData("users", "user_active = 1 AND user_person = 0 AND user_acctype = 0");
	$digitr = countData("users", "user_active = 1 AND user_person = 1 AND user_acctype = 0");
	$digit = $digitl + $digitr;
	$per1 = round($digitr/($digit)*100);
	
	$pr1 = ($per1 <= 50 ? round($per1*360/100) : 180);
	$pr2 = ($per1 > 50 ? round(($per1-50)*360/100) : 0);
	?>
	<style>
	#pieSlice4 {-webkit-transform:rotate(180deg); -moz-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(50deg);}
	#pieSlice3 .pie {-webkit-transform:rotate(<?php echo $pr1; ?>deg); -moz-transform:rotate(<?php echo $pr1; ?>deg); -o-transform:rotate(<?php echo $pr1; ?>deg); transform:rotate(<?php echo $pr1; ?>deg);}
	#pieSlice4 .pie {-webkit-transform:rotate(<?php echo $pr2; ?>deg); -moz-transform:rotate(<?php echo $pr2; ?>deg); -o-transform:rotate(<?php echo $pr2; ?>deg); transform:rotate(<?php echo $pr2; ?>deg);}
	</style>
	<div style="padding: 10px;"></div>
	<div class="pieLabel left"><div class="digit"><?php echo $digitl; ?></div>fiziniai asmenys</div><div class="pieContainer">
		<div class="pieHole"><div class="digit"><?php echo $digit; ?></div>registruoti geradariai</div>
		<div class="pieBackground slice2"></div>
		<div id="pieSlice3" class="hold"><div class="pie slice1"></div></div>
		<div id="pieSlice4" class="hold"><div class="pie slice1"></div></div>
	</div><div class="pieLabel right"><div class="digit"><?php echo $digitr; ?></div>juridiniai asmenys</div>
</div>
<?php
endif;

if(isset($pgcon_exploded[1])) echo '<div class="sid-back sid-custom-con">'.$pgcon_exploded[1].'</div>';

?>
