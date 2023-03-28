<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of AccueilControllerTest
 *
 * @author PC-Charles
 */
class AccueilControllerTest extends WebTestCase
{
    /**
     * Teste l'accès a la page d'acceuil
     */
    public function testAccesPage()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
    
    /**
     * Teste l'accès a la page des Conditions Générales d'Utilisation
     */
    public function testAccesCgu()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $client->clickLink("Conditions Générales d'Utilisation");
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $uri = $client->getRequest()->server->get("REQUEST_URI");
        $this->assertEquals('/cgu', $uri);
    }
    
}
