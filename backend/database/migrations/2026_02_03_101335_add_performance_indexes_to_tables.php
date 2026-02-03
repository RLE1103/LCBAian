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
        // Users table indexes
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                // Composite index for filtering alumni (only if columns exist)
                if (Schema::hasColumn('users', 'role') && Schema::hasColumn('users', 'is_verified')) {
                    if (!$this->indexExists('users', 'users_role_is_verified_index')) {
                        $table->index(['role', 'is_verified'], 'users_role_is_verified_index');
                    }
                }
                
                // Index for program and batch filtering
                if (Schema::hasColumn('users', 'program') && Schema::hasColumn('users', 'batch')) {
                    if (!$this->indexExists('users', 'users_program_batch_index')) {
                        $table->index(['program', 'batch'], 'users_program_batch_index');
                    }
                }
                
                // Index for created_at (for recent registrations)
                if (Schema::hasColumn('users', 'created_at')) {
                    if (!$this->indexExists('users', 'users_created_at_index')) {
                        $table->index('created_at');
                    }
                }
            });
        }

        // Job posts table indexes
        if (Schema::hasTable('job_posts')) {
            Schema::table('job_posts', function (Blueprint $table) {
                // Index for status
                if (Schema::hasColumn('job_posts', 'status')) {
                    if (!$this->indexExists('job_posts', 'job_posts_status_index')) {
                        $table->index('status');
                    }
                }
                
                // Index for created_at (for sorting by date)
                if (Schema::hasColumn('job_posts', 'created_at')) {
                    if (!$this->indexExists('job_posts', 'job_posts_created_at_index')) {
                        $table->index('created_at');
                    }
                }
            });
        }

        // Events table indexes
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (Schema::hasColumn('events', 'created_at')) {
                    if (!$this->indexExists('events', 'events_created_at_index')) {
                        $table->index('created_at');
                    }
                }
            });
        }

        // Posts table indexes
        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                if (Schema::hasColumn('posts', 'created_at')) {
                    if (!$this->indexExists('posts', 'posts_created_at_index')) {
                        $table->index('created_at');
                    }
                }
            });
        }

        // Reports table indexes
        if (Schema::hasTable('reports')) {
            Schema::table('reports', function (Blueprint $table) {
                if (Schema::hasColumn('reports', 'created_at')) {
                    if (!$this->indexExists('reports', 'reports_created_at_index')) {
                        $table->index('created_at');
                    }
                }
            });
        }

        // Messages table indexes
        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                if (Schema::hasColumn('messages', 'created_at')) {
                    if (!$this->indexExists('messages', 'messages_created_at_index')) {
                        $table->index('created_at');
                    }
                }
            });
        }

        // Applications table indexes
        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
                // Index for status filtering (only if column exists)
                if (Schema::hasColumn('applications', 'status')) {
                    if (!$this->indexExists('applications', 'applications_status_index')) {
                        $table->index('status');
                    }
                }
                
                // Index for created_at (only if column exists)
                if (Schema::hasColumn('applications', 'created_at')) {
                    if (!$this->indexExists('applications', 'applications_created_at_index')) {
                        $table->index('created_at');
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Users table
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if ($this->indexExists('users', 'users_role_is_verified_index')) $table->dropIndex('users_role_is_verified_index');
                if ($this->indexExists('users', 'users_program_batch_index')) $table->dropIndex('users_program_batch_index');
                if ($this->indexExists('users', 'users_created_at_index')) $table->dropIndex('users_created_at_index');
            });
        }

        // Job posts table
        if (Schema::hasTable('job_posts')) {
            Schema::table('job_posts', function (Blueprint $table) {
                if ($this->indexExists('job_posts', 'job_posts_status_index')) $table->dropIndex('job_posts_status_index');
                if ($this->indexExists('job_posts', 'job_posts_created_at_index')) $table->dropIndex('job_posts_created_at_index');
            });
        }

        // Events table
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if ($this->indexExists('events', 'events_created_at_index')) $table->dropIndex('events_created_at_index');
            });
        }

        // Posts table
        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                if ($this->indexExists('posts', 'posts_created_at_index')) $table->dropIndex('posts_created_at_index');
            });
        }

        // Reports table
        if (Schema::hasTable('reports')) {
            Schema::table('reports', function (Blueprint $table) {
                if ($this->indexExists('reports', 'reports_created_at_index')) $table->dropIndex('reports_created_at_index');
            });
        }

        // Messages table
        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                if ($this->indexExists('messages', 'messages_created_at_index')) $table->dropIndex('messages_created_at_index');
            });
        }

        // Applications table
        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
                if ($this->indexExists('applications', 'applications_status_index')) $table->dropIndex('applications_status_index');
                if ($this->indexExists('applications', 'applications_created_at_index')) $table->dropIndex('applications_created_at_index');
            });
        }
    }

    /**
     * Check if index exists
     */
    private function indexExists(string $table, string $index): bool
    {
        $indexes = Schema::getIndexes($table);
        foreach ($indexes as $indexData) {
            if ($indexData['name'] === $index) {
                return true;
            }
        }
        return false;
    }
};
