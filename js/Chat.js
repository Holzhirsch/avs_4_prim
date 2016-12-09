
var API_URL = "http://192.168.0.11/AVS_3/API.php";

var chat_room = "";
var ip_adress = "";
var last_message = 0;
var update_interval;

function load(ip) {
    ip_adress = (ip_adress === "::1") ? "127.0.0.1" : ip_adress;
    write2console("Your IP: " + ip);
}

function getChat(room) {
    resetLastMessages();
    resetChatWindow();
    resetUpdateIntervall();
    makeChatWindowVisible();
    setActiveChatRoom(room);
    setUpdateInterval();
}

function chatUpdate() {
    var data = {
        "function": "getUpdate",
        "chat_room": chat_room,
        "ip": ip_adress,
        "last_msg": last_message
    };

    makeAjaxCall(API_URL, data);
    if (!isEmptyUpdate()) {
        appendToChatWindow(getUpdateResponse());
        updateLastChatMessageCount();
    }
}

function sendMessage() {
    chatUpdate();
    var message = getChatMessage();
    if (isEmptyMessage(message)) {
        return;
    }

    var data = {
        "function": "setMessage",
        "chat_message": message,
        "chat_room": chat_room,
        "ip": ip_adress
    };

    makeAjaxCall(API_URL, data);
    resetChatMessageWindow();
    chatUpdate();
}

function isEmptyUpdate() {
    var messages = getUpdateResponse().split("<br>");
    return messages[0] === "";
}

function updateLastChatMessageCount() {
    last_message = receivedObj.message_nr;
}

function getUpdateResponse() {
    var response = receivedObj.response.replace(/(?:\r\n|\r|\n)/g, "<br />");
    return response;
}

function isEmptyMessage(message) {
    message = message.replace(/%0A/g, "");
    return !message;
}

function getChatMessage() {
    var message = document.getElementById("chatmessage").value;
    message = encodeChatMessage(message);
    return message;
}

function encodeChatMessage(message) {
    return encodeURIComponent(message);
}

function resetChatMessageWindow() {
    document.getElementById("chatmessage").value = "";
}

function setUpdateInterval() {
    update_interval = setInterval(function () {
        chatUpdate();
    }, 2000);
}

function setActiveChatRoom(room) {
    chat_room = room;
}

function resetUpdateIntervall() {
    clearInterval(update_interval);
}

function resetLastMessages() {
    last_message = 0;
}

function makeAjaxCall(URL, data) {
    var ajaxCom = new Ajax(URL, receive);
    receivedObj = {"response": 0, "message_nr": 0};
    ajaxCom.send(data);
    ajaxCom.disconnect();
}

function appendToChatWindow(text) {
    var objDiv = getChatWindow();
    objDiv.innerHTML = objDiv.innerHTML + text;
}

function resetChatWindow() {
    var objDiv = getChatWindow();
    objDiv.innerHTML = "";
}

function getChatWindow() {
    return document.getElementById("chatwindow");
}

function makeChatWindowVisible() {
    document.getElementById("chat").style.display = "block";
}

function startServerExchange() {
    var data = {
        "function": "startRepoEx",
        "ip": ip_adress
    };
    makeAjaxCall(API_URL, data);
}
