<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('external_id')->nullable()->index();
            $table->string('name')->index();
            $table->string('category')->nullable()->index();
            $table->string('street')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable()->index();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->text('source_url')->nullable();
            $table->timestamp('scraped_at')->nullable();
            $table->timestamps();
        });

        // Partial unique index: enforces uniqueness on email only when it is NOT NULL,
        // allowing multiple rows with a NULL email.
        DB::statement('CREATE UNIQUE INDEX companies_email_unique ON companies (email) WHERE email IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
