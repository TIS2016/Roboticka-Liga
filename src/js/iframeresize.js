function sendResizeRequest() {
    var height = $("#wrapper").height();

    if (window.location != window.parent.location)
        window.top.postMessage(height, "http://www.fll.sk");
}

/* TOP WINDOW:
 window.addEventListener('message', function(event) {
 if (~event.origin.indexOf('http://kempelen.ii.fmph.uniba.sk')) {
 document.getElementById("lliframe").style.height = event.data+"px";
 } else {
 return;
 }
 });
 */

