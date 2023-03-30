<?php

declare(strict_types=1);

namespace MyProject\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Creates the users table
 */
final class Version20230330075617 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates the Users table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('users');

        $table->addColumn('id', 'bigint', ['unsigned' => true, 'autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('email', 'string', ['length' => 255]);
        $table->addColumn('points_balance', 'integer');

        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('users');
    }
}
