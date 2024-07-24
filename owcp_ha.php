<?php
/**
 * Plugin Name: H&A CRM Child Plugin
 * Plugin URI: https://ow.consulting
 * Description: Hepworth HL AI Tenant Portal Child Plugin
 * Version: 1.0
 * Author: OWConsulting
 * Author URI: https://ow.consulting
 */


function owcp_ha_on_plugin_activate()
{
	update_option(
		"owcp_settings_option_name",
		array(
			"owcp_api_key" => "patgOsDUHjLSVn5RW.9fa30eda9eaaafc00493723c6394b8052fb91f333868138a12209fb60f47cd7a",
			"owcp_base_id" => "appS0LhkvZkx6CCOQ",
			"owcp_table_id" => "tblBkjcP05kC6C4CZ",
			"botpress_api_key" => "bp_pat_HYzYFs5Zm0GZuQWM7F5MOuMjIQuNNPE21jdD",
			"botpress_user_id" => "dcbffe57-87b4-46ae-bbf1-f2c38880031e",
			"botpress_integreation_id" => "87b01760-ede8-49d5-afc6-6afc0d0d1bdb",
			"auth0_client_id" => "",
			"auth0_client_secret" => "",
			"auth0_sub_domain" => "",
			"airtable_table_ids" => array(
			),
			"hide_header_nav" => false,
			"plugin_ou" => "hepworth",
			"admin_login" => '',
			"auth_method" => "custom",
			'twilio_sid' => "AC77eb868d121d91f484f37c8864996178",
			'twilio_token' => "d037962fe138f8c816df1f7953ae4eb2",
			'twilio_from_phone' => '+18018727531',
			'from_mail' => '',
			'from_name' => ''
		)
	);
}

include 'templates/chat-element.php';
include 'templates/ai_step.php';

register_activation_hook(__FILE__, 'owcp_ha_on_plugin_activate');
define('OWCP_LOGO_URL', 'https://www.hepworthlegal.com/wp-content/uploads/2023/01/hepworth-logo-updated.svg');

// define base path
define('OWCP_HA_BASE_PATH', plugin_dir_path(__FILE__));
define("OWCP_HA_AI_TENANT_FORM_ID", 27);

function owcp_ha_add_pages($owcportal)
{
	$pages = array(
		new OWCPage('AI Tenant Solution', 'aisolution', true, '' ),
		new OWCPage("Attorney Tenant Solution", 'attorneysolution' , false, ''),
		new OWCPage("AI Reviewing Form", 'aireviewing', false, '', true ),
		

	);

	$owcportal->add_pages(plugin_dir_path(__FILE__), $pages);
}
add_action('owcp_portal_add_pages', 'owcp_ha_add_pages');


