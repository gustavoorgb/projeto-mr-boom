<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250910025023 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE car_make (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE car_model (id INT AUTO_INCREMENT NOT NULL, car_make_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_83EF70E9C148837 (car_make_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE car_year (id INT AUTO_INCREMENT NOT NULL, car_model_id INT NOT NULL, year INT NOT NULL, INDEX IDX_70E8679BF64382E3 (car_model_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE car_model ADD CONSTRAINT FK_83EF70E9C148837 FOREIGN KEY (car_make_id) REFERENCES car_make (id)');
        $this->addSql('ALTER TABLE car_year ADD CONSTRAINT FK_70E8679BF64382E3 FOREIGN KEY (car_model_id) REFERENCES car_model (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE car_model DROP FOREIGN KEY FK_83EF70E9C148837');
        $this->addSql('ALTER TABLE car_year DROP FOREIGN KEY FK_70E8679BF64382E3');
        $this->addSql('DROP TABLE car_make');
        $this->addSql('DROP TABLE car_model');
        $this->addSql('DROP TABLE car_year');
    }
}
