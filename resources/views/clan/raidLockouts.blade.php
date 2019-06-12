@extends('layouts.template')

@section('body')
<section id="raid-lockouts" class="container text-center mb-4">
  <div class="mt-4 mb-4">
    <h1 class="text-yellow">Weekly Raid Lockouts</h1>
    <div class="text-secondary">
      <small>Updated every ~ 5 mins</small>
    </div>
  </div>
  <div class="loader"></div>
  <div class="loader-text mb-4">Fetching Members...</div>

  <div>
    <div class="row">
      <div class="col-md-3">
        <div id="sub-menu" class="mb-5 sticky-top pt-md-4">
          <ul class="nav flex-column">
            <li class="nav-item pt-md-3">
              <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">- Raids -</a>
            </li>
          </ul>
        </div>
      </div>
      <div class="col-md-9">
        <div class="stats-container row"></div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('header')
<link rel="stylesheet" href="/css/lockouts.css?<?=time()?>"/>
@endsection

@section('footer')
<script>
$(document).ready(function(){
  var y2RaidNameMap = {
    'cos': 'Crown of Sorrows',
    'sotp': 'Scourge of the Past',
    'lw': 'Last Wish',
  };

  // convention p behind y1 raids for prestige
  var y1RaidNameMap = {
    'levi': 'Leviathan',
    'levip': 'Prestige Leviathan',
    'eow': 'Eaters of World',
    'eowp': 'Prestige Eaters of World',
    'sos': 'Spire of Stars',
    'sosp': 'Prestige Spire of Stars',
  };

  $.get('/bungie/members/get', function(memberData){

    $('.loader-text').text('Fetching Raid Lockouts...');

    $.get('/clan/lockouts/get', function(raidLockoutData){
      makeTable(y2RaidNameMap, raidLockoutData, memberData);
      makeTable(y1RaidNameMap, raidLockoutData, memberData);
    });
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
        name: username,
        warlock: userData['warlock'][key] == 1 ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>',
        hunter: userData['hunter'][key] == 1 ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>',
        titan: userData['titan'][key] == 1 ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>',
        total: userData['warlock'][key] + userData['titan'][key] + userData['hunter'][key]
      });
    }

    $('.stats-container').append('<div id="'+key+'" class="col-md-12 mb-4 pt-md-5"><h2 class="text-yellow text-left mb-3 stats-container-header"><a href="#'+key+'">'+raidMap[key]+'</a></h2></div>');
    $('#'+key).append('<div id="'+key+'-stats-table"></div>');
    $('#sub-menu > ul').append('<li class="nav-item"><a class="nav-link" href="#'+key+'">'+raidMap[key]+'</a></li>');

    $('.loader').hide();
    $('.loader-text').hide();
    $('#sub-menu').show();

    var table = new Tabulator("#"+key+"-stats-table", {
      data:tableData, //assign data to table
      columns:[ //Define Table Columns
        {title:"Name", field:"name", frozen:true},
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
</script>
@endsection