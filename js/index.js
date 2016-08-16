function onhashchange() {
	var regex = new RegExp("^#(home|apply|about|videos|video(\@.+)?|stream|forum|members|(node(\@.+)?)|(profile\@.+\/.+\/.+)|statistics|rank)$", "i");
	var hash = window.location.hash;
	
	if(!hash && hash.length === 0)
		hash = "#home";
		
	$(".nav.navbar-nav").find("li").each(function() {
		$(this).removeClass("active");
	});
	
	if( regex.test(hash)) {
		var ref = hash.match(/(\@([\u00c0-\u00fc]|\w|\d|\_|-|=|\/)+)$/i);
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
		case "apply":
			$("#nav-apply").addClass("active");
			webi.content.load("views/apply.html");
			break;
		case "videos":
			$("#nav-videos").addClass("active");
			webi.content.load("views/videos.php");
			break;
		case "video":
			if( ref !== null ) {
				ref = ref[0].replace(/^\@/i, "");
				ref = ref.replace(/.*\//i, "");
				webi.content.load("views/video.php?tag=" + encodeURIComponent(ref));
			}
			break;
		case "about":
			$("#nav-about").addClass("active");
			webi.content.load("views/about.html");
			break;
		case "stream":
			$("#nav-stream").addClass("active");
			webi.content.load("views/stream.php");
			break;
		case "members":
			$("#nav-members").addClass("active");
			webi.content.load("views/members.php");
			break;
		case "node":
			if( ref !== null ) {
				ref = ref[0].replace(/^\@/i, "");
				ref = ref.replace(/.*\//i, "");
				$("#nav-guides").addClass("active");
				webi.content.load("views/guides.php?tag=" + encodeURIComponent(ref));
			}
			break;
		case "profile":
			if( ref !== null ) {
				ref = ref[0].replace(/^\@/i, "");
				ref = ref.replace(/\//gi, " ");
				var res = ref.split(" ");
				$("#nav-members").addClass("active");
				webi.content.load("views/profile.php?action=show&character=" + encodeURIComponent(res[2]) + "&server=" + encodeURIComponent(res[1]) + "&region=" + encodeURIComponent(res[0]) + "&metric=" + encodeURIComponent((res[3]) || ""));
			}
			break;
		case "statistics":
			$("#nav-statistics").addClass("active");
			webi.content.load("views/statistics.html");
			break;
		case "rank":
			$("#nav-rank").addClass("active");
			webi.content.load("views/rank.php");
			break;
		default:
			webi.content.load("views/notfound.html");
		}

	} else {
		webi.content.load("views/notfound.html");
	}
}

Number.prototype.round = function(places) {
  return +(Math.round(this + "e+" + places)  + "e-" + places);
}