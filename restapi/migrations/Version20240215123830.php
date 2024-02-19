<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240215123830 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial setup with user roles';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        if (!$schema->hasTable("user")) {
            $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');

            $this->addSql('CREATE TABLE "user" (
                id INT PRIMARY KEY DEFAULT NEXTVAL(\'user_id_seq\'),
                email VARCHAR(180) NOT NULL,
                roles JSONB NOT NULL,  -- Use JSONB instead of JSON for better performance
                password VARCHAR(255) NOT NULL
            )');

            $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');

            // Check if roles parameter is provided
            $roles = $this->connection->quote(json_encode($_SERVER['ROLES'] ?? ['ROLE_USER']));

            // Check if email and password parameters are provided
            $email = $this->connection->quote($_SERVER['EMAIL'] ?? 'admin@example.com');
            $password = password_hash($_SERVER['PASSWORD'] ?? 'password', PASSWORD_DEFAULT);

            // Insert initial user data
            $this->addSql('INSERT INTO "user" (email, roles, password) VALUES (:email, :roles::jsonb, :password)', [
                'email' => $email,
                'roles' => $roles,
                'password' => $password,
            ]);
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE IF EXISTS "user_id_seq" CASCADE');
        $this->addSql('DROP TABLE IF EXISTS "user"');
    }
}

