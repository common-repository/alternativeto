jQuery(document).ready(function ($) {
    //$("#a2widget").html('<div style="text-align: center; padding: 20px 0 20px 0; font-weight: bold;"><div style="margin-bottom: 5px;text-align: center;">Loading AlternativeTo Widget</div><img src="http://alternativeto.net/resources/gfx/ajax-loader.gif"></div>');

    $(".a2options").each(function (index, value) {
		var a2widget = $(this).next();
		a2widget.css("display", "none");
		//if ($('#a2options').length != 0) {
        var a2options = $(this).val().split(",");
        var a2app = a2options[1];
        var a2count = a2options[0];
        var a2category = a2options[2];
        var a2theme = a2options[3];
        var is_preview = a2options[4];
        var a2license = a2options[5];
        var a2platform = a2options[6];

        var ref = "?ref=plugin";

        var callUrl = "http://api.alternativeto.net/" + a2category + "/" + a2app + "/?count=" + a2count;
        
        if(a2platform != ''){
           callUrl += "&platform=" + a2platform;
        }
        
        if(a2license != ''){
           callUrl += "&license=" + a2license;
        }

        callUrl += "&jsoncallback=?";

        if (is_preview == 1) { ref = ""; };
        $.ajax({ type: "GET",
            dataType: "json",
            url: callUrl,
            success: function (data) {
                var sHtml = "";

                if (a2theme == "default") {
                    sHtml = "<div class='a2header'><div class='a2header-left'><div class='a2icon'><img src=" + data.IconUrl + " />";
                    sHtml += "<div class='a2likes a2likes-top'>" + data.Votes + "<span>likes</span></div></div><h2>";
                    sHtml += "<a rel='nofollow' href='" + data.Url + ref + "'>" + data.Name + "</a></h2>";
                    sHtml += "<p class='a2desc'>" + data.ShortDescription + "</p></div>";
                    sHtml += "<div class='a2header-right'><h2>Platforms</h2><span>" + a2_getPlatformString(data.Platforms, false) + "</span><hr>";
                    sHtml += "<h2>License</h2><span>" + a2_getLicenseString(data.License) + "</span></div>";
                    sHtml += "<br style='clear: both;' />";
                    sHtml += "</div><div class='a2list-content'>";
                    sHtml += "<h2>The best alternatives to " + data.Name + " from <a rel='nofollow' href='http://alternativeto.net'>AlternativeTo</a></h2>";
                    sHtml += "<ul id='alt2-list'>";

                    $.each(data.Items, function (i, item) {
                        sHtml += '<li><div class="a2likes">' + item.Votes + '<span>likes</span></div><a rel="nofollow" href="' + item.Url + ref + '">' + a2_cutString(item.Name) + '</a> <em>' + a2_getLicenseString(item.License) + ' | ' + a2_getPlatformString(item.Platforms, true) + '</em></li>';
                    });

                    sHtml += "</ul><br style='clear: both' /></div>";
                } else if (a2theme == "small") {
                    sHtml = "<div class='a2header'><div class='a2icon'><img src=" + data.IconUrl + " /></div>";
                    sHtml += "<h2><a rel='nofollow' href='" + data.Url + ref + "'>" + data.Name + "</a></h2>";
                    sHtml += "<em>" + data.Votes + " likes, " + a2_getLicenseString(data.License, false) + " for " + a2_getPlatformString(data.Platforms) + "</em></div><div class='a2list-content'>";
                    sHtml += "<h2>The best alternatives to " + data.Name + " from <a  rel='nofollow' href='http://alternativeto.net'>AlternativeTo</a></h2>";
                    sHtml += "<ul id='alt2-list'>";

                    $.each(data.Items, function (i, item) {
                        sHtml += '<li><a  rel="nofollow" href="' + item.Url + ref + '">' + a2_cutString(item.Name) + '</a> <em>' + item.Votes + ' likes | ' + a2_getLicenseString(item.License) + ' | ' + a2_getPlatformString(item.Platforms, true) + '</em></li>';
                    });

                    sHtml += "</ul><br style='clear: both' /></div>";

                } else if (a2theme == "minimal") {
                    sHtml = data.Name + " alternatives: ";
                    $.each(data.Items, function (i, item) {
                        sHtml += '<a rel="nofollow" href="' + item.Url + ref + '">' + item.Name + '</a>, ';
                    });

                    sHtml = sHtml.slice(0, -2);
                }
                console.log(a2widget);
				a2widget.html(sHtml);
                a2widget.css("display", "block");
                return;
            }
        });
    });
});

function a2_getPlatformString(platform, shortVersion) {
    if (shortVersion)
        return platform.join(", ").replace("Windows", "Win");
    else
        return platform.join(", ");
}

function a2_getLicenseString(license) {
    return license.replace('Free with limited functionality', 'Free (Limited)');
}

function a2_cutString(itemName) {
    var name;
    if (itemName.length > 24) {
        name = itemName.substring(0, 24) + "...";
    } else {
        name = itemName;
    }

    return name;
}