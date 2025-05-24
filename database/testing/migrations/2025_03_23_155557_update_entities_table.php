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
        Schema::table('students', function (Blueprint $table) {
            //
            $table->string('profile_pix_url')->nullable();
            $table->string('name_on_cert')->nullable();
        });
        Schema::table('admins', function (Blueprint $table) {
            //
            $table->string('profile_pix_url')->nullable();
        });

        Schema::table('lecturers', function (Blueprint $table) {
            //
            $table->string('profile_pix_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            //
            $table->dropColumn('profile_pix_url');
            $table->dropColumn('name_on_cert');
        });

        Schema::table('admins', function (Blueprint $table) {
            //
            $table->dropColumn('profile_pix_url');
        });

        Schema::table('lecturers', function (Blueprint $table) {
            //
            $table->dropColumn('profile_pix_url');
        });
    }
};
