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

          var steamID = getSanitizedName(memberData[i].destinyUserInfo.displayName);

          var tableDataEntry = {
            name: steamID + '<a href="/clan/exotics/'+memberData[i].destinyUserInfo.membershipId+'/'+memberData[i].destinyUserInfo.displayName+'" class="text-dark"><i class="fas fa-external-link-alt ml-1 fa-xs" style="position: relative; bottom: 1px;"></i></a>'
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

          // No Collected
          weapons_collected = weapons.filter(w => w.is_collected == true).length;
          warlock_collected = warlockArmors.filter(w => w.is_collected == true).length;
          titan_collected = titanArmors.filter(w => w.is_collected == true).length;
          hunter_collected = hunterArmors.filter(w => w.is_collected == true).length;

          // Progress bar
          weaponsCompletedPercent = Math.floor(weapons_collected / weapons.length * 100);
          $('.weapons-progress-bar').append('<div class="progress"><div class="progress-bar bg-success" style="width: '+weaponsCompletedPercent+'%;" role="progressbar" aria-valuenow="'+weaponsCompletedPercent+'" aria-valuemin="0" aria-valuemax="100">'+weaponsCompletedPercent+'%</div></div>');
          warlockCompletedPercent = Math.floor(warlock_collected / warlockArmors.length * 100);
          $('.warlock-progress-bar').append('<div class="progress"><div class="progress-bar bg-success" style="width: '+warlockCompletedPercent+'%;" role="progressbar" aria-valuenow="'+warlockCompletedPercent+'" aria-valuemin="0" aria-valuemax="100">'+warlockCompletedPercent+'%</div></div>');
          titanCompletedPercent = Math.floor(titan_collected / titanArmors.length * 100);
          $('.titan-progress-bar').append('<div class="progress"><div class="progress-bar bg-success" style="width: '+titanCompletedPercent+'%;" role="progressbar" aria-valuenow="'+titanCompletedPercent+'" aria-valuemin="0" aria-valuemax="100">'+titanCompletedPercent+'%</div></div>');
          hunterCompletedPercent = Math.floor(hunter_collected / hunterArmors.length * 100);
          $('.hunter-progress-bar').append('<div class="progress"><div class="progress-bar bg-success" style="width: '+hunterCompletedPercent+'%;" role="progressbar" aria-valuenow="'+hunterCompletedPercent+'" aria-valuemin="0" aria-valuemax="100">'+hunterCompletedPercent+'%</div></div>');

          // Set Title Counts
          $('.summary-collected-weapons').text( "(" + weapons_collected + "/" + weapons.length + ")" );
          $('.summary-collected-warlock').text( "(" + warlock_collected + "/" + warlockArmors.length + ")" );
          $('.summary-collected-titan').text( "(" + titan_collected + "/" + titanArmors.length + ")" );
          $('.summary-collected-hunter').text( "(" + hunter_collected + "/" + hunterArmors.length + ")" );

          $('.exotic-weapons h4').text( "Weapons (" + weapons_collected + "/" + weapons.length + ")" );
          $('.exotic-warlock h4').text( "Warlock (" + warlock_collected + "/" + warlockArmors.length + ")" );
          $('.exotic-titan h4').text( "Titan (" + titan_collected + "/" + titanArmors.length + ")" );
          $('.exotic-hunter h4').text( "Hunter (" + hunter_collected + "/" + hunterArmors.length + ")" );

          // Hide / Show Stuff
          $('.loader').hide();
          $('.loader-text').hide();
          $('.exotic-collection-container').show();

          // Scroll to on load
          if( window.location.hash ) {
            $('html,body').animate({scrollTop: $(window.location.hash).offset().top},'fast');
          }
        }
      });
    }
  });
});

function getExoticItemHTML(item) {

  // `+((item.is_collected==0 && item.guide!='' && item.guide.includes('http'))?'<div class="exotic-item-guide"><a href="'+item.guide+'" target="_blank">How to get?</a></div>':'')+`

  return `
  <div class="col-lg-3 col-md-4 col-sm-6 mb-md-3 mb-2">
    `+((item.guide!='' && item.guide.includes('http'))?'<a href="'+item.guide+'" target="_blank" title="View guide in new window" class="exotic-item-guide">':'')+`
    <div class="exotic-item d-flex align-items-stretch`+(item.is_collected==0?' missing':' collected')+`">
      <img class="img-fluid" src="https://bungie.net`+item.icon+`"/>
      <div class="d-flex align-items-center justify-content-between text-left p-3 w-100`+((item.guide!='' && item.guide.includes('http'))?' has-exotic-guide':'')+`">
        <div class="`+(item.is_collected==0?' text-danger':'')+`">`+item.name+`</div>
        `+((item.guide!='' && item.guide.includes('http'))?'<div><a href="'+item.guide+'" target="_blank" title="View guide in new window" class="exotic-item-guide '+(item.is_collected==0?' text-danger':'')+'"><i class="fas fa-external-link-alt fa-xs"></i></a></div>':'')+`
      </div>
    </div>
    `+((item.guide!='' && item.guide.includes('http'))?'</a>':'')+`
  </div>`;
}