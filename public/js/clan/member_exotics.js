$(document).ready(function(){
  $.ajax({
    url: ccbNS.bungie_api_url+'/GroupV2/'+ccbNS.clan_id+'/Members/',
    headers: {
      'X-API-Key': ccbNS.bungie_api
    }
  }).done(function(data){

    if( data.Response.results && data.Response.results.length > 0 ) {

      memberData = data.Response.results;

      memberData = memberData.filter(function(m){
        return m.destinyUserInfo.membershipId == member.id;
      });

      $('.loader-text').text('Fetching Collection...');

      $.get('/clan/exotics/get', function(exoticData){
        var tableData = [];
        var weaponCollectionData = exoticData.clan_exotic_weapon_collection;
        var armorCollectionData = exoticData.clan_exotic_armor_collection;
        var exoticItemsData = exoticData.exotic_definition;

        for(var i=0; i<memberData.length; i++) {

          // Decode html entities
          var txt = document.createElement("textarea");
          txt.innerHTML = memberData[i].destinyUserInfo.displayName;
          var steamID = txt.value;

          var tableDataEntry = {
            name: _.escape(steamID) + '<a href="/clan/exotics/'+memberData[i].destinyUserInfo.membershipId+'/'+memberData[i].destinyUserInfo.displayName+'" class="text-dark"><i class="fas fa-external-link-alt ml-1 fa-xs" style="position: relative; bottom: 1px;"></i></a>'
          };

          var memberWeaponCollectionData = weaponCollectionData.filter(function(data){
            return data.user_id == memberData[i].destinyUserInfo.membershipId;
          });

          var memberArmorCollectionData = armorCollectionData.filter(function(data){
            return data.user_id == memberData[i].destinyUserInfo.membershipId;
          });

          var weapons = [];
          var warlockArmors = [];
          var titanArmors = [];
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
          /*
          weapons = _.sortBy(weapons, ['is_collected', 'name']);
          warlockArmors = _.sortBy(warlockArmors, ['is_collected', 'name']);
          titanArmors = _.sortBy(titanArmors, ['is_collected', 'name']);
          hunterArmors = _.sortBy(hunterArmors, ['is_collected', 'name']);
          */

          weapons = _.sortBy(weapons, ['name']);
          warlockArmors = _.sortBy(warlockArmors, ['name']);
          titanArmors = _.sortBy(titanArmors, ['name']);
          hunterArmors = _.sortBy(hunterArmors, ['name']);

          // HTML
          for(var j=0; j<weapons.length; j++) {
            $('.exotic-weapons-container').append( getExoticItemHTML(weapons[j]) );
          }

          for(var j=0; j<warlockArmors.length; j++) {
            $('.exotic-warlock-container').append( getExoticItemHTML(warlockArmors[j]) );
          }

          for(var j=0; j<titanArmors.length; j++) {
            $('.exotic-titan-container').append( getExoticItemHTML(titanArmors[j]) );
          }

          for(var j=0; j<hunterArmors.length; j++) {
            $('.exotic-hunter-container').append( getExoticItemHTML(hunterArmors[j]) );
          }

          // Set Title Counts
          $('.summary-collected-weapons').text( "(" + weapons.filter(w => w.is_collected == true).length + "/" + weapons.length + ")" );
          $('.summary-collected-warlock').text( "(" + warlockArmors.filter(w => w.is_collected == true).length + "/" + warlockArmors.length + ")" );
          $('.summary-collected-titan').text( "(" + titanArmors.filter(w => w.is_collected == true).length + "/" + titanArmors.length + ")" );
          $('.summary-collected-hunter').text( "(" + hunterArmors.filter(w => w.is_collected == true).length + "/" + hunterArmors.length + ")" );

          $('.exotic-weapons h4').text( "Weapons (" + weapons.filter(w => w.is_collected == true).length + "/" + weapons.length + ")" );
          $('.exotic-warlock h4').text( "Warlock (" + warlockArmors.filter(w => w.is_collected == true).length + "/" + warlockArmors.length + ")" );
          $('.exotic-titan h4').text( "Titan (" + titanArmors.filter(w => w.is_collected == true).length + "/" + titanArmors.length + ")" );
          $('.exotic-hunter h4').text( "Hunter (" + hunterArmors.filter(w => w.is_collected == true).length + "/" + hunterArmors.length + ")" );

          // Hide / Show Stuff
          $('.loader').hide();
          $('.loader-text').hide();
          $('.exotic-collection-container').show();
        }
      });
    }
  });
});

function getExoticItemHTML(item) {

  return `
  <div class="col-md-2 mb-md-3 mb-2">
    <div class="exotic-item d-flex align-items-center`+(item.is_collected==0?' missing':' collected')+`">
      <img class="img-fluid" src="https://bungie.net`+item.icon+`"/>
      <div class="text-left">`+item.name+`</div>
    </div>
  </div>`;
}