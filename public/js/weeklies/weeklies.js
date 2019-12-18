$(document).ready(function(){

  fetchMilestones();

  function fetchMilestones() {

    $('section#weeklies > div.loader, section#weeklies > div.loader-text').show();
    $('#weeklies-raid-item-container, #weeklies-vanguard-item-container, #weeklies-gambit-item-container, #weeklies-crucible-item-container, #weeklies-vendors-item-container').empty();
    $('#weeklies-raid-item-container, #weeklies-vanguard-item-container, #weeklies-gambit-item-container, #weeklies-crucible-item-container, #weeklies-vendors-item-container').append('<div class="grid-sizer"></div><div class="gutter-sizer"></div>');
    $('#weeklies-raid-item-container-wrapper, #weeklies-vanguard-item-container-wrapper, #weeklies-gambit-item-container-wrapper, #weeklies-crucible-item-container-wrapper, #weeklies-vendors-item-container-wrapper').hide();
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
        'Prime "Civic Duty" Bounty',
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

      var tessExcludeFilter = [
        'Consumable'
      ];

      // excludes
      var benedictFilter = [
        'Buff',
        'Armor Set',
        'Quest Step',
      ];

      var bansheeFilter = [
        'Solstice of Heroes Warlock Armor Upgrade',
        'Additional Bounties'
      ];

      var spiderRareBounty = [
        'WANTED: Combustor Valus',
        'WANTED: Arcadian Chord',
        'WANTED: The Eye in the Dark',
        'WANTED: Gravetide Summoner',
        'WANTED: Silent Fang',
        'WANTED: Blood Cleaver'
      ];

      console.log( milestonesData );

      if( data.length > 0 ) {

        /*************************
        ****** Raid Section
        *************************/

        var weekliesItems = [];

        // GOS Challenge
        weekliesItems.push( getVendorStr([milestonesData['gos_challenge']], 'Garden of Salvation Challenge') );

        // Suraya Raid Bounties
        var raid_bounties = data.filter(function(item){ return item.vendor_hash == vendorHash['Suraya Hawthorne'] && item.itemTypeDisplayName == 'Weekly Bounty' });

        if( raid_bounties.length > 0 ) {
          weekliesItems.push( getVendorStr(raid_bounties, 'Hawthorne\'s Raid ' + (raid_bounties.length > 1 ? 'Bounties' : 'Bounty') ) );
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

        /// Sundial
        var sundialModifiers = milestonesData['milestones'].filter(function(d){ return d.type == 'sundial' });

        if( sundialModifiers.length > 0 ) {
          weekliesItems.push( getVendorStr( sundialModifiers, "Sundial") );
        }

        // Menagerie Modifiers
        var menagerieModifiers = milestonesData['milestones'].filter(function(d){ return d.type == 'menagerie' });

        if( menagerieModifiers.length > 0 ) {
          weekliesItems.push( getVendorStr( menagerieModifiers, "The Menagerie (Heroic)") );
        }

        if( weekliesItems.length > 0 ) {

          // Append items to DOM
          for(var i=0; i<weekliesItems.length; i++) {
            $('#weeklies-raid-item-container.grid').append( weekliesItems[i] );
          }

          // Enable tooltips
          $('[data-toggle="tooltip"]').tooltip({
            html: true
          });

          $('section#weeklies > div.loader, section#weeklies > div.loader-text').hide();
          $('#weeklies-raid-item-container-wrapper').fadeIn();

          $('#weeklies-raid-item-container.grid').masonry({
            itemSelector: '.grid-item',
            gutter: 0,
            columnWidth: '.grid-sizer',
            gutter: '.gutter-sizer',
            percentPosition: true
          });
        }

        /*************************
        ****** Vanguard Section
        *************************/

        var weekliesItems = [];

        // Nightfall
        if( milestonesData['nightfalls'].length > 0 ) {
          weekliesItems.push( getVendorStr(milestonesData['nightfalls'], 'Nightfalls') );
        }

        // Daily Modifiers
        var dailyModifiers = milestonesData['milestones'].filter(function(d){ return d.type == 'strike' });

        if( dailyModifiers.length > 0 ) {
          weekliesItems.push( getVendorStr(dailyModifiers, 'Daily Modifiers <small class="text-yellow" style="font-size: 70%;font-style: italic;">Strike / The Menagerie / Heroic Story</small>') );
        }

        // Weekly Flashpoint
        var flashpoint = milestonesData['milestones'].filter(function(d){ return d.type == 'flashpoint' });

        if( flashpoint.length > 0 ) {
          weekliesItems.push( getVendorStr(flashpoint, 'Flashpoint') );
        }

        // Altar of Sorrows
        weekliesItems.push( getVendorStr(milestonesData['altar_of_sorrows'], 'Altars of Sorrow') );

        // Dreaming City
        var dreamingCity = [milestonesData['ascendant_challenge'], milestonesData['dreaming_city_mission'], milestonesData['dreaming_city_curse_level']];
        weekliesItems.push( getVendorStr( dreamingCity, "Dreaming City") );

        // Escalation Protocol
        weekliesItems.push( getVendorStr( [milestonesData['escalation_protocol']], "Escalation Protocol") );

        // Whisper of the worm
        weekliesItems.push( getVendorStr( [milestonesData['whisper_singe']], "Whisper of the Worm (Heroic)") );

        // Outbreak Perfected
        weekliesItems.push( getVendorStr( [milestonesData['outbreak_singe']], 'Outbreak Perfected (Heroic) <small style="font-size: 70%;font-style: italic;"><a href="/outbreak">Solution <i class="fas fa-external-link-alt"></i></a></small>') );

        if( weekliesItems.length > 0 ) {

          // Append items to DOM
          for(var i=0; i<weekliesItems.length; i++) {
            $('#weeklies-vanguard-item-container.grid').append( weekliesItems[i] );
          }

          // Enable tooltips
          $('[data-toggle="tooltip"]').tooltip({
            html: true
          });

          $('section#weeklies > div.loader, section#weeklies > div.loader-text').hide();
          $('#weeklies-vanguard-item-container-wrapper').fadeIn();

          $('#weeklies-vanguard-item-container.grid').masonry({
            itemSelector: '.grid-item',
            gutter: 0,
            columnWidth: '.grid-sizer',
            gutter: '.gutter-sizer',
            percentPosition: true
          });
        }

        /*************************
        ****** Gambit Section
        *************************/

        var weekliesItems = [];

        // Gambit
        var gambit_bounties = data.filter(function(item){ return item.vendor_hash == vendorHash['The Drifter'] && gambitBountiesFilter.includes(item.itemTypeDisplayName) });

        if( gambit_bounties.length > 0 ) {
          weekliesItems.push( getVendorStr(gambit_bounties, 'Gambit Bounties (World)') );
        }

        // Reckoning
        var reckoning = milestonesData['reckoning'][1];
        reckoning.unshift( milestonesData['reckoning'][0] );

        var reckoningModifiers = milestonesData['milestones'].filter(function(d){ return d.type == 'reckoning' });
        reckoning = Array(reckoning.shift()).concat(reckoningModifiers).concat(reckoning);

        weekliesItems.push( getVendorStr( reckoning, 'The Reckoning') );

        if( weekliesItems.length > 0 ) {

          // Append items to DOM
          for(var i=0; i<weekliesItems.length; i++) {
            $('#weeklies-gambit-item-container.grid').append( weekliesItems[i] );
          }

          // Enable tooltips
          $('[data-toggle="tooltip"]').tooltip({
            html: true
          });

          $('section#weeklies > div.loader, section#weeklies > div.loader-text').hide();
          $('#weeklies-gambit-item-container-wrapper').fadeIn();

          $('#weeklies-gambit-item-container.grid').masonry({
            itemSelector: '.grid-item',
            gutter: 0,
            columnWidth: '.grid-sizer',
            gutter: '.gutter-sizer',
            percentPosition: true
          });
        }

        /*************************
        ****** Crucible Section
        *************************/

        var weekliesItems = [];

        /*
        saladin_bounties = data.filter(function(item){ return item.vendor_hash == vendorHash['Lord Saladin'] && item.itemTypeDisplayName == 'Iron Banner Bounty' });

        if( saladin_bounties.length > 0 ) {
          weekliesItems.push( getVendorStr(saladin_bounties, 'Iron Banner') );
        }
        */

        if( weekliesItems.length > 0 ) {

          // Append items to DOM
          for(var i=0; i<weekliesItems.length; i++) {
            $('#weeklies-crucible-item-container.grid').append( weekliesItems[i] );
          }

          // Enable tooltips
          $('[data-toggle="tooltip"]').tooltip({
            html: true
          });

          $('section#weeklies > div.loader, section#weeklies > div.loader-text').hide();
          $('#weeklies-crucible-item-container-wrapper').fadeIn();

          $('#weeklies-crucible-item-container.grid').masonry({
            itemSelector: '.grid-item',
            gutter: 0,
            columnWidth: '.grid-sizer',
            gutter: '.gutter-sizer',
            percentPosition: true
          });
        }

        /*************************
        ****** Vendors Section
        *************************/

        var weekliesItems = [];

        // Xur
        xur_wares = data.filter(function(item){ return item.vendor_hash == vendorHash['Xur'] && item.itemTypeDisplayName != 'Challenge Card' && item.itemTypeDisplayName != 'Invitation of the Nine' });

        if( xur_wares.length > 1 ) {
          weekliesItems.push( getXurVendorStr(xur_wares, 'Xûr <small style="font-size: 70%;font-style: italic;"><a href="https://wherethefuckisxur.com/" target="_blank" id="xur-link">Where is Xur? <i class="fas fa-external-link-alt"></i></a></small>', 'vertical', milestonesData['xur_sales_item_perks']) );
        }

        // Banshee
        banshee_wares = data.filter(function(item){ return item.vendor_hash == vendorHash['Banshee-44'] && item.icon != '' && bansheeFilter.includes(item.name) == false });

        if( banshee_wares.length > 0 ) {
          banshee_wares = _.orderBy(banshee_wares, ['cost_name'], ['desc']);
          weekliesItems.push( getVendorStr(banshee_wares, 'Banshee-44') );
        }

        // Spider
        spider_wares = data.filter(function(item){ return item.vendor_hash == vendorHash['Spider'] && item.itemTypeDisplayName == '' });

        if( spider_wares.length > 0 ) {
          weekliesItems.push( getVendorStr(spider_wares, 'The Spider') );
        }

        // Tess
        tess_wares = data.filter(function(item){ return item.vendor_hash == vendorHash['Tess Everis'] && item.cost_name == 'Bright Dust' && tessExcludeFilter.includes(item.itemTypeDisplayName) == false });

        if( tess_wares.length > 0 ) {
          tess_wares = _.orderBy(tess_wares, ['itemTypeDisplayName'], ['desc']);
          weekliesItems.push( getVendorStr(tess_wares, 'Tess Everis (Bright Dust)') );
        }

        // Benedict
        benedict_wares = data.filter(function(item){ return item.vendor_hash == vendorHash['Benedict 99-40'] && item.itemTypeDisplayName != 'Weekly Bounty' && item.name != 'Runefinder' });

        if( benedict_wares.length > 0 ) {
          weekliesItems.push( getVendorStr(benedict_wares, 'Benedict 99-40') );
        }

        // Eva Granny
        eva_bounties = data.filter(function(item){ return item.vendor_hash == vendorHash['Eva Levante'] && item.cost != null && item.name != 'Additional Bounties' });

        if( eva_bounties.length > 0 ) {
          weekliesItems.push( getVendorStr(eva_bounties, 'Eva Levante') );
        }

        if( weekliesItems.length > 0 ) {

          // Append items to DOM
          for(var i=0; i<weekliesItems.length; i++) {
            $('#weeklies-vendors-item-container.grid').append( weekliesItems[i] );
          }

          // Enable tooltips
          $('[data-toggle="tooltip"]').tooltip({
            html: true
          });

          $('section#weeklies > div.loader, section#weeklies > div.loader-text').hide();
          $('#weeklies-vendors-item-container-wrapper').fadeIn();

          $('#weeklies-vendors-item-container.grid').masonry({
            itemSelector: '.grid-item',
            gutter: 0,
            columnWidth: '.grid-sizer',
            gutter: '.gutter-sizer',
            percentPosition: true
          });
        }
      }
    });
  }

  var nightfall_loot = {
    'Tree of Probabilities': {
      'name': 'D.F.A.',
      'icon': '/common/destiny2_content/icons/6e692a14162839d0489e11cf9d84746e.jpg',
      'description': '"Osiris said that he started to pity the Red Legion, getting trapped in here for infinite eternities. I think they\'re getting exactly what they deserve." —Sagira',
      'type': 'Legendary Hand Cannon'
    },
    'Strange Terrain': {
      'name': 'BrayTech Osprey',
      'icon': '/common/destiny2_content/icons/659ebe95206951d7c97022b47a93c459.jpg',
      'description': 'Expected Use Timeframe: UNKNOWN.',
      'type': 'Legendary Rocket Launcher'
    },
    'Savathûn\'s Song': {
      'name': 'Duty Bound',
      'icon': '/common/destiny2_content/icons/0497af906c184a43fa7e2accae899c35.jpg',
      'description': '"Due respect, Commander? I was there when the Hive found us on Earth. I was there when we stopped them on Titan. And I\'ll be there when we wipe them out." —Sloane',
      'type': 'Legendary Auto Rifle'
    },
    'The Pyramidion': {
      'name': 'Silicon Neuroma',
      'icon': '/common/destiny2_content/icons/77364d95fdc16bb1d23f6f00817dc6ab.jpg',
      'description': '"My future is concurrently irreversible and unknowable. Before it overtakes me, I desire a more abrupt end to those responsible." —Asher Mir',
      'type': 'Legendary Sniper Rifle'
    },
    'The Arms Dealer': {
      'name': 'Tilt Fuse',
      'icon': '/common/destiny2_content/icons/a2dd642b18b15f764db069f845f5173c.jpg',
      'description': '"[whistle] With a few more revs, Zahn woulda turned this thing into the fastest bomb you never saw. Good thing for all of us he never got the chance." —Amanda Holliday',
      'type': 'Exotic Sparrow'
    },
    'Exodus Crash': {
      'name': 'Impact Velocity',
      'icon': '/common/destiny2_content/icons/4d0ecd27dd8a6d02a8a0f3b2618a097e.jpg',
      'description': '"Captain, this conveyance\'s top speed is a fraction of the Exodus Black\'s when it crashed into Nessus!" "Try not to find out for yourself." —Failsafe',
      'type': 'Exotic Sparrow'
    },
    'The Inverted Spire': {
      'name': 'Trichromatica',
      'icon': '/common/destiny2_content/icons/bb72e6b7b2a7ac6165431d4a47171b2f.jpg',
      'description': '"Void, Solar, then Arc. Hmm. We\'re not naive enough to think the order is a coincidence. But we\'ve got bigger things to worry about." —Zavala',
      'type': 'Exotic Ghost Shell'
    },
    'A Garden World': {
      'name': 'Universal Wavefunction',
      'icon': '/common/destiny2_content/icons/d6c77755df5761e5626b052d440cf5c7.jpg',
      'description': '"I believed your presence at the genesis of the Infinite Forest would lead to a comprehensive understanding of the Vex. When will I learn that things are never so simple?" —Ikora',
      'type': 'Exotic Ship'
    },
    'Lake of Shadows': {
      'name': 'The Militia\'s Birthright',
      'icon': '/common/destiny2_content/icons/39b67dae56153d70e935bfad21faecc7.jpg',
      'description': '"Earth is our home. Not Mars, not Venus, not even the Reef. We must ensure it is a place we can continue to live for many generations to come." —Devrim Kay',
      'type': 'Legendary Grenade Launcher'
    },
    'Will of the Thousands': {
      'name': 'Worm God Incarnation',
      'icon': '/common/destiny2_content/icons/2d6ff9e9e65253a82ec0856f310e2b94.jpg',
      'description': 'Modifications for your ship\'s transmat systems, so you\'ll always arrive in style.',
      'type': 'Legendary Transmat Effect'
    },
    'The Insight Terminus': {
      'name': 'The Long Goodbye',
      'icon': '/common/destiny2_content/icons/fe07633a2ee87f0c00d5b0c0f3838a7d.jpg',
      'description': 'Yeah… I lost a lot of these out on Nessus. Long story. Lots of dead Vex." —The Drifter',
      'type': 'Legendary Sniper Rifle'
    },
    'The Hollowed Lair': {
      'name': 'Mindbender\'s Ambition',
      'icon': '/common/destiny2_content/icons/0d39a47ea705e188a3674fa5f41b99a5.jpg',
      'description': '"Hiraks always did like to leave an impression." —The Spider',
      'type': 'Legendary Shotgun'
    },
    'Warden of Nothing': {
      'name': 'Warden\'s Law',
      'icon': '/common/destiny2_content/icons/89a68f864854dd80155eb194ee8f5cb7.jpg',
      'description': 'Fight. Win. Li- Li- Li- Li- Li- FATAL EXCEPTION HAS OCCURRED AT 0028:C001E36',
      'type': 'Legendary Hand Cannon'
    },
    'The Corrupted': {
      'name': 'Horror\'s Least',
      'icon': '/common/destiny2_content/icons/c5454c80b15ecb3b3abf2d69d4bfe5ff.jpg',
      'description': '"Some things should not be saved." —Techeun Sedia',
      'type': 'Legendary Pulse Rifle'
    }
  };

  function getVendorStr(data, title, icon="") {
    str = `
    <div class="grid-item col-md-4">
      <div class="mb-3 border-warning border">
        <div class="border-warning border-bottom p-2 text-left">`+title+`</div>
        <div class="pl-2 pr-2 pt-2 pb-1">
    `;

    for(var i=0; i<data.length; i++) {

      var tooltip = '';
      var cost = '';

      if( data[i].description ) {

        if( data[i].cost ) {
          cost = `<div class='mt-2'>Price: `+data[i].cost+` `+data[i].cost_name+`</div>`;
        }

        // Price
        if( data[i].costs && data[i].costs.length > 0 ) {
          costs = [];

          for(var j=0; j<data[i].costs.length; j++) {
            costs.push( data[i].costs[j].cost_quantity + " " + data[i].costs[j].cost_name );
          }

          cost = `<div class='mt-2'>Price: <br/>`+ costs.join('<br/>') +`</div>`;
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
        else if ( title == 'Nightfalls' &&  data[i].name.includes('Ordeal')) {

          nf_key = Object.keys(nightfall_loot).filter(function(name){
            return name.includes( data[i].name.replace('Nightfall The Ordeal: ', '') );
          });

          if( nf_key.length > 0 ) {
            tooltip = `
            <div>
              <h6 class='font-weight-bold mb-1'>`+data[i].name+`</h6>
              <div class='mb-1'>
                `+nightfall_loot[nf_key].description.replace(/"/g, "'")+`
              </div>

              <div>Rare Drop</div>

              <div class='d-flex align-items-start mt-2'>
                <div>
                  <img src='https://bungie.net`+nightfall_loot[nf_key].icon+`' class='mt-1 mb-1 mr-2 tooltip-icon' style='width: 50px; height: 50px;'/>
                </div>
                <div>
                  <div class='text-weight-bold'><h6 class='mb-1'>`+nightfall_loot[nf_key].name+`</h6></div>
                  <div>`+nightfall_loot[nf_key].type.replace(/"/g, "'")+`</div>
                </div>
              </div>
            </div>
            `;
          }
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

      if( data[i].icon ) {
        str += `
        <div class="d-flex mb-1 align-items-center vendor-item text-left" data-toggle="tooltip" title="`+tooltip+`">
          <img class="img-fluid" src="https://bungie.net`+data[i].icon+`" style="width: 20px; height: 20px; margin-right: 5px;"/>`+data[i].name+`
        </div>
        `;
      }
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
              // perks += `<div class='mb-1'>Perk `+roll+`</div><div class='mb-2 border border-secondary rounded'>`;
              perks += `<div class='mb-2 border border-secondary'>`;
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
      <div class="d-flex mb-1 align-items-center vendor-item text-left" data-toggle="tooltip" title="`+tooltip+`">
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
});