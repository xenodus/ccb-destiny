$(document).ready(function(){

  fetchMilestones();

  function fetchMilestones() {

    var weekliesItems = [];

    $('section#weeklies > div.loader, section#weeklies > div.loader-text').show();
    $('#weeklies-item-container').empty();
    $('#weeklies-item-container').append('<div class="grid-sizer"></div><div class="gutter-sizer"></div>');
    $('#weeklies-item-container').hide();
    $('.tooltip').remove();

    $.get('/api/milestones', function(milestonesData){

      // Vendor Sales Data
      var data = milestonesData['vendor_sales'];

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
        'Tess Everis': '3361454721',
        'Benedict 99-40': '1265988377',
        'Eva Levante': '919809084'
      };

      var gambitBountiesFilter = [
        'Gambit Bounty',
        'Weekly Drifter Bounty'
      ];

      // includes
      var tessFilter = [
        'Emote',
        'Ghost Shell',
        'Ship',
        'Transmat Effect',
        'Vehicle',
        'Weapon Ornament',
        'Armor Ornament',
        'Multiplayer Emote',
        'Shader'
      ];

      // excludes
      var benedictFilter = [
        'Buff',
        'Armor Set',
        'Quest Step',
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

        benedict_bounties = data.filter(function(item){ return item.vendor_hash == vendorHash['Benedict 99-40'] && item.itemTypeDisplayName == 'Weekly Bounty' });

        eva_bounties = data.filter(function(item){ return item.vendor_hash == vendorHash['Eva Levante'] && item.cost != null });

        gambit_bounties = data.filter(function(item){ return item.vendor_hash == vendorHash['The Drifter'] && gambitBountiesFilter.includes(item.itemTypeDisplayName) });

        power_surge_bounties = data.filter(function(item){ return item.vendor_hash == vendorHash['The Drifter'] && item.itemTypeDisplayName == 'Power Surge Bounty' });

        spider_wares = data.filter(function(item){ return item.vendor_hash == vendorHash['Spider'] && item.name.includes('Purchase') && item.itemTypeDisplayName == '' });

        spider_powerful_bounty = data.filter(function(item){ return item.vendor_hash == vendorHash['Spider'] && item.itemTypeDisplayName == 'Weekly Bounty' && spiderRareBounty.includes(item.name) });

        banshee_wares = data.filter(function(item){ return item.vendor_hash == vendorHash['Banshee-44'] && item.icon != '' });

        xur_wares = data.filter(function(item){ return item.vendor_hash == vendorHash['Xur'] && item.itemTypeDisplayName != 'Challenge Card' && item.itemTypeDisplayName != 'Invitation of the Nine' });

        tess_wares = data.filter(function(item){ return item.vendor_hash == vendorHash['Tess Everis'] && item.cost_name != 'Silver' && tessFilter.includes(item.itemTypeDisplayName) });

        tess_silver_wares = data.filter(function(item){ return item.vendor_hash == vendorHash['Tess Everis'] && item.cost_name == 'Silver' && tessFilter.includes(item.itemTypeDisplayName) });

        saladin_bounties = data.filter(function(item){ return item.vendor_hash == vendorHash['Lord Saladin'] && item.itemTypeDisplayName == 'Iron Banner Bounty' });

        ada_frames = data.filter(function(item){ return item.vendor_hash == vendorHash['Ada-1'] && item.cost_name == 'Ballistics Log' });

        // All Mighty Xur
        if( xur_wares.length > 1 ) {
          weekliesItems.push( getXurVendorStr(xur_wares, 'Xur\'s Shinies <small style="font-size: 70%;font-style: italic;"><a href="https://wherethefuckisxur.com/" target="_blank" id="xur-link">Where is Xur? <i class="fas fa-external-link-alt"></i></a></small>', 'vertical', milestonesData['xur_sales_item_perks']) );
        }

        // Nightfall
        if( milestonesData['nightfalls'].length > 0 ) {
          weekliesItems.push( getVendorStr(milestonesData['nightfalls'], 'Nightfalls') );
        }

        // Leviathan
        var leviOrder = milestonesData['milestones'].filter(function(d){ return d.type == 'levi_order' });

        if( leviOrder.length > 0 ) {
          var title = String(leviOrder[0].description).replace(/>/g, '<i class="fas fa-chevron-right fa-xs mx-1"></i>');
          var leviData = [{icon: leviOrder[0].icon, name: title}];
          var leviChallenge = milestonesData['milestones'].filter(function(d){ return d.type == 'levi_challenge' });

          if( leviChallenge.length > 0 ) {
            leviData.unshift(leviChallenge[0]);
          }

          weekliesItems.push( getVendorStr(leviData, 'Leviathan') );
        }

        // Y1 Prestige Raid Modifiers
        var y1RaidModifiers = milestonesData['milestones'].filter(function(d){ return d.type == 'y1_prestige_raid' });
        if( y1RaidModifiers.length > 0 ) {
          weekliesItems.push( getVendorStr(y1RaidModifiers, 'Y1 Prestige Raid Modifiers <small class="text-yellow" style="font-size: 70%;font-style: italic;">EOW / SOS</small>') );
        }

        // Daily Modifiers
        var dailyModifiers = milestonesData['milestones'].filter(function(d){ return d.type == 'strike' });

        if( dailyModifiers.length > 0 ) {
          weekliesItems.push( getVendorStr(dailyModifiers, 'Daily Modifiers <small class="text-yellow" style="font-size: 70%;font-style: italic;">Strike / Menagerie / Heroic Story</small>') );
        }

        // Menagerie Modifiers
        var menagerieModifiers = milestonesData['milestones'].filter(function(d){ return d.type == 'menagerie' });

        if( menagerieModifiers.length > 0 ) {
          var menagerie = getMenagerie(menagerieModifiers);

          if( menagerie.length > 0 ) {
            weekliesItems.push( getVendorStr( menagerie, "The Menagerie (Heroic)") );
          }
        }

        // Weekly Flashpoint
        var flashpoint = milestonesData['milestones'].filter(function(d){ return d.type == 'flashpoint' });

        if( flashpoint.length > 0 ) {
          weekliesItems.push( getVendorStr(flashpoint, 'Flashpoint') );
        }

        // Vendors
        if( eva_bounties.length > 0 ) {
          weekliesItems.push( getVendorStr(eva_bounties, 'Eva Levante\'s Bounties') );
        }

        if( raid_bounties.length > 0 ) {
          weekliesItems.push( getVendorStr(raid_bounties, 'Hawthorne\'s Raid ' + (raid_bounties.length > 1 ? 'Bounties' : 'Bounty') ) );
        }

        if( benedict_bounties.length > 0 ) {
          // weekliesItems.push( getVendorStr(benedict_bounties, 'Benedict 99-40\'s Bounties') );
        }

        if( spider_powerful_bounty.length > 0 ) {
          weekliesItems.push( getVendorStr(spider_powerful_bounty, 'Spider\'s Powerful Bounty') );
        }

        if( banshee_wares.length > 0 ) {
          banshee_wares = _.orderBy(banshee_wares, ['cost_name'], ['desc']);
          weekliesItems.push( getVendorStr(banshee_wares, 'Banshee-44') );
        }

        if( spider_wares.length > 0 ) {
          weekliesItems.push( getVendorStr(spider_wares, 'Spider\'s Wares') );
        }

        ascendant_challenge = getAscendantChallenge();
        weekly_dc_mission = getWeeklyDCMission();
        curse_level = getCurseLevel();

        if( ascendant_challenge.length > 0 && weekly_dc_mission.length > 0  && curse_level.length > 0 ) {
          var title = 'Dreaming City <small style="font-size: 70%;font-style: italic;"><a href="https://i.imgur.com/LA9TMcS.jpg" data-title="https://i.imgur.com/LA9TMcS.jpg" data-lightbox="Ascendant Challenge Map" target="_blank">Ascendant Challenge Map <i class="fas fa-external-link-alt"></i></a></small>';
          weekliesItems.push( getVendorStr( ascendant_challenge.concat(weekly_dc_mission).concat(curse_level) , title) );
        }

        reckoning = getReckoning();

        if( reckoning.length > 0  ) {
          var reckoningModifiers = milestonesData['milestones'].filter(function(d){ return d.type == 'reckoning' });
          if( reckoningModifiers.length > 0 ) {
            reckoning = Array(reckoning.shift()).concat(reckoningModifiers).concat(reckoning);
          }

          weekliesItems.push( getVendorStr( reckoning, 'The Reckoning') );
        }

        if( saladin_bounties.length > 0 ) {
          // $('.right-col').append( getVendorStr(saladin_bounties, 'Lord Salad\'s Bounties') );
        }

        if( tess_wares.length > 0 ) {
          tess_wares = _.orderBy(tess_wares, ['itemTypeDisplayName'], ['desc']);
          weekliesItems.push( getVendorStr(tess_wares, 'Tess\'s Dust Stash') );
        }

        if( tess_wares.length > 0 ) {
          // weekliesItems.push( getVendorStr(tess_silver_wares, 'Tess\'s Silverwares') );
        }

        outbreak_config = getOutbreakSinge();

        if( outbreak_config.length > 0  ) {
          var title = 'Outbreak Perfected (Heroic) <small style="font-size: 70%;font-style: italic;"><a href="/outbreak">Solution Generator <i class="fas fa-external-link-alt"></i></a></small>';
          weekliesItems.push( getVendorStr( outbreak_config, title) );
        }

        whisper_singe = getWhisperSinge();

        if( whisper_singe.length > 0  ) {
          var title = 'Whisper of the Worm (Heroic)';
          weekliesItems.push( getVendorStr( whisper_singe, title) );
        }

        escalation_protocol = getEscalationProtocol();

        if( escalation_protocol.length > 0  ) {
          weekliesItems.push( getVendorStr( escalation_protocol, 'Escalation Protocol') );
        }

        // Append items to DOM
        for(var i=0; i<weekliesItems.length; i++) {
          $('.grid').append( weekliesItems[i] );
        }

        // Enable tooltips
        $('[data-toggle="tooltip"]').tooltip({
          html: true
        });

        $('section#weeklies > div.loader, section#weeklies > div.loader-text').hide();
        $('#weeklies-item-container').fadeIn();
        $('#weeklies-note').fadeIn();

        $('.grid').masonry({
          itemSelector: '.grid-item',
          gutter: 0,
          columnWidth: '.grid-sizer',
          gutter: '.gutter-sizer',
          percentPosition: true
        });

        get_xur_location();
      }
    });
  }

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
      <div class="d-flex mb-1 align-items-center vendor-item" data-toggle="tooltip" title="`+tooltip+`">
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
      <div class="d-flex mb-1 align-items-center vendor-item" data-toggle="tooltip" title="`+tooltip+`">
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

  function get_xur_location() {
    $.get('/api/xur', function(data){
      if( data.location ) {
        $("a#xur-link").html(data.location+' <i class="fas fa-external-link-alt"></i>');
        $('.grid').masonry('layout');
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

  function getWhisperSinge() {

    var whisperSinge = [
      {name: 'Void', icon: '/common/destiny2_content/icons/150c14552f0138feadcc157571e0b0e6.png', description: 'Void damage increases slightly from all sources.'},
      {name: 'Arc', icon: '/common/destiny2_content/icons/ee1536e4ab72c6286ab68980d1ce6ecb.png', description: 'Arc damage increases slightly from all sources.'},
      {name: 'Solar', icon: '/common/destiny2_content/icons/608fb3a03d42f16f85788abe799b0af0.png', description: 'Solar damage increases slightly from all sources.'}
    ];

    var startDate = moment('2019-07-31 01:00:00', 'YYYY-MM-DD H:mm:ss');
    var currDate = moment();

    var index = 0;
    var found = false;

    while(found == false) {

      if( index == Object.keys(whisperSinge).length ) {
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
      name: whisperSinge[index].name + ' Singe',
      icon: '/common/destiny2_content/icons/b760b737519af909e26f21009d6a1487.jpg',
      description: whisperSinge[index].description
    }];
  }

  function getMenagerie(modifiers) {

    var menagerieBosses = [
      {hash: '2509539867', name: 'Hasapiko', description: "The Boss for this week is Hasapiko, a Vex Minotaur. The related flawless triumph is, <u>Break a Leg</u>."},
      {hash: '2509539865', name: 'Arunak', description: "The Boss for this week is Arunak, a Hive Ogre. The related flawless triumph is, <u>Uncontrolled Rage</u>."},
      {hash: '2509539864', name: 'Pagouri', description: "The Boss for this week is Pagouri, a Vex Hydra. The related flawless triumph is, <u>Lambs to the Slaughter</u>."}
    ];

    var startDate = moment('2019-08-07 01:00:00', 'YYYY-MM-DD H:mm:ss');
    var currDate = moment();

    var index = 0;
    var found = false;

    while(found == false) {

      if( index == Object.keys(menagerieBosses).length ) {
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

    var boss = [{
      name: 'Boss: ' + menagerieBosses[index].name,
      icon: '/common/destiny2_content/icons/52c7544a41c3c7b2d0514991fe77d8b7.png',
      description: menagerieBosses[index].description
    }];

    return boss.concat(modifiers);
  }

  function getOutbreakSinge() {

    var outbreakSinge = [
      'Void',
      'Arc',
      'Solar'
    ];

    var startDate = moment('2019-05-08 01:00:00', 'YYYY-MM-DD H:mm:ss');
    var currDate = moment();

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

    var description = 'The configuration type is <u class="color-'+outbreakSinge[index].toLowerCase()+'">' + outbreakSinge[index].toUpperCase() + '</u> for Zero Hour (Heroic).';

    return [{
      name: outbreakSinge[index] + ' Configuration',
      icon: '/common/destiny2_content/icons/c013e41cdb32779bc2322337614ea06b.jpg',
      description: description
    }];
  }

  function getReckoning() {

    var bosses = [
      'Sword Knights',
      'Likeness of Oryx'
    ];

    var startDate = moment('2019-05-29 01:00:00', 'YYYY-MM-DD H:mm:ss');
    var currDate = moment();

    var index = 0;
    var found = false;

    while(found == false) {

      if( index == Object.keys(bosses).length ) {
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

    if( index == 0 )
      var description = 'The bosses for this week\'s reckoning activity are the <u>' + bosses[index] + '</u>.';
    else
      var description = 'The boss for this week\'s reckoning activity is the <u>' + bosses[index] + '</u>.';

    var data = [{
      name: 'Tier 2/3 Boss: ' + bosses[index],
      icon: '/common/destiny2_content/icons/fc31e8ede7cc15908d6e2dfac25d78ff.png',
      description: description
    }];

    if( index == 0 ) {
      var items = ([
        {
          "name": "Lonesome",
          "description": "Kinetic Sidearm<br/><br/>Am I the only one who sees?",
          "icon": "/common/destiny2_content/icons/abd91ac904ddb37308898c9a5fd38b02.jpg",
        },
        {
          "name": "Night Watch",
          "description": "Kinetic Scout Rifle<br/><br/>Sleep with both eyes open.",
          "icon": "/common/destiny2_content/icons/f32f6b8896ca5b2684c6e02d447f5182.jpg",
        },
        {
          "name": "Sole Survivor",
          "description": "<span class='color-arc'>Arc</span> Sniper Rifle<br/><br/>Names mean nothing to the dead.",
          "icon": "/common/destiny2_content/icons/0ae824a841009f28327d905c0610b03c.jpg",
        },
        {
          "name": "Last Man Standing",
          "description": "<span class='color-solar'>Solar</span> Shotgun<br/><br/>Call me Ozymandias.",
          "icon": "/common/destiny2_content/icons/d39006fe5498ec8720622da5a31dd066.jpg",
        },
        {
          "name": "Just in Case (Tier 3 Only)",
          "description": "<span class='color-solar'>Solar</span> Sword<br/><br/>Even contingencies need contingencies.",
          "icon": "/common/destiny2_content/icons/c32e9275a505a1e39bfc146dca3702b6.jpg",
        },
      ]);
    }
    else {
      var items = ([
        {
          "name": "Spare Rations",
          "description": "Kinetic Hand Cannon<br/><br/>Whether times are lean or fat.",
          "icon": "/common/destiny2_content/icons/7106d949c81a1b2b281964ae2184d6b2.jpg",
        },
        {
          "name": "Bug-Out Bag",
          "description": "<span class='color-solar'>Solar</span> SMG<br/><br/>Grab and go.",
          "icon": "/common/destiny2_content/icons/870aa58f8314ca60ec3075f937735885.jpg",
        },
        {
          "name": "Outlast",
          "description": "<span class='color-solar'>Solar</span> Pulse Rifle<br/><br/>No such word as extinction.",
          "icon": "/common/destiny2_content/icons/7967ce5273a19ca50fe3ec1fd1b1b375.jpg",
        },
        {
          "name": "Gnawing Hunger",
          "description": "<span class='color-void'>Void</span> Auto Rifle<br/><br/>Don't let pride keep you from a good meal.",
          "icon": "/common/destiny2_content/icons/48037e6416c3c9da07030a72931e0ca9.jpg",
        },
        {
          "name": "Doomsday (Tier 3 Only)",
          "description": "<span class='color-arc'>Arc</span> Grenade Launcher<br/><br/>The age-old chant: The end of days draws nigh.",
          "icon": "/common/destiny2_content/icons/f689eb2328e786599701352b9c01b64d.jpg",
        },
      ]);
    }

    data = data.concat(items);

    return data;
  }

  function getCurseLevel() {

    var curseLevels = [
      'Low',
      'Medium',
      'High'
    ];

    var startDate = moment('2019-01-16 01:00:00', 'YYYY-MM-DD H:mm:ss');
    var currDate = moment();

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