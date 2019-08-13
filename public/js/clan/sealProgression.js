var sealHashes = {
  'Cursebreaker': {
    'name': 'The Dreaming City',
    'description': 'Complete all Dreaming City Triumphs.',
    'hash': '1693645129',
    'objectives': [
      1522035006,
      3405837767,
      2144075647,
      2144075646,
      2144075645,
      749838902,
      2314271318,
      1079063233,
      2101481124,
      1441693899,
      1416291884
    ]
  },
  'Reckoner': {
    'name': 'Reckoner',
    'description': 'Complete all Jokers Wild Triumphs.',
    'hash': '1313291220',
    'objectives': [
      2794426212,
      3601025763,
      2690344985,
      1466482627,
      590872239,
      1559002718,
      2129401906,
      417454997,
      1599750028,
      3191146526,
      452100546,
      2874049730,
      1291112668
    ]
  },
  'Rivensbane': {
    'name': 'Raids',
    'description': 'Complete all Raid Triumphs.',
    'hash': '2182090828',
    'objectives': [
      751035753,
      2196415799,
      3806804934,
      2822000740,
      613834558,
      567795114,
      2195455623,
      3899933775,
      4177910003,
      149192209,
      2398356743,
      1373122528,
      1672792871,
      1004616690,
      2983721440,
      2673596601
    ]
  },
  'Chronicler': {
    'name': 'Lore',
    'description': 'Complete all Lore Triumphs.',
    'hash': '1754983323',
    'objectives': [
      1841779644,
      2234714130,
      195315622,
      3084088704,
      3518847135,
      3523455218,
      565725664,
      157348228,
      1971661116,
      2122886722
    ]
  },
  'Dredgen': {
    'name': 'Gambit',
    'description': 'Complete all Gambit Triumphs.',
    'hash': '3798931976',
    'objectives': [
      1975718024,
      3948150872,
      1676823618,
      1694320690,
      28292202,
      3901785488,
      1071663279,
      3802330071,
      3492541163,
      2734663473,
      723502823
    ]
  },
  'Wayfarer': {
    'name': 'Destinations',
    'description': 'Complete all Destination Triumphs.',
    'hash': '2757681677',
    'objectives': [
      3488769908,
      2676320666,
      4269157841,
      4174911913,
      3686586344,
      3015917345,
      3015917346,
      3015917347,
      1683000545,
      2137287373,
      1632551190,
      3630489247,
      633055621,
      3408598133,
      3632308741,
      3727584102,
      1417930213,
      376008854,
      641038864,
      3517489196,
      3308633273,
      3122478469
    ]
  },
  'Unbroken': {
    'name': 'Crucible',
    'description': 'Complete all lifetime Valor and Glory rank triumphs.',
    'hash': '3369119720',
    'objectives': [
      1711079800,
      277879384,
      559943871,
      115001349,
      3008350477,
      200792717,
      1486384135,
      2158033469,
      4185918315,
      1602622486
    ]
  },
  'Blacksmith': {
    'name': 'Black Armory',
    'description': 'Complete all Black Armory Triumphs.',
    'hash': '2053985130',
    'objectives': [
      4160670554,
      3723768609,
      2748367119,
      578385083,
      212587483,
      2957876347,
      2487226217,
      142013189,
      903845240,
      3608027344,
      2172518965,
      2648109757,
      4162926221,
      1804999028,
      1428463716,
      2489553554,
      622242972
    ]
  },
  'Shadow': {
    'name': 'A Shadow Rises',
    'description': 'Complete all Season of Opulence Triumphs.',
    'hash': '1883929036',
    'objectives': [
      52802522,
      2439024314,
      2439024319,
      2439024318,
      1959753477,
      3141945846,
      2351146132,
      137611858,
      1593047161,
      1769517637,
      2372209106,
      1987500905,
      2575965017,
      2422246600,
      2422246592,
      2422246593,
      2472579457,
      1558682416,
      1558682429,
      1575460005,
      1575460004,
      1575460003,
      1575460002
    ]
  },
  'MMXIX': {
    'name': 'A Shadow Rises',
    'description': 'Complete these feats of strength before 8/27/2019 to claim your unique rewards.',
    'hash': '2254764897',
    'objectives': [
      1076096270,
      1732935196,
      2630441634,
      1386173696,
      444152197,
      1518215963,
      2181708792,
      1294551872,
      4175049478,
      1492080644,
      488987738,
      1567884322,
      3710831503,
      3728188192,
      700444358,
      2556866202,
      277879384,
      3332500563,
      1913403057,
      1760928123,
      2314271318,
      2195455623,
      4060320345,
      1558682421
    ]
  },
};

