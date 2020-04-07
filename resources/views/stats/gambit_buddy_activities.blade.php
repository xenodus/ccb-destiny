@extends('layouts.template')

@section('body')
<section class="stats text-center container-fluid mb-4">

  <div class="mt-4">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent pl-0 py-0 mb-3" itemscope itemtype="https://schema.org/BreadcrumbList">
        <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
          <a itemprop="item" itemtype="https://schema.org/WebPage" href="/stats">
            <span itemprop="name">Stats</span>
          </a>
          <meta itemprop="position" content="1">
        </li>

        <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
          <a itemprop="item" itemtype="https://schema.org/WebPage" href="/stats/gambit/buddy">
            <span itemprop="name">Gambit Buddies</span>
          </a>
          <meta itemprop="position" content="2">
        </li>

        <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
          <a itemprop="item" itemtype="https://schema.org/WebPage" href="/stats/gambit/buddy/{{$member->id}}">
            <span itemprop="name">{{ $member->display_name }}</span>
          </a>
          <meta itemprop="position" content="3">
        </li>

        <li class="breadcrumb-item active" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
          <a itemprop="item" itemtype="https://schema.org/WebPage" href="/stats/gambit/buddy/{{$member->id}}/{{$buddy_id}}">
            <span itemprop="name" class="buddy_name">{{ $buddy_id }}</span>
          </a>
          <meta itemprop="position" content="3">
        </li>
      </ol>
    </nav>

    <h1 class="text-yellow text-left">Gambit Activities between {{ $member->display_name }} & <span class="buddy_name"></span></h1>
  </div>

  <div class="stats-container mt-3"></div>
</section>
@endsection

@section('header')
<link rel="stylesheet" href="{{ mix('/css/compiled/stats.css') }}"/>
@endsection

@section('footer')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css" integrity="sha256-aa0xaJgmK/X74WM224KMQeNQC2xYKwlAt08oZqjeF0E=" crossorigin="anonymous" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" integrity="sha256-Uv9BNBucvCPipKQ2NS9wYpJmi8DTOEfTA/nH2aoJALw=" crossorigin="anonymous"></script>

