(function ($) {
    $(document).ready(function () {

        // Define an array of status messages to simulate AI processing
        var statusMessages = [
            'Sorting through knowledge base...',
            'Querying the Hepworth Legal documents for relevant information...',
            'Building a detailed report...',
            'Analyzing previous case studies...',
            'Gathering expert opinions...',
        ];

        var statusMessagesAiReviewing = 'Our AI is reviewing and generating a response, this can take up to 10 seconds...';

        // Counter to cycle through status messages
        var messageIndex = 0;

        // get the URL parameters
        var urlParams = new URLSearchParams(window.location.search);

        // if tab = "aireviewing" then run check for updates every 10 seconds
        var tab = urlParams.get('tab');
        if (tab === 'aireviewing') {
            var currSecond = 10;
            //pass new url into checkForUpdates function, while checking for updates every 10 seconds
            setInterval(function () {
                var newUrl = window.location.href.replace('tab=aireviewing', 'tab=aisolution');
                checkForUpdates(newUrl);
                // set chat-input value to be a list of messages
                $('#chat-input').val(statusMessagesAiReviewing);
            }, 10 * 1000);

            // every 1 second, update the chat-input value to be a countdown
            var counter = setInterval(function () {
                $('#chat-input').val(statusMessagesAiReviewing + ' ' + currSecond + ' seconds remaining...');
                currSecond -= 1;

                if (currSecond < 0) {
                    currSecond = 10;
                    statusMessagesAiReviewing = "This is taking longer than expected. Please wait a moment...";
                }
            }, 1 * 1000);
        }

        if (tab == 'aisolution') {
            window.scrollTo(0, document.body.scrollHeight);
            // on firefox, inputs can be filled in from the previous page, so we need to clear the input
            $('#chat-input').val('');
        }

        /**
         * Handles the form submission for the chat input.
         * Prevents the default form submission, retrieves the message from the input field,
         * and sends it using the sendChatMessage function if the message is not empty.
         * 
         * @param {Event} e The event object representing the form submission.
         * @return {void}
         */
        $('#chat-form').on('submit', function (e) {
            e.preventDefault();
            var message = $('#chat-input').val();
            if (message === '') {
                return;
            }
            sendChatMessage(message, 'text');
        });

        /*
        * Set it to auto scroll to the last message sesnt
        */
        function scrollToLastChatBubble() {
            var chatWindow = $('.ha-chat-window');
            var lastChatBubble = chatWindow.find('.ha-chatbubble__wrapper:last-child');
            console.log(lastChatBubble);
            if (lastChatBubble.length) {

                chatWindow.scrollTop(lastChatBubble.offset().top - chatWindow.offset().top + chatWindow.scrollTop());
            }
        }

        if ($('.ha-chat-window').length > 0) {
            scrollToLastChatBubble();
        }


        /**
         * If the user presses the enter key, it triggers a popup confirmation, if the user confirms, it submits the form.
         */
        $('#chat-input').on('keypress', function (e) {
            if (e.which === 13) {
                e.preventDefault();
                // push a confirm dialog
                var confirmSubmit = confirm("Are you sure you want to submit this message?\nPress ENTER or click OK to confirm.");
                if (confirmSubmit) {
                    var message = $('#chat-input').val();
                    if (message === '') {
                        return;
                    }
                    sendChatMessage(message, 'text');
                }

            }
        });

        /**
         * When an action button is clicked, it retrieves the button ID and calls
         * the appropriate function based on the button's ID.
         * 
         * @return {void}
         */
        $('.action-button').on('click', function () {
            var buttonId = $(this).attr('id');
            // Call a function based on the button ID
            if (buttonId === 'speak_to_attorney') {
                sendChatMessage('I would like to speak to an attorney', 'OptionSelect');
            }
            // Add more functions for other buttons here
        });

        /**
         * When a selection button is clicked, it retrieves the selection data attribute
         * and sends a chat message based on the selection.
         *  
         * @return {void}
        **/
        $('.selection-button').on('click', function () {
            var selection = $(this).data('selection');

            // if the selection is yes, send a CalendyEmbed request
            if (selection === 'yes') {
                sendChatMessage("Yes, I'd like to schedule a consultation", 'CalendyEmbed');
            } else {
                sendChatMessage('No', 'text');
            }
        });

        function sendChatMessage(message, type) {
            var urlParams = new URLSearchParams(window.location.search);
            var recordId = urlParams.get('record_id');
            var access_token = urlParams.get('access_token');

            $.ajax({
                type: 'POST',
                url: ajax_object.ajax_url,
                data: {
                    action: 'owcp_ha_submit_chat_message',
                    message: message,
                    recordId: recordId,
                    access_token: access_token,
                    type: type
                },
            });

            handleSubmission(message);

        }

        /**
         * Sends a chat message to the server using an AJAX POST request.
         * Retrieves the `record_id` and `access_token` from the URL parameters and sends them along with the message.
         * Calls the `handleSubmission` function after sending the message.
         * 
         * @param {string} message The message content to be sent.
         * @param {string} type The type of the message (e.g., 'text', 'OptionSelect', 'CalendyEmbed').
         * @return {void}
         */
        function handleSubmission($message) {
            // append the message to the chat window
            var chatWindow = $('.ha-chat-window');

            // paramterize the above html string with message
            var messageDiv = `<div class="d-flex justify-content-end mb-4 ha-chatbubble__wrapper" usergroup="user"><span class="ha-chatbubble-icon"><i class="fa-regular fa-user" aria-hidden="true"></i></span><div class="p-2 rounded ha-chatbubble ha-chatbubble-client">${$message}</div></div>`;

            scrollToLastChatBubble();
            
            chatWindow.append(messageDiv);

            // Set the chat input to "A message has been sent. Please wait for a response."
            $('#chat-input').val('A message has been sent. Please wait for a response.');

            // Disable the chat input and the submit button
            $('#chat-input').prop('disabled', true);
            $('#chat-submit').prop('disabled', true);

            // set interval for 5 seconsds to cycle through status messages
            var statusInterval = setInterval(() => {
                // set the chat-input value to be a list of messages
                $('#chat-input').val(statusMessages[messageIndex]);
                messageIndex = (messageIndex + 1) % statusMessages.length;
                checkForUpdates();
            }, 5 * 1000);

            setTimeout(function () {
                clearInterval(statusInterval);
                var errorMessage = "There was an error processing your request. Please try again later.";
                var errorDiv = `<div class="d-flex justify-content-start mb-2" usergroup="ai"><div class="p-2 rounded ha-chatbubble-ai">${errorMessage}</div></div>`;
                chatWindow.append(errorDiv);
            }, 60 * 1000);
        }

        /**
         * Checks for updates in the chat by sending an AJAX POST request.
         * Retrieves the user group from the last message in the chat window and the record ID from the URL.
         * If there are new updates, it reloads the page.
         * 
         * @requires Assumes user submitted message and is waiting for a response
         * 
         * @return {void}
         */
        function checkForUpdates($urlParams) {

            // get the recordId from the URL
            var urlParams = new URLSearchParams(window.location.search);
            var recordId = urlParams.get('record_id');
            var access_token = urlParams.get('access_token');

            $.ajax({
                type: 'POST',
                url: ajax_object.ajax_url,
                data: {
                    action: 'owcp_ha_check_for_updates',
                    userGroup: "user",
                    recordId: recordId,
                    access_token: access_token
                },
                success: function (response) {
                    if (response == "true") {
                        // if $urlParams is not empty, redirect to the new url
                        $('#chat-input').val('Your reply is ready! Reloading the page...');
                        if ($urlParams) {
                            window.location.href = $urlParams;
                            // remove interval
                            clearInterval(counter);
                        } else {
                            // if $urlParams is empty, reload the page
                            location.reload();
                        }
                    }
                }
            });

        }
    });
})(jQuery)