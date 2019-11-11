/**
 * @author Marx
 */
function loadscript(url)
{
  	jQuery.ajax({
  	    url: url,
  	    crossDomain: true,
  	    dataType: "script",
  	    success: function () {
  	        // script is loaded
  	    	console.log( url+" via ajax. Load was performed." );
  	    },
  	    error: function () {
  	        // handle errors
  	    	console.log( url+" via ajax. Load error." );
  	    }
  	});	
}

function loadjsonp(url)
{
	console.log( "load json for url:"+url );
	jQuery.ajax({
  	    url: url,
  	    crossDomain: true,
  	    type:"GET",
  	    contentType: "application/json",
  	    dataType: "jsonp", 
  	    jsonpCallback: 'callback'
  	});	
}

function loadjsonpPost(url,postData)
{
	console.log( "load json for url:"+url );
	jQuery.ajax({
  	    url: url,
  	    crossDomain: true,
  	    type:"POST",
  	    contentType: "application/json",
  	    data : postData,
  	    jsonpCallback: 'callback'
  	});	
}