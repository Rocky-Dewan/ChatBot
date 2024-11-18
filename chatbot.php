<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot for Dewan High School</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>

<body>
    <div class="wrapper">
        <div class="title">
            <img src="logo.png" alt="School Logo" style="height: 50px; vertical-align: middle; margin-right: 10px;">
            Chat With DHS
        </div>

        <div class="form">
            <div class="bot-inbox inbox">
                <div class="icon">
                    <img src="bot-profile.jpg" alt="Bot Profile" class="profile-img">
                </div>
                <div class="msg-header">
                    <p>Hello there, how can I help you?</p>
                    <span class="msg-time"><span class="current-day-time"></span>, <span class="current-time"></span></span> <!-- Time next to the message -->
                </div>
            </div>
        </div>

        <div class="typing-field">
            <div class="input-data">
                <a href="http://localhost/HSMS/" class="back-btn" style="text-decoration: none; color: #fff; background-color: #4CAF50; padding: 10px 20px; border-radius: 5px; font-size: 14px;">Back</a>
                <input id="data" type="text" placeholder="Type something here.." required>
                <button id="send-btn">Send</button>
            </div>
        </div>
    </div>

    <script>
        // Function to get current date and time in "Day, Month Date, Year, HH:MM AM/PM" format
        function getCurrentTime() {
            const date = new Date();
            const options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' };
            let time = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }); // 12-hour format with AM/PM
            let day = date.toLocaleDateString('en-US', options); // Get full date with day name, month as short (e.g., Nov)
            return { day: day, time: time };
        }

        function sendMessage() {
            const $value = $("#data").val().trim();
            if ($value !== "") {
                const currentTime = getCurrentTime();
                const $msg = 
                    '<div class="user-inbox inbox">' +
                    '   <div class="icon">' +
                    '       <img src="user-profile.png" alt="User Profile" class="profile-img">' +
                    '   </div>' +
                    '   <div class="msg-header"><p>' + $value + '</p><span class="msg-time">' + currentTime.day + ', ' + currentTime.time + '</span></div>' +
                    '</div>';
                $(".form").append($msg);
                $("#data").val('');

                // start ajax code
                $.ajax({
                    url: 'message.php',
                    type: 'POST',
                    data: 'text=' + $value,
                    success: function(result) {
                        const botTime = getCurrentTime();
                        const $replay = 
                            '<div class="bot-inbox inbox">' +
                            '   <div class="icon">' +
                            '       <img src="bot-profile.jpg" alt="Bot Profile" class="profile-img">' +
                            '   </div>' +
                            '   <div class="msg-header"><p>' + result + '</p><span class="msg-time">' + botTime.day + ', ' + botTime.time + '</span></div>' +
                            '</div>';
                        $(".form").append($replay);
                        $(".form").scrollTop($(".form")[0].scrollHeight);
                    }
                });
            }
        }

        // Trigger send button on click
        $("#send-btn").on("click", sendMessage);

        // Trigger send button on Enter key press
        $("#data").on("keypress", function(e) {
            if (e.which == 13) {  // 13 is the Enter key
                sendMessage();
                return false; // Prevent form submission
            }
        });

        // Function to update real-time day and time dynamically for each message
        function updateRealTime() {
            const currentTime = getCurrentTime();
            $(".current-day-time").text(currentTime.day); // Set the current date (Day, Month Date, Year)
            $(".current-time").text(currentTime.time); // Set the current time (HH:MM AM/PM)
        }

        // Call the function to update time dynamically when the page loads
        $(document).ready(function() {
            updateRealTime();
        });
    </script>
</body>

</html>
