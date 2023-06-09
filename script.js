"></script>
    <script>
        $(document).ready(function () {
            // Submit form when user presses Enter key
            $('#message-input').keypress(function (e) {
                if (e.which === 13) {
                    e.preventDefault();
                    submitForm();
                }
            });

            // Handle form submission
            $('#message-form').submit(function (e) {
                e.preventDefault();
                submitForm();
            });

            // Function to submit form and process user message
            function submitForm() {
                var messageInput = $('#message-input');
                var message = messageInput.val().trim();

                if (message !== '') {
                    // Clear input field
                    messageInput.val('');

                    // Display user message on the page
                    displayMessage('User: ' + message);

                    // Send user message to backend for processing
                    $.ajax({
                        url: 'backend.php',
                        method: 'POST',
                        data: {message: message},
                        success: function (response) {
                            // Display assistant response on the page
                            displayMessage(response);
                        },
                        error: function () {
                            displayMessage('Error: Failed to communicate with the server.');
                        }
                    });
                }
            }

            // Function to display messages on the page
            function displayMessage(message) {
                var chatContainer = $('#chat-container');
                var messageElement = $('<div class="message">' + message + '</div>');
                chatContainer.append(messageElement);

                // Scroll to the bottom of the chat container
                chatContainer.scrollTop(chatContainer[0].scrollHeight);
            }
        });
    </script>
