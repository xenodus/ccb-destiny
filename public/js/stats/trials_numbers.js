var queueStatus = 0;

$(document).ready(function(){

  print_trials_stats();

  function print_trials_stats() {

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

        $('.loader-text').text('Fetching Trials Stats...');

        $.get('/bungie/trials/get', function(memberTrialsData){

          var tableData = [];
          var lastUpdated = null;

          for(var i=0; i<memberData.length; i++) {

            $('.loader-text').text('Processing ' + (i+1) + ' of ' + memberData.length + '...');

            trialsData = memberTrialsData.filter(function(member){ return member.user_id == memberData[i].destinyUserInfo.membershipId })[0];

            if( trialsData ) {

              displayName = getSanitizedName(memberData[i].destinyUserInfo.displayName);
              lastUpdated = trialsData.last_updated;

              tableData.push({
                name: '<a data-sort-name="'+displayName+'" href="https://destinytrialsreport.com/report/3/'+memberData[i].destinyUserInfo.membershipId+'" target="_blank" class="text-dark member-name">'+displayName+'<i class="fas fa-external-link-alt ml-1 fa-xs" style="position: relative; bottom: 1px;"></i></a>',
                kd: trialsData.kd,
                kda: trialsData.kda,
                kad: trialsData.kad,
                super_kills: trialsData.weaponKillsSuper,
                melee_kills: trialsData.weaponKillsMelee,
                grenade_kills: trialsData.weaponKillsGrenade,
                flawless: trialsData.flawless,
                weaponBestType: trialsData.weaponBestType,
                combatRating: trialsData.combatRating,
                winRate: Math.round((trialsData.activitiesWon / trialsData.activitiesEntered)*100),
                activitiesEntered: trialsData.activitiesEntered,
                activitiesWon: trialsData.activitiesWon
              });
            }
            else {
              tableData.push({
                name: memberData[i].destinyUserInfo.displayName,
                kd: 0,
                kda: 0,
                kad: 0,
                super_kills: 0,
                melee_kills: 0,
                grenade_kills: 0,
                flawless: 0,
                weaponBestType: '',
                combatRating: 0,
                winRate: 0,
                activitiesEntered: 0,
                activitiesWon: 0
              });

              console.log("Unable to retrieve Trials data for: " + memberData[i].destinyUserInfo.displayName);
            }
          }

          $('.loader-text').text('Generating Table...');

          $('.stats-container').append('<div id="trials-stats-table"></div>');

          $('.loader').hide();
          $('.loader-text').hide();
          $('.filter-container').show();

          var autoNumFormatter = function(){
            return $("#trials-stats-table .tabulator-row").length;
          };

          var format = {precision: 0};

          var table = new Tabulator("#trials-stats-table", {
            data:tableData,
            layout:"fitColumns",
            columns:[
              {title:"Name", field:"name", formatter:"html", frozen:true, minWidth:180},
              {title:"Flawless", field:"flawless", formatter:"money", formatterParams: format},
              {title:"Win Rate %", field:"winRate", formatter:"money", formatterParams: format},
              {title:"KD", field:"kd", formatter:"money", formatterParams: {precision: 2}},
              {title:"KDA", field:"kda", formatter:"money", formatterParams: {precision: 2}},
              {title:"KAD", field:"kad", formatter:"money", formatterParams: {precision: 2}},
              {title:"Combat Rating", field:"combatRating", formatter:"money", formatterParams: {precision: 2}},
              {title:"Best Weapon", field:"weaponBestType"},
              {title:"Played", field:"activitiesEntered", formatter:"money", formatterParams: format},
              {title:"Won", field:"activitiesWon", formatter:"money", formatterParams: format},
              {title:"Super Kills", field:"super_kills", formatter:"money", formatterParams: format},
              {title:"Melee Kills", field:"melee_kills", formatter:"money", formatterParams: format},
              {title:"Grenade Kills", field:"grenade_kills", formatter:"money", formatterParams: format},
            ],
            initialSort: [
              {column:"winRate", dir:"desc"},
              {column:"flawless", dir:"desc"},
            ],
            layout:"fitDataFill",
            height:"500px",
            resizableColumns:true,
          });

          $("#nameFilter").on("input", function(){
            table.setFilter("name", "like", $(this).val());
          });

          if( lastUpdated ) {
            $('.stats-container').append('<div id="weapon-stats-info" class="text-center"><small>Last checked: '+lastUpdated+'</small></div>');
          }
        });
      }
      else {
        $('.loader-text').html('Unable to retrieve members 😟 <br/>Bungie API might be under maintenance');
      }

    });
  }
});