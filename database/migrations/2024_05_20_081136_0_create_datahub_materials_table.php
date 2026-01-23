<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datahub_materials', function (Blueprint $table) {
            $table->id();
            $table->string('matnr');
            $table->string('ersda')->nullable();
            $table->string('ernam')->nullable();
            $table->string('maktx');
            $table->string('laeda')->nullable();
            $table->string('meins')->nullable();
            $table->string('aenam')->nullable();
            $table->string('matkl')->nullable();
            $table->string('ekgrp')->nullable();
            $table->string('vpsta')->nullable();
            $table->string('bklas')->nullable();
            $table->string('pstat')->nullable();
            $table->string('lvorm')->nullable();
            $table->string('mtart')->nullable();
            $table->string('mbrsh')->nullable();
            $table->string('mseht')->nullable();
            $table->string('pstatWerks')->nullable();
            $table->string('lvormWerks')->nullable();
            $table->string('dismm')->nullable();
            $table->string('beskz')->nullable();
            $table->string('vprsv')->nullable();
            $table->string('verpr')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datahub_materials');
    }
};
