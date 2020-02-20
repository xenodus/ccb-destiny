$(document).ready(function(){
  $.ajax({
    url: ccbNS.bungie_api_url+'/GroupV2/'+ccbNS.clan_id+'/Members/',
    headers: {
      'X-API-Key': ccbNS.bungie_api
    }
  })
  .fail(function(){
    $('.loader-text').html('Unable to retrieve members 😟 <br/>Bungie API might be under maintenance');
  })
  .done(function(data){

    if( data.Response.results && data.Response.results.length > 0 ) {

      memberData = data.Response.results;

      $('.loader-text').text('Fetching Triumphs...');

      $.get('/clan/seals/get', function(sealData){

        var tableData = [];
        var tableColumns = [
          {title:"Name", field:"name", formatter:"html", frozen:true, minWidth:180},
        ];

        for(var i=0; i<sealData.length; i++) {
          var username = memberData.filter(function(member){ return member.destinyUserInfo.membershipId == sealData[i].id }).map(function(member){ return member.destinyUserInfo.displayName })[0];

          steamID = getSanitizedName(username);

          var tableDataEntry = {
            name: '<a data-sort-name="'+steamID+'" href="/clan/seals/member/'+sealData[i].id+'/'+slugify(steamID)+'/" class="text-dark member-name">'+steamID+'<i class="fas fa-external-link-alt ml-1 fa-xs" style="position: relative; bottom: 1px;"></i></a>'
          };

          var memberSealData = JSON.parse( sealData[i].data );
          var memberSealTotal = 0;

          for(const key in memberSealData) {
            tableDataEntry[key] = memberSealData[key] > 0 ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>';

            memberSealTotal = memberSealData[key] > 0 ? memberSealTotal + 1 : memberSealTotal;

            if(i==0) {
              tableColumns.push({title:key, field:key, formatter:"html", cssClass: "text-center"});
            }
          }

          tableDataEntry["total"] = memberSealTotal;
          // tableDataEntry["link"] = '<a href="/clan/seals/member/'+sealData[i].id+'">Go</a>';
          tableData.push(tableDataEntry);
        }

        tableColumns.push({title:"Total", field:"total", cssClass: "text-center", visible:true});
        // tableColumns.push({title:"Details", field:"link", formatter:"html", cssClass: "text-center"});

        $('.stats-container').append('<div id="stats-table"></div>');
        $('.loader').hide();
        $('.loader-text').hide();
        $('.filter-container').show();

        var table = new Tabulator("#stats-table", {
          data:tableData, //assign data to table
          columns: tableColumns,
          initialSort: [
            {column:"name", dir:"asc"},
            {column:"total", dir:"desc"},
          ],
          layout:"fitDataFill",
          height: "500px",
          resizableColumns:true,
        });

        $("#nameFilter").on("input", function(){
          table.setFilter("name", "like", $(this).val());
        });

      });
    }
    else {
      $('.loader-text').html('Unable to retrieve members 😟 <br/>Bungie API might be under maintenance');
    }

  });
});