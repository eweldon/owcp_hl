<?php
/**
 * View Application Page
 * Version: 8JUN2024
 */

global $client_data;
global $record_id;
global $portal;

$record_id = $client_data->get("Record ID", "");
$application_id = isset($_GET['app_id']) ? $_GET['app_id'] : null;
$login_url = OWCPLogin::logged_in_url();

$first_name = $client_data->get("First Name");
$first_name = $client_data->get("Last Name");


// create a test array that looks like this [{userGroup:"user", content:"Hello"}, {userGroup:"agent", content:"Hi"]
// convert json string to array
$conversation_history = json_decode(
    $client_data->get(
        "Elerion Conversation",
        "[]"
    ),
    true
);

// Define your action buttons
$action_buttons = array(
    array(
        'id' => 'speak_to_attorney',
    'label' => "Want to speak to an attorney?",
    ),
);

?>

<div class="pb-5">
            <?php render_progress_bar(3) ?>
        <div class="mt-5 owcpc_container">
            <div class="owcpc_divider"><p><span>Chat</span></p></div>
            <div class="ha-chat-window rounded p-3">
                <?php
                foreach ($conversation_history as $message) {
                    render_chat_message($message);
                }
                ?>
            </div>
        </div>
        <div class="position-relative owcpc_input__wrapper">
            <div class="owcpc_action-btn__wrapper">
                <!-- Render the action buttons -->
                <?php foreach ($action_buttons as $button): ?>
                    <button id="<?php echo $button['id']; ?>" class="btn owcp_btn action-button"><?php echo $button['label']; ?></button>
                <?php endforeach; ?>
            </div>
            <form id="chat-form" class="owcp_chat-input">

                <textarea id="chat-input" class="form-control" placeholder="Type a message..."></textarea>
                <button type="submit" class="btn owcp_btn"><i class="fa-solid fa-paper-plane"></i></button>
            </form>
        </div>
    
</div>