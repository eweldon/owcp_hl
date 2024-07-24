<?php

include_once 'chat-types/text.php';
include_once 'chat-types/calendy.php';
include_once 'chat-types/select.php';

function render_chat_message($message) {
    $userGroup = $message['userGroup'];
    $alignment = $userGroup === 'user' ? 'justify-content-end' : 'justify-content-start';
    $image_folder_url = plugin_dir_url(dirname(__FILE__)) .'assets/images/';
    // embed the usergroup in the div class
    echo "<div class='d-flex $alignment mb-4 ha-chatbubble__wrapper' userGroup=$userGroup>";
        echo '<span class="ha-chatbubble-icon">' . ($userGroup === 'user' ? '<i class="fa-regular fa-user"></i>' : '<img src="' . $image_folder_url . 'logo-icon.png">') . '</span>';
        if ($userGroup === 'user') {
            display_message_text($message);
        } else {
            switch ($message['type']) {
                case 'CalendyEmbed':
                    display_message_calendy($message);
                    break;
                case 'OptionSelect':
                    display_message_select($message);
                    break;
                default:
                    display_message_text($message);
                    break;
            }
        }
    echo "</div>";
}
