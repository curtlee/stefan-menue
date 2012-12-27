<?php

class menuedienstLib {
	function generateRotatingMealPlan() {
		global $downloadDirectory;
		global $numberOfMeals;
		
		$menuHTML = '';
		$dateArray = getdate();
		$beginningOfCurrentWeek = mktime( 0, 0, 0, $dateArray['mon'], $dateArray['mday'] - $dateArray['wday'] + 1, $dateArray['year']);
		$oneDay = 24*60*60;
		for ( $i = 0; $i < $numberOfMeals; $i++ ) {
			$firstDayOfWeek = $beginningOfCurrentWeek + ($i*7*$oneDay);
			$lastDayOfWeek = $firstDayOfWeek + (6*$oneDay);

			$weekString = 'Woche vom ' . date('d.m.Y', $firstDayOfWeek) . ' - ' . date('d.m.Y', $lastDayOfWeek);
			$weekNo = (int) date('W', $lastDayOfWeek );
			$menuNo = (($weekNo-1) % 52) % $numberOfMeals + 1;

			$pdfFileName = $downloadDirectory . 'menu0' . $menuNo . '.pdf';
			$menuHTML .= '<div class="mealPlanItem">';
			if ( file_exists( $pdfFileName ) ) {
				$menuHTML .= '<a href="' . $pdfFileName . '" target="_blank">' . $weekString . '</a>';
			} else {
				$menuHTML .= $weekString;
			}
			$menuHTML .= '</div>';
		}
		$menuHTML = '<div class="mealPlan">' . $menuHTML . '</div>';
		return $menuHTML;
	}
	
	/**
	 * Handles the contact's form input if there was any
	 * it will send an email
	 *
	 * returns a error-message string to be displayed if some values are missing
	 */
	function handleContactForm() {
		$contactFormInfo = array();

		$contactFormInfo['message'] = isset($_POST['message']) ? strip_tags($_POST['message']) : '';
		$contactFormInfo['name'] = isset($_POST['name']) ? strip_tags($_POST['name']) : '';
		$contactFormInfo['emailSender'] = isset($_POST['email']) ? stripslashes(strip_tags($_POST['email'])) : '';
		$contactFormInfo['telefon'] = isset($_POST['telefon']) ? strip_tags($_POST['telefon']) : '';

		if ( !isset($_POST['type']) || ($_POST['type'] != 'email') ) {
			return $contactFormInfo;
		}
		
		if ( empty( $contactFormInfo['message'] ) || empty( $contactFormInfo['name'] ) || empty( $contactFormInfo['emailSender'] ) ) {
			$contactFormInfo['errormessage'] = '<div class="error">Bitte überprüfen Sie Ihre Eingaben auf Vollständigkeit.</div>';
			return $contactFormInfo;
		}
		
		$message = "Name: " . strval($contactFormInfo['name']) . "\nEmail: " . $contactFormInfo['emailSender'] . "\nTelefon: " . $contactFormInfo['telefon'] . "\n\nFrage:\n" . $contactFormInfo['message'];
		mail("info@menuedienst-rhein-sieg.de", "Email-Anfrage über www.menuedienst-rhein-sieg.de", $message, "From: ".$contactFormInfo['emailSender']."\nX-Mailer: PHP/" . phpversion(). "\nCC: ");
		
			// header redirect
		header('location: anfrage-erfolgreich.html');
		
		return $contactFormInfo;
	}
}

?>