<script type="text/javascript">
$(document).ready(function(){
  var member = @json($member);
  var activity_instances = @json($activity_instances);
  var clan_members = @json($clan_members);
  var activity_definition = @json($activity_definition);
  var buddy_id = '{{ $buddy_id }}';

  getBuddyName();

  // console.log( member );
  // console.log( buddy_id );
  // console.log( activity_definition );

  /**********************
  // Table
  ***********************/

  var tableData = [];

  for(var i=0; i<activity_instances.length; i++) {

    if( activity_instances[i].pgcr ) {

      var pgcr = JSON.parse( activity_instances[i].pgcr.pgcr );

      if( pgcr ) {

        // console.log(pgcr);

        // pgcr data
        var your_stats = {
          'kill': 0,
          'death': 0,
          'kd': 0,
          'score': 0,
          'motes_deposited': 0,
          'motes_lost': 0,
          'invasion_kills': 0,
          'team': '',
          'outcome': '',
          'primeval_dmg': 0
        };

        var buddy_stats = {
          'kill': 0,
          'death': 0,
          'kd': 0,
          'score': 0,
          'motes_deposited': 0,
          'motes_lost': 0,
          'invasion_kills': 0,
          'team': '',
          'outcome': '',
          'primeval_dmg': 0
        };

        // your kad
        var memberData = pgcr.entries.filter(function(p){
          return p.player.destinyUserInfo.membershipId == member.id;
        });

        // Ignore multiple characters
        if( memberData.length > 0 ) {
          your_stats['kill'] = memberData[0].values.kills.basic.value;
          your_stats['death'] = memberData[0].values.deaths.basic.value;
          your_stats['kad'] = memberData[0].values.efficiency.basic.value;
          your_stats['score'] = memberData[0].values.score.basic.value;

          your_stats['motes_deposited'] = memberData[0].extended.values.motesDeposited.basic.value;
          your_stats['motes_lost'] = memberData[0].extended.values.motesLost.basic.value;
          your_stats['invasion_kills'] = memberData[0].extended.values.invasionKills.basic.value;

          t = pgcr.teams.filter(function(t){
            return t.teamId == memberData[0].values.team.basic.value;
          });

          if( t.length > 0 ) {
            your_stats['team'] = memberData[0].values.team.basic.value == 17 ? 'Bravo' : 'Alpha';
            your_stats['outcome'] = t[0].standing.basic.value; // 0 == win

            // Primeval Dmg %
            var team_mates = pgcr.entries.filter(function(p){
              return p.values.team.basic.value == memberData[0].values.team.basic.value;
            });

            var team_primeval_total_dmg = 0;

            for(var p=0; p<team_mates.length; p++) {
              team_primeval_total_dmg += team_mates[p].extended.values.primevalDamage.basic.value;
            }

            if( team_primeval_total_dmg > 0 ) {
              your_stats['primeval_dmg'] = Number(memberData[0].extended.values.primevalDamage.basic.value / team_primeval_total_dmg * 100).toFixed(0);
            }
          }
        }

        // your buddy's kad
        var buddyData = pgcr.entries.filter(function(p){
          return p.player.destinyUserInfo.membershipId == buddy_id;
        });

        // Ignore multiple characters
        if( buddyData.length > 0 ) {
          buddy_stats['kill'] = buddyData[0].values.kills.basic.value;
          buddy_stats['death'] = buddyData[0].values.deaths.basic.value;
          buddy_stats['kad'] = buddyData[0].values.efficiency.basic.value;
          buddy_stats['score'] = buddyData[0].values.score.basic.value;

          buddy_stats['motes_deposited'] = buddyData[0].extended.values.motesDeposited.basic.value;
          buddy_stats['motes_lost'] = buddyData[0].extended.values.motesLost.basic.value;
          buddy_stats['invasion_kills'] = buddyData[0].extended.values.invasionKills.basic.value;

          t = pgcr.teams.filter(function(t){
            return t.teamId == buddyData[0].values.team.basic.value;
          });

          if( t.length > 0 ) {
            buddy_stats['team'] = buddyData[0].values.team.basic.value == 17 ? 'Bravo' : 'Alpha';
            buddy_stats['outcome'] = t[0].standing.basic.value; // 0 == win

            // Primeval Dmg %
            var team_mates = pgcr.entries.filter(function(p){
              return p.values.team.basic.value == buddyData[0].values.team.basic.value;
            });

            var team_primeval_total_dmg = 0;

            for(var p=0; p<team_mates.length; p++) {
              team_primeval_total_dmg += team_mates[p].extended.values.primevalDamage.basic.value;
            }

            if( team_primeval_total_dmg > 0 ) {
              buddy_stats['primeval_dmg'] = Number(buddyData[0].extended.values.primevalDamage.basic.value / team_primeval_total_dmg * 100).toFixed(0);
            }
          }
        }
      }

      console.log( your_stats['motes_deposited'] );
      console.log( buddy_stats['motes_deposited'] );

      tableData.push({
        id: activity_instances.activity_id,
        activity_name: activity_definition[ pgcr.activityDetails.directorActivityHash ].displayProperties.name,
        date: moment(pgcr.period).format('D MMM Y, hh:mm A'),
        same_team: your_stats['team'] == buddy_stats['team'] ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>',
        outcome: your_stats['outcome'] == 0 ? '<span class="text-success">Victory</span>' : '<span class="text-danger">Defeat</span>',
        your_motes_deposited: your_stats['motes_deposited'],
        buddy_motes_deposited: buddy_stats['motes_deposited'],
        your_invasion_kills: your_stats['invasion_kills'],
        buddy_invasion_kills: buddy_stats['invasion_kills'],
        your_primeval_dmg: your_stats['primeval_dmg'] + "%",
        buddy_primeval_dmg: buddy_stats['primeval_dmg'] + "%",
        link: '<a class="text-dark" target="_blank" href="https://destinytracker.com/d2/pgcr/' + activity_instances[i].activity_id + '">Go</a>'
      });
    }
  }

  $('.stats-container').append('<div id="raid-stats-table"></div>');

  var format = {precision: 0};

  var table = new Tabulator("#raid-stats-table", {
    data:tableData, //assign data to table
    columns:[ //Define Table Columns
      {formatter:"rownum", width:40, headerSort:false},
      {title:"Activity ID", field:"id", visible: false, cssClass: 'activity_id'},
      {title:"Activity", field:"activity_name", cssClass: 'activity_name'},
      {title:"<div class='text-center'>Date</div>", field:"date", cssClass: 'text-right', sorter:"date", headerSort:false,
        sorterParams:{ format:"D MMM Y, hh:mm A" }
      },
      {title: "Teammate", field:"same_team", formatter:"html", cssClass: 'text-center', headerSort:false},
      {title: "<div class='mx-3'>Result</div>", field:"outcome", formatter:"html", cssClass: 'text-center', headerSort:false},
      {title: "Motes Depo.<br/><small>" + member.display_name + "</small>", field:"your_motes_deposited", cssClass: 'text-center', headerSort:false},
      {title: "Motes Depo.<br/><small><span class='table_header_buddy_name'>"+buddy_id+"</span></small>", field:"buddy_motes_deposited", cssClass: 'text-center', headerSort:false},
      {title: "Invasion Kills<br/><small>" + member.display_name + "</small>", field:"your_invasion_kills", cssClass: 'text-center', headerSort:false},
      {title: "Invasion Kills<br/><small><span class='table_header_buddy_name'>"+buddy_id+"</span></small>", field:"buddy_invasion_kills", cssClass: 'text-center', headerSort:false},
      {title: "Boss DMG<br/><small>" + member.display_name + "</small>", field:"your_primeval_dmg", cssClass: 'text-center', headerSort:false},
      {title: "Boss DMG<br/><small><span class='table_header_buddy_name'>"+buddy_id+"</span></small>", field:"buddy_primeval_dmg", cssClass: 'text-center', headerSort:false},
      {title:"D2 Tracker", field:"link", formatter:"html", cssClass: 'text-center', headerSort:false},
    ],
    initialSort: [
      {column:"date", dir:"desc"}
    ],
    layout:"fitDataFill",
    resizableColumns:false
  });

  function getBuddyName() {
    $.ajax({
      url: ccbNS.bungie_api_url+'/Destiny2/'+ccbNS.platform_id+'/Profile/'+buddy_id+'/?components=100',
      headers: {
        'X-API-Key': ccbNS.bungie_api
      }
    }).done(function(data){
      $('.buddy_name').text( data.Response.profile.data.userInfo.displayName + ' ❤️');
      $('.table_header_buddy_name').text( data.Response.profile.data.userInfo.displayName );
      $('nav .buddy_name').text( data.Response.profile.data.userInfo.displayName );
    });
  }

});
</script>
@endsection