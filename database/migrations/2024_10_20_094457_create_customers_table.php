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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_catalogue_id');
            $table->string('name', 255);
            $table->string('phone', 255)->nullable();
            $table->string('province_id', 10)->nullable();
            $table->string('district_id', 10)->nullable();
            $table->string('ward_id', 10)->nullable();
            $table->string('address')->nullable();
            $table->dateTime('birthday')->nullable();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('ip')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->tinyInteger('publish')->default(0);
            $table->timestamp('deleted_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->foreign('customer_catalogue_id')->references('id')->on('customer_catalogues');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
