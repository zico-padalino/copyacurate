<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['customer', 'vendor', 'both']);
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('tax_number')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->enum('type', ['product', 'service']);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->decimal('buying_price', 15, 2)->default(0);
            $table->decimal('stock', 14, 2)->default(0);
            $table->decimal('minimum_stock', 14, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('contacts');
            $table->string('number')->unique();
            $table->date('invoice_date');
            $table->date('due_date');
            $table->enum('status', ['draft', 'sent', 'partial', 'paid', 'overdue'])->default('draft');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->timestamps();
        });
        Schema::create('purchase_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('contacts');
            $table->string('number')->unique();
            $table->date('bill_date');
            $table->date('due_date');
            $table->enum('status', ['draft', 'received', 'partial', 'paid', 'overdue'])->default('draft');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->timestamps();
        });
        Schema::create('cash_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['cash', 'bank', 'e_wallet']);
            $table->string('account_number')->nullable();
            $table->decimal('balance', 15, 2)->default(0);
            $table->timestamps();
        });
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('rate', 5, 2);
            $table->enum('type', ['sales', 'purchase', 'both']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique();
            $table->string('name');
            $table->date('acquired_at');
            $table->decimal('acquisition_cost', 15, 2);
            $table->unsignedInteger('useful_life_months');
            $table->decimal('accumulated_depreciation', 15, 2)->default(0);
            $table->enum('status', ['active', 'disposed'])->default('active');
            $table->timestamps();
        });
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('budget', 15, 2)->default(0);
            $table->enum('status', ['planning', 'active', 'completed', 'archived'])->default('planning');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
        Schema::dropIfExists('fixed_assets');
        Schema::dropIfExists('tax_rates');
        Schema::dropIfExists('cash_accounts');
        Schema::dropIfExists('purchase_bills');
        Schema::dropIfExists('sales_invoices');
        Schema::dropIfExists('products');
        Schema::dropIfExists('contacts');
    }
};
