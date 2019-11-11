// JS script 

function coretest()
{
	console.log("executing core test");
}
function ajaxlink(linkname,linkurl)
{
	 console.log("executing ajaxlink "+linkname+" "+linkurl);
	
  	 var linkHtml = '<span id="'+linkname+'" style="display:block;"><a href="'+linkurl+'"></a></span>';
  	 jQuery("body").prepend(linkHtml);

  	 console.log("prepending link="+linkHtml);
  	 //Make the link ajaxify.
  	 jQuery("#"+linkname).ajaxify();

  	
  	 jQuery("#"+linkname+" a").trigger("click");
  	 //ga('send', 'pageview');
  	 console.log("send pageview");

  	 //event.preventDefault();
	
}

function noajaxlink(linkname,linkurl)
{
	 console.log("executing ajaxlink "+linkname+" "+linkurl);
	
  	 var linkHtml = '<span id="'+linkname+'" style="display:block;"><a href="'+linkurl+'" class="no-ajaxy"></a></span>';
  	 jQuery("body").prepend(linkHtml);

  	 console.log("prepending link="+linkHtml);
  	 //Make the link ajaxify.
  	 //jQuery("#"+linkname).ajaxify();
  	
  	 jQuery("#"+linkname+" a").trigger("click");
  	 //ga('send', 'pageview');
  	 console.log("send pageview");

  	 //event.preventDefault();
	
}



function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}