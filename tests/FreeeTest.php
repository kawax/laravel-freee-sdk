<?php

namespace Revolution\Freee\Tests;

use Mockery as m;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

use Freee\Accounting\Configuration;
use Freee\Accounting\Api\AccountItemsApi;
use Freee\Accounting\Api\BanksApi;
use Freee\Accounting\Api\CompaniesApi;
use Freee\Accounting\Api\DealsApi;

use Revolution\Freee\Facades\Freee;
use Revolution\Freee\FreeeManager;
use Revolution\Freee\Contracts\Factory;
use Revolution\Freee\Contracts\Accounting;
use Revolution\Freee\Drivers\AccountingClient;

class FreeeTest extends TestCase
{
    public function testManagerInstance()
    {
        $client = new FreeeManager(app());

        $this->assertInstanceOf(FreeeManager::class, $client);
    }

    public function testManagerContainer()
    {
        $client = app(Factory::class);

        $this->assertInstanceOf(FreeeManager::class, $client);
    }

    public function testAccountingInstance()
    {
        $client = new AccountingClient([
            'client_id'     => '',
            'client_secret' => '',
        ]);

        $this->assertInstanceOf(AccountingClient::class, $client);
    }

    public function testAccountingContainer()
    {
        $client = app(Accounting::class);

        $this->assertInstanceOf(AccountingClient::class, $client);
    }

    public function testDriver()
    {
        $driver = Freee::driver('accounting');
        $default = Freee::driver();

        $this->assertInstanceOf(AccountingClient::class, $driver);
        $this->assertInstanceOf(AccountingClient::class, $default);
    }

    public function testConfig()
    {
        $config = Configuration::getDefaultConfiguration()->setAccessToken('test');

        $companiesApi = Freee::config($config)
                             ->setHeaderSelector(null)
                             ->setHostIndex(0)
                             ->companies();

        $this->assertInstanceOf(CompaniesApi::class, $companiesApi);
        $this->assertSame($config, $companiesApi->getConfig());
    }

    public function testMagicApis()
    {
        $this->assertInstanceOf(AccountItemsApi::class, Freee::accountItems());
        $this->assertInstanceOf(BanksApi::class, Freee::banks());
        $this->assertInstanceOf(CompaniesApi::class, Freee::companies());
        $this->assertInstanceOf(DealsApi::class, Freee::deals());
    }

    public function testHttpClient()
    {
        $http = Freee::httpClient();

        $this->assertInstanceOf(Client::class, $http);
    }

    public function testRefresh()
    {
        $mock = new MockHandler([
            new Response(200, [], '{ "refresh_token" : "test" }'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $res = Freee::setHttpClient($client)->refreshToken('token');

        $this->assertSame('test', $res['refresh_token']);
    }

    public function testTrait()
    {
        $client = (new User())->freee();

        $this->assertInstanceOf(AccountingClient::class, $client);
    }

    public function testTraitDriverNotSupported()
    {
        $this->expectException(\InvalidArgumentException::class);

        $client = (new User())->freee('test');
    }

    public function testCustomDriver()
    {
        Freee::extend('custom', function ($app) {
            return new AccountingClient([
                'client_id'     => '',
                'client_secret' => '',
            ]);
        });

        $driver = Freee::driver('custom');

        $this->assertInstanceOf(AccountingClient::class, $driver);
    }

    public function testMacro()
    {
        Freee::macro('test', function () {
            return 'test';
        });

        $test = Freee::test();

        $this->assertSame('test', $test);
    }

    public function testMacroException()
    {
        $this->expectException(\BadMethodCallException::class);

        Freee::fail();
    }
}
