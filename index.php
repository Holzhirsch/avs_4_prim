<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
        <title>Prime Service</title>
        <link rel="stylesheet" href="style/default.css">
    </head>
    <body>
        <div id='wrapper'>
            <input type="text" name="value" id='value' />
            <input type="button" value="submit" onclick="sendInt()" />
            <div id='result'></div>
            <div id='output'></div>
            <button class='btn' onclick='startServerExchange()'>1</button>
            <div>
                Start Server_Repo_Exchange: <br>
                http://192.168.0.11/AVS_4/API.php?function=startRepoEx
            </div>
        </div>

        <script type='text/javascript' src='js/Ajax.js'></script>
        <script type='text/javascript' src='js/Writer.js'></script>
        <script type='text/javascript' src='js/prime.js'></script>
    </body>
</html>
