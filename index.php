<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
        <title>Directory Service</title>
        <link rel="stylesheet" href="style/default.css">
    </head>
    <body>
        <div id='wrapper'>
            <button class='btn' onclick='getChat("room1")'>Chat 1</button>
            <button class='btn' onclick='getChat("room2")'>Chat 2</button>
            <button class='btn' onclick='getChat("room3")'>Chat 3</button>
            <div id='chat'>
                <div id='chatwindow'> </div>
                <textarea id='chatmessage' placeholder="Message:" ></textarea>
                <button class='btn' onclick='sendMessage()'>Send</button>
            </div>
            <div id='output'></div>
            <button class='btn' onclick='try1()'>1</button>
            <button class='btn' onclick='try2()'>2</button>
            <button class='btn' onclick='try3()'>3</button>
        </div>

        <script type='text/javascript' src='js/Ajax.js'></script>
        <script type='text/javascript' src='js/Writer.js'></script>
        <script type='text/javascript' src='js/Chat.js'></script>
        
        <?php
        $ip = !empty($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        echo "<script type='text/javascript'>",
        "ip_adress = '$ip';",
        "window.onload = load(ip_adress);",
        "</script>"
        ?>
        
    </body>
</html>