function owcp_ha_setting_scripts()
{
	wp_register_style('owcp_ha_css', plugin_dir_url(__FILE__) . '/assets/css/style.css', '', time());
	wp_enqueue_style('owcp_ha_css');
	wp_enqueue_script('owcp_ha_js', plugin_dir_url(__FILE__) . '/assets/js/script.js', array('jquery'), time(), true);
	wp_localize_script('owcp_ha_js', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

}
add_action('wp_enqueue_scripts', 'owcp_ha_setting_scripts');

/** 
 * Register the needed variables for this child plugin to the parent portal instance to use
 */
function owcp_ha_add_child_vars($owcportal)
{


}
add_action('owcp_add_child_vars', 'owcp_ha_add_child_vars');


/**
 * Add a custom class to the body tag when the URL path is 'portal'.
 * This is used to apply custom styles to the portal pages.
 */
function owcp_ha_add_custom_class_for_portal($classes) {
    // Get the current URL path
    $current_url_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

    // Check if the URL path is 'portal'
    if ($current_url_path === 'portal') {
        $classes[] = 'owcp-portal';
    }
    return $classes;
}
add_filter('body_class', 'owcp_ha_add_custom_class_for_portal');


/**
 * Handles the submission of the onboarding form.
 * Validates the form data and sends a POST request to Botpress.
 * Uses the Botpress API key from WordPress options.
 *
 * @return void
 */
function owcp_ha_submit_onboarding_form($entry, $form) {
	
	// parse the form responses
	$responses = get_form_responses($entry, $form);

	// get "record_id" from the form
	$recordId = $responses['record_id'];

	// build a message payload where its "label: value]\n"
	$messagePayload = "INITIAL_MESSAGE\n";
	// unset record_id and auto-login-key
	
	unset($responses['record_id']);
	unset($responses['auto-login-key']);

	//$messagePayload = "";

	foreach ($responses as $label => $value) {
		$messagePayload .= "$label: $value<br>";
	}

	// get client data from airtable
	$record_query = OWCP::fetch_airtable_data("{Record ID} = '$recordId'");

	if (count($record_query) == 0) {
		error_log("Record not found");
		//wp_die();
	}

	$client_data = new RecordContainer($record_query[0]["fields"]);

	// send inital message to botpress
	$botpressResponse = owcp_ha_send_botpress_chat($recordId, ['text' => $messagePayload]);

	// now update the record with the conversation history with the initial message

	$client_data->set("Elerion Conversation", json_encode([[
		'userGroup' => 'user',
		'content' => $messagePayload,
		'type' => 'text'
	]]));

	// set "Elerion Services" to "Yes" to indicate the user has completed the onboarding form
	$client_data->set("Elerion Services", "Yes");

	// check if there was an error
	if (isset($botpressResponse['error'])) {
		error_log("Error sending message to botpress: " . $botpressResponse['error']);
		//wp_die();
	}

	//wp_die(); // this is required to terminate immediately and return a proper response
}
add_action("gform_after_submission_" . OWCP_HA_AI_TENANT_FORM_ID, "owcp_ha_submit_onboarding_form", 10, 2);

/**
 * Handles chat message submission for a specified record ID.
 * Updates the conversation history and sends the message to Botpress.
 * Validates the record and sends a POST request to Botpress if the record is found.
 *
 * @return void
 */
function owcp_ha_submit_chat_message()
{
	$message = $_POST['message'];
	$recordId = $_POST['recordId'];
	$type = $_POST['type'] ?? 'text';


	// get the record 
	$record_query = OWCP::fetch_airtable_data("{Record ID} = '$recordId'");

	if (count($record_query) == 0) {
		error_log("Record not found");
		wp_die();
	}

	$client_data = new RecordContainer($record_query[0]["fields"]);

	$conversation_history = json_decode($client_data->get("Elerion Conversation", "[]"), true);

	$conversation_history[] = array(
		'userGroup' => 'user',
		'content' => $message,
		'type' => $type
	);

	$messagePayload = [
		'text' => $message
	];

	$client_data->set("Elerion Conversation", json_encode($conversation_history));
	
	// submit the message to botpress
	$botpressResponse = owcp_ha_send_botpress_chat($recordId, $messagePayload);

	// check if there was an error
	if (isset($botpressResponse['error'])) {
		error_log("Error sending message to botpress: " . $botpressResponse['error']);
		wp_die();
	}

	

	wp_die(); // this is required to terminate immediately and return a proper response
}
add_action('wp_ajax_owcp_ha_submit_chat_message', 'owcp_ha_submit_chat_message');
add_action('wp_ajax_nopriv_owcp_ha_submit_chat_message', 'owcp_ha_submit_chat_message');

/**
 * Sends a chat message to Botpress for a specified record ID.
 * Constructs the message payload and sends a POST request to the Botpress webhook URL.
 * Uses the Botpress API key from WordPress options.
 *
 * @param string $recordId The ID of the record (user) to send the message to.
 * @param array $messagePayload The message payload containing the text to send.
 * @param string $type The type of message, default is 'text'.
 * 
 * @return string|void Returns an error message if the request fails.
 */
function owcp_ha_send_botpress_chat($recordId, $messagePayload, $type = 'text')
{
	$url = 'https://webhook.botpress.cloud/2b6f2dd7-e01a-4c2e-bd95-ffa03177162c';
	$tagsObject = empty($tags) ? new stdClass() : (object) $tags;
	// the userId will be "plugin_ou_recordId"
	$user_id = get_option('owcp_settings_option_name')['plugin_ou'] . "_" . $recordId;
	$data = [
		'text' => $messagePayload['text'],
		'userId' => $user_id,
		'conversationId' => $user_id,
		'messageId' => uniqid(),
		'type' => $type,
	];

	// get the botpress api key
	$botpressApiKey = get_option('owcp_settings_option_name')['botpress_api_key'];

	$jsonData = wp_json_encode($data);

	// Set up request arguments
	$args = [
		'body' => $jsonData,
		'headers' => [
			'Content-Type' => 'application/json',
			'Authorization' => 'Bearer ' . $botpressApiKey,
		],
		'method' => 'POST',
		'data_format' => 'body'
	];

	// Send the POST request
	$response = wp_remote_post($url, $args);

	// Check for errors
	if (is_wp_error($response)) {
		return 'Error: ' . $response->get_error_message();
	}

}

/**
 * Checks for new updates in a user's conversation history.
 * Retrieves the record based on the provided record ID and compares the 
 * user group of the last message to the current user group.
 * If the user groups differ, it indicates the ai has sent a new message.
 *
 * @return void
 */
function owcp_ha_check_for_updates() {
	// get the userGroup
	$userGroup = $_POST['userGroup'];
	$recordId = $_POST['recordId'];
	
	// get the record
	$record_query = OWCP::fetch_airtable_data("{Record ID} = '$recordId'");

	if (count($record_query) == 0) {
		error_log("Record not found");
		wp_die();
	}

	$client_data = new RecordContainer($record_query[0]["fields"]);

	$conversation_history = json_decode($client_data->get("Elerion Conversation", "[]"), true);

	// get the last message
	$lastMessage = end($conversation_history);

	error_log("Last message: " . json_encode($lastMessage));
	error_log("User group: " . $userGroup);

	// check if the userGroups match, if they don't there is a new message and return true to the response
	if ($lastMessage['userGroup'] !== $userGroup) {
		echo "true";
	} else {
		echo "false";
	}

	wp_die();
}
add_action('wp_ajax_owcp_ha_check_for_updates', 'owcp_ha_check_for_updates');
add_action('wp_ajax_nopriv_owcp_ha_check_for_updates', 'owcp_ha_check_for_updates');

/**
 * Generates a key-value array of the form labels and their corresponding responses.
 *
 * @param array $form The form structure containing field details.
 * @param array $entry The entry data containing user responses.
 * @return array The key-value array of labels and responses.
 */
function get_form_responses($entry, $form) {
    $response_data = array();

    foreach ($form['fields'] as $field) {
        $field_id = $field['id'];
        $label = $field['label'];

        if (isset($entry[$field_id])) {
            $response = $entry[$field_id];

            // Handle different field types (e.g., name, radio with multiple choices)
            switch ($field['type']) {
                case 'name':
                    // Concatenate name fields
                    $name = [];
                    foreach ($field['inputs'] as $input) {
                        if (isset($entry[$input['id']])) {
                            $name[] = $entry[$input['id']];
                        }
                    }
                    $response_data[$label] = implode(' ', $name);
                    break;

                case 'radio':
                    // Handle radio buttons
                    foreach ($field['choices'] as $choice) {
                        if ($choice['value'] == $response) {
                            $response_data[$label] = $choice['text'];
                            break;
                        }
                    }
                    break;

                default:
                    // Handle other field types
                    $response_data[$label] = $response;
                    break;
            }
        }
    }

    return $response_data;
}
