$(document).ready(function(){
  $.get('/bungie/members/get', function(memberData){

    if( memberData.length > 0 ) {

      $('.loader-text').text('Fetching Triumphs...');

      $.get('/clan/seals/get', function(sealData){

        var tableData = [];
        var tableColumns = [
          {title:"Name", field:"name", formatter:"html", frozen:true},
        ];

        for(var i=0; i<sealData.length; i++) {
          var username = memberData.filter(function(member){ return member.destinyUserInfo.membershipId == sealData[i].id }).map(function(member){ return member.destinyUserInfo.displayName })[0];

          var tableDataEntry = {
            name: username+'<a href="/clan/seals/member/'+sealData[i].id+'"><i class="fas fa-external-link-alt ml-1 fa-xs" style="position: relative; bottom: 1px;"></i></a>'
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
          tableData.push(tableDataEntry);
        }

        tableColumns.push({title:"Total", field:"total", visible:true});

        $('.stats-container').append('<div id="stats-table"></div>');
        $('.loader').hide();
        $('.loader-text').hide();

        var table = new Tabulator("#stats-table", {
          data:tableData, //assign data to table
          columns: tableColumns,
          initialSort: [
            {column:"name", dir:"asc"},
            {column:"total", dir:"desc"},
          ],
          layout:"fitDataFill",
          // height: "350px",
          resizableColumns:false,
        });

      });
    }
    else {
      $('.loader-text').html('Unable to retrieve members 😟 <br/>Bungie API might be under maintenance');
    }

  });
});