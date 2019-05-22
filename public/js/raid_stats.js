$(document).ready(function(){

  $(document).on('click', 'button.refresh-btn', function(){
    refreshBtn = $(this);
    refreshBtn.prop('disabled', true);

    $('.loader').show();
    $('.loader-text').show();
    $('.loader-text').text('Refreshing data. Go grab a drink...');
    $('.stats-container').empty();

    $.get('/bungie/raid/update', function(res){
      refreshBtn.prop('disabled', false);
      print_raid_stats();
    });
  });

  print_raid_stats();

  function print_raid_stats() {
    $.get('/bungie/members/get', function(memberData){

      $('.loader-text').text('Fetching Raid Stats...');

      $.get('/bungie/raid/get', function(memberRaidData){

        //console.log(memberData);

        var tableData = [];

        for(var i=0; i<memberData.length; i++) {

          $('.loader-text').text('Processing ' + (i+1) + ' of ' + memberData.length + '...');

          raidData = memberRaidData.filter(function(member){ return member.user_id == memberData[i].destinyUserInfo.membershipId })[0];

          tableData.push({
            name: memberData[i].destinyUserInfo.displayName,
            levi: raidData.levi,
            levip: raidData.levip,
            eow: raidData.eow,
            eowp: raidData.eowp,
            sos: raidData.sos,
            sosp: raidData.sosp,
            lw: raidData.lw,
            petra: raidData.petra > 0 ? '<div class="text-center"><i class="fas fa-check text-success"></i></div>' : '<div class="text-center"><i class="fas fa-times text-danger"></i></div>',
            sotp: raidData.sotp,
            diamond: raidData.diamond > 0 ? '<div class="text-center"><i class="fas fa-check text-success"></i></div>' : '<div class="text-center"><i class="fas fa-times text-danger"></i></div>',
            total: (raidData.levi+raidData.levip+raidData.eow+raidData.eowp+raidData.sos+raidData.sosp+raidData.lw+raidData.sotp),
          });
        }

        $('.loader-text').text('Generating Table...');

        $('.stats-container').append('<div id="raid-stats-table"></div>');

        $('.loader').hide();
        $('.loader-text').hide();

        var format = {precision: 0};

        var table = new Tabulator("#raid-stats-table", {
          data:tableData, //assign data to table
          layout:"fitColumns", //fit columns to width of table (optional)
          columns:[ //Define Table Columns
            {title:"Name", field:"name", formatter:"money", formatterParams: format, frozen:true},
            {title:"Levi", field:"levi", formatter:"money", formatterParams: format},
            {title:"P Levi", field:"levip", formatter:"money", formatterParams: format},
            {title:"EOW", field:"eow", formatter:"money", formatterParams: format},
            {title:"P EOW", field:"eowp", formatter:"money", formatterParams: format},
            {title:"SOS", field:"sos", formatter:"money", formatterParams: format},
            {title:"P SOS", field:"sosp", formatter:"money", formatterParams: format},
            {title:"Last Wish", field:"lw", formatter:"money", formatterParams: format},
            {title:"Petra", field:"petra", formatter:"html"},
            {title:"SoTP", field:"sotp", formatter:"money", formatterParams: format},
            {title:"Diamond", field:"diamond", formatter:"html"},
            {title:"Total", field:"total", formatter:"money", formatterParams: format},
          ],
          initialSort: [
            {column:"total", dir:"desc"}
          ],
          layout:"fitDataFill",
          height:"350px",
        });

        $('.stats-container').append('<div id="raid-stats-info" class="text-center"><small>Last checked: '+raidData.last_updated+'</small> <br/><button type="button" class="btn btn-primary btn-sm badge badge-info refresh-btn"><i class="fas fa-sync-alt"></i> Resync data</button></div>');
      });
    });
  }
});