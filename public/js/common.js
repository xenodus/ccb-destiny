$(document).ready(function(){
  checkMembersOnline();

  setInterval(checkMembersOnline, 10000);

  $( "section#members-online" ).on("click", function() {
    if( $("small#members-online-toggle-icon").data("status") == "up" ) {
      $("small#members-online-toggle-icon").html('<i class="fas fa-times fa-lg animated rotateIn delay-0.5s"></i>');
      $("small#members-online-toggle-icon").data("status", "down");

      $("body").data("scroll", 0);
      $("body").addClass("no-scroll");
    }
    else {
      $("small#members-online-toggle-icon").html('<i class="fas fa-chevron-down fa-lg animated rotateIn delay-0.5s"></i>');
      $("small#members-online-toggle-icon").data("status", "up");

      $("body").data("scroll", 1);
      $("body").removeClass("no-scroll");
    }
    $("div#members-online-table").slideToggle();
  });
});

function checkMembersOnline() {
  $.get('/activity', function(data){

    console.log(data);

    if( data.length > 0 ) {
      if( $("section#members-online").data("show") == "0" ) {
        $("section#members-online").slideToggle();
        $("section#members-online").data("show", "1");
      }

      if( $("header").hasClass("topBarMargin") == false ) {
        $("header").addClass("topBarMargin");
      }

      $("span#member-count").text( data.length + " member" + (data.length>1?"s":"") );

      var tableHtml = '<table><thead class="text-success"><tr><th>Name</th><th>Activity</th><th>Last Seen</th></tr></thead>';

      for(var i=0; i<data.length; i++) {
        var member_activity = {
          name: data[i].displayName,
          lastSeen: data[i].lastSeen,
          latestActivity: data[i].latestActivity.originalDisplayProperties.name,
          activityDescription: data[i].latestActivity.originalDisplayProperties.description ? data[i].latestActivity.originalDisplayProperties.description : '',
          activityIcon: data[i].latestActivity.originalDisplayProperties.icon,
          activityStarted: data[i].latestActivityTime
        }

        tableHtml += '<tr><td class="pl-1 pr-1">'+data[i].displayName+'</td><td class="pl-1 pr-1">'+member_activity.latestActivity+'</td><td class="pl-1 pr-1">'+member_activity.lastSeen+'</td></tr>';
      }

      tableHtml += '</table>';

      $("#members-online-table").html(tableHtml);
    }
    else {
      if( $("section#members-online").data("show") == "1" ) {
        $("section#members-online").slideToggle();
        $("section#members-online").data("show", "0");
      }

      if( $("header").hasClass("topBarMargin") ) {
        $("header").removeClass("topBarMargin");
      }
    }
  });
}