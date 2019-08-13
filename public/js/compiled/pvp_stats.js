var queueStatus=0,valorRanks=["Guardian","Brave","Heroic","Fabled","Mythic","Legend"],gloryRanks=["Guardian I","Guardian II","Guardian III","Brave I","Brave II","Brave III","Heroic I","Heroic II","Heroic III","Fabled I","Fabled II","Fabled III","Mythic I","Mythic II","Mythic III","Legend"];$(document).ready(function(){$.ajax({url:ccbNS.bungie_api_url+"/GroupV2/"+ccbNS.clan_id+"/Members/",headers:{"X-API-Key":ccbNS.bungie_api}}).done(function(e){e.Response.results&&e.Response.results.length>0?(memberData=e.Response.results,$(".loader-text").text("Fetching PvP Stats..."),$.get("/bungie/pvp/get",function(e){for(var a=[],t=0;t<memberData.length;t++)$(".loader-text").text("Processing "+(t+1)+" of "+memberData.length+"..."),pvpData=e.filter(function(e){return e.user_id==memberData[t].destinyUserInfo.membershipId})[0],pvpData?(glory_rank=pvpData.glory_step==gloryRanks.length?gloryRanks[gloryRanks.length-1]:gloryRanks[pvpData.glory_step],valor_rank=pvpData.valor_step==valorRanks.length?valorRanks[valorRanks.length-1]:valorRanks[pvpData.valor_step],a.push({name:memberData[t].destinyUserInfo.displayName,kd:pvpData.kd,kda:pvpData.kda,kad:pvpData.kad,glory:pvpData.glory,glory_step:glory_rank,valor:pvpData.valor,valor_step:valor_rank,valor_resets:pvpData.valor_resets,super_kills:pvpData.weaponKillsSuper,melee_kills:pvpData.weaponKillsMelee,grenade_kills:pvpData.weaponKillsGrenade})):(a.push({name:memberData[t].destinyUserInfo.displayName,kd:0,kda:0,kad:0,glory:0,glory_step:"",valor:0,valor_step:"",valor_resets:0,super_kills:0,melee_kills:0,grenade_kills:0}),console.log("Unable to retrieve PVP data for: "+memberData[t].destinyUserInfo.displayName));$(".loader-text").text("Generating Table..."),$(".stats-container").append('<div id="pvp-stats-table"></div>'),$(".loader").hide(),$(".loader-text").hide();var r={precision:0};new Tabulator("#pvp-stats-table",{data:a,layout:"fitColumns",columns:[{title:"Name",field:"name",formatter:"money",formatterParams:r,frozen:!0},{title:"KD",field:"kd",formatter:"money",formatterParams:{precision:2}},{title:"KDA",field:"kda",formatter:"money",formatterParams:{precision:2}},{title:"KAD",field:"kad",formatter:"money",formatterParams:{precision:2}},{title:"Glory",field:"glory",formatter:"money",formatterParams:r},{title:"Glory Rank",field:"glory_step"},{title:"Valor",field:"valor",formatter:"money",formatterParams:r},{title:"Valor Rank",field:"valor_step"},{title:"Valor Resets",field:"valor_resets",formatter:"money",formatterParams:r},{title:"Super Kills",field:"super_kills",formatter:"money",formatterParams:r},{title:"Melee Kills",field:"melee_kills",formatter:"money",formatterParams:r},{title:"Grenade Kills",field:"grenade_kills",formatter:"money",formatterParams:r}],initialSort:[{column:"glory",dir:"desc"}],layout:"fitDataFill",height:"500px",resizableColumns:!1}),$(".stats-container").append('<div id="weapon-stats-info" class="text-center"><small>Last checked: '+pvpData.last_updated+"</small></div>")})):$(".loader-text").html("Unable to retrieve members 😟 <br/>Bungie API might be under maintenance")})});
