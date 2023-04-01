<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration to create the transactions table
 */
final class Version20230401100401 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create the transactions table to track point balance transactions';
    }
    public function up(Schema $schema): void
    {
        $table = $schema->createTable('transactions');

        $table->addColumn('id', 'bigint', ['unsigned' => true, 'autoincrement' => true]);
        $table->addColumn('user_id', 'bigint', ['unsigned' => true]);
        $table->addColumn('description', 'string', ['length' => 255]);
        $table->addColumn('point_change', 'integer');

        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint($schema->getTable('users'), ['user_id'], ['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('users');
    }
}
