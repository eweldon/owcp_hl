<?php

function display_message_calendy($message) {
    $userGroup = $message['userGroup'];
    $messageClass = $userGroup === 'user' ? 'ha-chatbubble-client' : 'ha-chatbubble-agent';

    echo "<div class='p-2 ha-chat-bubble $messageClass'>";
    echo '<iframe src="https://calendly.com/hepworth/30min?embed_domain=www.hepworthlegal.com&amp;embed_type=Inline&amp;hide_event_type_details=1&amp;hide_gdpr_banner=1&amp;primary_color=a8845e&amp;name=&amp;email=" width="500px" height="550px" frameborder="0" title="Select a Date &amp; Time - Calendly"></iframe>';
    echo "</div>";
}