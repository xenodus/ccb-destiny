var queueStatus = 0;

$(document).ready(function(){

  print_raid_stats();

  function print_raid_stats() {
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

        $('.loader-text').text('Fetching Raid Stats...');

        $.get('/bungie/raid/get', function(memberRaidData){

          //console.log(memberData);

          var tableData = [];

          for(var i=0; i<memberData.length; i++) {

            $('.loader-text').text('Processing ' + (i+1) + ' of ' + memberData.length + '...');

            raidData = memberRaidData.filter(function(member){ return member.user_id == memberData[i].destinyUserInfo.membershipId })[0];

            if( raidData ) {
              tableData.push({
                membershipId: memberData[i].destinyUserInfo.membershipId,
                name: memberData[i].destinyUserInfo.displayName+'<a href="https://raid.report/pc/'+memberData[i].destinyUserInfo.membershipId+'" target="_blank" class="text-dark"><i class="fas fa-external-link-alt ml-1 fa-xs" style="position: relative; bottom: 1px;"></i></a>',
                levi: ( raidData.levi == 0 && raidData.levip ==0 ) ? 0 : ( raidData.levi + raidData.levip + "<div class='mx-1'><small>N: " + raidData.levi + " <span class='ml-1'>P: " + raidData.levip + "</span></small></div>" ),
                //levip: raidData.levip,
                eow: ( raidData.eow == 0 && raidData.eowp == 0 ) ? 0 : ( raidData.eow + raidData.eowp + "<div class='mx-1'><small>N: " + raidData.eow + " <span class='ml-1'>P: " + raidData.eowp + "</span></small></div>" ),
                //eowp: raidData.eowp,
                sos: ( raidData.sos == 0 && raidData.sosp == 0 ) ? 0 : ( raidData.sos + raidData.sosp + "<div class='mx-1'><small>N: " + raidData.sos + " <span class='ml-1'>P: " + raidData.sosp + "</span></small></div>" ),
                //sosp: raidData.sosp,
                lw: raidData.lw,
                petra: raidData.petra > 0 ? '<div class="text-center"><i class="fas fa-check text-success"></i></div>' : '<div class="text-center"><i class="fas fa-times text-danger"></i></div>',
                sotp: raidData.sotp,
                diamond: raidData.diamond > 0 ? '<div class="text-center"><i class="fas fa-check text-success"></i></div>' : '<div class="text-center"><i class="fas fa-times text-danger"></i></div>',
                cos: raidData.cos,
                crown: raidData.crown > 0 ? '<div class="text-center"><i class="fas fa-check text-success"></i></div>' : '<div class="text-center"><i class="fas fa-times text-danger"></i></div>',
                gos: raidData.gos,
                perfection: raidData.perfection > 0 ? '<div class="text-center"><i class="fas fa-check text-success"></i></div>' : '<div class="text-center"><i class="fas fa-times text-danger"></i></div>',
                total: (raidData.levi+raidData.levip+raidData.eow+raidData.eowp+raidData.sos+raidData.sosp+raidData.lw+raidData.sotp+raidData.cos+raidData.gos),
              });
            }
            else {
              tableData.push({
                membershipId: memberData[i].destinyUserInfo.membershipId,
                name: memberData[i].destinyUserInfo.displayName+'<a href="https://raid.report/pc/'+memberData[i].destinyUserInfo.membershipId+'" target="_blank" class="text-dark"><i class="fas fa-external-link-alt ml-1 fa-xs" style="position: relative; bottom: 1px;"></i></a>',
                levi: 0,
                levip: 0,
                eow: 0,
                eowp: 0,
                sos: 0,
                sosp: 0,
                lw: 0,
                petra: '<div class="text-center"><i class="fas fa-times text-danger"></i></div>',
                sotp: 0,
                diamond: '<div class="text-center"><i class="fas fa-times text-danger"></i></div>',
                cos: 0,
                crown: '<div class="text-center"><i class="fas fa-times text-danger"></i></div>',
                gos: 0,
                perfection: '<div class="text-center"><i class="fas fa-times text-danger"></i></div>',
                total: 0,
              });

              console.log("No data: " + memberData[i].destinyUserInfo.displayName);
            }
          }

          $('.loader-text').text('Generating Table...');

          $('.stats-container').append('<div id="raid-stats-table"></div>');

          $('.loader').hide();
          $('.loader-text').hide();
          $('.filter-container').show();

          var autoNumFormatter = function(){
            return $("#raid-stats-table .tabulator-row").length;
          };

          var format = {precision: 0};

          var table = new Tabulator("#raid-stats-table", {
            data:tableData, //assign data to table
            layout:"fitColumns", //fit columns to width of table (optional)
            columns:[ //Define Table Columns
              //{formatter:autoNumFormatter, width:40},
              {title:"Name", field:"name", formatter:"html", formatterParams: format, frozen:true},
              {title:"Member ID", field:"membershipId", visible: false, cssClass: 'memberID'},
              {title:"Levi", field:"levi", headerSort:false, formatter:"html", cssClass: 'text-center'},
              // {title:"P Levi", field:"levip", formatter:"money", formatterParams: format},
              {title:"EOW", field:"eow", headerSort:false, formatter:"html", cssClass: 'text-center'},
              //{title:"P EOW", field:"eowp", formatter:"money", formatterParams: format},
              {title:"SOS", field:"sos", headerSort:false, formatter:"html", cssClass: 'text-center'},
              //{title:"P SOS", field:"sosp", formatter:"money", formatterParams: format},
              {title:"LW", field:"lw", formatter:"money", formatterParams: format, cssClass: 'text-center'},
              {title:"Flawless LW", field:"petra", formatter:"html"},
              {title:"SoTP", field:"sotp", formatter:"money", formatterParams: format, cssClass: 'text-center'},
              {title:"Flawless SoTP", field:"diamond", formatter:"html"},
              {title:"CoS", field:"cos", formatter:"money", formatterParams: format, cssClass: 'text-center'},
              {title:"Flawless CoS", field:"crown", formatter:"html"},
              {title:"GoS", field:"gos", formatter:"money", formatterParams: format, cssClass: 'text-center'},
              {title:"Flawless GoS", field:"perfection", formatter:"html"},
              {title:"Total", field:"total", formatter:"money", formatterParams: format, cssClass: 'text-center'},
            ],
            initialSort: [
              {column:"total", dir:"desc"}
            ],
            layout:"fitDataFill",
            height:"500px",
            resizableColumns:false,
          });

          $("#nameFilter").on("input", function(){
            table.setFilter("name", "like", $(this).val());
          });

          $('.stats-container').append('<div id="raid-stats-info" class="text-center"><small>Last checked: '+raidData.last_updated+'</small></div>');
        });
      }
      else {
        $('.loader-text').html('Unable to retrieve members 😟 <br/>Bungie API might be under maintenance');
      }

    });
  }
});