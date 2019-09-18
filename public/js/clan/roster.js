$(document).ready(function(){
  $.get('/bungie/roster/get', function(members){

    var tableData = [];
    var charExist = false;

    for(var i=0; i<members.length; i++) {

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
      txt.innerHTML = members[i].platform_profile[0] ? members[i].platform_profile[0].steamDisplayName : '';
      var steamID = txt.value;

      tableData.push({
        name: members[i].display_name,
        steam: steamID,
        warlock: warlock.length > 0 ? warlock[0].light : 0,
        hunter: hunter.length > 0 ? hunter[0].light : 0,
        titan: titan.length > 0 ? titan[0].light : 0,
        last_online: moment(members[i].last_online).year() == '1970' ? 'n/a' : moment(members[i].last_online).format('DD MMM YYYY'),
        raid_activities: '<a href="/clan/activities/raid/'+members[i].id +'">View</a>',
        pvp_activities: '<a href="/clan/activities/pvp/'+members[i].id +'">View</a>',
        gambit_activities: '<a href="/clan/activities/gambit/'+members[i].id +'">View</a>',
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
          {title:"Name", field:"name", headerSort:false},
          {title:"Steam", field:"steam", headerSort:false},
          {title:"Warlock", field:"warlock", sorter:"number", headerSort:false},
          {title:"Hunter", field:"hunter", sorter:"number", headerSort:false},
          {title:"Titan", field:"titan", sorter:"number", headerSort:false},
          {title:"Last Online", field:"last_online", sorter:"date", sorterParams:{format:"DD MMM YYYY"}, headerSort:false},
          {title:"<div class='mx-3'>Raid</div><small>Activities</small>", field:"raid_activities", formatter: "html", cssClass: 'text-center', headerSort:false},
          {title:"<div class='mx-3'>PvP</div><small>Activities</small>", field:"pvp_activities", formatter: "html", cssClass: 'text-center', headerSort:false},
          {title:"<div class='mx-3'>Gambit</div><small>Activities</small>", field:"gambit_activities", formatter: "html", cssClass: 'text-center', headerSort:false},
        ],
        initialSort: [
          {column:"name", dir:"asc"},
          {column:"last_online", dir:"desc"}
        ],
        height:"500px",
        resizableColumns:true,
        layoutColumnsOnNewData:true,
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