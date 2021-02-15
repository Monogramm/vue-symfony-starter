<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201222154307 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add LDAP default role parameter';
    }

    public function up(Schema $schema) : void
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');
        $this->addSql('INSERT INTO parameter(id, name, value, type, description, created_at, updated_at) VALUES (\''.Uuid::uuid4().'\',\'LDAP_USER_DEFAULT_ROLE\',\'ROLE_ADMIN\', \'string\', \'LDAP default role on first login. Valid values are: "ROLE_ADMIN" and no value for "ROLE_USER".\', date(\'now\'), date(\'now\') )');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DELETE FROM parameter WHERE name = \'LDAP_USER_DEFAULT_ROLE\'');
    }
}
