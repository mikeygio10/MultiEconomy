[![](https://poggit.pmmp.io/shield.state/MultiEconomy)](https://poggit.pmmp.io/p/MultiEconomy) [![](https://poggit.pmmp.io/shield.api/MultiEconomy)](https://poggit.pmmp.io/p/MultiEconomy) [![](https://poggit.pmmp.io/shield.dl.total/MultiEconomy)](https://poggit.pmmp.io/p/MultiEconomy)
[![](https://poggit.pmmp.io/shield.dl/MultiEconomy)](https://poggit.pmmp.io/p/MultiEconomy)
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
| /addtobalance \<player> \<currency> \<amount> | /addtobal | Add to a players balance | multieconomy.addtobalance |
| /balance [player/currency] [currency]  | /bal | Check yours or another players balance | None |
| /balancetop [currency] [page]  | /baltop | Show the top balances for a currency | None |
| /pay \<player> \<currency> \<amount>  | None | Pay another player money for a currency | None |
| /removefrombalance \<player> \<currency> \<amount> | /remfrombal | Remove from a players balance | multieconomy.removefrombalance |
| /setbalance \<player> \<currency> \<balance> | /setbal | Set a player's balance for a currency | multieconomy.setbalance |
 
## Languages
MultiEconomy has a language system, the list below are implemented languages and their 3 digit code
 - English: ``eng``
> Create a Pull Request with the language name, and a proper translation of ``eng.yml`` to request another language
 
## API
MultiEconomy has a built in API, so you can interrogate MultiEconomy with your own plugins
To get the API class, you have to use ``PluginManager::getPlugin()`` to get an instance of MultiEconomy's main class.
```php
/** @var PluginBase $this */
$plugin = $this->getServer()->getPluginManager()->getPlugin("MultiEconomy");
```
Now you have an instance of the main class, you can use the ``getAPI()`` function
```php
$api = $plugin->getAPI();
```  
To create a new currency in order to register it, you can do the following:
```php
$name = "Dollars"; // Currency name
$symbol = "$"; // Symbol for currency amounts 
$symbolAfter = false; // Wether the symbol comes before or after the amount
$startingAmount = 0; // The amount a player starts on when they first get a balance
$minAmount = 0; // Minimum amount a player can have in the currency
$maxAmount = 999999; // Maximum amount a player can have in the currency
$currency = new Twisted\MultiEconomy\Currency($name, $symbol, $symbolAfter, $startingAmount, $minAmount, $maxAmount);
```
### Functions
| Function | Parameters | Return Type | Description |
| ---------- | ---------- | ---------- | ---------- |
| registerCurrency | Twisted\MultiEconomy\Currency $currency | \Boolean | Register a currency to the plugin, returns true/false on success/failure |
| getLangData | \string $language | pocketmine\config\Config | Get config file for a certain language |
| getMessage | \string $key, \array $values | Get a translated message to servers language | \string |
| getCurrencies | None | Twisted\MultiEconomy\Currency[] | Get all the registered currencies |
| getCurrencyNames | None | \string[] | Get all the registered currencies' names |
| checkBalance | \string $player, \string $currency | \void | Sets a players balance to the starting amount for given currency if they don't have a balance |
| addToBalance | \string $player, \string $currency, \int $amount | \void | Adds to a players balance for a currency |
| getBalance | \string $player, \string $currency | \null \int | Returns the players balance for a currency, null if they don't have a balance |
| getBalances | \string $currency | pocketmine\config\Config | Returns a config with all balances for the currency |
| setBalance | \string $player, \string $currency, \int $amount | \void | Set a players balance for a currency |
| takeFromBalance | \string $player, \string $currency, \string $amount | \void | Takes from a players balance for a currency |

