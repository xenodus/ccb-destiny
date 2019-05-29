var queueStatus = 0;

var infamyRanks = [
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

function formatNumber(n) {
  const ranges = [
    { divider: 1e18 , suffix: 'E' },
    { divider: 1e15 , suffix: 'P' },
    { divider: 1e12 , suffix: 'T' },
    { divider: 1e9 , suffix: 'G' },
    { divider: 1e6 , suffix: 'M' },
    { divider: 1e3 , suffix: 'k' }
  ];

  for (var i = 0; i < ranges.length; i++) {
    if (n >= ranges[i].divider) {
      return parseFloat((n / ranges[i].divider)).toFixed(2) + ' ' + ranges[i].suffix;
    }
  }
  return n.toString();
}

$(document).ready(function(){

  $(document).on('click', 'button.refresh-btn', function(){
    refreshBtn = $(this);
    refreshBtn.prop('disabled', true);
    update_gambit_stats();
  });

  print_gambit_stats();

  function update_gambit_stats() {

    $('.loader').show();
    $('.loader-text').show();
    if( queueStatus == 0 )
      $('.loader-text').text('Refreshing data. Go grab a drink...');
    $('.stats-container').empty();

    $.get('/bungie/gambit/update', function(res){
      if(res.status == 2) {
        $('.loader-text').text('Resync already in progress. Queueing...');
        queueStatus = 1;
        setTimeout(update_gambit_stats, 5000);
      }
      else {
        queueStatus = 0;
        refreshBtn = $('button.refresh-btn');
        refreshBtn.prop('disabled', false);
        print_gambit_stats();
      }
    });
  }

  function print_gambit_stats() {
    $.get('/bungie/members/get', function(memberData){

      $('.loader-text').text('Fetching Gambit Stats...');

      $.get('/bungie/gambit/get', function(memberGambitData){

        var tableData = [];

        for(var i=0; i<memberData.length; i++) {

          $('.loader-text').text('Processing ' + (i+1) + ' of ' + memberData.length + '...');

          gambitData = memberGambitData.filter(function(member){ return member.user_id == memberData[i].destinyUserInfo.membershipId })[0];

          infamy_rank = gambitData.infamy_step == infamyRanks.length ? infamyRanks[infamyRanks.length-1] : infamyRanks[ gambitData.infamy_step ];

          tableData.push({
            name: memberData[i].destinyUserInfo.displayName,
            infamy: gambitData.infamy,
            infamy_step: infamy_rank,
            infamy_resets: gambitData.infamy_resets,
            kills: gambitData.kills,
            deaths: gambitData.deaths,
            kd: gambitData.killsDeathsRatio,
            activitiesEntered: gambitData.activitiesEntered,
            activitiesWon: gambitData.activitiesEntered > 0 ? ((gambitData.activitiesWon / gambitData.activitiesEntered) * 100) : 0,
            invasionKills: gambitData.invasionKills,
            invaderKills: gambitData.invaderKills,
            invaderDeaths: gambitData.invaderDeaths,
            primevalHealing: gambitData.primevalHealing + '%',
            primevalDamage: gambitData.primevalDamage,
            motesDeposited: gambitData.motesDeposited,
            motesLost: gambitData.motesLost,
            motesDenied: gambitData.motesDenied,
          });
        }

        $('.loader-text').text('Generating Table...');

        $('.stats-container').append('<div id="gambit-stats-table"></div>');

        $('.loader').hide();
        $('.loader-text').hide();

        var format = {precision: 0};

        var table = new Tabulator("#gambit-stats-table", {
          data:tableData, //assign data to table
          layout:"fitColumns", //fit columns to width of table (optional)
          columns:[ //Define Table Columns
            {title:"Name", field:"name", frozen:true},
            {title:"Infamy", field:"infamy", formatter:"money", formatterParams: format},
            {title:"Infamy Rank", field:"infamy_step"},
            {title:"Resets", field:"infamy_resets", formatter:"money", formatterParams: format},
            {title:"Kills", field:"kills", formatter:"money", formatterParams: format},
            {title:"Deaths", field:"deaths", formatter:"money", formatterParams: format},
            {title:"KD", field:"kd", formatter:"money", formatterParams: {precision: 2}},
            {title:"Played", field:"activitiesEntered", formatter:"money", formatterParams: format},
            {title:"Win %", field:"activitiesWon", formatter:"money", formatterParams: format},
            {title:"Invasion Kills", field:"invasionKills", formatter:"money", formatterParams: format},
            {title:"Invaders Killed", field:"invaderKills", formatter:"money", formatterParams: format},
            {title:"Deaths by Invader", field:"invaderDeaths", formatter:"money", formatterParams: format},
            {title:"Primeval Healing", field:"primevalHealing", formatter:"money", formatterParams: format},
            {title:"Primeval Damage", field:"primevalDamage", formatter:"money", formatterParams: format},
            {title:"Motes Banked", field:"motesDeposited", formatter:"money", formatterParams: format},
            {title:"Motes Lost", field:"motesLost", formatter:"money", formatterParams: format},
            {title:"Motes Denied", field:"motesDenied", formatter:"money", formatterParams: format},
          ],
          initialSort: [
            {column:"name", dir:"asc"}
          ],
          layout:"fitDataFill",
          height:"350px",
        });

        $('.stats-container').append('<div id="weapon-stats-info" class="text-center"><small>Last checked: '+gambitData.last_updated+'</small> <br/><button type="button" class="btn btn-primary btn-sm badge badge-info refresh-btn"><i class="fas fa-sync-alt"></i> Resync data</button></div>');
      });
    });
  }
});