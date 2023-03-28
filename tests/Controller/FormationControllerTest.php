<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of FormationControllerTest
 *
 * @author PC-Charles
 */
class FormationControllerTest extends WebTestCase
{
    private const FIRST_FORMATION = "Eclipse n°8 : Déploiement";
    
    public function getFormationPage() : string
    {
        return '/formations';
    }
    
    public function testAccesPage()
    {
        $client = static::createClient();
        $client->request('GET', $this->getFormationPage());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
    
    public function ClickThis(string $buttonId, string $expectedURI, array $expectedResult)
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getFormationPage());
        $button = $crawler->filter($buttonId)->first();
        $client->click($button->link());
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $uri = $client->getRequest()->server->get("REQUEST_URI");
        $this->assertEquals($expectedURI, $uri);
        $this->assertSelectorTextContains($expectedResult[0], $expectedResult[1]);
    }
    
    
    public function testSortTitleDesc()
    {
        $this->ClickThis(
            '#sortTitleDESC',
            '/formations/tri/title/DESC',
            array('h5', 'UML : Diagramme de paquetages')
        );
    }
    
    public function testSortTitleAsc()
    {
        $this->ClickThis(
            '#sortTitleASC',
            '/formations/tri/title/ASC',
            array('h5', 'Android Studio (complément n°1) : Navigation Drawer et Fragment')
        );
    }
    
    public function testSortPlaylistAsc()
    {
        $this->ClickThis(
            '#sortPlaylistASC',
            '/formations/tri/name/ASC/playlist',
            array('h5', 'Bases de la programmation n°74 - POO : collections')
        );
    }
    
    public function testSortPlaylistDesc()
    {
        $this->ClickThis(
            '#sortPlaylistDESC',
            '/formations/tri/name/DESC/playlist',
            array('h5', 'C# : ListBox en couleur')
        );
    }
    
    public function testSortDateAsc()
    {
        $this->ClickThis(
            '#sortDateASC',
            '/formations/tri/publishedAt/ASC',
            array('h5', "Cours UML (1 à 7 / 33) : introduction et cas d'utilisation")
        );
    }
    
    public function testSortDateDesc()
    {
        $this->ClickThis(
            '#sortDateDESC',
            '/formations/tri/publishedAt/DESC',
            array('h5', self::FIRST_FORMATION)
        );
    }
    
    public function testFiltreTitle()
    {
        $client = static::createClient();
        $client->request('GET', $this->getFormationPage());
        $crawler = $client->submitForm('filtrerTitle', [
           'recherche' => 'Eclipse'
        ]);
        $this->assertCount(9, $crawler->filter('h5'));
        $this->assertSelectorTextContains('h5', self::FIRST_FORMATION);
    }
    
    public function testFiltrePlaylist()
    {
        $client = static::createClient();
        $client->request('GET', $this->getFormationPage());
        $crawler = $client->submitForm('filtrerPlaylist', [
           'recherche' => 'Cours Curseurs'
        ]);
        $this->assertCount(2, $crawler->filter('h5'));
        $this->assertSelectorTextContains(
            'h5',
            'Cours Curseurs(5 à 8 / 8) : curseur historique et curseur dans le SGBDR'
        );
    }
    
    public function testFiltreCategories()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getFormationPage());

        $form = $crawler->filter('form#flitrerCatergories')->form();
        $form['recherche']->select(1);
        $client->submit($form);
        
        // Attente du rechargement de la page
        $timeout = 10;
        $interval = 200;
        $startTime = microtime(true);
        while (microtime(true) - $startTime < $timeout) {
            $crawler = $client->getCrawler();
            if ($crawler->filter('.formations-container')->count() > 0) {
                break;
            }
            usleep($interval * 1000);
        }

        $this->assertCount(15, $crawler->filter('h5'));
    }
    
    public function testShowOne()
    {
        $this->ClickThis(
            '#showOne',
            '/formations/formation/1',
            array('h4', self::FIRST_FORMATION)
        );
    }
}
