<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('api_token', 80)->unique()->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_official')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        $now = Carbon::now();
        $userDatas = [
            ['id' => '1', 'name' => 'admin', 'username' => 'admin', 'email' => 'admin@gmail.com', 'password' => '$2y$10$tlnbjM3hICVVMfhVTMTC/ubwVbRy1mDPHLFz6WD.EXl8A3KFT.yMS', 'api_token' => Str::random(60), 'image_url' => '/storage/preset/default_avatar.png', 'is_official' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => '2', 'name' => 'guest', 'username' => 'guest', 'email' => 'guest@gmail.com', 'password' => '$2y$10$tlnbjM3hICVVMfhVTMTC/ubwVbRy1mDPHLFz6WD.EXl8A3KFT.yMS', 'api_token' => Str::random(60), 'image_url' => '/storage/preset/default_avatar.png', 'is_official' => false, 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('users')->insert($userDatas);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
