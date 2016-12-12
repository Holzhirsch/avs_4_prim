var API_URL = "http://192.168.0.11/AVS_4/API.php";

function makeAjaxCall(URL, data) {
    var ajaxCom = new Ajax(URL, receive);
    receivedObj = {"response": 0};
    ajaxCom.send(data);
    ajaxCom.disconnect();
}

function getUpdateResponse() {
    var response = receivedObj.response;
    console.log(response);
    return response;
}

function sendInt() {
    var num = parseInt(document.getElementById('value').value);
    if(!Number.isInteger(num)) {
        console.log("not a number we want");
        return;
    }
    if(num <= 1) {
        return 1;
    }
    console.log(num);
    var data = {
        "function": "processNumber",
        "number": num
    };
    makeAjaxCall(API_URL, data);
//    getUpdateResponse();
}

function startServerExchange() {
    var data = {
        "function": "startRepoEx"
    };
    makeAjaxCall(API_URL, data);
}