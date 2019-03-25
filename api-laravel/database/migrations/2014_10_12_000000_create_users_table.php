<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class CreateUsersTable extends Migration {
    /**
     * @return void
     */
    public function up() {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');

            $table->string('email')->unique();
            $table->string('role',20)->nullable();;
            $table->string('name')->nullable();;
            $table->string('surname')->nullable();;
            $table->string('password')->nullable();;
            $table->timestamp('email_verified_at')->nullable();

            // Se encarga de crear el campo de creacion y actualización automaticamente
            $table->timestamps();
            $table->rememberToken();
        });
    }
    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        Schema::dropIfExists('users');
    }
}
