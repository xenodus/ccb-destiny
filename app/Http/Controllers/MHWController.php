<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App;
use Illuminate\Http\Request;
use App\Classes\Post;

class MHWController extends Controller
{
    public function monsters()
    {
      $data['site_title'] = 'Monster Hunter World: Weakness Table';
      $data['site_image'] = 'https://www.monsterhunterworld.com/images/share.png';
      $data['site_description'] = 'Information of all monsters from the Hunter\'s log in a neat table.';

      $data['monsters'] = App\Classes\MHW_Monster::with('weak_points')->get();
      $data['monster_types'] = $data['monsters']->pluck('type')->unique();

      return view('mhw.monsters', $data);
    }
}