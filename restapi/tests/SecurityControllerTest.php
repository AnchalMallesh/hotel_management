<?php
namespace App\Tests\Controller;

use Symfony\Component\Panther\PantherTestCase;
use Symfony\Component\Panther\Client;

class SecurityControllerTest extends PantherTestCase
{
    protected function getConfiguredClient(): Client
    {
        $configuredClient = parent::createPantherClient();
        $configuredClient->setMaxRedirects(5); // Set max number of redirections
        $configuredClient->setServerParameters([
            'timeout' => 10.0, // Set custom timeout in seconds
        ]);
        return $configuredClient;
    }

    public function testLoginWithCorrectCredentials()
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/login');

        // Wait for the email input to be visible
        $client->waitFor('#email');

        // Fill in the login form fields
        $form = $crawler->filter('form')->form();
        $form['email'] = 'user@example.com';
        $form['password'] = 'password123';

        // Submit the form
        $client->submit($form);

        // Wait for the page to load after successful login
        $client->waitFor('#dashboard');

        // Check if the user is redirected after successful login
        $this->assertStringContainsString('/dashboard', $client->getCurrentURL());
    }

    public function testLoginWithIncorrectCredentials()
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/login');

        // Wait for the email input to be visible
        $client->waitFor('#email');

        // Fill in the login form fields with incorrect credentials
        $form = $crawler->filter('form')->form();
        $form['email'] = 'user@example.com';
        $form['password'] = 'wrongpassword';

        // Submit the form
        $client->submit($form);

        // Wait for the page to load after incorrect login
        $client->waitFor('.alert-danger');

        // Check if the login form is still displayed due to incorrect credentials
        $this->assertCount(1, $crawler->filter('.alert-danger'));
    }
}
