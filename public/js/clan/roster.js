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

      tableData.push({
        name: members[i].display_name,
        warlock: warlock.length > 0 ? warlock[0].light : 0,
        hunter: hunter.length > 0 ? hunter[0].light : 0,
        titan: titan.length > 0 ? titan[0].light : 0,
        last_online: moment(members[i].last_online).year() == '1970' ? 'n/a' : moment(members[i].last_online).format('DD MMM YYYY'),
      });
    }

    if( charExist ) {

      $('.loader').hide();
      $('.loader-text').hide();

      var table = new Tabulator("#roster-container", {
        data:tableData, //assign data to table
        layout:"fitColumns", //fit columns to width of table (optional)
        columns:[ //Define Table Columns
          {formatter:"rownum", width:40, headerSort:false},
          {title:"Name", field:"name", headerSort:false},
          {title:"Warlock", field:"warlock", sorter:"number", headerSort:false},
          {title:"Hunter", field:"hunter", sorter:"number", headerSort:false},
          {title:"Titan", field:"titan", sorter:"number", headerSort:false},
          {title:"Last Online", field:"last_online", sorter:"date", sorterParams:{format:"DD MMM YYYY"}, headerSort:false},
        ],
        initialSort: [
          {column:"name", dir:"asc"},
          {column:"last_online", dir:"desc"}
        ],
        layout:"fitDataFill",
        height:"500px",
        resizableColumns:false,
      });
    }
    else {
      $('.loader-text').html('Unable to retrieve members 😟 <br/>Bungie API might be under maintenance');
    }
  });
});