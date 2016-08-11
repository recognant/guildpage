function onhashchange() {
	var regex = new RegExp("^#(home|author|about|videos|forum|members|(node(\@.+)?)|rankings)$", "i");
	var hash = window.location.hash;
	
	if(!hash && hash.length === 0)
		hash = "#videos";
		
	$(".nav.navbar-nav").find("li").each(function() {
		$(this).removeClass("active");
	});
	
	if( regex.test(hash)) {
		var ref = hash.match(/(\@(\w|\d|\_|-|=)+)$/i);
		hash = hash.replace(/^#/i, "");
		hash = hash.replace(/(\@.*)/i, "");
		
		switch(hash) {
		case "forum":
			window.location.href = "http://seelenwanderer.forumprofi.de/index.php";
			break;
		case "home":
			$("#nav-home").addClass("active");
			webi.content.load("views/home.html");
			break;
		case "author":
			$("#nav-author").addClass("active");
			webi.content.load("author/index.php");
			break;
		case "videos":
			$("#nav-videos").addClass("active");
			webi.content.load("views/videos.php");
			break;
		case "about":
			$("#nav-about").addClass("active");
			webi.content.load("views/about.html");
			break;
		case "members":
			$("#nav-members").addClass("active");
			webi.content.load("views/members.php");
			break;
		case "node":
			$("#nav-author").addClass("active");
			if( ref !== null ) {
				ref = ref[0].replace(/^\@/i, "");
				ref = ref.replace(/.*\//i, "");
				if( ref !== "" ) {
					webi.content.load("author/node/index.php?tag=" + encodeURIComponent(ref));
				}
				break;
			}
		case "rankings":
			$("#nav-rankings").addClass("active");
			webi.content.load("views/rankings.php");
			break;
		default:
			webi.content.load("views/notfound.html");
		}

	} else {
		webi.content.load("views/notfound.html");
	}
}