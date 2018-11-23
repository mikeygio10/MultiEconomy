# MultiEconomy
**MultiEconomy is an Economy system with multiple currencies!**

## Installation
 - You can get the compiled `.phar` in releases by clicking [here](https://github.com/TwistedAsylumMC/MultiEconomy/releases)
 - Add the `.phar` to your `/plugins` directory & restart your server.
 > Note: This plugin has been tested on PocketMine-MP 3.2.0. Additional support for forks will not happen
 
## Commands
MultiEconomy is still a work-in-progress but it has a few commands!

| Command | Aliases | Description | Permission |
| ---------- | ---------- | ---------- | ---------- |
| /balance [player/currency]  | /bal | Check yours or another players balance  | None |
 
## Languages
MultiEconomy has a language system, the list below are implemented languages and their 3 digit code
 - English: ``eng``
> Create a Pull Request with the language name, and a proper translation of ``eng.yml`` to request another language
 
## API
MultiEconomy has a built in API, so you can interrogate MultiEconomy with your own plugins
To get the API class, you have to use ``PluginManager::getPlugin()`` to get an instance of MultiEconomy's main class.
```php
$plugin = pocketmine\Server::getInstance()->getPluginManager()->getPlugin("MultiEconomy");
```
Now you have an instance of the main class, you can use the ``getAPI()`` function
```php
$api = $plugin->getAPI();
```  
### Functions
| Function | Parameters | Return Type | Description |
| ---------- | ---------- | ---------- | ---------- |
| getLangData() | string $language | pocketmine\utils\Config | Get the language file for a language |
| getMessage() | string $key, string $player = "", string $currency = "" | string | Get a message from the configured language with correct variables |
| getCurrencies() | None | array | Get an array of all currency names |
| getCurrencyData() | string $currency | array | Information of the currency (Symbol etc.) |
| addToBalance() | string $player, string $currency, int $amount | void | Add money to a player's specific currency |
| getBalance() | string $player, string $currency | int | Get a player's balance for a specific currency |
| getBalances() | string $currency | array | Returns all balances for the specific currency |
| setBalance() | string $player, string $currency, int $balance | void | Set a player's balance for a specific currency |
| takeFromBalance() | string $player, string $currency, int $amount | void | Take money from a player's specific currency |
