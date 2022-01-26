<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->date('birth_date')->nullable();               // type date
            $table->string('email')->nullable()->unique();
            $table->integer('phone')->unique();
            $table->string('country')->nullable();                // select
            $table->string('address')->nullable();                // type textarea
            $table->boolean('job_contact')->default(false); // type radiobutton
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
