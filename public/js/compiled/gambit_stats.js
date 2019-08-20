var queueStatus=0,infamyRanks=["Guardian I","Guardian II","Guardian III","Brave I","Brave II","Brave III","Heroic I","Heroic II","Heroic III","Fabled I","Fabled II","Fabled III","Mythic I","Mythic II","Mythic III","Legend"];function formatNumber(e){const t=[{divider:1e18,suffix:"E"},{divider:1e15,suffix:"P"},{divider:1e12,suffix:"T"},{divider:1e9,suffix:"G"},{divider:1e6,suffix:"M"},{divider:1e3,suffix:"k"}];for(var a=0;a<t.length;a++)if(e>=t[a].divider)return parseFloat(e/t[a].divider).toFixed(2)+" "+t[a].suffix;return e.toString()}$(document).ready(function(){$.ajax({url:ccbNS.bungie_api_url+"/GroupV2/"+ccbNS.clan_id+"/Members/",headers:{"X-API-Key":ccbNS.bungie_api}}).fail(function(){$(".loader-text").html("Unable to retrieve members 😟 <br/>Bungie API might be under maintenance")}).done(function(e){e.Response.results&&e.Response.results.length>0?(memberData=e.Response.results,$(".loader-text").text("Fetching Gambit Stats..."),$.get("/bungie/gambit/get",function(e){for(var t=[],a=0;a<memberData.length;a++)$(".loader-text").text("Processing "+(a+1)+" of "+memberData.length+"..."),gambitData=e.filter(function(e){return e.user_id==memberData[a].destinyUserInfo.membershipId})[0],gambitData&&(infamy_rank=gambitData.infamy_step==infamyRanks.length?infamyRanks[infamyRanks.length-1]:infamyRanks[gambitData.infamy_step],t.push({name:memberData[a].destinyUserInfo.displayName,infamy:gambitData.infamy,infamy_step:infamy_rank,infamy_resets:gambitData.infamy_resets,kills:gambitData.kills,deaths:gambitData.deaths,kd:gambitData.killsDeathsRatio,activitiesEntered:gambitData.activitiesEntered,activitiesWon:gambitData.activitiesEntered>0?gambitData.activitiesWon/gambitData.activitiesEntered*100:0,invasionKills:gambitData.invasionKills,invaderKills:gambitData.invaderKills,invaderDeaths:gambitData.invaderDeaths,primevalHealing:gambitData.primevalHealing,primevalDamage:gambitData.primevalDamage,motesDeposited:gambitData.motesDeposited,motesLost:gambitData.motesLost,motesDenied:gambitData.motesDenied}));$(".loader-text").text("Generating Table..."),$(".stats-container").append('<div id="gambit-stats-table"></div>'),$(".loader").hide(),$(".loader-text").hide(),$(".filter-container").show();var i={precision:0};new Tabulator("#gambit-stats-table",{data:t,layout:"fitColumns",columns:[{title:"Name",field:"name",frozen:!0},{title:"Infamy",field:"infamy",formatter:"money",formatterParams:i},{title:"Infamy Rank",field:"infamy_step"},{title:"Resets",field:"infamy_resets",formatter:"money",formatterParams:i},{title:"Kills",field:"kills",formatter:"money",formatterParams:i},{title:"Deaths",field:"deaths",formatter:"money",formatterParams:i},{title:"KD",field:"kd",formatter:"money",formatterParams:{precision:2}},{title:"Played",field:"activitiesEntered",formatter:"money",formatterParams:i},{title:"Win %",field:"activitiesWon",formatter:"money",formatterParams:i},{title:"Invasion Kills",field:"invasionKills",formatter:"money",formatterParams:i},{title:"Invaders Killed",field:"invaderKills",formatter:"money",formatterParams:i},{title:"Deaths by Invader",field:"invaderDeaths",formatter:"money",formatterParams:i},{title:"Primeval Healing (%)",field:"primevalHealing",formatter:"money",formatterParams:i},{title:"Primeval Damage",field:"primevalDamage",formatter:"money",formatterParams:i},{title:"Motes Banked",field:"motesDeposited",formatter:"money",formatterParams:i},{title:"Motes Lost",field:"motesLost",formatter:"money",formatterParams:i},{title:"Motes Denied",field:"motesDenied",formatter:"money",formatterParams:i}],initialSort:[{column:"name",dir:"asc"}],layout:"fitDataFill",height:"500px",resizableColumns:!1}),$(".stats-container").append('<div id="weapon-stats-info" class="text-center"><small>Last checked: '+gambitData.last_updated+"</small></div>")})):$(".loader-text").html("Unable to retrieve members 😟 <br/>Bungie API might be under maintenance")})});
