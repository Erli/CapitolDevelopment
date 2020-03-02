<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;
		$router->addRoute('funkce/<action>', 'Functions:');
		$router->addRoute('zamestnanec/<action>', 'Employee:');
        $router->addRoute('ma-funkci', 'Homepage:hasFunction');
        $router->addRoute('nema-funkci', 'Homepage:hasNotFunction');
        $router->addRoute('<presenter>/<action>', 'Homepage:default');
		return $router;
	}
}
