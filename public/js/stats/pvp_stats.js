var queueStatus = 0;

var valorRanks = [
  'Guardian',
  'Brave',
  'Heroic',
  'Fabled',
  'Mythic',
  'Legend',
];

var gloryRanks = [
  'Guardian I',
  'Guardian II',
  'Guardian III',
  'Brave I',
  'Brave II',
  'Brave III',
  'Heroic I',
  'Heroic II',
  'Heroic III',
  'Fabled I',
  'Fabled II',
  'Fabled III',
  'Mythic I',
  'Mythic II',
  'Mythic III',
  'Legend',
];

$(document).ready(function(){

  print_pvp_stats();

  function print_pvp_stats() {
    // $.get('/bungie/members/get', function(memberData){

    $.ajax({
      url: ccbNS.bungie_api_url+'/GroupV2/'+ccbNS.clan_id+'/Members/',
      headers: {
        'X-API-Key': ccbNS.bungie_api
      }
    }).done(function(data){

      if( data.Response.results && data.Response.results.length > 0 ) {

        memberData = data.Response.results;

        $('.loader-text').text('Fetching PvP Stats...');

        $.get('/bungie/pvp/get', function(memberPvpData){

          var tableData = [];

          for(var i=0; i<memberData.length; i++) {

            $('.loader-text').text('Processing ' + (i+1) + ' of ' + memberData.length + '...');

            pvpData = memberPvpData.filter(function(member){ return member.user_id == memberData[i].destinyUserInfo.membershipId })[0];

            if( pvpData ) {

              glory_rank = pvpData.glory_step == gloryRanks.length ? gloryRanks[gloryRanks.length-1] : gloryRanks[ pvpData.glory_step ];
              valor_rank = pvpData.valor_step == valorRanks.length ? valorRanks[valorRanks.length-1] : valorRanks[ pvpData.valor_step ];

              tableData.push({
                name: memberData[i].destinyUserInfo.displayName,
                kd: pvpData.kd,
                kda: pvpData.kda,
                kad: pvpData.kad,
                glory: pvpData.glory,
                glory_step: glory_rank,
                valor: pvpData.valor,
                valor_step: valor_rank,
                valor_resets: pvpData.valor_resets,
                super_kills: pvpData.weaponKillsSuper,
                melee_kills: pvpData.weaponKillsMelee,
                grenade_kills: pvpData.weaponKillsGrenade,
              });
            }
            else {
              tableData.push({
                name: memberData[i].destinyUserInfo.displayName,
                kd: 0,
                kda: 0,
                kad: 0,
                glory: 0,
                glory_step: '',
                valor: 0,
                valor_step: '',
                valor_resets: 0,
                super_kills: 0,
                melee_kills: 0,
                grenade_kills: 0,
              });

              console.log("Unable to retrieve PVP data for: " + memberData[i].destinyUserInfo.displayName);
            }
          }

          $('.loader-text').text('Generating Table...');

          $('.stats-container').append('<div id="pvp-stats-table"></div>');

          $('.loader').hide();
          $('.loader-text').hide();
          $('.filter-container').show();

          var autoNumFormatter = function(){
            return $("#pvp-stats-table .tabulator-row").length;
          };

          var format = {precision: 0};

          var table = new Tabulator("#pvp-stats-table", {
            data:tableData, //assign data to table
            layout:"fitColumns", //fit columns to width of table (optional)
            columns:[ //Define Table Columns
              //{formatter:autoNumFormatter, width:40},
              {title:"Name", field:"name", formatter:"money", formatterParams: format, frozen:true},
              {title:"KD", field:"kd", formatter:"money", formatterParams: {precision: 2}},
              {title:"KDA", field:"kda", formatter:"money", formatterParams: {precision: 2}},
              {title:"KAD", field:"kad", formatter:"money", formatterParams: {precision: 2}},
              {title:"Glory", field:"glory", formatter:"money", formatterParams: format},
              {title:"Glory Rank", field:"glory_step"},
              {title:"Valor", field:"valor", formatter:"money", formatterParams: format},
              {title:"Valor Rank", field:"valor_step"},
              {title:"Valor Resets", field:"valor_resets", formatter:"money", formatterParams: format},
              {title:"Super Kills", field:"super_kills", formatter:"money", formatterParams: format},
              {title:"Melee Kills", field:"melee_kills", formatter:"money", formatterParams: format},
              {title:"Grenade Kills", field:"grenade_kills", formatter:"money", formatterParams: format},
            ],
            initialSort: [
              {column:"glory", dir:"desc"}
            ],
            layout:"fitDataFill",
            height:"500px",
            resizableColumns:false,
          });

          $("#nameFilter").on("input", function(){
            table.setFilter("name", "like", $(this).val());
          });

          $('.stats-container').append('<div id="weapon-stats-info" class="text-center"><small>Last checked: '+pvpData.last_updated+'</small></div>');
        });
      }
      else {
        $('.loader-text').html('Unable to retrieve members 😟 <br/>Bungie API might be under maintenance');
      }

    });
  }
});