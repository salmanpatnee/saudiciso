<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per captured HTTP request.
     *
     * Deliberately carries no foreign keys: a cascading delete would destroy
     * the audit trail when a user is removed, which is the opposite of the
     * point. Actor name, email and role are denormalised snapshots so a deleted
     * user's history stays readable, and so each row reflects what was true at
     * the time rather than what is true now.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name', 191)->nullable();
            $table->string('user_email', 191)->nullable();
            $table->unsignedInteger('role_id')->nullable();
            $table->string('role_name', 60)->nullable();
            $table->boolean('is_authenticated')->default(false);
            $table->timestamp('linked_at')->nullable();

            $table->uuid('visitor_id')->nullable();
            $table->char('session_id', 40)->nullable();
            $table->unsignedBigInteger('user_session_id')->nullable();

            /**
             * dateTime rather than timestamp: no implicit session time zone
             * conversion on read or write, and no 2038 cap.
             */
            $table->dateTime('occurred_at');
            $table->unsignedInteger('duration_ms')->nullable();

            $table->string('method', 10);
            $table->text('url')->nullable();
            $table->string('path', 191)->nullable();
            $table->string('route_name', 150)->nullable();
            $table->string('controller_action', 191)->nullable();
            $table->string('referer', 512)->nullable();
            $table->boolean('is_ajax')->default(false);
            $table->json('query_params')->nullable();
            $table->json('payload')->nullable();
            $table->unsignedSmallInteger('status_code')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('browser', 50)->nullable();
            $table->string('browser_version', 20)->nullable();
            $table->string('platform', 50)->nullable();
            $table->string('platform_version', 20)->nullable();
            $table->string('device_type', 20)->nullable();
            $table->string('device_model', 60)->nullable();

            $table->char('country_code', 2)->nullable();
            $table->string('country', 60)->nullable();
            $table->string('region', 80)->nullable();
            $table->string('city', 80)->nullable();
            $table->string('geo_timezone', 64)->nullable();
            $table->string('isp', 120)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamp('geo_resolved_at')->nullable();

            $table->string('activity_type', 40)->default('other');
            $table->string('description', 255)->nullable();
            $table->string('label', 150)->nullable();
            $table->string('module', 60)->nullable();
            $table->string('subject_type', 120)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('meta')->nullable();

            $table->timestamp('created_at')->useCurrent();

            /**
             * Every dashboard query is date bounded first, so (discriminator,
             * occurred_at) composites seek then range scan, and the range scan
             * also satisfies ORDER BY occurred_at DESC.
             *
             * Secondary filters (route_name, module, status_code, country_code,
             * method, device_type) are intentionally left unindexed: they are
             * always combined with a date range, and each index costs a B-tree
             * write per insert on the hottest table in the application. Add
             * them later, one at a time, driven by EXPLAIN.
             */
            $table->index('occurred_at', 'al_occurred_idx');
            $table->index(['user_id', 'occurred_at'], 'al_user_occurred_idx');
            $table->index(['visitor_id', 'occurred_at'], 'al_visitor_occurred_idx');
            $table->index(['session_id', 'occurred_at'], 'al_session_occurred_idx');
            $table->index(['activity_type', 'occurred_at'], 'al_type_occurred_idx');
            $table->index(['ip_address', 'occurred_at'], 'al_ip_occurred_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
