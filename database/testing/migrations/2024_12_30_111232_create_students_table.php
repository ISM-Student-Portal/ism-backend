<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('matric_no')->nullable()->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('password')->nullable();
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('country');
            $table->string('city');
            $table->boolean('first_login')->default(true);
            $table->string('plan')->nullable();
            $table->string('education');
            $table->string('baptized')->nullable();
            $table->string('gender')->nullable();
            $table->string('attended_som_before')->nullable();
            $table->text('where_attended')->nullable();
            $table->string('participation_mode')->nullable();
            $table->string('ln_member')->nullable();
            $table->string('ministry')->nullable();
            $table->string('ministry_role')->nullable();
            $table->text('salvation_experience')->nullable();
            $table->text('expectations')->nullable();
            $table->dateTime('email_verified_at')->nullable();
            $table->boolean('payment_complete')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
