<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateMakeSlugFunction extends Migration
{
    public function up(): void
    {
        DB::unprepared("
            DROP FUNCTION IF EXISTS make_slug;
        ");

        DB::unprepared("
            CREATE FUNCTION make_slug(input VARCHAR(255))
            RETURNS VARCHAR(255)
            DETERMINISTIC
            RETURN LOWER(REPLACE(TRIM(input), ' ', '-'));
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP FUNCTION IF EXISTS make_slug;");
    }
}
