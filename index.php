<?php

	// the file name from the url (no path, just filename, should be secure)
global $requestedFilename;
$requestedFilename = basename($_SERVER['REQUEST_URI']);

$template = file_get_contents("template/mainTemplate.html");
$trees = simplexml_load_file("content/pagesConfig.xml");

	// query XML via XPath to find all topMenu pages
$topMenuPages = $trees->xpath("/trees/tree[@title='topMenu']/page");
$topMenu = generateMenu($topMenuPages);
$leftMenuPages = $trees->xpath("/trees/tree[@title='leftMenu']/page");
$leftMenu = generateMenu($leftMenuPages);
	// check if page exists in xml
$requestedPageInXML = $trees->xpath("/trees/tree/page[@src='$requestedFilename']");
if ( count($requestedPageInXML) && file_exists('content/'.$requestedFilename) ) {
	$content = file_get_contents('content/'.$requestedFilename);
} else { // page not found
	$content = file_get_contents('content/404.html');
}

$template = str_replace('###TOP_MENU###', $topMenu, $template);
$template = str_replace('###LEFT_MENU###', $leftMenu, $template);
$template = str_replace('###CONTENT###', $content, $template);

echo $template;


function generateMenu($pages) {
	global $requestedFilename;
	
	$menu = "<ul>\n";
	foreach( $pages as $page ) {
		if ( (string) $page['type'] == 'separator' ) {
			$menu .= '<li class="separator"></li>'."\n";
		} else {
			$class = ($requestedFilename == (string) $page['src']) ? 'selected' : '';
			$menu .= '<li class="'.$class.'"><a href="'.(string)$page['src'].'">'.(string)$page['navTitle']."</a></li>\n";
		}
	}
	$menu .= '</ul>';
	return $menu;
}

?>