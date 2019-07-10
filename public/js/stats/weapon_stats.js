var queueStatus = 0;

$(document).ready(function(){

  print_weapon_stats();

  function print_weapon_stats() {
    $.get('/bungie/members/get', function(memberData){

      if( memberData.length > 0 ) {

        $('.loader-text').text('Fetching Weapon Stats...');

        $.get('/bungie/weapon/get', function(memberWeaponData){

          var tableData = [];

          for(var i=0; i<memberData.length; i++) {

            $('.loader-text').text('Processing ' + (i+1) + ' of ' + memberData.length + '...');

            weaponData = memberWeaponData.filter(function(member){ return member.user_id == memberData[i].destinyUserInfo.membershipId })[0];

            tableData.push({
              name: memberData[i].destinyUserInfo.displayName,
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

          $('.loader-text').text('Generating Table...');

          $('.stats-container').append('<div id="weapon-stats-table"></div>');

          $('.loader').hide();
          $('.loader-text').hide();

          var format = {precision: 0};

          var table = new Tabulator("#weapon-stats-table", {
            data:tableData, //assign data to table
            layout:"fitColumns", //fit columns to width of table (optional)
            columns:[ //Define Table Columns
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
            //height:"350px",
            resizableColumns:false,
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