$(document).ready(function(){
  if( "member_id" in ccbNS ) {
    $.ajax({
      url: 'https://www.bungie.net/Platform/Destiny2/4/Profile/'+ccbNS['member_id']+'/?components=900',
      headers: {
        'X-API-Key': '856136fabe704c149dd4bd41344b54c8'
      }
    }).done(function(data){

      if( data.Response ) {

        memberData = data.Response;

        $.get('/api/manifest/get_record_definition', function(recordDefinition){
          //console.log(recordDefinition);
          //console.log(sealHashes);

          $('#sub-menu > ul').append('<li class="nav-item pt-4"><a class="nav-link disabled" href="#" aria-disabled="true">Seals</a></li>');

          for(var seal in sealHashes) {

            // Seal Header
            $('.stats-container').append('<div id="'+seal.toLowerCase()+'" class="col-md-12 mb-4 pt-md-5"><h2 class="text-yellow text-left mb-1 stats-container-header"><a href="#'+seal.toLowerCase()+'">'+seal+'</a></h2><div class="text-left text-secondary mb-2">'+sealHashes[seal].description+'</div></div>');

            var completed = [];
            var inProgress = [];
            var multipleObjectivesProgress = [];
            var singleObjectivesProgress = [];

            for(var i=0; i<sealHashes[seal]['objectives'].length; i++) {
              var recordID = sealHashes[seal]['objectives'][i];

              var isCompleted = false;
              var multipleObjectives = false;
              var objectProgress = '';
              var multipleObjectivesItem = {
                'completed': 0,
                'total': 0,
                'inProgress': []
              };

              // Check characters
              for(var char_id in memberData.characterRecords.data) {
                if( recordID in memberData.characterRecords.data[char_id].records ) {
                  if( memberData.characterRecords.data[char_id].records[recordID].objectives.length == 1 && memberData.characterRecords.data[char_id].records[recordID].objectives[0].complete == true ) {
                    isCompleted = true;
                  }
                  else if( memberData.characterRecords.data[char_id].records[recordID].objectives.length > 1 && memberData.characterRecords.data[char_id].records[recordID].objectives.filter(function(r){ return r.complete == true }).length == memberData.characterRecords.data[char_id].records[recordID].objectives.length ) {
                    isCompleted = true;
                  }
                  else if( memberData.characterRecords.data[char_id].records[recordID].objectives.length > 1 && memberData.characterRecords.data[char_id].records[recordID].objectives.filter(function(r){ return r.complete == true }).length != memberData.characterRecords.data[char_id].records[recordID].objectives.length ) {
                    multipleObjectives = true;
                    multipleObjectivesItem['completed'] = memberData.characterRecords.data[char_id].records[recordID].objectives.filter(function(r){ return r.complete == true }).length;
                    multipleObjectivesItem['total'] = memberData.characterRecords.data[char_id].records[recordID].objectives.length;
                    multipleObjectivesItem['inProgress'] = memberData.characterRecords.data[char_id].records[recordID].objectives.filter(function(r){ return r.complete != true });
                  }
                }
              }

              // Check profile
              if( recordID in memberData.profileRecords.data.records ) {
                if( memberData.profileRecords.data.records[recordID].objectives.length == 1 && memberData.profileRecords.data.records[recordID].objectives[0].complete == true ) {
                  isCompleted = true;
                }
                else if( memberData.profileRecords.data.records[recordID].objectives.length > 1 && memberData.profileRecords.data.records[recordID].objectives.filter(function(r){ return r.complete == true }).length == memberData.profileRecords.data.records[recordID].objectives.length ) {
                  isCompleted = true;
                }
                else if( memberData.profileRecords.data.records[recordID].objectives.length > 1 && memberData.profileRecords.data.records[recordID].objectives.filter(function(r){ return r.complete == true }).length != memberData.profileRecords.data.records[recordID].objectives.length ) {
                  multipleObjectives = true;
                  multipleObjectivesItem['completed'] = memberData.profileRecords.data.records[recordID].objectives.filter(function(r){ return r.complete == true }).length;
                  multipleObjectivesItem['total'] = memberData.profileRecords.data.records[recordID].objectives.length;
                  multipleObjectivesItem['inProgress'] = memberData.profileRecords.data.records[recordID].objectives.filter(function(r){ return r.complete != true });
                }

                if( memberData.profileRecords.data.records[recordID].objectives.length == 1 && memberData.profileRecords.data.records[recordID].objectives[0].complete == false ) {
                  if ( memberData.profileRecords.data.records[recordID].objectives[0].progress != 0 && memberData.profileRecords.data.records[recordID].objectives[0].completionValue != 0 ) {
                    objectProgress = memberData.profileRecords.data.records[recordID].objectives[0].progress + "/" + memberData.profileRecords.data.records[recordID].objectives[0].completionValue;
                  }
                }
              }

              if( isCompleted )
                completed.push(recordID);
              else {
                inProgress.push(recordID);

                if( multipleObjectives ) {
                  multipleObjectivesProgress[recordID] = multipleObjectivesItem;
                }
                else {
                  singleObjectivesProgress[recordID] = objectProgress;
                }
              }
            }

            //console.log(multipleObjectivesProgress);

            var total = completed.length + inProgress.length;
            var completedPercent = Math.floor(completed.length / total * 100);

            // Seal Header Progress Bar
            $('#' + seal.toLowerCase()).append('<div class="progress"><div class="progress-bar bg-success" style="width: '+completedPercent+'%;" role="progressbar" aria-valuenow="'+completedPercent+'" aria-valuemin="0" aria-valuemax="100">'+completedPercent+'%</div></div>');

            if( completedPercent != 100 ) {
              // Completed / In-progress containers
              $('#' + seal.toLowerCase()).append('<div class="row mt-3"></div>');
              $('#' + seal.toLowerCase() + ' > div.row').append('<div id="'+seal.toLowerCase()+'-inProgress" class="col-md-6 text-left mb-2"><h6 class="text-yellow">In-Progress ('+inProgress.length+'/'+total+')</h6></div>');
              $('#' + seal.toLowerCase() + ' > div.row').append('<div id="'+seal.toLowerCase()+'-completed" class="col-md-6 text-left mb-2"><h6 class="text-yellow">Completed ('+completed.length+'/'+total+')</h6></div>');

              // Completed Triumphs
              for(var key in completed) {
                if( completed[key] in recordDefinition ) {
                  var tooltip = `
                  <div>
                    <h6 class='font-weight-bold mb-1'>`+recordDefinition[completed[key]]["displayProperties"]["name"]+`</h6>
                    <div class='d-flex align-items-center'>
                      <div>
                        <img src='https://bungie.net`+recordDefinition[completed[key]]["displayProperties"]["icon"]+`' class='mt-1 mb-1 mr-2 tooltip-icon' style='width: 50px; height: 50px;'/>
                      </div>
                      <div>
                        `+recordDefinition[completed[key]]["displayProperties"]["description"].replace(/"/g, "'")+`
                      </div>
                    </div>
                  </div>
                  `;

                  var str = `
                  <div class="d-flex mb-1 vendor-item" data-toggle="tooltip" title="`+tooltip+`" data-hash="`+completed[key]+`">
                    <img class="img-fluid" src="https://bungie.net`+recordDefinition[completed[key]]["displayProperties"]["icon"]+`" style="width: 20px; height: 20px; margin-right: 5px; position: relative; top: 2px;"/>`+recordDefinition[completed[key]]["displayProperties"]["name"]+`
                  </div>
                  `;

                  $('#'+seal.toLowerCase()+'-completed').append("<div>"+str+"</div>");
                }
              }

              // In-progress Triumphs
              for(var key in inProgress) {
                if( inProgress[key] in recordDefinition ) {

                  var objectivesProgressStr = '';

                  if( inProgress[key] in multipleObjectivesProgress ) {
                    objectivesProgressStr = ' (' + multipleObjectivesProgress[inProgress[key]]['completed'] + '/' + multipleObjectivesProgress[inProgress[key]]['total'] + ')';
                  }

                  if( inProgress[key] in singleObjectivesProgress ) {
                    if( singleObjectivesProgress[inProgress[key]] ) {
                      objectivesProgressStr = ' (' + singleObjectivesProgress[inProgress[key]] + ')';
                    }
                  }

                  var tooltip = `
                  <div>
                    <h6 class='font-weight-bold mb-1'>`+recordDefinition[inProgress[key]]["displayProperties"]["name"]+objectivesProgressStr+`</h6>
                    <div class='d-flex align-items-center'>
                      <div>
                        <img src='https://bungie.net`+recordDefinition[inProgress[key]]["displayProperties"]["icon"]+`' class='mt-1 mb-1 mr-2 tooltip-icon' style='width: 50px; height: 50px;'/>
                      </div>
                      <div>
                        `+recordDefinition[inProgress[key]]["displayProperties"]["description"].replace(/"/g, "'")+`
                      </div>
                    </div>
                  </div>
                  `;

                  var str = `
                  <div class="d-flex mb-1 vendor-item" data-toggle="tooltip" title="`+tooltip+`" data-hash="`+inProgress[key]+`">
                    <img class="img-fluid" src="https://bungie.net`+recordDefinition[inProgress[key]]["displayProperties"]["icon"]+`" style="width: 20px; height: 20px; margin-right: 5px; position: relative; top: 2px;"/>`+recordDefinition[inProgress[key]]["displayProperties"]["name"]+objectivesProgressStr+`
                  </div>
                  `;

                  $('#'+seal.toLowerCase()+'-inProgress').append("<div>"+str+"</div>");
                }
              }

              $('#sub-menu > ul').append('<li class="nav-item"><a class="nav-link" href="#'+seal.toLowerCase()+'">'+seal+' ('+completedPercent+'%)</a></li>');
            }
            else {
              $('#sub-menu > ul').append('<li class="nav-item"><a class="nav-link" href="#'+seal.toLowerCase()+'">'+seal+'<i class="fas fa-check text-success ml-2"></i></a></li>');
              $('.stats-container #' + seal.toLowerCase() + ' h2').append('<i class="fas fa-check text-success ml-2"></i>');
            }
          }

          $('.loader').hide();
          $('.loader-text').hide();
          $("h1#member-name").show();
          $("#back-nav").show();
          $('#sub-menu').show();
          $('[data-toggle="tooltip"]').tooltip({
            html: true
          });
        });
      }
      else {
        $('.loader-text').html('Unable to retrieve data 😟 <br/>Bungie API might be under maintenance');
      }
    });
  }
});