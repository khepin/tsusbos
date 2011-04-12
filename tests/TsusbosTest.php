<?php

require_once __DIR__ . '/../vendor/Silex/silex.phar';

/**
 * Description of TsusbosTest
 *
 * @author sebastien.armand
 */
class TsusbosTest extends Silex\WebTestCase {

    public function createApp() {
        return require __DIR__ . '/../src/app.php';
    }

    public function testIndex() {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');
        $this->assertTrue($crawler->filter('html:contains("http://shortener.com/goog")')->count() > 0);
    }

    public function testAddFail() {
        $client = $this->createClient();
        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
        $client->request('GET', '/add/something');
    }

    public function testAdd() {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/add/my_key/goog?url=http://www.google.com');
        $this->assertTrue($crawler->filter('h1:contains("Congratulations")')->count() > 0);
        $this->assertTrue($crawler->filter('p:contains("http://www.google.com")')->count() > 0);
    }

    public function testUrlRedirect() {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/add/my_key/twitt?url=http://www.twitter.com');
        $this->assertTrue($crawler->filter('h1:contains("Congratulations")')->count() > 0);
        $this->assertTrue($crawler->filter('p:contains("http://www.twitter.com")')->count() > 0);
        $client->request('GET', '/twitt');
        $response = $client->getResponse();
        $this->assertTrue($response->isRedirect('http://www.twitter.com'));
    }

    public function testAddInvalidUrl(){
        $client = $this->createClient();
        $this->setExpectedException('Exception');
        $client->request('GET', '/add/my_key/short_name?url=http:/invalid.com');
    }

    public function testUniqueUrls(){
        $client = $this->createClient();
        $crawler = $client->request('GET', '/add/my_key/new_short?url=http://www.twitter.com');
        $this->setExpectedException('Exception');
        $crawler = $client->request('GET', '/add/my_key/new_short?url=http://www.facebook.com');
    }

    public function testList() {
        $client = $this->createClient();
        $crawler = $client->request('GET','/view/list');
        $this->assertTrue($crawler->filter('tr')->count() > 3);
        $this->assertTrue($crawler->filter('td:contains("http://www.twitter.com")')->count() > 1);
    }

    public function testInvalidKey(){
        $client = $this->createClient();
        $this->setExpectedException('Exception');
        $crawler = $client->request('GET', '/add/my_ky/new_short?url=http://www.twitter.com');
    }

}