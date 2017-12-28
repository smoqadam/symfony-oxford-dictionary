<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171228110446 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE definition (id INT AUTO_INCREMENT NOT NULL, word_id INT DEFAULT NULL, definition VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_68302FD8E357438D (word_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE example (id INT AUTO_INCREMENT NOT NULL, defenition_id INT DEFAULT NULL, example VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_6EEC9B9FCB22499F (defenition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE word (id INT AUTO_INCREMENT NOT NULL, word VARCHAR(255) NOT NULL, saved_from VARCHAR(255) NOT NULL, source VARCHAR(255) NOT NULL, pronunciation VARCHAR(255) NOT NULL, parts_of_speech VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE definition ADD CONSTRAINT FK_68302FD8E357438D FOREIGN KEY (word_id) REFERENCES word (id)');
        $this->addSql('ALTER TABLE example ADD CONSTRAINT FK_6EEC9B9FCB22499F FOREIGN KEY (defenition_id) REFERENCES definition (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE example DROP FOREIGN KEY FK_6EEC9B9FCB22499F');
        $this->addSql('ALTER TABLE definition DROP FOREIGN KEY FK_68302FD8E357438D');
        $this->addSql('DROP TABLE definition');
        $this->addSql('DROP TABLE example');
        $this->addSql('DROP TABLE word');
    }
}
