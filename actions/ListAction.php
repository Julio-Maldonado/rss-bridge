<?php
/**
 * This file is part of RSS-Bridge, a PHP project capable of generating RSS and
 * Atom feeds for websites that don't have one.
 *
 * For the full license information, please view the UNLICENSE file distributed
 * with this source code.
 *
 * @package	Core
 * @license	http://unlicense.org/ UNLICENSE
 * @link	https://github.com/rss-bridge/rss-bridge
 */

class ListAction extends ActionAbstract {
	public function execute() {
		$list = new StdClass();
		$list->bridges = array();
		$list->total = 0;

		$bridgeFac = new \BridgeFactory();
		$bridgeFac->setWorkingDir(PATH_LIB_BRIDGES);

		foreach($bridgeFac->getBridgeNames() as $bridgeName) {

			$bridge = $bridgeFac->create($bridgeName);

			if($bridge === false) { // Broken bridge, show as inactive

				$list->bridges[$bridgeName] = array(
					'status' => 'inactive'
				);

				continue;

			}

			$status = $bridgeFac->isWhitelisted($bridgeName) ? 'active' : 'inactive';

			$list->bridges[$bridgeName] = array(
				'status' => $status,
				'uri' => $bridge->getURI(),
				'name' => $bridge->getName(),
				'icon' => $bridge->getIcon(),
				'parameters' => $bridge->getParameters(),
				'maintainer' => $bridge->getMaintainer(),
				'description' => $bridge->getDescription()
			);

		}

		$list->total = count($list->bridges);

		header('Content-Type: application/json');
		$origin = $_SERVER['HTTP_ORIGIN'];
		$allowed_domains = [
			'http://127.0.0.1:3000',
			'http://127.0.0.1:3001',
			'rgvcovid19cases.com'
		];

		if (in_array($origin, $allowed_domains)) {
			header('Access-Control-Allow-Origin: ' . $origin);
		}
		header('Access-Control-Allow-Origin: *');
		echo json_encode($list, JSON_PRETTY_PRINT);
	}
}
