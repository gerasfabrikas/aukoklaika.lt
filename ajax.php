<?php
include("functions.php");
include("views/cities.php");

if(isset($_POST['getRegionChild']) and isset($regionsListChildren[$_POST['getRegionChild']])) :
	foreach($regionsListChildren[$_POST['getRegionChild']] as $children) :
		echo '<option value="'.$children.'">'.$citiesList[$children].'</option>';
	endforeach;
endif;
?>