<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220411044309 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507B4584665A');
        $this->addSql('DROP INDEX IDX_2246507B4584665A ON basket');
        $this->addSql('ALTER TABLE basket CHANGE product_id product_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507BDE18E50B FOREIGN KEY (product_id_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_2246507BDE18E50B ON basket (product_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507BDE18E50B');
        $this->addSql('DROP INDEX IDX_2246507BDE18E50B ON basket');
        $this->addSql('ALTER TABLE basket CHANGE product_id_id product_id INT NOT NULL');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507B4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_2246507B4584665A ON basket (product_id)');
    }
}
