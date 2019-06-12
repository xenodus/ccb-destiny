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

  $(document).on('click', 'button.refresh-btn', function(){
    refreshBtn = $(this);
    refreshBtn.prop('disabled', true);
    update_pvp_stats();
  });

  print_pvp_stats();

  function update_pvp_stats() {

    $('.loader').show();
    $('.loader-text').show();
    if( queueStatus == 0 )
      $('.loader-text').text('Refreshing data (~1 min). Go grab a drink...');
    $('.stats-container').empty();

    $.get('/bungie/pvp/update', function(res){
      if(res.status == 2) {
        $('.loader-text').text('Resync already in progress. Queueing...');
        queueStatus = 1;
        setTimeout(update_pvp_stats, 5000);
      }
      else {
        queueStatus = 0;
        refreshBtn = $('button.refresh-btn');
        refreshBtn.prop('disabled', false);
        print_pvp_stats();
      }
    });
  }

  function print_pvp_stats() {
    $.get('/bungie/members/get', function(memberData){

      $('.loader-text').text('Fetching PvP Stats...');

      $.get('/bungie/pvp/get', function(memberPvpData){

        var tableData = [];

        for(var i=0; i<memberData.length; i++) {

          $('.loader-text').text('Processing ' + (i+1) + ' of ' + memberData.length + '...');

          pvpData = memberPvpData.filter(function(member){ return member.user_id == memberData[i].destinyUserInfo.membershipId })[0];

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
          });
        }

        $('.loader-text').text('Generating Table...');

        $('.stats-container').append('<div id="pvp-stats-table"></div>');

        $('.loader').hide();
        $('.loader-text').hide();

        var format = {precision: 0};

        var table = new Tabulator("#pvp-stats-table", {
          data:tableData, //assign data to table
          layout:"fitColumns", //fit columns to width of table (optional)
          columns:[ //Define Table Columns
            {title:"Name", field:"name", formatter:"money", formatterParams: format, frozen:true},
            {title:"KD", field:"kd", formatter:"money", formatterParams: {precision: 2}},
            {title:"KDA", field:"kda", formatter:"money", formatterParams: {precision: 2}},
            {title:"KAD", field:"kad", formatter:"money", formatterParams: {precision: 2}},
            {title:"Glory", field:"glory", formatter:"money", formatterParams: format},
            {title:"Glory Rank", field:"glory_step"},
            {title:"Valor", field:"valor", formatter:"money", formatterParams: format},
            {title:"Valor Rank", field:"valor_step"},
            {title:"Valor Resets", field:"valor_resets", formatter:"money", formatterParams: format},
          ],
          initialSort: [
            {column:"glory", dir:"desc"}
          ],
          layout:"fitDataFill",
          //height:"350px",
          resizableColumns:false,
        });

        $('.stats-container').append('<div id="weapon-stats-info" class="text-center"><small>Last checked: '+pvpData.last_updated+'</small> <br/><button type="button" class="btn btn-primary btn-sm badge badge-info refresh-btn"><i class="fas fa-sync-alt"></i> Resync data</button></div>');
      });
    });
  }
});