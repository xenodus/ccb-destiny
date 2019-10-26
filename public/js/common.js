// Google Analytics
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'UA-35918300-6');

// Store globals inside our own NS
window.ccbNS = {};

ccbNS.platform_id = 3;
ccbNS.bungie_api_url = 'https://www.bungie.net/Platform';
ccbNS.bungie_api = '856136fabe704c149dd4bd41344b54c8';
ccbNS.clan_id = 3717919;

var lazyLoadInstance = new LazyLoad({
  elements_selector: ".lazy"
});

$(document).ready(function(){

  if( !('hideActivities' in ccbNS) ) {

    checkMembersOnline();

    setInterval(checkMembersOnline, 60000);

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
  }
});

function checkMembersOnline() {
  $.get('/api/activity', function(data){

    if( data.length > 0 ) {
      if( $("section#members-online").data("show") == "0" ) {
        $("section#members-online").slideToggle();
        $("section#members-online").data("show", "1");
      }

      if( $("header").hasClass("topBarMargin") == false ) {
        $("header").addClass("topBarMargin");
      }

      $("span#member-count").text( data.length + " member" + (data.length>1?"s":"") );

      var tableHtml = '<table><thead class="text-success"><tr><th class="pl-1 pr-1">Name</th><th class="pl-1 pr-1">Activity</th><th class="pl-1 pr-1">Last Seen</th></tr></thead>';
      var member_activities = [];

      for(var i=0; i<data.length; i++) {
        var member_activity = {
          name: data[i].displayName,
          lastSeen: data[i].lastSeen ? data[i].lastSeen : '',
          latestActivity: data[i].latestActivity.originalDisplayProperties.name ? data[i].latestActivity.originalDisplayProperties.name : '',
          activityDescription: data[i].latestActivity.originalDisplayProperties.description ? data[i].latestActivity.originalDisplayProperties.description : '',
          activityIcon: data[i].latestActivity.originalDisplayProperties.icon ? data[i].latestActivity.originalDisplayProperties.icon : '',
          activityStarted: data[i].latestActivityTime ? data[i].latestActivityTime : data[i].latestActivityTime
        };

        member_activities.push(member_activity);

        //tableHtml += '<tr><td class="pl-1 pr-1">'+data[i].displayName+'</td><td class="pl-1 pr-1">'+member_activity.latestActivity+'</td><td class="pl-1 pr-1">'+member_activity.lastSeen+'</td></tr>';
      }

      member_activities = _.sortBy(member_activities, ['latestActivity', 'name']);

      for(var i=0; i<member_activities.length; i++) {
        tableHtml += '<tr><td class="pl-1 pr-1">'+member_activities[i].name+'</td><td class="pl-1 pr-1">'+member_activities[i].latestActivity+'</td><td class="pl-1 pr-1">'+member_activities[i].lastSeen+'</td></tr>';
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