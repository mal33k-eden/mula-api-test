<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKycLevelRequirementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kyc_level_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kyc_level_id')->constrained('kyc_levels')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('connected_country_id')->constrained('connected_countries')->onDelete('cascade')->onUpdate('cascade');
            $table->string('document');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kyc_level_requirements');
    }
}
