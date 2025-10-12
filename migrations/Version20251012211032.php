<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251012211032 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tree_request DROP COLUMN needs_appointment');

        $this->addSql('ALTER TABLE tree_request ADD requested_appointment_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');

        $this->addSql('UPDATE tree_request SET requested_appointment_date = NOW() + INTERVAL \'1 day\' WHERE requested_appointment_date IS NULL');

        $this->addSql('ALTER TABLE tree_request ALTER COLUMN requested_appointment_date SET NOT NULL');

        $this->addSql('ALTER TABLE appointment DROP COLUMN proposed_date');
        $this->addSql('ALTER TABLE appointment DROP COLUMN confirmed_date');
        $this->addSql('ALTER TABLE appointment ADD appointment_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tree_request DROP COLUMN requested_appointment_date');
        $this->addSql('ALTER TABLE tree_request ADD needs_appointment BOOLEAN NOT NULL DEFAULT false');

        $this->addSql('ALTER TABLE appointment DROP COLUMN appointment_date');
        $this->addSql('ALTER TABLE appointment ADD proposed_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE appointment ADD confirmed_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }
}
