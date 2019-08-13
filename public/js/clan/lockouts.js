$(document).ready(function(){
  var y2RaidNameMap = {
    'cos': 'Crown of Sorrows',
    'sotp': 'Scourge of the Past',
    'lw': 'Last Wish',
  };

  // convention p behind y1 raids for prestige
  var y1RaidNameMap = {
    'sosp': 'Prestige Spire of Stars',
    'sos': 'Spire of Stars',
    'eowp': 'Prestige Eaters of World',
    'eow': 'Eaters of World',
    'levip': 'Prestige Leviathan',
    'levi': 'Leviathan',
  };

  $.ajax({
    url: 'https://www.bungie.net/Platform/GroupV2/3717919/Members/',
    headers: {
      'X-API-Key': '856136fabe704c149dd4bd41344b54c8'
    }
  }).done(function(data){

    if( data.Response.results && data.Response.results.length > 0 ) {

      memberData = data.Response.results;

      $('.loader-text').text('Fetching Raid Lockouts...');

      $.get('/clan/lockouts/get', function(data){
        $('#weekly-lockout-dates').html(data['start_of_week'] + " to " + data['end_of_week']);
        $('#sub-menu > ul').append('<li class="nav-item pt-0 pt-md-4"><a class="nav-link disabled" href="#" aria-disabled="true">Year 2 Raids</a></li>');
        makeTable(y2RaidNameMap, data['raid_lockouts'], memberData);
        $('#sub-menu > ul').append('<li class="nav-item"><a class="nav-link disabled" href="#" aria-disabled="true">Year 1 Raids</a></li>');
        makeTable(y1RaidNameMap, data['raid_lockouts'], memberData);
        $('.stats-container').prepend('<div class="col-md-12 mb-3 mb-md-0 text-left"><small>Note: If your guardian is missing, make sure "Show my Destiny game Activity Feed on Bungie.net" is checked under your Bungie.net privacy settings.</small></div>');
      });
    }
    else {
      $('.loader-text').html('Unable to retrieve members 😟 <br/>Bungie API might be under maintenance');
    }

  });
});

function makeTable(raidMap, raidLockoutData, memberData) {
  var game_classes = [
    'warlock',
    'hunter',
    'titan'
  ];

  for(var key in raidMap) {
    var tableData = [];
    var anyLocked = false;

    for(var i=0; i<raidLockoutData.length; i++) {
      username = memberData.filter(function(member){ return member.destinyUserInfo.membershipId == raidLockoutData[i].id }).map(function(member){ return member.destinyUserInfo.displayName })[0];
      userData = JSON.parse(raidLockoutData[i].data);

      // If prestige y1 raids done, normal = done too
      for(var c=0; c<game_classes.length; c++) {
        if( key+"p" in userData[ game_classes[c] ] ) {
          if( userData[game_classes[c]][key+"p"] == 1 ) {
            userData[game_classes[c]][key] = 1;
          }
        }
      }

      tableData.push({
        name: username + '<a href="https://raid.report/pc/'+raidLockoutData[i].id+'" target="_blank" class="text-dark"><i class="fas fa-external-link-alt ml-1 fa-xs" style="position: relative; bottom: 1px;"></i></a>',
        warlock: userData['warlock'][key] == 1 ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>',
        hunter: userData['hunter'][key] == 1 ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>',
        titan: userData['titan'][key] == 1 ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>',
        total: userData['warlock'][key] + userData['titan'][key] + userData['hunter'][key]
      });

      if(userData['warlock'][key] || userData['hunter'][key] || userData['titan'][key]) anyLocked = true;
    }

    $('.stats-container').append('<div id="'+key+'" class="col-md-12 mb-4 pt-md-5"><h2 class="text-yellow text-left mb-3 stats-container-header"><a href="#'+key+'">'+raidMap[key]+'</a></h2></div>');
    $('#'+key).append('<div id="'+key+'-stats-table"></div>');
    $('#sub-menu > ul').append('<li class="nav-item"><a class="nav-link" href="#'+key+'">'+raidMap[key]+(anyLocked?'<i class="fas fa-check text-success ml-2"></i>':'')+'</a></li>');

    $('.loader').hide();
    $('.loader-text').hide();
    $('#sub-menu').show();

    var table = new Tabulator("#"+key+"-stats-table", {
      data:tableData, //assign data to table
      columns:[ //Define Table Columns
        {title:"Name", field:"name", formatter:"html", frozen:true},
        {title:"Warlock", field:"warlock", formatter:"html", cssClass: "text-center"},
        {title:"Hunter", field:"hunter", formatter:"html", cssClass: "text-center"},
        {title:"Titan", field:"titan", formatter:"html", cssClass: "text-center"},
        {title:"Total", field:"total", cssClass: "text-center", visible:false},
      ],
      initialSort: [
        {column:"name", dir:"asc"},
        {column:"total", dir:"desc"},
      ],
      layout:"fitColumns",
      height: "350px",
      resizableColumns:false,
      responsiveLayout:true
    });
  }
}