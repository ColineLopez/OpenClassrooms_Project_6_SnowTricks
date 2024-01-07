<?php

// tests/Controller/ResetPasswordControllerTest.php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ResetPasswordControllerTest extends WebTestCase
{
    //#[Route('/test', name: 'app_test')]
    public function testResetPasswordEmail()
    {
        $client = static::createClient();

        // Simule une demande de réinitialisation du mot de passe (par exemple, en accédant à une route)
        $client->request('GET', '/reset-password');

        // Vérifie que la page de réinitialisation du mot de passe est accessible
        $this->assertResponseIsSuccessful();

        // Vérifie si l'e-mail a été envoyé
        $mailCollector = $client->getProfile()->getCollector('mailer');
        $this->assertCount(1, $mailCollector->getEvents()->getMessages());

        // Récupère le dernier e-mail envoyé
        $messages = $mailCollector->getEvents()->getMessages();
        $message = end($messages);

        // Vérifie le contenu de l'e-mail
        $this->assertInstanceOf(\Symfony\Component\Mime\Email::class, $message);
        $this->assertSame('Réinitialisation du mot de passe', $message->getSubject());
        $this->assertSame('from@example.com', $message->getFrom()[0]->getAddress());
        $this->assertSame('to@example.com', $message->getTo()[0]->getAddress());
        $this->assertStringContainsString('Pour réinitialiser votre mot de passe', $message->getTextBody());
    }
}
