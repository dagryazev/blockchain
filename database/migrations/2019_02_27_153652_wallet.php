<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Wallet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('wallet', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->string('hash');
          $table->integer('user_id');
          $table->bigInteger('balance')->default(0);
          $table->bigInteger('balance_delegate')->default(0);
          $table->timestamp('created_at')->useCurrent();
          $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
