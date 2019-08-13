var queueStatus=0;$(document).ready(function(){$.ajax({url:ccbNS.bungie_api_url+"/GroupV2/"+ccbNS.clan_id+"/Members/",headers:{"X-API-Key":ccbNS.bungie_api}}).done(function(e){e.Response.results&&e.Response.results.length>0?(memberData=e.Response.results,$(".loader-text").text("Fetching Weapon Stats..."),$.get("/bungie/weapon/get",function(e){for(var a=[],t=0;t<memberData.length;t++)$(".loader-text").text("Processing "+(t+1)+" of "+memberData.length+"..."),weaponData=e.filter(function(e){return e.user_id==memberData[t].destinyUserInfo.membershipId})[0],weaponData&&a.push({name:memberData[t].destinyUserInfo.displayName,weaponKillsAutoRifle:weaponData.weaponKillsAutoRifle,weaponKillsBow:weaponData.weaponKillsBow,weaponKillsFusionRifle:weaponData.weaponKillsFusionRifle,weaponKillsHandCannon:weaponData.weaponKillsHandCannon,weaponKillsTraceRifle:weaponData.weaponKillsTraceRifle,weaponKillsPulseRifle:weaponData.weaponKillsPulseRifle,weaponKillsRocketLauncher:weaponData.weaponKillsRocketLauncher,weaponKillsScoutRifle:weaponData.weaponKillsScoutRifle,weaponKillsShotgun:weaponData.weaponKillsShotgun,weaponKillsSniper:weaponData.weaponKillsSniper,weaponKillsSubmachinegun:weaponData.weaponKillsSubmachinegun,weaponKillsSideArm:weaponData.weaponKillsSideArm,weaponKillsSword:weaponData.weaponKillsSword,weaponKillsGrenadeLauncher:weaponData.weaponKillsGrenadeLauncher});$(".loader-text").text("Generating Table..."),$(".stats-container").append('<div id="weapon-stats-table"></div>'),$(".loader").hide(),$(".loader-text").hide();var l={precision:0};new Tabulator("#weapon-stats-table",{data:a,layout:"fitColumns",columns:[{title:"Name",field:"name",frozen:!0},{title:"Auto Rifle",field:"weaponKillsAutoRifle",formatter:"money",formatterParams:l},{title:"Bow",field:"weaponKillsBow",formatter:"money",formatterParams:l},{title:"Fusion Rifle",field:"weaponKillsFusionRifle",formatter:"money",formatterParams:l},{title:"Hand Cannon",field:"weaponKillsHandCannon",formatter:"money",formatterParams:l},{title:"Trace Rifle",field:"weaponKillsTraceRifle",formatter:"money",formatterParams:l},{title:"Pulse Rifle",field:"weaponKillsPulseRifle",formatter:"money",formatterParams:l},{title:"Rocket Launcher",field:"weaponKillsRocketLauncher",formatter:"money",formatterParams:l},{title:"Scout Rifle",field:"weaponKillsScoutRifle",formatter:"money",formatterParams:l},{title:"Shotgun",field:"weaponKillsShotgun",formatter:"money",formatterParams:l},{title:"Sniper",field:"weaponKillsSniper",formatter:"money",formatterParams:l},{title:"SMG",field:"weaponKillsSubmachinegun",formatter:"money",formatterParams:l},{title:"Sidearm",field:"weaponKillsSideArm",formatter:"money",formatterParams:l},{title:"Sword",field:"weaponKillsSword",formatter:"money",formatterParams:l},{title:"Grenade Launcher",field:"weaponKillsGrenadeLauncher",formatter:"money",formatterParams:l}],layout:"fitDataFill",height:"500px",resizableColumns:!1}),$(".stats-container").append('<div id="weapon-stats-info" class="text-center"><small>Last checked: '+weaponData.last_updated+"</small></div>")})):$(".loader-text").html("Unable to retrieve members 😟 <br/>Bungie API might be under maintenance")})});
