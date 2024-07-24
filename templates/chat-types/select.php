<?php

function display_message_select($message) {
    // get content from message
    $content = $message['content'];

   ?>
    <div class="chat-message ha-chat-message-select p-2 rounded ha-chatbubble ha-chatbubble-agent">
        <div class="chat-message-content">
            <h4>Have an attorney review your issue.</h4>
            <p>An attorney will provide expert insight and personalized advice that ensures every aspect of your case is thoroughly examined and strategically planned. Schedule a consultation today to secure the best possible resolution for your tenant issue.</p>
            <div class="d-flex">
                <button class="btn owcp_btn selection-button" data-selection="yes">Schedule A Consult</button>
                <!-- <button class="btn owcp_btn selection-button" data-selection="no">I'd like to hire an attourny to join the chat.</button> -->
            </div>
        </div>
    </div>
<?php
}