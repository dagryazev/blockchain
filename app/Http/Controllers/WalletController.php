<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Wallet;
use App\Transaction;
use App\Servers;
use App\User;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class WalletController extends Controller
{
  private function changeBalance($coins, $idWallet){
    $wallet = Wallet::where('user_id', JWTAuth::parseToken()->authenticate()->id)
      ->where('id', $idWallet);
    if ($coins < 0)
      $wallet->decrement('balance', abs($coins));
    elseif ($coins > 0)
      $wallet->increment('balance', abs($coins));
    return [
      "user" => JWTAuth::parseToken()->authenticate()->id,
      "balance" => 0,
      "status" => "ok"
    ];
  }

  private function changeBalanceDelegate($coins, $idWallet){
    $wallet = Wallet::where('user_id', JWTAuth::parseToken()->authenticate()->id)
      ->where('id', $idWallet);
    if ($coins > 0)
      $wallet->decrement('balance_delegate', abs($coins));
    elseif ($coins < 0)
      $wallet->increment('balance_delegate', abs($coins));
    return [
      "user" => JWTAuth::parseToken()->authenticate()->id,
      "balance" => 0,
      "status" => "ok"
    ];
  }

  public function create(){
    $wallet = new Wallet;
    $hash = uniqid(0,true);
    $wallet->hash = $hash;
    $wallet->user_id = JWTAuth::parseToken()->authenticate()->id;
    $wallet->balance = 0;
    $wallet->save();
    return [
      "user" => JWTAuth::parseToken()->authenticate()->id,
      "wallet_id" => $hash,
      "balance" => 0,
      "status" => "ok"
    ];
  }

  public function delete($id){
    Wallet::where('id', $id)
    ->where('user_id', JWTAuth::parseToken()->authenticate()->id)
    ->delete();
    return [
      "wallet_id" => $id,
      "balance" => 0,
      "status" => "ok"
    ];
  }

  public function payment_in($wallet_id, $coins){
    $bool = false;
    $user_info = JWTAuth::parseToken()->authenticate();
    $wallets = Wallet::where('user_id', $user_info->id)->select('id')->get();
    foreach ($wallets as $key => $value) {
      if($value->id == $wallet_id) $bool = true;
    }
    if(!$bool)
      return [
        "status" => "error"
      ];
    $transaction = new Transaction;
    $transaction->type = "put";
    $transaction->from = $wallet_id;
    $transaction->value = $coins;
    $transaction->save();
    $this->changeBalance($coins, $wallet_id);
    return [
      "wallet_id" => $wallet_id,
      "coins" => $coins,
      "status" => "ok"
    ];
  }

  public function payment_out($wallet_id, $coins){
    $bool = false;
    $user_info = JWTAuth::parseToken()->authenticate();
    $wallets = Wallet::where('user_id', $user_info->id)->select('id')->get();
    foreach ($wallets as $key => $value) {
      if($value->id == $wallet_id) $bool = true;
    }
    if(!$bool)
      return [
        "status" => "error"
      ];
    $transaction = new Transaction;
    $transaction->type = "out";
    $transaction->to = $wallet_id;
    $transaction->value = $coins;
    $transaction->save();
    $this->changeBalance(-$coins, $wallet_id);
    return [
      "wallet_id" => $wallet_id,
      "coins" => $coins,
      "status" => "ok"
    ];
  }

  public function delegate($wallet_id, $server_id, $coins){
    $bool = false;
    if( !Servers::where('id', $server_id)->first() )
      return [
        "status" => "error"
      ];
    $user_info = JWTAuth::parseToken()->authenticate();
    $wallets = Wallet::where('user_id', $user_info->id)->select('id')->get();
    foreach ($wallets as $key => $value) {
      if($value->id == $wallet_id) $bool = true;
    }
    if(!$bool)
      return [
        "status" => "error"
      ];
    $transaction = new Transaction;
    $transaction->type = "delegate_to";
    $transaction->to = $wallet_id;
    $transaction->from_server = $server_id;
    $transaction->value = $coins;
    $transaction->save();
    $this->changeBalance($coins, $wallet_id);
    $this->changeBalanceDelegate(-$coins, $wallet_id);
    return [
      "wallet_id" => $wallet_id,
      "coins" => $coins,
      "server_id" => $server_id,
      "status" => "ok"
    ];
  }

  public function undelegate($wallet_id, $server_id, $coins){
    $bool = false;
    if( !Servers::where('id', $server_id)->first() )
      return [
        "status" => "error"
      ];
    $user_info = JWTAuth::parseToken()->authenticate();
    $wallets = Wallet::where('user_id', $user_info->id)->select('id')->get();
    foreach ($wallets as $key => $value) {
      if($value->id == $wallet_id) $bool = true;
    }
    if(!$bool)
      return [
        "status" => "error"
      ];
    $transaction = new Transaction;
    $transaction->type = "delegate_from";
    $transaction->from = $wallet_id;
    $transaction->from_server = $server_id;
    $transaction->value = $coins;
    $transaction->save();
    $this->changeBalance(-$coins, $wallet_id);
    $this->changeBalanceDelegate(-$coins, $wallet_id);
    return [
      "wallet_id" => $wallet_id,
      "coins" => $coins,
      "server_id" => $server_id,
      "status" => "ok"
    ];
  }

  public function reward($wallet_id, $server_id, $coins){
    $transaction = new Transaction;
    $transaction->type = "reward";
    $transaction->from = $wallet_id;
    $transaction->from_server = $server_id;
    $transaction->value = $coins;
    $transaction->save();
    $this->changeBalance($coins, $wallet_id);
    return [
      "wallet_id" => $wallet_id,
      "coins" => $coins,
      "server_id" => $server_id,
      "status" => "ok"
    ];
  }

  public function getAll(){
    $wallet = Wallet::where('user_id', JWTAuth::parseToken()->authenticate()->id)
    ->get();
    return [
      "data" => [
        "wallet" => $wallet
      ]
    ];
  }

  public function getAllTransaction($wallet_id = false, Request $request){
    if ($wallet_id) {
      $transaction = Transaction::where('to', $wallet_id)
        ->orWhere('from', $wallet_id)
        ->get();
    } else {
      $wallets_id = $request->only('wallets_id')['wallets_id'];
      $transaction = Transaction::whereIn('to', $wallets_id)
        ->orWhereIn('from', $wallets_id)
        ->get();
    }

    return [
      "data" => [
        "transaction" => $transaction
      ]
    ];
  }

  public function show($id){
    $wallet = DB::table('wallet')
      ->where('id', $id)
      ->select('id','balance','hash','created_at')
      ->first();

    $wallet->balance_delegate = 0;

    $transaction = DB::table('transaction')
      ->select('id','type','value', 'to', 'from', 'from_server', 'created_at')
      ->where('to', $id)
      ->orWhere('from', $id)
      ->get()
      ->toArray();

    $servers = DB::table('servers')
      ->select('id','title','address')
      ->get()
      ->toArray();


    foreach ($transaction as $key => $value) {
      if( $value->type == 'put' && $value->from )
        $wallet->balance += $value->value;
      if( $value->type == 'delegate_to' ){
        $wallet->balance -= $value->value;
        $wallet->balance_delegate += $value->value;
        $transaction[$key]->from_server = DB::table('servers')
          ->where('id', $transaction[$key]->from_server)
          ->select('title')
          ->first()
          ->title;
      }
      if( $value->type == 'delegate_from' ){
        $wallet->balance += $value->value;
        $wallet->balance_delegate -= $value->value;
        $transaction[$key]->from_server = DB::table('servers')
          ->where('id', $transaction[$key]->from_server)
          ->select('title')
          ->first()
          ->title;
      }
      if( $value->type == 'reward' )
        $wallet->balance += $value->value;
      if( $value->type == 'out' )
        $wallet->balance -= $value->value;
    }

    return view('wallet', ["data" => [
        'wallet' => $wallet,
        'transaction' => $transaction,
        'servers' => $servers
      ]
     ]);
  }
}
