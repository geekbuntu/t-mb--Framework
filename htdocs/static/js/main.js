function ieHover()
{
	var nav = document.getElementById("navigation");
	if (nav)
	{
		var nodes = nav.getElementsByTagName("li");
		for (var i=0; i<nodes.length; i++)
		{
			nodes[i].onmouseover = function()
			{
				this.className += " hover";
				if(this.getElementsByTagName('ul').length > 0)
					hideSelectBoxes(this);
			}
			nodes[i].onmouseout = function()
			{
				this.className = this.className.replace(" hover", "");
				if(this.getElementsByTagName('ul').length > 0)
					showSelectBoxes(this);
			}
		}
	}
}

if (window.attachEvent && !window.opera){
	window.attachEvent("onload", ieHover);
}
