<?php

function display_message_text($message) {
    $userGroup = $message['userGroup'];
    $content = htmlspecialchars($message['content']);
    $alignment = $userGroup === 'user' ? 'justify-content-end' : 'justify-content-start';
    $messageClass = $userGroup === 'user' ? 'ha-chatbubble-client' : 'ha-chatbubble-agent';

    // remove any instance of INITIAL_MESSAGE in the content
    $content = str_replace('INITIAL_MESSAGE', '', $content);
    

    echo "<div class='p-2 rounded ha-chatbubble $messageClass'>" . htmlspecialchars_decode($content) . "</div>";
}