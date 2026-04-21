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
        Schema::table('users', function (Blueprint $table) {
            $table->string('approver_name')->nullable()->comment('temporary for PO purposes')->after('email');
            $table->string('approver_title')->nullable()->comment('temporary for PO purposes')->after('approver_name');
            $table->string('branch_code', 2)->after('approver_title');
            $table->string('position', 50)->after('branch_code');
            $table->boolean('is_active')->default(true)->after('position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['approver_name', 'approver_title', 'branch_code', 'position', 'is_active']);
        });
    }
};
