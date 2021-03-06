<?php

namespace Sea\Components\Routing;

use Sea\Components\Request;

/**
 * Test class for Router.
 * Generated by PHPUnit on 2011-07-14 at 21:56:19.
 */
class RouterTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Router
     */
    protected $router;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
		$rules = $this->getRules();
        $this->router = new Router(new RouteMatcher($rules), new RouteExtractor($rules));
    }
	
	public function testGetControllerDataFromRoute()
	{
		$route = new Route('', 'admin.example.com', '/users/');
		$controllerData = array('administration', 'users', array(''));
		$this->assertEquals($controllerData, $this->router->getControllerDataFrom($route));
	}
	
	public function testGetControllerDataIfRouteMatches()
	{
		$route = new Route('', 'admin.example.com', '/user-hector0193');
		$controllerData = array('users', 'show', array('hector0193'));
		$this->assertEquals($controllerData, $this->router->getControllerDataFrom($route));
	}
	
	private function getRules()
	{
		return array(
			'www'	=>	array(
				'controller'	=>	'index'
			),
			
			'admin'	=>	array(
				'static_controller'	=>	'administration',
				'routes'	=>	array(
					'^user-([a-z0-9]+)$'	=>	array('users', 'show')
				)
			)
		);
	}

}

?>
