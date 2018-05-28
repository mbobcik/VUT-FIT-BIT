function setVisibility()
{
  console.log(document.cookie);
   var CookieName= "visible"
   var value=getCookie(CookieName);
   console.log(value);
   var element=document.getElementById("script-invisible");
    if(value == 1)
  {
    element.style.display="block";
    setcookie(CookieName,2,5);
  }
  else if(value == -1)
  {
    element.style.display="none";
    setcookie(CookieName,1,5);
  }
  else
  {
    element.style.display="none";
    setcookie(CookieName,1,5);
  }
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

//nenastavuj9 se mi Cookies :(
function setcookie(name, value, days)
{
  if (days)
  {
    var date = new Date();
    date.setTime(date.getTime()+days*24*60*60*1000); // ) removed
    var expires = "; expires=" + date.toGMTString(); // + added
  }
  else
    var expires = "";
  document.cookie = name+"=" + value+expires + ";path=/"; // + and " added
}