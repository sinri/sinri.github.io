function sayHello(name) {	//var name=document.getElementById('hello_ajax_input_text').value;	alertGetFromAJAX("http://sinri.users.sourceforge.net/HelloAPI/hello.php?name="+name);}function jsonpHello(name) {$.ajax({    url : "http://sinri.users.sourceforge.net/HelloAPI/hello.php?name="+name,    dataType:"jsonp",    //jsonp:"mycallback",    success:function(data)    {        alert("get: "+data);    }});}