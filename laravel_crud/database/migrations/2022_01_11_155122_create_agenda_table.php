<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgendaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agenda', function (Blueprint $table) {
            $table->id();

            $table->char('name', 30)->unique();
            $table->string('slug');
            $table->char('email', 30)->nullable()->unique();
            $table->char('phone', 30)->unique();
            $table->char('address', 30)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('agenda', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agenda');
    }
}
