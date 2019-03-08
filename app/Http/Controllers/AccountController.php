<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Transaction;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AccountController extends Controller
{
    public function show(){
      $id = JWTAuth::parseToken()->authenticate()->id;
      $users = DB::table('users')
        ->where('users.id', $id)
        ->select('users.id', 'users.name', 'users.email')
        ->first();

      $w = DB::table('wallet')
        ->where('user_id', $id)
        ->select('id','balance','hash')
        ->get()
        ->toArray();
      foreach ($w as $key => $value) {
        $wallet[$value->id] = $value;
        $wallet[$value->id]->balance_delegate = 0;
        $transaction = DB::table('transaction')
          ->orWhere('from', $value->id);
      }
      $transaction = DB::table('transaction')
        ->select('id','type','value', 'to', 'from', 'from_server', 'created_at')
        ->get()
        ->toArray();


      foreach ($transaction as $key => $value) {
        if( !isset( $wallet[ $value->from ]->balance ) && $value->from ) $wallet[ $value->from ]->balance = 0;
        if( $value->type == 'put' && $value->from )
          $wallet[ $value->from ]->balance += $value->value;
        if( $value->type == 'delegate_to' ){
          $wallet[ $value->to ]->balance -= $value->value;
          $wallet[ $value->to ]->balance_delegate += $value->value;
        }
        if( $value->type == 'delegate_from' ){
          $wallet[ $value->from ]->balance += $value->value;
          $wallet[ $value->from ]->balance_delegate -= $value->value;
        }
        if( $value->type == 'reward' )
          $wallet[ $value->from ]->balance += $value->value;
        if( $value->type == 'out' )
          $wallet[ $value->to ]->balance -= $value->value;
      }

      return view('account', ["data" => [
          'user' => Auth::user(),
          'wallet' => $wallet,
          'transaction' => $transaction
        ]
       ]);
    }
}
