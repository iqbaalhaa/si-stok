<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('system_setting', function (Blueprint $table) {
            $table->string('login_logo')->nullable()->after('logo');
        });
    }

    public function down(): void
    {
        Schema::table('system_setting', function (Blueprint $table) {
            $table->dropColumn('login_logo');
        });
    }
};