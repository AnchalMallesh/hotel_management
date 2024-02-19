<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240217063534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Insert initial user data
        $users = [
            [
                'email' => 'anchal@example.com',
                'roles' => json_encode(['ROLE_ADMIN']),
                'password' => password_hash('password', PASSWORD_DEFAULT),
            ],
            [
                'email' => 'user@example.com',
                'roles' => json_encode(['ROLE_USER']),
                'password' => password_hash('password123', PASSWORD_DEFAULT),
            ],
            // Add more users as needed
        ];

        foreach ($users as $userData) {
            $this->addSql('INSERT INTO login (email, roles, password) VALUES (:email, :roles, :password)', [
                'email' => $userData['email'],
                'roles' => $userData['roles'],
                'password' => $userData['password'],
            ]);
        }
    }
    
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        // $this->addSql('CREATE SCHEMA public');
        // $this->addSql('DROP INDEX UNIQ_AA08CB10E7927C74');
        // $this->addSql('ALTER TABLE customer ALTER name SET NOT NULL');
        // $this->addSql('ALTER TABLE customer ALTER email SET NOT NULL');
    }
}


