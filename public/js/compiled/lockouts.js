function makeTable(e,a,t){var s=["warlock","hunter","titan"];for(var r in e){for(var i=[],l=!1,n=0;n<a.length;n++){username=t.filter(function(e){return e.destinyUserInfo.membershipId==a[n].id}).map(function(e){return e.destinyUserInfo.displayName})[0],userData=JSON.parse(a[n].data);for(var o=0;o<s.length;o++)r+"p"in userData[s[o]]&&1==userData[s[o]][r+"p"]&&(userData[s[o]][r]=1);i.push({name:username+'<a href="https://raid.report/pc/'+a[n].id+'" target="_blank" class="text-dark"><i class="fas fa-external-link-alt ml-1 fa-xs" style="position: relative; bottom: 1px;"></i></a>',warlock:1==userData.warlock[r]?'<i class="fas fa-check text-success"></i>':'<i class="fas fa-times text-danger"></i>',hunter:1==userData.hunter[r]?'<i class="fas fa-check text-success"></i>':'<i class="fas fa-times text-danger"></i>',titan:1==userData.titan[r]?'<i class="fas fa-check text-success"></i>':'<i class="fas fa-times text-danger"></i>',total:userData.warlock[r]+userData.titan[r]+userData.hunter[r]}),(userData.warlock[r]||userData.hunter[r]||userData.titan[r])&&(l=!0)}$(".stats-container").append('<div id="'+r+'" class="col-md-12 mb-4 pt-md-5"><h2 class="text-yellow text-left mb-3 stats-container-header"><a href="#'+r+'">'+e[r]+"</a></h2></div>"),$("#"+r).append('<div id="'+r+'-stats-table"></div>'),$("#sub-menu > ul").append('<li class="nav-item"><a class="nav-link" href="#'+r+'">'+e[r]+(l?'<i class="fas fa-check text-success ml-2"></i>':"")+"</a></li>"),$(".loader").hide(),$(".loader-text").hide(),$("#sub-menu").show();new Tabulator("#"+r+"-stats-table",{data:i,columns:[{title:"Name",field:"name",formatter:"html",frozen:!0},{title:"Warlock",field:"warlock",formatter:"html",cssClass:"text-center"},{title:"Hunter",field:"hunter",formatter:"html",cssClass:"text-center"},{title:"Titan",field:"titan",formatter:"html",cssClass:"text-center"},{title:"Total",field:"total",cssClass:"text-center",visible:!1}],initialSort:[{column:"name",dir:"asc"},{column:"total",dir:"desc"}],layout:"fitColumns",height:"350px",resizableColumns:!1,responsiveLayout:!0})}}$(document).ready(function(){var e={cos:"Crown of Sorrows",sotp:"Scourge of the Past",lw:"Last Wish"},a={sosp:"Prestige Spire of Stars",sos:"Spire of Stars",eowp:"Prestige Eaters of World",eow:"Eaters of World",levip:"Prestige Leviathan",levi:"Leviathan"};$.ajax({url:ccbNS.bungie_api_url+"/GroupV2/"+ccbNS.clan_id+"/Members/",headers:{"X-API-Key":ccbNS.bungie_api}}).done(function(t){t.Response.results&&t.Response.results.length>0?(memberData=t.Response.results,$(".loader-text").text("Fetching Raid Lockouts..."),$.get("/clan/lockouts/get",function(t){$("#weekly-lockout-dates").html(t.start_of_week+" to "+t.end_of_week),$("#sub-menu > ul").append('<li class="nav-item pt-0 pt-md-4"><a class="nav-link disabled" href="#" aria-disabled="true">Year 2 Raids</a></li>'),makeTable(e,t.raid_lockouts,memberData),$("#sub-menu > ul").append('<li class="nav-item"><a class="nav-link disabled" href="#" aria-disabled="true">Year 1 Raids</a></li>'),makeTable(a,t.raid_lockouts,memberData),$(".stats-container").prepend('<div class="col-md-12 mb-3 mb-md-0 text-left"><small>Note: If your guardian is missing, make sure "Show my Destiny game Activity Feed on Bungie.net" is checked under your Bungie.net privacy settings.</small></div>')})):$(".loader-text").html("Unable to retrieve members 😟 <br/>Bungie API might be under maintenance")})});
