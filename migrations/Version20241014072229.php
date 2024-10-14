<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241014072229 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fruits CHANGE quantity_amount quantity_amount INT NOT NULL');
        $this->addSql('ALTER TABLE vegetables CHANGE quantity_amount quantity_amount INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fruits CHANGE quantity_amount quantity_amount NUMERIC(8, 3) NOT NULL');
        $this->addSql('ALTER TABLE vegetables CHANGE quantity_amount quantity_amount NUMERIC(8, 3) NOT NULL');
    }
}
