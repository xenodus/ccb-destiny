$(document).ready(function(){
  $.get('/bungie/roster/get', function(members){

    var tableData = [];
    var charExist = false;

    for(var i=0; i<members.length; i++) {

      aliases = members[i].aliases.filter(function(alias){
        return alias.name != members[i].display_name;
      });

      if( aliases.length > 0 ) {
        known_aliases =  aliases.map(function(alias){
          return alias.name;
        });

        known_aliases = known_aliases.join(', ');
      }
      else {
        known_aliases = '';
      }

      if( members[i].platform_profile[0] ) {
        blizzardDisplayName = members[i].platform_profile[0].blizzardDisplayName ? members[i].platform_profile[0].blizzardDisplayName : '';
      }
      else {
        blizzardDisplayName = '';
      }

      if( members[i].characters.length > 0 )
        charExist = true;

      hunter = members[i].characters.filter(function(char){
        return char.class == 'hunter';
      });

      titan = members[i].characters.filter(function(char){
        return char.class == 'titan';
      });

      warlock = members[i].characters.filter(function(char){
        return char.class == 'warlock';
      });

      // Decode html entities
      var txt = document.createElement("textarea");
      txt.innerHTML = members[i].display_name;
      var steamID = txt.value;

      tableData.push({
        name: steamID,
        warlock: warlock.length > 0 ? warlock[0].light : 0,
        hunter: hunter.length > 0 ? hunter[0].light : 0,
        titan: titan.length > 0 ? titan[0].light : 0,
        last_online: moment(members[i].last_online).year() == '1970' ? 'n/a' : moment(members[i].last_online).format('DD MMM YYYY'),
        member_exotics: '<a href="/clan/exotics/'+members[i].id +'/'+slugify(members[i].display_name)+'">View</a>',
        raid_activities: '<a href="/clan/activities/raid/'+members[i].id+'/'+slugify(members[i].display_name)+'">View</a>',
        pvp_activities: '<a href="/clan/activities/pvp/'+members[i].id+'/'+slugify(members[i].display_name)+'">View</a>',
        gambit_activities: '<a href="/clan/activities/gambit/'+members[i].id+'/'+slugify(members[i].display_name)+'">View</a>',
        known_aliases: known_aliases,
        blizzardDisplayName: blizzardDisplayName,
      });
    }

    if( charExist ) {

      $('.loader').hide();
      $('.loader-text').hide();
      $('.filter-container').show();

      var table = new Tabulator("#roster-container", {
        data:tableData, //assign data to table
        layout:"fitDataFill", //fit columns to width of table (optional)
        columns:[ //Define Table Columns
          {formatter:"rownum", width:40, headerSort:false},
          {title:"Name", field:"name", headerSort:false, minWidth:180},
          {title:"Warlock", field:"warlock", sorter:"number", headerSort:false},
          {title:"Hunter", field:"hunter", sorter:"number", headerSort:false},
          {title:"Titan", field:"titan", sorter:"number", headerSort:false},
          {title:"Last Online", field:"last_online", sorter:"date", sorterParams:{format:"DD MMM YYYY"}, headerSort:false},
          {title:"<div class='mx-3'>Exotics</div><small>Uncollected</small>", field:"member_exotics", formatter: "html", cssClass: 'text-center', headerSort:false},
          {title:"<div class='mx-3'>Raid</div><small>Activities</small>", field:"raid_activities", formatter: "html", cssClass: 'text-center', headerSort:false},
          {title:"<div class='mx-3'>PvP</div><small>Activities</small>", field:"pvp_activities", formatter: "html", cssClass: 'text-center', headerSort:false},
          {title:"<div class='mx-3'>Gambit</div><small>Activities</small>", field:"gambit_activities", formatter: "html", cssClass: 'text-center', headerSort:false},
          {title:"Battle.net", field:"blizzardDisplayName", headerSort:false},
          {title:"Past Steam Aliases", field:"known_aliases", headerSort:false},
        ],
        initialSort: [
          {column:"name", dir:"asc"},
          {column:"last_online", dir:"desc"}
        ],
        height:"500px",
        resizableColumns:true,
        // layoutColumnsOnNewData:true,
      });

      $("#nameFilter").on("input", function(){
        table.setFilter("name", "like", $(this).val());
      });
    }
    else {
      $('.loader-text').html('Unable to retrieve members 😟 <br/>Bungie API might be under maintenance');
    }
  });
});