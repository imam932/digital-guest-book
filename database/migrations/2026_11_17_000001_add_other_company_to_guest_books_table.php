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
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            $table = 'guest_books';
            $newTable = 'guest_books_new';

            Schema::create($newTable, function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
                $table->string('other_company')->nullable();
                $table->string('guest_name');
                $table->text('message');
                $table->enum('display_status', ['pending', 'displayed', 'hidden'])->default('pending');
                $table->timestamps();

                $table->index('company_id');
                $table->index('display_status');
            });

            DB::statement("INSERT INTO {$newTable} (id, company_id, other_company, guest_name, message, display_status, created_at, updated_at) SELECT id, company_id, NULL, guest_name, message, display_status, created_at, updated_at FROM {$table}");

            Schema::dropIfExists($table);
            Schema::rename($newTable, $table);

            return;
        }

        Schema::table('guest_books', function (Blueprint $table) {
            $table->string('other_company')->nullable()->after('company_id');
        });

        DB::statement('ALTER TABLE guest_books MODIFY company_id BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            $table = 'guest_books';
            $newTable = 'guest_books_new';

            Schema::create($newTable, function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->restrictOnDelete();
                $table->string('guest_name');
                $table->text('message');
                $table->enum('display_status', ['pending', 'displayed', 'hidden'])->default('pending');
                $table->timestamps();

                $table->index('company_id');
                $table->index('display_status');
            });

            DB::statement("INSERT INTO {$newTable} (id, company_id, guest_name, message, display_status, created_at, updated_at) SELECT id, company_id, guest_name, message, display_status, created_at, updated_at FROM {$table}");

            Schema::dropIfExists($table);
            Schema::rename($newTable, $table);

            return;
        }

        DB::statement('ALTER TABLE guest_books MODIFY company_id BIGINT UNSIGNED NOT NULL');

        Schema::table('guest_books', function (Blueprint $table) {
            $table->dropColumn('other_company');
        });
    }
};
