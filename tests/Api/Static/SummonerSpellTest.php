<?php

use LeagueWrap\Api;
use Mockery as m;

class StaticSummonerSpellTest extends PHPUnit_Framework_TestCase
{
    protected $client;

    public function setUp()
    {
        $client = m::mock('LeagueWrap\Client');
        $this->client = $client;
    }

    public function tearDown()
    {
        m::close();
    }

    public function testGetSummonerSpellDefault()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/static-data/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/summoner-spells', [
                        'api_key'  => 'key',
                        'dataById' => 'true',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/Static/summonerspell.json'));

        $api = new Api('key', $this->client);
        $spells = $api->staticData()->getSummonerSpells();
        $spell = $spells->getSpell(12);
        $this->assertEquals('Teleport', $spell->name);
    }

    public function testArrayAccess()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/static-data/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/summoner-spells', [
                        'api_key'  => 'key',
                        'dataById' => 'true',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/Static/summonerspell.json'));

        $api = new Api('key', $this->client);
        $spells = $api->staticData()->getSummonerSpells();
        $this->assertEquals('Teleport', $spells[12]->name);
    }

    public function testGetSummonerSpellRegionTR()
    {
        $this->client->shouldReceive('baseUrl')->with('https://tr1.api.riotgames.com/lol/static-data/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/summoner-spells', [
                        'api_key'  => 'key',
                        'dataById' => 'true',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/Static/summonerspell.tr.json'));

        $api = new Api('key', $this->client);
        $spells = $api->setRegion('tr')
                      ->staticData()->getSummonerSpells();
        $spell = $spells->getSpell(6);
        $this->assertEquals('Hayalet', $spell->name);
        $this->assertEquals("SummonerHaste", $spell->key);
    }

    public function testGetSummonerSpellById()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/static-data/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/summoner-spells/1', [
                        'api_key'  => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/Static/summonerspell.1.json'));

        $api = new Api('key', $this->client);
        $spell = $api->staticData()->getSummonerSpell(1);
        $this->assertEquals('6', $spell->summonerLevel);
    }

    public function testGetSummonerSpellAll()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/static-data/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/summoner-spells', [
                        'api_key'   => 'key',
                        'dataById'  => 'true',
                        'tags' => 'all',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/Static/summonerspell.all.json'));

        $api = new Api('key', $this->client);
        $spells = $api->staticData()->getSummonerSpells('all');
        $spell = $spells->getSpell(7);
        $this->assertEquals('f1', $spell->vars[0]->key);
    }
}
