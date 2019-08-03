var queueStatus = 0;

$(document).ready(function(){

  print_raid_stats();

  function print_raid_stats() {
    $.get('/bungie/members/get', function(memberData){

      if( memberData.length > 0 ) {

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
                levi: raidData.levi,
                levip: raidData.levip,
                eow: raidData.eow,
                eowp: raidData.eowp,
                sos: raidData.sos,
                sosp: raidData.sosp,
                lw: raidData.lw,
                petra: raidData.petra > 0 ? '<div class="text-center"><i class="fas fa-check text-success"></i></div>' : '<div class="text-center"><i class="fas fa-times text-danger"></i></div>',
                sotp: raidData.sotp,
                diamond: raidData.diamond > 0 ? '<div class="text-center"><i class="fas fa-check text-success"></i></div>' : '<div class="text-center"><i class="fas fa-times text-danger"></i></div>',
                cos: raidData.cos,
                crown: raidData.crown > 0 ? '<div class="text-center"><i class="fas fa-check text-success"></i></div>' : '<div class="text-center"><i class="fas fa-times text-danger"></i></div>',
                total: (raidData.levi+raidData.levip+raidData.eow+raidData.eowp+raidData.sos+raidData.sosp+raidData.lw+raidData.sotp+raidData.cos),
              });
            }
            else {
              console.log(memberData[i].destinyUserInfo.membershipId);
            }
          }

          $('.loader-text').text('Generating Table...');

          $('.stats-container').append('<div id="raid-stats-table"></div>');

          $('.loader').hide();
          $('.loader-text').hide();

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
              {title:"Levi", field:"levi", formatter:"money", formatterParams: format},
              {title:"P Levi", field:"levip", formatter:"money", formatterParams: format},
              {title:"EOW", field:"eow", formatter:"money", formatterParams: format},
              {title:"P EOW", field:"eowp", formatter:"money", formatterParams: format},
              {title:"SOS", field:"sos", formatter:"money", formatterParams: format},
              {title:"P SOS", field:"sosp", formatter:"money", formatterParams: format},
              {title:"LW", field:"lw", formatter:"money", formatterParams: format},
              {title:"Flawless LW", field:"petra", formatter:"html"},
              {title:"SoTP", field:"sotp", formatter:"money", formatterParams: format},
              {title:"Flawless SoTP", field:"diamond", formatter:"html"},
              {title:"CoS", field:"cos", formatter:"money", formatterParams: format},
              {title:"Flawless CoS", field:"crown", formatter:"html"},
              {title:"Total", field:"total", formatter:"money", formatterParams: format},
            ],
            initialSort: [
              {column:"total", dir:"desc"}
            ],
            layout:"fitDataFill",
            height:"500px",
            resizableColumns:false,
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