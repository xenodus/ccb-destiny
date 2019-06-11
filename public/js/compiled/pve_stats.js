var queueStatus = 0;

$(document).ready(function(){

  $(document).on('click', 'button.refresh-btn', function(){
    refreshBtn = $(this);
    refreshBtn.prop('disabled', true);
    update_pve_stats();
  });

  print_pve_stats();

  function update_pve_stats() {
    $('.loader').show();
    $('.loader-text').show();
    if( queueStatus == 0 )
      $('.loader-text').text('Refreshing data. Go grab a drink...');
    $('.stats-container').empty();

    $.get('/bungie/pve/update', function(res){
      if(res.status == 2) {
        $('.loader-text').text('Resync already in progress. Queueing...');
        queueStatus = 1;
        setTimeout(update_pve_stats, 5000);
      }
      else {
        queueStatus = 0;
        refreshBtn = $('button.refresh-btn');
        refreshBtn.prop('disabled', false);
        print_pve_stats();
      }
    });
  }

  function print_pve_stats() {
    $.get('/bungie/members/get', function(memberData){

      $('.loader-text').text('Fetching PvE Stats...');

      $.get('/bungie/pve/get', function(memberPveData){

        var tableData = [];

        for(var i=0; i<memberData.length; i++) {

          $('.loader-text').text('Processing ' + (i+1) + ' of ' + memberData.length + '...');

          pveData = memberPveData.filter(function(member){ return member.user_id == memberData[i].destinyUserInfo.membershipId })[0];

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

        $('.loader-text').text('Generating Table...');

        $('.stats-container').append('<div id="pve-stats-table"></div>');

        $('.loader').hide();
        $('.loader-text').hide();

        var format = {precision: 0};

        var table = new Tabulator("#pve-stats-table", {
          data:tableData, //assign data to table
          layout:"fitColumns", //fit columns to width of table (optional)
          columns:[ //Define Table Columns
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
          height:"350px",
        });

        $('.stats-container').append('<div id="weapon-stats-info" class="text-center"><small>Last checked: '+pveData.last_updated+'</small> <br/><button type="button" class="btn btn-primary btn-sm badge badge-info refresh-btn"><i class="fas fa-sync-alt"></i> Resync data</button></div>');
      });
    });
  }
});