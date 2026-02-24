<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpecialDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('special_days', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('birthday_header', 255)->nullable();
            $table->longText('birthday_header_text')->nullable();
            $table->string('birthday_header_image', 255)->nullable();
            $table->string('birthday_header_video', 255)->nullable();
            $table->string('birthday_header_audio', 255)->nullable();
            $table->string('birthday_header_pdf', 255)->nullable();
            $table->string('anniversary_header', 255)->nullable();
            $table->longText('anniversary_header_text')->nullable();
            $table->string('anniversary_header_image', 255)->nullable();
            $table->string('anniversary_header_video', 255)->nullable();
            $table->string('anniversary_header_audio', 255)->nullable();
            $table->string('anniversary_header_pdf', 255)->nullable();
            $table->longText('birthday_body')->nullable();
            $table->longText('birthday_footer')->nullable();
            $table->longText('anniversary_body')->nullable();
            $table->longText('anniversary_footer')->nullable();
            $table->integer('company_id')->nullable();
            $table->integer('type')->default(1)->comment('birthday=0, anniversary=1');
            $table->timestamps(0); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('special_days');
    }
}
