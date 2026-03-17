<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('uuid');
            $table->string('sub_dataset_codename')->nullable();
            $table->string('associated_dataset')->nullable();
            $table->string('associated_subdataset')->nullable();
            $table->string('parent_project_codename')->nullable();
            $table->string('qbrc_pathology_case_id')->nullable();
            $table->string('final_pathologic_diagnosis')->nullable();
            $table->string('stain')->nullable();
            $table->string('stain_marker')->nullable();
            $table->string('block_number')->nullable();
            $table->string('magnification')->nullable();
            $table->string('scanner_specs')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('batch_id');
            $table->boolean('is_delete')->default(false);
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
        Schema::dropIfExists('images');
    }
}
