<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateBorrowingTrigger extends Migration
{
    public function up()
    {
        
        DB::unprepared('
            CREATE TRIGGER after_borrowing_approve
            AFTER UPDATE ON borrowings
            FOR EACH ROW
            BEGIN
                IF NEW.status = "approved" AND OLD.status != "approved" THEN
                    UPDATE items
                    SET stock = stock - NEW.quantity
                    WHERE id = NEW.item_id;
                END IF;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER after_borrowing_reject_or_return
            AFTER UPDATE ON borrowings
            FOR EACH ROW
            BEGIN
                IF (NEW.status = "rejected" OR NEW.status = "returned") 
                AND (OLD.status = "approved" OR OLD.status = "pending") THEN
                    UPDATE items
                    SET stock = stock + OLD.quantity
                    WHERE id = OLD.item_id;
                END IF;
            END
        ');
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS after_borrowing_approve');
        DB::unprepared('DROP TRIGGER IF EXISTS after_borrowing_reject_or_return');
    }
}