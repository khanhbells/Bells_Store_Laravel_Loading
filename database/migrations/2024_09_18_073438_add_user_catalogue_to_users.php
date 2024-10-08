<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Kiểm tra nếu cột chưa tồn tại để tránh lỗi duplicate
        if (!Schema::hasColumn('users', 'user_catalogue_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->bigInteger('user_catalogue_id')->default(2);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('user_catalogue_id');
        });
    }
};
