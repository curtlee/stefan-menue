<?php

require_once('config.php');
require_once('library.php');

	// the file name from the url (no path, just filename, should be secure)
global $requestedFilename;
$requestedFilename = basename($_SERVER['REQUEST_URI']);

$template = file_get_contents("template/mainTemplate.html");
$trees = simplexml_load_file("content/pagesConfig.xml");

	// query XML via XPath to find all topMenu pages
$topMenuPages = $trees->xpath("/trees/tree[@title='topMenu']/page");
$leftMenuPages = $trees->xpath("/trees/tree[@title='leftMenu']/page");
	// check if page exists in xml
if ( substr($requestedFilename, -5, 5) == ".html" ) {
	$requestedPageInXML = $trees->xpath("/trees/tree/page[@src='$requestedFilename']");
} else {
	$requestedPageInXML = $trees->xpath("/trees/tree/page[@default='1']");
	$requestedFilename = (string)$requestedPageInXML[0]['src'];
}
if ( count($requestedPageInXML) && file_exists('content/'.$requestedFilename) ) {
	$content = file_get_contents('content/'.$requestedFilename);
} else { // page not found
	$content = file_get_contents('content/404.html');
}

$topMenu = generateMenu($topMenuPages);
$leftMenu = generateMenu($leftMenuPages);
$keyvisualStyle = isset($requestedPageInXML) ? "background: url('" . (string) $requestedPageInXML[0]['keyvisual'] . "');" : '';
$backgroundStyle = isset($requestedPageInXML) ? "background-image: url('" . (string) $requestedPageInXML[0]['background'] . "');" : '';

$rotatingMealPlan = menuedienstLib::generateRotatingMealPlan();
$contactFormInfo = menuedienstLib::handleContactForm();
$contactFormErrors = isset($contactFormInfo['errormessage']) ? $contactFormInfo['errormessage'] : '';

$template = str_replace('###TOP_MENU###', $topMenu, $template);
$template = str_replace('###LEFT_MENU###', $leftMenu, $template);
$template = str_replace('###CONTENT###', $content, $template);
$template = str_replace('###KEYVISUAL_STYLE###', $keyvisualStyle, $template);
$template = str_replace('###BACKGROUND_STYLE###', $backgroundStyle, $template);

$template = str_replace('###ROTATING_MEAL_PLAN###', $rotatingMealPlan, $template);
$template = str_replace('###CONTACT_FORM_ERRORS###', $contactFormErrors, $template);
$template = str_replace('###CONTACT_FORM_NAME###', $contactFormInfo['name'], $template);
$template = str_replace('###CONTACT_FORM_MESSAGE###', $contactFormInfo['message'], $template);
$template = str_replace('###CONTACT_FORM_EMAIL###', $contactFormInfo['emailSender'], $template);
$template = str_replace('###CONTACT_FORM_TELEFON###', $contactFormInfo['telefon'], $template);

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