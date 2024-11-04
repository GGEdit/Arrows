<?php

use App\Consts\RoomType;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('type');
            $table->integer('owner_id');
            $table->integer('latest_message_id')->nullable();
            $table->integer('opening_meet_id')->nullable();
            $table->timestamps();
        });

        $now = Carbon::now();
        $myRoomDatas = [
            ['name' => 'マイルーム', 'type' => RoomType::MY_MESSAGE, 'owner_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'マイルーム', 'type' => RoomType::MY_MESSAGE, 'owner_id' => 2, 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('rooms')->insert($myRoomDatas);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rooms');
    }
}
