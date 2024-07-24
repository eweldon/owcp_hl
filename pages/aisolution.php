	<?php
/**
 * View AI Attorney Solution
 * Version: 19JUN2024
 */


global $client_data;

// Get the field value "Elerion Services" from the client data

$services = $client_data->get("Elerion Services", "");

// if = yes, show the AI Tenant Solution page

if ($services == "Yes") {
	// use the plugin path + templates + aitenant.php, dont use ".."
	include_once OWCP_HA_BASE_PATH . 'templates/aitenant.php';
} else {
	// use the plugin path + templates + onboardingform.php, dont use ".."
	include_once OWCP_HA_BASE_PATH . 'templates/onboardingform.php';
}




