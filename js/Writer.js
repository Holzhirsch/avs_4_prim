function Logger() {
    this.allText = "";
    this.con = document.getElementById("output");
}
Logger.prototype.writeOut= function(text) {
    this.allText += text+"<hr>";
    this.con.innerHTML = this.allText;
};
function write2console(text) {
    this.wrt;
    if(this.wrt === undefined) {
        this.wrt = new Logger();
    }
    this.wrt.writeOut(text);
    console.log(text);
}

