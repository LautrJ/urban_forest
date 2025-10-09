<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251009144506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tree_type RENAME COLUMN carbon_growth_coefficient TO carbon_growth_k');
        $this->addSql('ALTER TABLE tree_type RENAME COLUMN cool_zone_growth_coefficient TO cool_zone_growth_k');

        $this->addSql('ALTER TABLE tree_type ALTER COLUMN carbon_growth_k TYPE INT');
        $this->addSql('ALTER TABLE tree_type ALTER COLUMN cool_zone_growth_k TYPE INT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tree_type ALTER COLUMN carbon_growth_k TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE tree_type ALTER COLUMN cool_zone_growth_k TYPE DOUBLE PRECISION');

        $this->addSql('ALTER TABLE tree_type RENAME COLUMN carbon_growth_k TO carbon_growth_coefficient');
        $this->addSql('ALTER TABLE tree_type RENAME COLUMN cool_zone_growth_k TO cool_zone_growth_coefficient');
    }
}
