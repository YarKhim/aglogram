<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebSocket Example</title>
</head>

<body>
    <h1>WebSocket Example</h1>
    <input type="text" id="messageInput" placeholder="Type your message here..." />
    <button id="sendButton">Send</button>
    <div id="messages"></div>

    <script>
        const socket = new WebSocket('ws://localhost:8888/socket');

        socket.onopen = function() {
            console.log('Connected to the WebSocket server');
        };

        socket.onmessage = function(event) {
            const messagesDiv = document.getElementById('messages');
            messagesDiv.innerHTML += '<p>' + event.data + '</p>';
        };

        document.getElementById('sendButton').addEventListener('click', function() {
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value;
            socket.send(message);
            messageInput.value = '';
        });

        socket.onclose = function() {
            console.log('Disconnected from the WebSocket server');
        };
    </script>
</body>

</html>
