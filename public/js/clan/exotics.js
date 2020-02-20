$(document).ready(function(){
  $.ajax({
    url: ccbNS.bungie_api_url+'/GroupV2/'+ccbNS.clan_id+'/Members/',
    headers: {
      'X-API-Key': ccbNS.bungie_api
    }
  }).done(function(data){

    if( data.Response.results && data.Response.results.length > 0 ) {

      memberData = data.Response.results;

      $('.loader-text').text('Fetching Collection...');

      $.get('/clan/exotics/get', function(exoticData){
        var tableData = [];
        var tableColumns = [
          {title:"Name", field:"name", frozen:true, formatter:"html", widthGrow:1},
          {title:"Weapon", field:"weapons", formatter:"html", headerSort:false},
          {title:"Warlock", field:"warlockArmors", formatter:"html", headerSort:false},
          {title:"Titan", field:"titanArmors", formatter:"html", headerSort:false},
          {title:"Hunter", field:"hunterArmors", formatter:"html", headerSort:false},
        ];

        var weaponCollectionData = exoticData.clan_exotic_weapon_collection;
        var armorCollectionData = exoticData.clan_exotic_armor_collection;
        var exoticItemsData = exoticData.exotic_definition;

        for(var i=0; i<memberData.length; i++) {

          var steamID = getSanitizedName(memberData[i].destinyUserInfo.displayName);

          var tableDataEntry = {
            name: '<a data-sort-name="'+steamID+'" href="/clan/exotics/'+memberData[i].destinyUserInfo.membershipId+'/'+memberData[i].destinyUserInfo.displayName+'" class="text-dark member-name">'+steamID+'<i class="fas fa-external-link-alt ml-1 fa-xs" style="position: relative; bottom: 1px;"></i></a>'
          };

          var memberWeaponCollectionData = weaponCollectionData.filter(function(data){
            return data.user_id == memberData[i].destinyUserInfo.membershipId;
          });

          var memberArmorCollectionData = armorCollectionData.filter(function(data){
            return data.user_id == memberData[i].destinyUserInfo.membershipId;
          });

          var weaponsStr = '';
          var weapons = [];
          var warlockArmorsStr = '';
          var warlockArmors = [];
          var titanArmorsStr = '';
          var titanArmors = [];
          var hunterArmorsStr = '';
          var hunterArmors = [];

          if( memberWeaponCollectionData.length > 0 ) {
            for(var j=0; j<memberWeaponCollectionData.length; j++) {

              exoticWeapon = exoticItemsData.filter(function(data){
                return data.id == memberWeaponCollectionData[j].item_hash;
              });

              if( exoticWeapon ) {
                weapObj = exoticWeapon[0];
                weapObj.is_collected = memberWeaponCollectionData[j].is_collected;
                weapons.push(weapObj);
              }
            }
          }

          if( memberArmorCollectionData.length > 0 ) {
            for(var j=0; j<memberArmorCollectionData.length; j++) {

              exoticArmor = exoticItemsData.filter(function(data){
                return data.id == memberArmorCollectionData[j].item_hash;
              });

              if( exoticArmor ) {
                armorObj = exoticArmor[0];
                armorObj.is_collected = memberArmorCollectionData[j].is_collected;

                if( memberArmorCollectionData[j].class == 'warlock' ) {
                  warlockArmors.push(armorObj);
                }
                else if( memberArmorCollectionData[j].class == 'titan' ) {
                  titanArmors.push(armorObj);
                }
                else {
                  hunterArmors.push(armorObj);
                }
              }
            }
          }

          // Sort
          weapons = _.sortBy(weapons, ['is_collected', 'name']);
          warlockArmors = _.sortBy(warlockArmors, ['is_collected', 'name']);
          titanArmors = _.sortBy(titanArmors, ['is_collected', 'name']);
          hunterArmors = _.sortBy(hunterArmors, ['is_collected', 'name']);

          // HTML
          for(var j=0; j<weapons.length; j++) {
            weaponsStr += `
            <div data-item-name="`+weapons[j].name+`" class="d-flex mb-1 align-items-center exotic-item`+(weapons[j].is_collected==0?' missing':'')+`">
              <img class="img-fluid" src="https://bungie.net`+weapons[j].icon+`"/>`+weapons[j].name+`
            </div>`;
          }

          for(var j=0; j<warlockArmors.length; j++) {
            warlockArmorsStr += `
            <div data-item-name="`+warlockArmors[j].name+`" class="d-flex mb-1 align-items-center exotic-item`+(warlockArmors[j].is_collected==0?' missing':'')+`">
              <img class="img-fluid" src="https://bungie.net`+warlockArmors[j].icon+`"/>`+warlockArmors[j].name+`
            </div>
            `;
          }

          for(var j=0; j<titanArmors.length; j++) {
            titanArmorsStr += `
            <div data-item-name="`+titanArmors[j].name+`" class="d-flex mb-1 align-items-center exotic-item`+(titanArmors[j].is_collected==0?' missing':'')+`">
              <img class="img-fluid" src="https://bungie.net`+titanArmors[j].icon+`"/>`+titanArmors[j].name+`
            </div>
            `;
          }

          for(var j=0; j<hunterArmors.length; j++) {
            hunterArmorsStr += `
            <div data-item-name="`+hunterArmors[j].name+`" class="d-flex mb-1 align-items-center exotic-item`+(hunterArmors[j].is_collected==0?' missing':'')+`">
              <img class="img-fluid" src="https://bungie.net`+hunterArmors[j].icon+`"/>`+hunterArmors[j].name+`
            </div>
            `;
          }

          // Tabulator Data
          tableDataEntry['weapons'] = weaponsStr;
          tableDataEntry['warlockArmors'] = warlockArmorsStr;
          tableDataEntry['titanArmors'] = titanArmorsStr;
          tableDataEntry['hunterArmors'] = hunterArmorsStr;
          tableData.push(tableDataEntry);
        }

        $('.stats-container').append('<div id="exotics-table"></div>');
        $('.loader').hide();
        $('.loader-text').hide();
        $('.filter-container').show();

        var table = new Tabulator("#exotics-table", {
          data:tableData,
          columns: tableColumns,
          initialSort: [
            {column:"name", dir:"asc"},
          ],
          layout:"fitDataFill",
          height: "600px",
          resizableColumns:true,
        });

        $('[data-toggle="tooltip"]').tooltip();

        $("#nameFilter").on("input", function(){
          table.setFilter("name", "like", $(this).val());
        });
      });
    }
  });
});