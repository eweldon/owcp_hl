<?php render_progress_bar(2) ?>
<div class="mt-3 owcpc_container">
    <div class="owcpc_divider">
        <p><span>Chat</span></p>
    </div>
    <div class="ha-chat-window rounded p-3">
        <div class="d-flex justify-content-start mb-4 ha-chatbubble__wrapper" usergroup="ai"><span
                class="ha-chatbubble-icon"><img src="/wp-content/plugins/owcp_hl/assets/images/logo-icon.png"></span>
            <div class="p-2 rounded ha-chatbubble ha-chatbubble-agent">
                <h5>Our AI is reviewing your details and generating a recommended solution. Please do not refresh the page.</h5>
            </div>
    </div>
    <div class="row mb-5">
        <div class="col-12 text-center">
            <form id="chat-form" class="owcp_chat-input is-loading">
                <textarea id="chat-input" class="form-control"
                    placeholder="Type a message...">Reviewing documents</textarea>
                <button type="submit" class="btn owcp_btn"><i class="fa-solid fa-paper-plane"></i></button>
            </form>
        </div>
    </div>
</div>