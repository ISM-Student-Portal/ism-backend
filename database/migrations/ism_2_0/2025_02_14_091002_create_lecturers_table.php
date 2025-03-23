<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lecturers', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_lecturer')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::table('admins', function (Blueprint $table) {

            $table->boolean('is_admin')->default(true);

        });
        Schema::table('students', function (Blueprint $table) {
            $table->boolean('is_student')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecturers');
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('is_student');
        });
    }
};
