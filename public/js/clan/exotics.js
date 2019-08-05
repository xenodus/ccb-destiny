$(document).ready(function(){
  $.get('/bungie/members/get', function(memberData){
    if( memberData.length > 0 ) {

      $('.loader-text').text('Fetching Collection...');

        $.get('/clan/exotics/get', function(exoticData){
          var tableData = [];
          var tableColumns = [
            {title:"Name", field:"name", frozen:true, widthGrow:1},
            {title:"Weapons", field:"missingWeapons", formatter:"html", widthGrow:1},
            {title:"Warlock", field:"missingArmorsWarlock", formatter:"html", widthGrow:1},
            {title:"Titan", field:"missingArmorsTitan", formatter:"html", widthGrow:1},
            {title:"Hunter", field:"missingArmorsHunter", formatter:"html", widthGrow:1},
          ];

          var weaponCollectionData = exoticData.clan_exotic_weapon_collection;
          var armorCollectionData = exoticData.clan_exotic_armor_collection;
          var exoticItemsData = exoticData.exotic_definition;

          for(var i=0; i<memberData.length; i++) {

            var tableDataEntry = {
              name: memberData[i].destinyUserInfo.displayName
            };

            var missingWeaponsStr = '';
            var missingArmorsWarlockStr = '';
            var missingArmorsTitanStr = '';
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
                    missingWeaponsStr += `
                    <div class="d-flex mb-1 align-items-center">
                      <img class="img-fluid" src="https://bungie.net`+exoticWeapon[0].icon+`" style="width: 30px; height: 30px; margin-right: 5px;"/>`+exoticWeapon[0].name+`
                    </div>
                    `;
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
                      var missingArmorStr = `
                      <div class="d-flex mb-1 align-items-center">
                        <img class="img-fluid" src="https://bungie.net`+exoticArmor[0].icon+`" style="width: 30px; height: 30px; margin-right: 5px;"/>`+exoticArmor[0].name+`
                      </div>
                      `;

                    if( memberArmorCollectionData[j].class == 'warlock' ) {
                      missingArmorsWarlockStr += missingArmorStr;
                    }
                    else if( memberArmorCollectionData[j].class == 'titan' ) {
                      missingArmorsTitanStr += missingArmorStr;
                    }
                    else {
                      missingArmorsHunterStr += missingArmorStr;
                    }
                  }
                }
              }
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

          $("#nameFilter").on("input", function(){
            table.setFilter("name", "like", $(this).val());
          });
        });
    }
  });
});