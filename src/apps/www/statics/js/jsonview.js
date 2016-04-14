function showJSON(obj, fold){
	fold = (typeof(arguments[1])=="undefined")?true:arguments[1];
	var defCss = "glyphicon-minus";
	if(fold) {
		defCss = "glyphicon-plus";
	}
	var html = "<span id=\"jsonview-obj-icon\" class=\"glyphicon " + defCss + "\" onclick=\"javascript:foldJSONObj(this);\" /> <span class=\"jsonview-obj-title\">&lt;ROOT&gt;</span>";
	return html + jsonObj2Html(obj, fold);
}

// 将json对象展示为ul-li格式的树形结构
function jsonObj2Html(obj, fold) {
	fold = (typeof(arguments[1])=="undefined")?true:arguments[1];
	var html;
	var defCss;
	if(fold) {
		html = "<ul class=\"jsonview-obj\" style=\"display:none;\">";
		defCss = "glyphicon-plus";
	}
	else {
		html = "<ul class=\"jsonview-obj\">";
		defCss = "glyphicon-minus";
	}
    for(var k in obj){
		html += "<li>";
        var v=obj[k];
		var t=typeof(v);
		if(!v){
			html = html + k + ": null";
		}
        else if(t=="object"){
			depth ++;
			// 在ul之前放一个图标span，并对此span对象添加点击操作，以隐藏或显示子内容
			html = html + "<span id=\"jsonview-obj-icon\" class=\"glyphicon "+defCss+"\" onclick=\"javascript:foldJSONObj(this);\" /> <span class=\"jsonview-obj-title\">" + k + ":</span>" + jsonObj2Html(v, fold);
			depth --;
        }
		else if(t=="string"){
            html = html + k + ": \"" + v +"\"";
        }
		else {
            html = html + k + ": " + v;
        }
		html += "</li>";
    }
	return html + "</ul>";
}

// 折叠或显示对象
function foldJSONObj(eventObj) {
	var $this = $(eventObj);
	var iconClassOpenName = "glyphicon-minus";
	var iconClassCloseName = "glyphicon-plus";
	var isFold = !$this.hasClass(iconClassOpenName);
	var $objUl = $this.parent().children("ul").eq(0);
	if(isFold) {
		$this.removeClass(iconClassCloseName);
		$this.addClass(iconClassOpenName);
		$objUl.slideDown();
	}
	else {
		$this.removeClass(iconClassOpenName);
		$this.addClass(iconClassCloseName);
		$objUl.slideUp();
	}
}
