# Freee SDK for Laravel

[![Build Status](https://travis-ci.com/kawax/laravel-freee-sdk.svg?branch=master)](https://travis-ci.com/kawax/laravel-freee-sdk)
[![Maintainability](https://api.codeclimate.com/v1/badges/04a09c18b2f041394f74/maintainability)](https://codeclimate.com/github/kawax/laravel-freee-sdk/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/04a09c18b2f041394f74/test_coverage)](https://codeclimate.com/github/kawax/laravel-freee-sdk/test_coverage)

`freee-accounting-sdk-php`をLaravelから使いやすくするパッケージ。  
https://github.com/freee/freee-accounting-sdk-php

accounting以外のSDKが登場した場合はこのパッケージのまま対応するかもしれません。

## Requirements
- PHP >= 7.2
- Laravel >= 6.0

## Installation
```
composer require revolution/laravel-freee-sdk
```

### config/services.php
`freee/socialite-freee-accounting`と同じなのでそのまま流用可能。

```
    'freee-accounting' => [
        'client_id' => env('FREEE_ACCOUNTING_CLIENT_ID'),
        'client_secret' => env('FREEE_ACCOUNTING_CLIENT_SECRET'),
    ],
```

### .env
```
FREEE_ACCOUNTING_CLIENT_ID=
FREEE_ACCOUNTING_CLIENT_SECRET=
```

## 使い方
Facade・Trait・Macroを使う方法とそれぞれテスト時の注意があるのでよく確認してください。

### Facade
```php
use Freee\Accounting\Configuration;
use Revolution\Freee\Facades\Freee;

class Controller
{
    public function meFacade(Request $request)
    {
        $user = $request->user();

        // Freee::driver()
        // Freee::driver('accounting')
        // デフォルトはaccountingなのでどちらでも同じ。
        // driver()を省略しても同じ。
        // 現在はaccounting SDKのみ対応なのでFreee=AccountingClientのインスタンスの認識でいいです。
        // 今後SDKが増えたら変わる。

        // 以降の使い方はaccountingが前提。

        // 普通の使い方と同じように$configを作ってセット。
        $config = Configuration::getDefaultConfiguration()->setAccessToken($user->token);
        // Freee::config($config)->companies()と繋げることも可能
        Freee::config($config);

        // companies()はCompaniesApiクラスのインスタンスを返すマジックメソッド。
        // 先頭のみ小文字、最後のApiを除いたメソッド名がルール。TrialBalanceApiならtrialBalance()
        // getCompanies()はCompaniesApiクラスのメソッド。後の使い方は同じ。
        // Responseは(string)でjsonに変換されるのでdecodeしてarrayで取得。Laravelならこの形のほうが扱いやすい。
        $companiesResponse = json_decode((string) Freee::companies()->getCompanies(), true);
        $targetCompanyId = $companiesResponse['companies'][0]['id'];

        // deals()はDealsApi。変換せずにそのままviewへ。null多すぎの解決方法はmacroで。
        $limit = 5;
        $deals = Freee::deals()->getDeals(
            $targetCompanyId,
            null, null, null, null, null, null, null, null, null, null, null, null,
            $limit)->getDeals();

        return view('account.me', compact('user', 'deals'));
    }
}
```

短いFacadeは自動では登録されないので必要なら手動で`config/app.php`にて登録してください。

```php
'Freee' => Revolution\Freee\Facades\Freee::class,
```

### Facadeのテスト

```php
    public function testMeFacade()
    {
        Freee::shouldReceive('config');

        // shouldReceiveですべてモックするのでAPIは実行されない。
        Freee::shouldReceive('companies->getCompanies')->andReturn(json_encode([
            'companies' => [
                [
                    'id' => 1,
                    'role' => 'admin',
                ]
            ]
        ]));

        Freee::shouldReceive('deals->getDeals->getDeals')->andReturn([]);

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->get(route('me_facade'));

        $response->assertStatus(200)
            ->assertViewHas(['user', 'deals'])
            ->assertSeeText($user->email);
    }
```

### Trait
「traitで機能の有効化」で一段とLaravelらしくなる。

Userモデルに`FreeeSDK`traitを追加。`tokenForFreee()`でtokenを返す。

```php
use Revolution\Freee\Traits\FreeeSDK;

class User
{
    use FreeeSDK;

    /**
     * @param  string  $driver
     * 
     * @return string
     */
    protected function tokenForFreee(string $driver): string
    {
        return $this->token ?? '';
    }
}
```

```php
    public function meTrait(Request $request)
    {
        $user = $request->user();

        // $user->freee()はconfigをセット済のAccountingClientなので後は同じ。freee()もaccountingがデフォルト。
        // 他のSDKにも対応したら$user->freee('custom')->で使用。
        $companiesResponse = json_decode($user->freee()->companies()->getCompanies(), true);
        $targetCompanyId = $companiesResponse['companies'][0]['id'];
        
        $limit = 5;
        $deals = $user->freee()->deals()->getDeals(
            $targetCompanyId,
            null, null, null, null, null, null, null, null, null, null, null, null,
            $limit)->getDeals();

        return view('account.me', compact('user', 'deals'));
    }
```

### Traitのテスト

```php
    public function testMeTrait()
    {
        // freee()内では毎回driver()->config()が実行されているのでモック時にも必要
        Freee::shouldReceive('driver->config->companies->getCompanies')->andReturn(json_encode([
            'companies' => [
                [
                    'id' => 1,
                    'role' => 'admin',
                ]
            ]
        ]));

        Freee::shouldReceive('driver->config->deals->getDeals->getDeals')->andReturn([]);

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->get(route('me_trait'));

        $response->assertStatus(200)
            ->assertViewHas(['user', 'deals'])
            ->assertSeeText($user->email);
    }
```

### Macro
Laravelのmacroと同じなのでどんなメソッドでも追加可能。
`AppServiceProvider@boot`などで定義。

```php
use Revolution\Freee\Facades\Freee;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // AccountingClientへのmacroの追加なことに注意。
        // 他のSDKにも対応した場合はFreee::driver('')->macro()
        Freee::macro('getCompanyId', function ($index = 0) {
            $companiesApi = $this->companies();
            $companiesResponse = $companiesApi->getCompanies();
            return $companiesResponse->getCompanies()[$index]->getId();
        });

        Freee::driver()->macro('getDeals', function ($company_id, $limit = 5) {
            $dealsApi = $this->deals();
            $dealsResponse = $dealsApi->getDeals(
                $company_id,
                null, null, null, null, null, null, null, null, null, null, null, null,
                $limit);
            return $dealsResponse->getDeals();
        });
    }
}
```

```php
    public function meMacro(Request $request)
    {
        $user = $request->user();

        // 長いコードをmacroに追い出したのでこれだけで済む
        $company_id = $user->freee()->getCompanyId();

        $deals = $user->freee()->getDeals($company_id);

        return view('account.me', compact('user', 'deals'));
    }
```

### Macroのテスト

```php
    public function testMeMacro()
    {
        // freee()なのでdriver->configは必要だけどその後はmacroのメソッドのみ
        Freee::shouldReceive('driver->config->getCompanyId')->andReturn(0);
        Freee::shouldReceive('driver->config->getDeals')->andReturn([]);

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->get(route('me_macro'));

        $response->assertStatus(200)
            ->assertSeeText($user->email);
    }
```

### refreshToken
access tokenの有効期限は24時間。`Freee::refreshToken()`で更新。

```php
    public function refresh(Request $request)
    {
        $response = Freee::refreshToken($request->user()->refresh_token);

        $request->user()->fill([
            'token' => $response['access_token'] ?? '',
            'refresh_token' => $response['refresh_token'] ?? '',
            'expired_at' => now()->addSeconds($response['expires_in']),
        ])->save();

        return back();
    }
```

### Laravel外で使う

```php
use Freee\Accounting\Configuration;
use Revolution\Freee\Drivers\AccountingClient;

$freee = new AccountingClient(['client_id' => '', 'client_secret' => '']);
$config = Configuration::getDefaultConfiguration()->setAccessToken('token');
$freee->config($config);
$companiesResponse = json_decode($freee->companies()->getCompanies(), true);
```

## LICENCE
MIT
