var queueStatus = 0;

$(document).ready(function(){

  print_weapon_stats();

  function print_weapon_stats() {
    $.ajax({
      url: ccbNS.bungie_api_url+'/GroupV2/'+ccbNS.clan_id+'/Members/',
      headers: {
        'X-API-Key': ccbNS.bungie_api
      }
    })
    .fail(function(){
      $('.loader-text').html('Unable to retrieve members 😟 <br/>Bungie API might be under maintenance');
    })
    .done(function(data){

      if( data.Response.results && data.Response.results.length > 0 ) {

        memberData = data.Response.results;

        $('.loader-text').text('Fetching Weapon Stats...');

        $.get('/bungie/weapon/get', function(memberWeaponData){

          var tableData = [];

          for(var i=0; i<memberData.length; i++) {

            $('.loader-text').text('Processing ' + (i+1) + ' of ' + memberData.length + '...');

            weaponData = memberWeaponData.filter(function(member){ return member.user_id == memberData[i].destinyUserInfo.membershipId })[0];

            if( weaponData ) {

              // Decode html entities
              var txt = document.createElement("textarea");
              txt.innerHTML = memberData[i].destinyUserInfo.displayName;
              var steamID = txt.value;

              tableData.push({
                name: steamID,
                weaponKillsAutoRifle: weaponData.weaponKillsAutoRifle,
                //weaponKillsBeamRifle: weaponData.weaponKillsBeamRifle,
                weaponKillsBow: weaponData.weaponKillsBow,
                weaponKillsFusionRifle: weaponData.weaponKillsFusionRifle,
                weaponKillsHandCannon: weaponData.weaponKillsHandCannon,
                weaponKillsTraceRifle: weaponData.weaponKillsTraceRifle,
                weaponKillsPulseRifle: weaponData.weaponKillsPulseRifle,
                weaponKillsRocketLauncher: weaponData.weaponKillsRocketLauncher,
                weaponKillsScoutRifle: weaponData.weaponKillsScoutRifle,
                weaponKillsShotgun: weaponData.weaponKillsShotgun,
                weaponKillsSniper: weaponData.weaponKillsSniper,
                weaponKillsSubmachinegun: weaponData.weaponKillsSubmachinegun,
                //weaponKillsRelic: weaponData.weaponKillsRelic,
                weaponKillsSideArm: weaponData.weaponKillsSideArm,
                weaponKillsSword: weaponData.weaponKillsSword,
                weaponKillsGrenadeLauncher: weaponData.weaponKillsGrenadeLauncher,
              });
            }
          }

          $('.loader-text').text('Generating Table...');

          $('.stats-container').append('<div id="weapon-stats-table"></div>');

          $('.loader').hide();
          $('.loader-text').hide();
          $('.filter-container').show();

          var autoNumFormatter = function(){
            return $("#weapon-stats-table .tabulator-row").length;
          };

          var format = {precision: 0};

          var table = new Tabulator("#weapon-stats-table", {
            data:tableData, //assign data to table
            layout:"fitColumns", //fit columns to width of table (optional)
            columns:[ //Define Table Columns
              //{formatter:autoNumFormatter, width:40},
              {title:"Name", field:"name", frozen:true},
              {title:"Auto Rifle", field:"weaponKillsAutoRifle", formatter:"money", formatterParams: format},
              //{title:"Beam Rifle", field:"weaponKillsBeamRifle", formatter:"money", formatterParams: format},
              {title:"Bow", field:"weaponKillsBow", formatter:"money", formatterParams: format},
              {title:"Fusion Rifle", field:"weaponKillsFusionRifle", formatter:"money", formatterParams: format},
              {title:"Hand Cannon", field:"weaponKillsHandCannon", formatter:"money", formatterParams: format},
              {title:"Trace Rifle", field:"weaponKillsTraceRifle", formatter:"money", formatterParams: format},
              {title:"Pulse Rifle", field:"weaponKillsPulseRifle", formatter:"money", formatterParams: format},
              {title:"Rocket Launcher", field:"weaponKillsRocketLauncher", formatter:"money", formatterParams: format},
              {title:"Scout Rifle", field:"weaponKillsScoutRifle", formatter:"money", formatterParams: format},
              {title:"Shotgun", field:"weaponKillsShotgun", formatter:"money", formatterParams: format},
              {title:"Sniper", field:"weaponKillsSniper", formatter:"money", formatterParams: format},
              {title:"SMG", field:"weaponKillsSubmachinegun", formatter:"money", formatterParams: format},
              //{title:"Relic", field:"weaponKillsRelic", formatter:"money", formatterParams: format},
              {title:"Sidearm", field:"weaponKillsSideArm", formatter:"money", formatterParams: format},
              {title:"Sword", field:"weaponKillsSword", formatter:"money", formatterParams: format},
              {title:"Grenade Launcher", field:"weaponKillsGrenadeLauncher", formatter:"money", formatterParams: format},
            ],
            layout:"fitDataFill",
            height:"500px",
            resizableColumns:false,
          });

          $("#nameFilter").on("input", function(){
            table.setFilter("name", "like", $(this).val());
          });

          $('.stats-container').append('<div id="weapon-stats-info" class="text-center"><small>Last checked: '+weaponData.last_updated+'</small></div>');
        });
      }
      else {
        $('.loader-text').html('Unable to retrieve members 😟 <br/>Bungie API might be under maintenance');
      }

    });
  }
});