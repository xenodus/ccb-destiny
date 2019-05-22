$(document).ready(function(){

  $('#weeklies-item-container').hide();

  $.get('/api/vendor', function(data){

    var vendorHash = {
      'Suraya Hawthorne': '3347378076',
      'Ada-1': '2917531897',
      'Banshee-44': '672118013',
      'Spider': '863940356',
      'Lord Shaxx': '3603221665',
      'The Drifter': '248695599',
      'Lord Saladin': '895295461',
      'Commander Zavala': '69482069',
      'Xur': '2190858386',
      'Tess Everis': '3361454721'
    };

    var gambitBountiesFilter = [
      'Gambit Bounty',
      'Weekly Drifter Bounty'
    ];

    var tessFilter = [
      'Emote',
      'Ghost Shell',
      'Ship',
      'Transmat Effect',
      'Vehicle',
      'Weapon Ornament',
      'Armor Ornament'
    ];

    var spiderRareBounty = [
      'WANTED: Combustor Valus',
      'WANTED: Arcadian Chord',
      'WANTED: The Eye in the Dark',
      'WANTED: Gravetide Summoner',
      'WANTED: Silent Fang',
      'WANTED: Blood Cleaver'
    ];

    if( data.length > 0 ) {
      raid_bounties = data.filter(function(item){ return item.vendor_hash == vendorHash['Suraya Hawthorne'] && item.itemTypeDisplayName == 'Weekly Bounty' });

      gambit_bounties = data.filter(function(item){ return item.vendor_hash == vendorHash['The Drifter'] && gambitBountiesFilter.includes(item.itemTypeDisplayName) });

      power_surge_bounties = data.filter(function(item){ return item.vendor_hash == vendorHash['The Drifter'] && item.itemTypeDisplayName == 'Power Surge Bounty' });

      spider_wares = data.filter(function(item){ return item.vendor_hash == vendorHash['Spider'] && item.itemTypeDisplayName == '' });

      spider_powerful_bounty = data.filter(function(item){ return item.vendor_hash == vendorHash['Spider'] && item.itemTypeDisplayName == 'Weekly Bounty' && spiderRareBounty.includes(item.name) });

      banshee_wares = data.filter(function(item){ return item.vendor_hash == vendorHash['Banshee-44'] && item.icon != '' });

      xur_wares = data.filter(function(item){ return item.vendor_hash == vendorHash['Xur'] && item.itemTypeDisplayName != 'Challenge Card' && item.itemTypeDisplayName != 'Invitation of the Nine' });

      tess_wares = data.filter(function(item){ return item.vendor_hash == vendorHash['Tess Everis'] && tessFilter.includes(item.itemTypeDisplayName) });

      saladin_bounties = data.filter(function(item){ return item.vendor_hash == vendorHash['Lord Saladin'] && item.itemTypeDisplayName == 'Iron Banner Bounty' });

      ada_frames = data.filter(function(item){ return item.vendor_hash == vendorHash['Ada-1'] && item.cost_name == 'Ballistics Log' });

      if( raid_bounties.length > 0 ) {
        $('#weeklies-item-container').append( getVendorStr(raid_bounties, 'Raid ' + (raid_bounties.length > 1 ? 'Bounties' : 'Bounty') ) );
      }

      ascendant_challenge = getAscendantChallenge();
      weekly_dc_mission = getWeeklyDCMission();
      curse_level = getCurseLevel();

      if( ascendant_challenge.length > 0 && weekly_dc_mission.length > 0  && curse_level.length > 0 ) {
        $('#weeklies-item-container').append( getVendorStr( ascendant_challenge.concat(weekly_dc_mission).concat(curse_level) , 'Dreaming City <small style="font-size: 70%;font-style: italic;"><a href="https://i.imgur.com/LA9TMcS.jpg" data-title="https://i.imgur.com/LA9TMcS.jpg" data-lightbox="Ascendant Challenge Map" target="_blank">Ascendant Challenge Map <i class="fas fa-external-link-alt"></i></a></small>') );
      }

      outbreak_config = getOutbreakSinge();

      if( outbreak_config.length > 0  ) {
        $('#weeklies-item-container').append( getVendorStr( outbreak_config, 'Outbreak Catalyst <small style="font-size: 70%;font-style: italic;"><a href="/outbreak">Solution Generator <i class="fas fa-external-link-alt"></i></a></small>') );
      }

      if( banshee_wares.length > 0 ) {
        $('#weeklies-item-container').append( getVendorStr(banshee_wares, 'Banshee-44\'s Mods') );
        // $('.last-updated-info').append(' <div>Bounties & Vendors <i class="fas fa-long-arrow-alt-right"></i> ' + moment(banshee_wares[0].date_added).local().format('D MMM YYYY h:mm A')+'</div>');
      }

      if( spider_powerful_bounty.length > 0 ) {
        $('#weeklies-item-container').append( getVendorStr(spider_powerful_bounty, 'Spider\'s Powerful Bounty') );
      }

      if( spider_wares.length > 0 ) {
        $('#weeklies-item-container').append( getVendorStr(spider_wares, 'Spider\'s Wares') );
      }

      escalation_protocol = getEscalationProtocol();

      if( escalation_protocol.length > 0  ) {
        $('#weeklies-item-container').append( getVendorStr( escalation_protocol, 'Escalation Protocol') );
      }

      if( saladin_bounties.length > 0 ) {
        // $('.right-col').append( getVendorStr(saladin_bounties, 'Lord Salad\'s Bounties') );
      }

      raid_lair_modifiers = {
        'Gladiator': {
          'name': 'Prestige: Gladiator',
          'description': 'Melee kills buff weapon damage, and weapon kills buff melee damage.',
          'icon': 'https://bungie.net/common/destiny2_content/icons/8d4cc5b8420f2a647c877610b9f286ed.png'
        },
        'Arsenal': {
          'name': 'Prestige: Arsenal',
          'description': 'Weapons have no reserve ammo. Emptying the clip of a weapon refills the clips of your holstered weapons.',
          'icon': 'https://bungie.net/common/destiny2_content/icons/5e870c7f571cf35554183a9b330cbf23.png'
        },
        'Prism': {
          'name': 'Prism',
          'description': 'Attacks matching the periodically rotating focused element do more damage. Other elemental damage is reduced. Incoming damage is unaffected.',
          'icon': 'https://bungie.net/common/destiny2_content/icons/7cd52fc7131a02c6b03544df779cb8c6.png'
        }
      };

      current_raid_lair_modifiers = {
        loadouts: {
          primary: 'SMG',
          energy: 'Anything',
          power: 'Grenade Launcher'
        },
        modifier: {
          name: raid_lair_modifiers['Prism'].name,
          description: raid_lair_modifiers['Prism'].description,
          icon: raid_lair_modifiers['Prism'].icon
        },
        expiry: moment('2019-02-13 01:00:00', 'YYYY-MM-DD H:mm:ss'),
        updated: '2019-02-07 02:00:00'
      };

      if( current_raid_lair_modifiers.expiry.diff() > 0 ) {
        $('#weeklies-item-container').append( getRaidLairModifiers(current_raid_lair_modifiers) );
        // $('.last-updated-info').append(' <div>Y1 Raid Lair Modifiers <i class="fas fa-long-arrow-alt-right"></i> ' + moment(current_raid_lair_modifiers.updated).format('D MMM YYYY h:mm A')+'</div>');
      }

      if( tess_wares.length > 0 ) {
        $('#weeklies-item-container').append( getVendorStr(tess_wares, 'Tess\'s Precious') );
      }

      $('[data-toggle="tooltip"]').tooltip({
        html: true
      });
    }

    $.get('/api/nightfall', function(data){

      if( data.length > 0 ) {
        // console.log( data );
        $('#weeklies-item-container').prepend( getVendorStr(data, 'Nightfalls') );
        // $('.last-updated-info').append(' <div>Nightfalls <i class="fas fa-long-arrow-alt-right"></i> ' + moment(data[0].date_added).local().format('D MMM YYYY h:mm A')+'</div>');

        $('[data-toggle="tooltip"]').tooltip({
          html: true
        });
      }

      $.get('/api/levi', function(data){
        if( data.order ) {
          //console.log( data );
          $('#weeklies-item-container').prepend( getVendorStr([{icon: '/common/destiny2_content/icons/b8177e166f01c2cd914fc3e925ae902d.png', name: data.order}], 'Leviathan') );
        }

        // XUR
        $.get('/api/sales-item-perks/' + vendorHash['Xur'], function(data){

          if( xur_wares.length > 1 ) {
            $('#weeklies-item-container').prepend( getXurVendorStr(xur_wares, 'Xur\'s Shinies <small style="font-size: 70%;font-style: italic;"><a href="https://wherethefuckisxur.com/" target="_blank" id="xur-link">Where is Xur? <i class="fas fa-external-link-alt"></i></a></small>', 'vertical', data) );

            $('[data-toggle="tooltip"]').tooltip({
              html: true
            });
          }

          $('section#weeklies > div.loader, section#weeklies > div.loader-text').hide();
          $('#weeklies-item-container').fadeIn();

          $('.grid').masonry({
            itemSelector: '.grid-item',
            gutter: 0,
            columnWidth: '.grid-sizer',
            gutter: '.gutter-sizer',
            percentPosition: true
          });

          get_xur_location();
        });
      });
    });
  });

  var nightfall_loot = {
    'Nightfall: Tree of Probabilities': {
      'name': 'D.F.A.',
      'icon': '/common/destiny2_content/icons/6e692a14162839d0489e11cf9d84746e.jpg',
      'description': '"Osiris said that he started to pity the Red Legion, getting trapped in here for infinite eternities. I think they\'re getting exactly what they deserve." —Sagira',
      'type': 'Legendary Hand Cannon'
    },
    'Nightfall: Strange Terrain': {
      'name': 'BrayTech Osprey',
      'icon': '/common/destiny2_content/icons/659ebe95206951d7c97022b47a93c459.jpg',
      'description': 'Expected Use Timeframe: UNKNOWN.',
      'type': 'Legendary Rocket Launcher'
    },
    'Nightfall: Savathûn\'s Song': {
      'name': 'Duty Bound',
      'icon': '/common/destiny2_content/icons/0497af906c184a43fa7e2accae899c35.jpg',
      'description': '"Due respect, Commander? I was there when the Hive found us on Earth. I was there when we stopped them on Titan. And I\'ll be there when we wipe them out." —Sloane',
      'type': 'Legendary Auto Rifle'
    },
    'Nightfall: The Pyramidion': {
      'name': 'Silicon Neuroma',
      'icon': '/common/destiny2_content/icons/77364d95fdc16bb1d23f6f00817dc6ab.jpg',
      'description': '"My future is concurrently irreversible and unknowable. Before it overtakes me, I desire a more abrupt end to those responsible." —Asher Mir',
      'type': 'Legendary Sniper Rifle'
    },
    'Nightfall: The Arms Dealer': {
      'name': 'Tilt Fuse',
      'icon': '/common/destiny2_content/icons/a2dd642b18b15f764db069f845f5173c.jpg',
      'description': '"[whistle] With a few more revs, Zahn woulda turned this thing into the fastest bomb you never saw. Good thing for all of us he never got the chance." —Amanda Holliday',
      'type': 'Exotic Sparrow'
    },
    'Nightfall: Exodus Crash': {
      'name': 'Impact Velocity',
      'icon': '/common/destiny2_content/icons/4d0ecd27dd8a6d02a8a0f3b2618a097e.jpg',
      'description': '"Captain, this conveyance\'s top speed is a fraction of the Exodus Black\'s when it crashed into Nessus!" "Try not to find out for yourself." —Failsafe',
      'type': 'Exotic Sparrow'
    },
    'Nightfall: The Inverted Spire': {
      'name': 'Trichromatica',
      'icon': '/common/destiny2_content/icons/bb72e6b7b2a7ac6165431d4a47171b2f.jpg',
      'description': '"Void, Solar, then Arc. Hmm. We\'re not naive enough to think the order is a coincidence. But we\'ve got bigger things to worry about." —Zavala',
      'type': 'Exotic Ghost Shell'
    },
    'Nightfall: A Garden World': {
      'name': 'Universal Wavefunction',
      'icon': '/common/destiny2_content/icons/d6c77755df5761e5626b052d440cf5c7.jpg',
      'description': '"I believed your presence at the genesis of the Infinite Forest would lead to a comprehensive understanding of the Vex. When will I learn that things are never so simple?" —Ikora',
      'type': 'Exotic Ship'
    },
    'Nightfall: Lake of Shadows': {
      'name': 'The Militia\'s Birthright',
      'icon': '/common/destiny2_content/icons/39b67dae56153d70e935bfad21faecc7.jpg',
      'description': '"Earth is our home. Not Mars, not Venus, not even the Reef. We must ensure it is a place we can continue to live for many generations to come." —Devrim Kay',
      'type': 'Legendary Grenade Launcher'
    },
    'Nightfall: Will of the Thousands': {
      'name': 'Worm God Incarnation',
      'icon': '/common/destiny2_content/icons/2d6ff9e9e65253a82ec0856f310e2b94.jpg',
      'description': 'Modifications for your ship\'s transmat systems, so you\'ll always arrive in style.',
      'type': 'Legendary Transmat Effect'
    },
    'Nightfall: The Insight Terminus': {
      'name': 'The Long Goodbye',
      'icon': '/common/destiny2_content/icons/fe07633a2ee87f0c00d5b0c0f3838a7d.jpg',
      'description': 'Yeah… I lost a lot of these out on Nessus. Long story. Lots of dead Vex." —The Drifter',
      'type': 'Legendary Sniper Rifle'
    },
    'Nightfall: The Hollowed Lair': {
      'name': 'Mindbender\'s Ambition',
      'icon': '/common/destiny2_content/icons/0d39a47ea705e188a3674fa5f41b99a5.jpg',
      'description': '"Hiraks always did like to leave an impression." —The Spider',
      'type': 'Legendary Shotgun'
    },
    'Nightfall: Warden of Nothing': {
      'name': 'Warden\'s Law',
      'icon': '/common/destiny2_content/icons/89a68f864854dd80155eb194ee8f5cb7.jpg',
      'description': 'Fight. Win. Li- Li- Li- Li- Li- FATAL EXCEPTION HAS OCCURRED AT 0028:C001E36',
      'type': 'Legendary Hand Cannon'
    },
    'Nightfall: The Corrupted': {
      'name': 'Horror\'s Least',
      'icon': '/common/destiny2_content/icons/c5454c80b15ecb3b3abf2d69d4bfe5ff.jpg',
      'description': '"Some things should not be saved." —Techeun Sedia',
      'type': 'Legendary Pulse Rifle'
    }
  };

  function getVendorStr(data, title, type="vertical") {
    str = `
    <div class="grid-item col-md-4">
      <div class="mb-3 border-warning border">
        <div class="border-warning border-bottom p-2 text-left">`+title+`</div>
        <div class="pl-2 pr-2 pt-2 pb-1 `+(type=='vertical'?'':'d-md-flex flex-md-wrap justify-content-md-around')+`">
    `;

    for(var i=0; i<data.length; i++) {

      var tooltip = '';
      var cost = '';

      if( data[i].description ) {

        if( data[i].cost ) {
          cost = `<div class='mt-2'>Price: `+data[i].cost+` `+data[i].cost_name+`</div>`;
        }

        if( title == 'Nightfalls' && Object.keys(nightfall_loot).includes(data[i].name) )
        {
          tooltip = `
          <div>
            <h6 class='font-weight-bold mb-1'>`+data[i].name+`</h6>
            <div class='mb-1'>
              `+data[i].description.replace(/"/g, "'")+`
            </div>
            <div class='d-flex align-items-start mt-2'>
              <div>
                <img src='https://bungie.net`+nightfall_loot[data[i].name].icon+`' class='mt-1 mb-1 mr-2 tooltip-icon' style='width: 50px; height: 50px;'/>
              </div>
              <div>
                <div class='text-weight-bold'><h6 class='mb-1'>`+nightfall_loot[data[i].name].name+`</h6></div>
                <div>`+nightfall_loot[data[i].name].type.replace(/"/g, "'")+`</div>
              </div>
            </div>
          </div>
          `;
        }
        else {
          tooltip = `
          <div>
            <h6 class='font-weight-bold mb-1'>`+data[i].name+`</h6>
            <div class='d-flex align-items-start'>
              <div>
                <img src='https://bungie.net`+data[i].icon+`' class='mt-1 mb-1 mr-2 tooltip-icon' style='width: 50px; height: 50px;'/>
              </div>
              <div>`+data[i].description.replace(/"/g, "'")+`</div>
            </div>
            `+cost+`
          </div>
          `;
        }
      }

      str += `
      <div class="d-flex mb-1 vendor-item" data-toggle="tooltip" title="`+tooltip+`">
        <img class="img-fluid" src="https://bungie.net`+data[i].icon+`" style="width: 20px; height: 20px; margin-right: 5px;"/>`+data[i].name+`
      </div>
      `;
    }

    str += `
        </div>
      </div>
    </div>
    `
    return str;
  }

  function getXurVendorStr(data, title, type="vertical", xur_item_perks) {

    str = `
    <div class="grid-item col-md-4">
      <div class="mb-3 border-warning border">
        <div class="border-warning border-bottom p-2 text-left">`+title+`</div>
        <div class="pl-2 pr-2 pt-2 pb-1 `+(type=='vertical'?'':'d-md-flex flex-md-wrap justify-content-md-around')+`">
    `;

    for(var i=0; i<data.length; i++) {

      var tooltip = '';
      var cost = '';
      var perks = '';
      var item_perks = xur_item_perks.filter(function(perks){ return perks.vendor_sales_id == data[i].id  });

      if( data[i].description ) {

        if( data[i].cost ) {
          cost = `<div class='mt-2'>Price: `+data[i].cost+` `+data[i].cost_name+`</div>`;
        }

        if( item_perks.length > 0 ) {
          perks = `<div class='mt-2'>`;

          prev_perk_group = '';
          roll = 1;

          for(var j=0; j<item_perks.length; j++) {
            if( j == 0 ) {
              perks += `<div class='mb-1'>Perk `+roll+`</div><div class='mb-2 border border-secondary rounded'>`;
            }
            else if( prev_perk_group != item_perks[j].perk_group ) {
              roll++;
              perks += `</div><div class='mt-1 mb-1'>Perk `+roll+`</div><div class='mb-2 border border-secondary rounded'>`;
            }

            perks += `<div class='d-flex align-items-center pt-1 pb-2 pl-2 pr-2 `+(prev_perk_group == item_perks[j].perk_group && j!=0 ? 'border-top border-secondary':'')+`'>
                        <div>
                          <img src='https://bungie.net`+item_perks[j].icon+`' class='mt-1 mb-1 mr-2 tooltip-icon' style='width: 30px; height: 30px;'/>
                        </div>
                        <div>
                          <div class='font-weight-bold'>`+item_perks[j].name.replace(/"/g, "'")+`</div>
                          <div class='perk-description mt-1'>`+item_perks[j].description.replace(/"/g, "'").replace(/•/g, "<br>• ")+`</div>
                        </div>
                      </div>`;

            if( j+1 == item_perks.length ) {
              perks += `</div>`;
            }

            prev_perk_group = item_perks[j].perk_group;
          }

          perks += `</div>`;
        }

        tooltip = `
        <div>
          <h6 class='font-weight-bold mb-1'>`+data[i].name+`</h6>
          <div class='d-flex align-items-start'>
            <div>
              <img src='https://bungie.net`+data[i].icon+`' class='mt-1 mb-1 mr-2 tooltip-icon' style='width: 50px; height: 50px;'/>
            </div>
            <div>`+data[i].description.replace(/"/g, "'")+`</div>
          </div>
          `+cost+`
          `+perks+`
        </div>
        `;
      }

      str += `
      <div class="d-flex mb-1 vendor-item" data-toggle="tooltip" title="`+tooltip+`">
        <img class="img-fluid" src="https://bungie.net`+data[i].icon+`" style="width: 20px; height: 20px; margin-right: 5px;"/>`+data[i].name+`
      </div>
      `;
    }

    str += `
        </div>
      </div>
    </div>
    `
    return str;
  }

  function getRaidLairModifiers(data) {

    var tooltip = `
    <h6 class='font-weight-bold mb-1'>`+data.modifier.name+`</h6>
    <div>`+data.modifier.description+`</div>
    `;

    var str = `
    <div class="mb-3 border-warning border">
      <div class="border-warning border-bottom p-2">Y1 Prestige Raid Lair Modifiers</div>
      <div class="pl-2 pr-2 pt-2 pb-1">
        <div class="mb-1 vendor-item">
          <div><u>Loadout</u></div>
          <div class="d-flex">
            <img class="img-fluid" src="https://bungie.net/common/destiny2_content/icons/dc4bb9bcdd4ae8a83fb9007a51d7d711.png" style="width: 20px; height: 20px; margin-right: 5px;"/> Primary: `+data.loadouts.primary+`
          </div>
          <div class="d-flex">
            <img class="img-fluid" src="https://bungie.net/common/destiny2_content/icons/b6d3805ca8400272b7ee7935b0b75c79.png" style="width: 20px; height: 20px; margin-right: 5px;"/> Energy: `+data.loadouts.energy+`
          </div>
          <div class="d-flex">
            <img class="img-fluid" src="https://bungie.net/common/destiny2_content/icons/9fa60d5a99c9ff9cea0fb6dd690f26ec.png" style="width: 20px; height: 20px; margin-right: 5px;"/> Power: `+data.loadouts.power+`
          </div>
          <div class="mt-2"><u>Modifier</u></div>
          <div class="d-flex mt-1" data-toggle="tooltip" title="`+tooltip+`">
            <img class="img-fluid" src="`+data.modifier.icon+`" style="width: 20px; height: 20px; margin-right: 5px;"/>
            `+data.modifier.name+`
          </div>
        </div>
      </div>
    </div>
    `
    return str;
  }

  function get_xur_location() {
    $.get('/api/xur', function(data){
      if( data.location ) {
        $("a#xur-link").text(data.location);
      }
    });
  }

  function getEscalationProtocol() {

    var escalationProtocol = {
      'Kathok, Roar of Xol': {
        name: 'IKELOS_SMG_v1.0.1',
        icon: '/common/destiny2_content/icons/85ad82abdfc13537325b45a85d6f4462.jpg'
      },
      'Damkath, The Mask': {
        name: 'IKELOS_SR_v1.0.1',
        icon: '/common/destiny2_content/icons/52630df015ef0e839555982c478d78f3.jpg'
      },
      'Naksud, the Famine': {
        name: 'All 3 Weapons',
        icon: '/common/destiny2_content/icons/d316fa414f16795f5f0674a35d2bdae7.jpg'
      },
      'Bok Litur, Hunger of Xol': {
        name: 'All 3 Weapons',
        icon: '/common/destiny2_content/icons/d316fa414f16795f5f0674a35d2bdae7.jpg'
      },
      'Nur Abath, Crest of Xol': {
        name: 'IKELOS_SG_v1.0.1',
        icon: '/common/destiny2_content/icons/edfdd807c9d604e80b48ad8fe39c8f36.jpg'
      },
    };

    var startACDate = moment('2019-01-16 01:00:00', 'YYYY-MM-DD H:mm:ss');
    var currDate = moment();

    var index = 0;
    var found = false;

    while(found == false) {

      if( index == Object.keys(escalationProtocol).length ) {
        index = 0;
      }

      nextWeek = moment( startACDate.format('YYYY-MM-DD H:mm:ss'), 'YYYY-MM-DD H:mm:ss' ).add(7, 'days');

      if( currDate.isBetween(startACDate, nextWeek) ) {
        found = true;
      }
      else {
        startACDate = nextWeek;
        index++;
      }
    }

    return [{
      name: escalationProtocol[ Object.keys(escalationProtocol)[index] ].name,
      icon: escalationProtocol[ Object.keys(escalationProtocol)[index] ].icon,
      description: 'Boss: ' + Object.keys(escalationProtocol)[index]
    }];
  }

  function getAscendantChallenge() {

    var ascendantChallenges = {
      'Gardens of Esila': "At the overlook's edge, the garden grows onward.",
      'Spine of Keres': "Climb the bones and you'll find your ruin.",
      'Harbinger’s Seclude': "Crush the first queen's crown beneath your bootheel.",
      'Bay of Drowned Wishes': "Drown in your wishes, dear squanderer.",
      'Chamber of Starlight': "Starlight, star bright, first untruth she'll craft tonight...",
      'Aphelion’s Rest': "They call it a 'rest,' but it is more truly a haunt."
    };

    var startACDate = moment('2019-01-16 01:00:00', 'YYYY-MM-DD H:mm:ss');
    var currDate = moment();
    // var currDate = moment('2019-05-01 05:55:55', 'YYYY-MM-DD H:mm:ss');

    var index = 0;
    var found = false;

    while(found == false) {

      if( index == Object.keys(ascendantChallenges).length ) {
        index = 0;
      }

      nextWeek = moment( startACDate.format('YYYY-MM-DD H:mm:ss'), 'YYYY-MM-DD H:mm:ss' ).add(7, 'days');

      if( currDate.isBetween(startACDate, nextWeek) ) {
        found = true;
      }
      else {
        startACDate = nextWeek;
        index++;
      }
    }

    return [{
      name: 'Ascendant: ' + Object.keys(ascendantChallenges)[index],
      icon: '/common/destiny2_content/icons/2f9e7dd03c415eb158c16bb59cc24c84.jpg',
      description: ascendantChallenges[ Object.keys(ascendantChallenges)[index] ]
    }];
  }

  function getWeeklyDCMission() {

    var weeklyDCMission = {
      'Broken Courier': "Respond to a distress call in the Strand.",
      'Oracle Engine': "The Taken threaten to take control of an irreplaceable Awoken communications device.",
      'Dark Monastery ': "Provide recon for Petra's forces by investigating strange enemy activity in Rheasilvia.",
    };

    var startDate = moment('2019-01-16 01:00:00', 'YYYY-MM-DD H:mm:ss');
    var currDate = moment();
    // var currDate = moment('2019-05-01 05:55:55', 'YYYY-MM-DD H:mm:ss');

    var index = 0;
    var found = false;

    while(found == false) {

      if( index == Object.keys(weeklyDCMission).length ) {
        index = 0;
      }

      nextWeek = moment( startDate.format('YYYY-MM-DD H:mm:ss'), 'YYYY-MM-DD H:mm:ss' ).add(7, 'days');

      if( currDate.isBetween(startDate, nextWeek) ) {
        found = true;
      }
      else {
        startDate = nextWeek;
        index++;
      }
    }

    return [{
      name: 'Mission: ' + Object.keys(weeklyDCMission)[index],
      icon: '/common/destiny2_content/icons/a6ce21a766375f5bcfb6cc01b093a383.png',
      description: weeklyDCMission[ Object.keys(weeklyDCMission)[index] ]
    }];
  }

  function getOutbreakSinge() {

    var outbreakSinge = [
      'Void',
      'Arc',
      'Solar'
    ];

    var startDate = moment('2019-05-08 01:00:00', 'YYYY-MM-DD H:mm:ss');
    var currDate = moment();
    // var currDate = moment('2019-05-01 05:55:55', 'YYYY-MM-DD H:mm:ss');

    var index = 0;
    var found = false;

    while(found == false) {

      if( index == Object.keys(outbreakSinge).length ) {
        index = 0;
      }

      nextWeek = moment( startDate.format('YYYY-MM-DD H:mm:ss'), 'YYYY-MM-DD H:mm:ss' ).add(7, 'days');

      if( currDate.isBetween(startDate, nextWeek) ) {
        found = true;
      }
      else {
        startDate = nextWeek;
        index++;
      }
    }

    var description = 'The configuration type is <u>' + outbreakSinge[index].toUpperCase() + '</u> for Zero Hour (Heroic).';

    return [{
      name: 'Singe Config: ' + outbreakSinge[index].toUpperCase(),
      icon: '/common/destiny2_content/icons/c013e41cdb32779bc2322337614ea06b.jpg',
      description: description
    }];
  }

  function getCurseLevel() {

    var curseLevels = [
      'Low',
      'Medium',
      'High'
    ];

    var startDate = moment('2019-01-16 01:00:00', 'YYYY-MM-DD H:mm:ss');
    var currDate = moment();
    // var currDate = moment('2019-05-01 05:55:55', 'YYYY-MM-DD H:mm:ss');

    var index = 0;
    var found = false;

    while(found == false) {

      if( index == Object.keys(curseLevels).length ) {
        index = 0;
      }

      nextWeek = moment( startDate.format('YYYY-MM-DD H:mm:ss'), 'YYYY-MM-DD H:mm:ss' ).add(7, 'days');

      if( currDate.isBetween(startDate, nextWeek) ) {
        found = true;
      }
      else {
        startDate = nextWeek;
        index++;
      }
    }

    var description = 'The curse level is <u>' + curseLevels[index].toLowerCase() + '</u> in the Dreaming City.';

    if( index == 2 )
      description += ' Shattered Throne is up.';

    return [{
      name: 'Curse Level: ' + curseLevels[index],
      icon: '/common/destiny2_content/icons/8f755eb3a9109ed7adfc4a8b27871e7a.png',
      description: description
    }];
  }
});