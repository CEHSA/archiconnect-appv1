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
        Schema::table('conversations', function (Blueprint $table) {
            // Check if foreign keys exist before trying to drop them.
            // Foreign key names are typically: tablename_columnname_foreign
            // However, this can vary. If these drops fail, it might be because
            // the foreign keys were not named conventionally or not created.

            if (Schema::hasColumn('conversations', 'participant1_id')) {
                // Attempt to drop foreign key if it exists.
                // This is a common convention, but might not match exactly.
                // If this fails, the foreign key might have a different name or not exist.
                try {
                    // Check if the foreign key constraint exists before dropping
                    $foreignKeys = Schema::getConnection()->getDoctrineSchemaManager()->listTableForeignKeys('conversations');
                    $hasForeignKey1 = false;
                    foreach ($foreignKeys as $foreignKey) {
                        if (in_array('participant1_id', $foreignKey->getLocalColumns())) {
                            $hasForeignKey1 = true;
                            $table->dropForeign($foreignKey->getName()); // Drop by actual name
                            break;
                        }
                    }
                } catch (\Exception $e) {
                    // Log or output warning: echo "Could not drop foreign key for participant1_id: " . $e->getMessage() . "\n";
                }
                $table->dropColumn('participant1_id');
            }

            if (Schema::hasColumn('conversations', 'participant2_id')) {
                try {
                    $foreignKeys = Schema::getConnection()->getDoctrineSchemaManager()->listTableForeignKeys('conversations');
                    $hasForeignKey2 = false;
                    foreach ($foreignKeys as $foreignKey) {
                        if (in_array('participant2_id', $foreignKey->getLocalColumns())) {
                            $hasForeignKey2 = true;
                            $table->dropForeign($foreignKey->getName()); // Drop by actual name
                            break;
                        }
                    }
                } catch (\Exception $e) {
                    // Log or output warning: echo "Could not drop foreign key for participant2_id: " . $e->getMessage() . "\n";
                }
                $table->dropColumn('participant2_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('conversations', 'participant1_id')) {
                $table->foreignId('participant1_id')->nullable()->after('created_by_user_id')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('conversations', 'participant2_id')) {
                $table->foreignId('participant2_id')->nullable()->after('participant1_id')->constrained('users')->onDelete('cascade');
            }
        });
    }
};
