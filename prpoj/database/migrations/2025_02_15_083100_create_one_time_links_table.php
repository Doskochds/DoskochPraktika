<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('one_time_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained()->onDelete('cascade');
            $table->string('token')->unique();
            $table->timestamps();
            $table->softDeletes();

            $table->timestamp('used_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('one_time_links');
    }
};


