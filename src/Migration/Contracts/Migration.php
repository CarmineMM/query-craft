<?php

namespace QueryCraft\Migration\Contracts;

interface Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void;

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void;
}
