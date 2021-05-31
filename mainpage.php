<?php
session_start();

function logout()
{
    //unset PHP session variables
    unset($_SESSION["username"]);
    unset($_SESSION["password"]);
    header("Location: login.php");
    exit();
    // NOTE: session storage will be cleared at login.php
}

if (isset($_GET["logout"]))
{
    logout();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="css/mainpage.css?<?php echo time();?>">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&display=swap" rel="stylesheet">
    <title>GroupChat</title>
</head>
<body>
    
    <div class="header">
        <a href="" class="logo">GroupChat</a>
        <div class="header-right">
            <a href="mainpage.php?logout=true">logout</a>
        </div>
    </div>
    
    <h2 id="greeting">"loading session storage..."</h2>

    <div class="wrapper">
        <aside id="active_users">
            <ul id="user_status_list">
                <li class="user_status">loading array...</li>
            </ul>
        </aside>
        
        <div id="chat">
            <div id="chat_display"></div>
            <textarea id="chat_input" cols="30" rows="10" maxlength="200"></textarea>
            <button id="chat_send">Send</button>
        </div>
    </div>
    
    <script type="text/javascript">
    
        // TAB SESSION STORAGE
        // ===================
        if (sessionStorage.getItem("username") === null
            && sessionStorage.getItem("password") === null)
        {
            sessionStorage.setItem("username", "<?php echo $_SESSION['username']?>");
            sessionStorage.setItem("password", "<?php echo $_SESSION['password']?>");    
        }
        
        
        // SETTINGS
        // ========
        const MESSAGE_TYPE = ["connect", "chat", "disconnect", "ping"];
        var ACTIVE_USERS = [];
        
        
        // GREETING
        // ========
        var greetingTxt = "Welcome, " + sessionStorage.getItem("username");
        $("#greeting").text(greetingTxt);


        // HANDLE SERVER SOCKET
        // ====================
        var serverSocket = new WebSocket("ws://groupchat.cloud:49153/");
        
        serverSocket.onerror = function(event)
        {
            console.error("ERROR::could not connect to server socket: ", event);
        }

        serverSocket.onopen = function(event)
        {
            return;
        }

        serverSocket.onmessage = function(event)
        {
            var [messageType, dataObj] = parseEventData(event.data);

            switch(messageType)
            {
                case MESSAGE_TYPE[0]:
                    receiveNewConnection(dataObj);
                    break;
                case MESSAGE_TYPE[1]:
                    handleChatFromServer(dataObj);
                    break;
                case MESSAGE_TYPE[2]:
                    handleDisconnection(dataObj);
                    break;
                case MESSAGE_TYPE[3]:
                    replyToPing(dataObj);
                    break;
            }
        }


        // HANDLE CHAT EVENT
        // =================
        $("#chat_input").keyup(function(event)
        {
            if (event.which == 13)
            {
                handleChatToServer();
                clearChatInput();
            }
        });
        
        $("#chat_send").click(function() {
            handleChatToServer();
            clearChatInput();
        });
        
        function handleChatToServer()
        {
            var message = $.trim($("#chat_input").val());

            if (message != "")
            {
                var chatData = {
                    type: MESSAGE_TYPE[1],
                    username: sessionStorage.getItem("username"),
                    message: message,
                    timestamp: getTimestamp()
                };

                serverSocket.send(JSON.stringify(chatData));
            }
        }
        
        function clearChatInput()
        {
            $("#chat_input").val("");
        }


        // HELPER FUNCTIONS I
        // ==================
        function receiveNewConnection(dataObj)
        {
            updateActiveStatus(dataObj);
        }

        function handleDisconnection(dataObj)
        {
            updateActiveStatus(dataObj);
        }

        function handleChatFromServer(dataObj)
        {
            var username = dataObj.username;
            var message = dataObj.message;
            var timestamp = dataObj.timestamp;
            var display = username.concat(" (", timestamp, "): ", message);

            var msg = document.createElement("div");
            msg.className = "chat_message";
            msg.textContent = display;
            $("#chat_display").prepend(msg);
        }
        
        function replyToPing(dataObj)
        {
            var pingData = {
                type: "ping",
                username: sessionStorage.getItem("username"),
                resourceId: dataObj.resourceId
            };
            
            serverSocket.send(JSON.stringify(pingData));
        }
        
        
        // HELPER FUNCTIONS II
        // ===================
        function updateActiveStatus(dataObj)
        {
            var activeUsers = dataObj.activeUsers;
            ACTIVE_USERS = activeUsers;
            // first clear the current list
            $("#user_status_list").empty();
            
            for (let i = 0; i < ACTIVE_USERS.length; i++)
            {
                var activeUser = document.createElement("li");
                activeUser.className = "user_status";
                activeUser.innerHTML = "<img src='img/active.png?<?php echo time();?>'>" + ACTIVE_USERS[i];
                $("#user_status_list").append(activeUser);
            }
        }
        
        function parseEventData(stringifiedData)
        {
            var dataObj = JSON.parse(stringifiedData);
            var messageType = dataObj.type;
            return [messageType, dataObj];
        } 

        function getTimestamp() 
        {
            var d = new Date();
            var hh = d.getHours().toString();
            var mm = d.getMinutes().toString();
            
            if (mm.length == 1) {
                mm = '0'.concat(mm);
            }

            var res = hh.concat(":", mm);
            return res;
        }

    </script>
</body>
</html>