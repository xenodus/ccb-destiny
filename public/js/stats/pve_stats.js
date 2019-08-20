var queueStatus = 0;

$(document).ready(function(){

  print_pve_stats();

  function print_pve_stats() {
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

        $('.loader-text').text('Fetching PvE Stats...');

        $.get('/bungie/pve/get', function(memberPveData){

          var tableData = [];

          for(var i=0; i<memberData.length; i++) {

            $('.loader-text').text('Processing ' + (i+1) + ' of ' + memberData.length + '...');

            pveData = memberPveData.filter(function(member){ return member.user_id == memberData[i].destinyUserInfo.membershipId })[0];

            if( pveData ) {
              tableData.push({
                name: memberData[i].destinyUserInfo.displayName,
                kills: pveData.kills,
                deaths: pveData.deaths,
                suicides: pveData.suicides,
                killsDeathsRatio: pveData.killsDeathsRatio,
                activitiesCleared: pveData.activitiesCleared,
                weaponKillsSuper: pveData.weaponKillsSuper,
                weaponKillsMelee: pveData.weaponKillsMelee,
                weaponKillsGrenade: pveData.weaponKillsGrenade,
                publicEventsCompleted: pveData.publicEventsCompleted,
                heroicPublicEventsCompleted: pveData.heroicPublicEventsCompleted,
              });
            }
          }

          $('.loader-text').text('Generating Table...');

          $('.stats-container').append('<div id="pve-stats-table"></div>');

          $('.loader').hide();
          $('.loader-text').hide();
          $('.filter-container').show();

          var autoNumFormatter = function(){
            return $("#pve-stats-table .tabulator-row").length;
          };

          var format = {precision: 0};

          var table = new Tabulator("#pve-stats-table", {
            data:tableData, //assign data to table
            layout:"fitColumns", //fit columns to width of table (optional)
            columns:[ //Define Table Columns
              //{formatter:autoNumFormatter, width:40},
              {title:"Name", field:"name", formatter:"money", formatterParams: format, frozen:true},
              {title:"Kills", field:"kills", formatter:"money", formatterParams: format},
              {title:"Deaths", field:"deaths", formatter:"money", formatterParams: format},
              {title:"Suicides", field:"suicides", formatter:"money", formatterParams: format},
              {title:"K/D", field:"killsDeathsRatio", formatter:"money", formatterParams: format},
              {title:"Activities", field:"activitiesCleared", formatter:"money", formatterParams: format},
              {title:"Super Kills", field:"weaponKillsSuper", formatter:"money", formatterParams: format},
              {title:"Melee Kills", field:"weaponKillsMelee", formatter:"money", formatterParams: format},
              {title:"Grenade Kills", field:"weaponKillsGrenade", formatter:"money", formatterParams: format},
              {title:"Public Event", field:"publicEventsCompleted", formatter:"money", formatterParams: format},
              {title:"Heroic Public Event", field:"heroicPublicEventsCompleted", formatter:"money", formatterParams: format},
            ],
            initialSort: [
              {column:"name", dir:"asc"}
            ],
            layout:"fitDataFill",
            height:"500px",
            resizableColumns:false,
          });

          $("#nameFilter").on("input", function(){
            table.setFilter("name", "like", $(this).val());
          });

          $('.stats-container').append('<div id="weapon-stats-info" class="text-center"><small>Last checked: '+pveData.last_updated+'</small></div>');
        });
      }
      else {
        $('.loader-text').html('Unable to retrieve members 😟 <br/>Bungie API might be under maintenance');
      }

    });
  }
});