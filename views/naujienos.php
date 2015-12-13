<?php

$page_id = (isset($_GET['blogid']) ? $_GET['blogid'] : false);
$page_site = 0;

if($page_id) :
	$pagerow = getRow('pages', "page_id = $page_id AND (page_site = $page_site OR page_site = 2) AND deleted = 0 AND page_published < NOW()");
	if(!$pagerow) :
		err('Naujiena neegzistuoja');
		return;
	endif;
else :

	$where = "SELECT * FROM pages WHERE page_type = 1 AND (page_site = $page_site OR page_site = 2) AND deleted = 0 AND page_published < NOW() ORDER BY page_published DESC";
	
	echo '<ul>';
	foreach(listData(false, false, pageNum(), $where) as $key => $data) :
		echo '<li><a href="/naujienos/'.$data['page_id'].'">'.$data['page_name'].'</a></li>';
	endforeach;
	echo '</ul>';
	pagination2(countData(false, false, $where), 10, 'naujienos/psl');
	return;
endif;

if($pagerow) :
	echo '<h2>'.$pagerow['page_name'].'</h2>';
	echo $pagerow['page_content'];
endif;

?>