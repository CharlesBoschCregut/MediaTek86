<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of PlaylistControllerTest
 *
 * @author PC-Charles
 */
class PlaylistControllerTest extends WebTestCase
{
    
    private const FIRST_PLAYLIST = "Bases de la programmation (C#)";
    
    //Test si toutes les playlists sont la
    public function checkNbPlaylists($playlists)
    {
        $nbplaylists = 27;
        $this->assertCount($nbplaylists, $playlists);
    }
    
    public function getPlaylistPage() : string
    {
        return "/playlists";
    }
    
    public function ClickThis(string $buttonId, string $expectedURI, array $expectedResult)
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getPlaylistPage());
        $button = $crawler->filter($buttonId)->first();
        $client->click($button->link());
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $uri = $client->getRequest()->server->get("REQUEST_URI");
        $this->assertEquals($expectedURI, $uri);
        $this->assertSelectorTextContains($expectedResult[0], $expectedResult[1]);
    }
    
    public function testAccesPage()
    {
        $client = static::createClient();
        $client->request('GET', $this->getPlaylistPage());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
    
    public function testSortNameAsc()
    {
        $this->ClickThis(
            '#sortNameASC',
            '/playlists/tri/name/ASC',
            array('h5', self::FIRST_PLAYLIST)
        );
    }
    
    public function testSortNameDesc()
    {
        $this->ClickThis(
            '#sortNameDESC',
            '/playlists/tri/name/DESC',
            array('h5', 'Visual Studio 2019 et C#')
        );
    }
    
    public function testSortNbFormationAsc()
    {
        $this->ClickThis(
            '#sortNbformationASC',
            '/playlists/tri/nbformation/ASC',
            array('h5', 'Cours Informatique embarquée')
        );
    }
    
    public function testSortNbFormationDesc()
    {
        $this->ClickThis(
            '#sortNbformationDESC',
            '/playlists/tri/nbformation/DESC',
            array('h5', self::FIRST_PLAYLIST)
        );
    }
    
    public function testNameFliter()
    {
        $client = static::createClient();
        $client->request('GET', $this->getPlaylistPage());
        $crawler = $client->submitForm('filtrerName', [
           'recherche' => 'visual'
        ]);
        $this->assertCount(1, $crawler->filter('h5'));
        $this->assertSelectorTextContains('h5', 'Visual Studio 2019 et C#');
    }
    
    public function testFiltreCategories()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $this->getPlaylistPage());

        $form = $crawler->filter('form#flitrerCatergories')->form();
        $form['recherche']->select(1);
        $client->submit($form);
        
        // Attente du rechargement de la page
        $timeout = 10;
        $interval = 200;
        $startTime = microtime(true);
        while (microtime(true) - $startTime < $timeout) {
            $crawler = $client->getCrawler();
            if ($crawler->filter('.playlists-container')->count() > 0) {
                break;
            }
            usleep($interval * 1000);
        }

        $this->assertCount(3, $crawler->filter('h5'));
    }
    
    public function testShowOne()
    {
        $this->ClickThis(
            '#showOne',
            '/playlists/playlist/13',
            array('h4', 'Bases de la programmation (C#)')
        );
    }
}
