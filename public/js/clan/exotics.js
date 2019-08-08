$(document).ready(function(){
  $.get('/bungie/members/get', function(memberData){
    if( memberData.length > 0 ) {

      $('.loader-text').text('Fetching Collection...');

        $.get('/clan/exotics/get', function(exoticData){
          var tableData = [];
          var tableColumns = [
            {title:"Name", field:"name", frozen:true, widthGrow:1},
            {title:"Weapon", field:"missingWeapons", formatter:"html", widthGrow:1, headerSort:false},
            {title:"Warlock", field:"missingArmorsWarlock", formatter:"html", widthGrow:1, headerSort:false},
            {title:"Titan", field:"missingArmorsTitan", formatter:"html", widthGrow:1, headerSort:false},
            {title:"Hunter", field:"missingArmorsHunter", formatter:"html", widthGrow:1, headerSort:false},
          ];

          var weaponCollectionData = exoticData.clan_exotic_weapon_collection;
          var armorCollectionData = exoticData.clan_exotic_armor_collection;
          var exoticItemsData = exoticData.exotic_definition;

          for(var i=0; i<memberData.length; i++) {

            var tableDataEntry = {
              name: memberData[i].destinyUserInfo.displayName
            };

            var missingWeapons = [];
            var missingWeaponsStr = '';
            var missingArmorsWarlock = [];
            var missingArmorsWarlockStr = '';
            var missingArmorsTitan = [];
            var missingArmorsTitanStr = '';
            var missingArmorsHunter = [];
            var missingArmorsHunterStr = '';

            var memberWeaponCollectionData = weaponCollectionData.filter(function(data){
              return data.user_id == memberData[i].destinyUserInfo.membershipId;
            });

            var memberArmorCollectionData = armorCollectionData.filter(function(data){
              return data.user_id == memberData[i].destinyUserInfo.membershipId;
            });

            if( memberWeaponCollectionData.length > 0 ) {
              for(var j=0; j<memberWeaponCollectionData.length; j++) {
                if( memberWeaponCollectionData[j].is_collected == 0 ) {

                  exoticWeapon = exoticItemsData.filter(function(data){
                    return data.id == memberWeaponCollectionData[j].item_hash;
                  });

                  if( exoticWeapon ) {
                    missingWeapons.push(exoticWeapon[0]);
                  }
                }
              }
            }

            if( memberArmorCollectionData.length > 0 ) {
              for(var j=0; j<memberArmorCollectionData.length; j++) {
                if( memberArmorCollectionData[j].is_collected == 0 ) {

                  exoticArmor = exoticItemsData.filter(function(data){
                    return data.id == memberArmorCollectionData[j].item_hash;
                  });

                  if( exoticArmor ) {
                    if( memberArmorCollectionData[j].class == 'warlock' ) {
                      missingArmorsWarlock.push(exoticArmor[0]);
                    }
                    else if( memberArmorCollectionData[j].class == 'titan' ) {
                      missingArmorsTitan.push(exoticArmor[0]);
                    }
                    else {
                      missingArmorsHunter.push(exoticArmor[0]);
                    }
                  }
                }
              }
            }

            // Sort
            missingWeapons = _.sortBy(missingWeapons, ['name']);
            missingArmorsWarlock = _.sortBy(missingArmorsWarlock, ['name']);
            missingArmorsTitan = _.sortBy(missingArmorsTitan, ['name']);
            missingArmorsHunter = _.sortBy(missingArmorsHunter, ['name']);

            console.log(missingArmorsWarlock);

            for(var j=0; j<missingWeapons.length; j++) {
              missingWeaponsStr += `
              <div class="d-flex mb-1 align-items-center exotic-item">
                <img class="img-fluid" src="https://bungie.net`+missingWeapons[j].icon+`" style="width: 30px; height: 30px; margin-right: 5px;"/>`+missingWeapons[j].name+`
              </div>
              `;
            }

            for(var j=0; j<missingArmorsWarlock.length; j++) {
              missingArmorsWarlockStr += `
              <div class="d-flex mb-1 align-items-center exotic-item">
                <img class="img-fluid" src="https://bungie.net`+missingArmorsWarlock[j].icon+`" style="width: 30px; height: 30px; margin-right: 5px;"/>`+missingArmorsWarlock[j].name+`
              </div>
              `;
            }

            for(var j=0; j<missingArmorsTitan.length; j++) {
              missingArmorsTitanStr += `
              <div class="d-flex mb-1 align-items-center exotic-item">
                <img class="img-fluid" src="https://bungie.net`+missingArmorsTitan[j].icon+`" style="width: 30px; height: 30px; margin-right: 5px;"/>`+missingArmorsTitan[j].name+`
              </div>
              `;
            }

            for(var j=0; j<missingArmorsHunter.length; j++) {
              missingArmorsHunterStr += `
              <div class="d-flex mb-1 align-items-center exotic-item">
                <img class="img-fluid" src="https://bungie.net`+missingArmorsHunter[j].icon+`" style="width: 30px; height: 30px; margin-right: 5px;"/>`+missingArmorsHunter[j].name+`
              </div>
              `;
            }

            tableDataEntry['missingWeapons'] = missingWeaponsStr;
            tableDataEntry['missingArmorsWarlock'] = missingArmorsWarlockStr;
            tableDataEntry['missingArmorsTitan'] = missingArmorsTitanStr;
            tableDataEntry['missingArmorsHunter'] = missingArmorsHunterStr;
            tableData.push(tableDataEntry);
          }

          $('.stats-container').append('<div id="exotics-table"></div>');
          $('.loader').hide();
          $('.loader-text').hide();
          $('.filter-container').show();

          var table = new Tabulator("#exotics-table", {
            data:tableData, //assign data to table
            columns: tableColumns,
            initialSort: [
              {column:"name", dir:"asc"},
            ],
            layout:"fitDataFill",
            height: "600px",
            resizableColumns:false,
          });

          $('[data-toggle="tooltip"]').tooltip();

          $("#nameFilter").on("input", function(){
            table.setFilter("name", "like", $(this).val());
          });
        });
    }
  });
});