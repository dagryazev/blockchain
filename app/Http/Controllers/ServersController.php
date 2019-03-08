<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Servers;
class ServersController extends Controller
{
    public function create($address, $title){
      $servers = new Servers;
      $servers->address = $address;
      $servers->title = $title;
      $servers->balance = 0;
      $servers->save();

      return [
        "type" => "add_server",
        "address" => $address,
        "title" => $title
      ];
    }

    public function getAll(){
      $servers = Servers::get();
      return [
        "data" => [
          "servers" => $servers
        ]
      ];
    }

    public function delete($id){
      Servers::where('id', $id)->delete();

      return [
        "type" => "delete_server",
        "id" => $id
      ];
    }
}
