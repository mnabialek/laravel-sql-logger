<?php

namespace Mnabialek\LaravelSqlLogger\Tests;

use Illuminate\Container\Container;
use Illuminate\Database\DatabaseManager;
use Mnabialek\LaravelSqlLogger\Config;
use Mnabialek\LaravelSqlLogger\Providers\ServiceProvider;
use Mnabialek\LaravelSqlLogger\SqlLogger;
use Mockery;

class ServiceProviderTest extends UnitTestCase
{
    /** @test */
    public function it_merges_config_and_publishes_when_nothing_should_be_logged()
    {
        $app = Mockery::mock(Container::class, \ArrayAccess::class);
        Container::setInstance($app);
        $config = Mockery::mock(Config::class);

        $app->shouldReceive('make')->once()->with(Config::class)->andReturn($config);

        $provider = Mockery::mock(ServiceProvider::class)->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $provider->__construct($app);

        $baseDir = '/some/sample/directory';

        $app->shouldReceive('make')->atLeast()->once()
            ->with('path.config')->andReturn($baseDir);

        $configFile = realpath(__DIR__ . '/../publish/config/sql_logger.php');
        $provider->shouldReceive('mergeConfigFrom')->once()->with(
            $configFile,
            'sql_logger'
        );

        $provider->shouldReceive('publishes')->once()->with(
            [$configFile => config_path('sql_logger.php')]
        );

        $config->shouldReceive('logAllQueries')->once()->withNoArgs()->andReturn(false);
        $config->shouldReceive('logSlowQueries')->once()->withNoArgs()->andReturn(false);

        $app->shouldNotReceive('make')->with(SqlLogger::class);

        $provider->register();
        $this->assertTrue(true);
    }

    /** @test */
    public function it_starts_listening_to_queries_when_normal_queries_should_be_logged()
    {
        $app = Mockery::mock(Container::class, \ArrayAccess::class);
        Container::setInstance($app);
        $config = Mockery::mock(Config::class);

        $app->shouldReceive('make')->once()->with(Config::class)->andReturn($config);

        $provider = Mockery::mock(ServiceProvider::class)->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $provider->__construct($app);

        $baseDir = '/some/sample/directory';

        $app->shouldReceive('make')->atLeast()->once()
            ->with('path.config')->andReturn($baseDir);

        $configFile = realpath(__DIR__ . '/../publish/config/sql_logger.php');
        $provider->shouldReceive('mergeConfigFrom')->once()->with(
            $configFile,
            'sql_logger'
        );

        $provider->shouldReceive('publishes')->once()->with(
            [$configFile => config_path('sql_logger.php')]
        );

        $config->shouldReceive('logAllQueries')->once()->withNoArgs()->andReturn(true);

        $logger = Mockery::mock(SqlLogger::class);
        $app->shouldReceive('make')->with(SqlLogger::class)->andReturn($logger);

        $db = Mockery::mock(DatabaseManager::class)->makePartial();
        $app->shouldReceive('offsetGet')->once()->with('db')->andReturn($db);

        $provider->shouldReceive('getListenClosure')->once()->with($logger)->andReturn('something');
        $db->shouldReceive('listen')->once()->with('something');

        $provider->register();
        $this->assertTrue(true);
    }

    /** @test */
    public function it_starts_listening_to_queries_when_slow_queries_should_be_logged()
    {
        $app = Mockery::mock(Container::class, \ArrayAccess::class);
        Container::setInstance($app);
        $config = Mockery::mock(Config::class);

        $app->shouldReceive('make')->once()->with(Config::class)->andReturn($config);

        $provider = Mockery::mock(ServiceProvider::class)->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $provider->__construct($app);

        $baseDir = '/some/sample/directory';

        $app->shouldReceive('make')->atLeast()->once()
            ->with('path.config')->andReturn($baseDir);

        $configFile = realpath(__DIR__ . '/../publish/config/sql_logger.php');
        $provider->shouldReceive('mergeConfigFrom')->once()->with(
            $configFile,
            'sql_logger'
        );

        $provider->shouldReceive('publishes')->once()->with(
            [$configFile => config_path('sql_logger.php')]
        );

        $config->shouldReceive('logAllQueries')->once()->withNoArgs()->andReturn(false);
        $config->shouldReceive('logSlowQueries')->once()->withNoArgs()->andReturn(true);

        $logger = Mockery::mock(SqlLogger::class);
        $app->shouldReceive('make')->with(SqlLogger::class)->andReturn($logger);

        $db = Mockery::mock(DatabaseManager::class)->makePartial();
        $app->shouldReceive('offsetGet')->once()->with('db')->andReturn($db);

        $provider->shouldReceive('getListenClosure')->once()->with($logger)->andReturn('something');
        $db->shouldReceive('listen')->once()->with('something');

        $provider->register();
        $this->assertTrue(true);
    }

    /** @test */
    public function it_uses_valid_listening_closure()
    {
        $app = Mockery::mock(Container::class, \ArrayAccess::class)->shouldIgnoreMissing(['make']);

        $class = new class($app) extends ServiceProvider {
            public function getClosureResult($sqlLogger)
            {
                return $this->getListenClosure($sqlLogger);
            }
        };

        $logger = Mockery::mock(SqlLogger::class);
        $closure = $class->getClosureResult($logger);

        $query = 'SELECT * FROM anything';
        $bindings = ['one', 2];
        $time = 55.12;
        $something = 83484;

        $logger->shouldReceive('log')->once()->with($query, $bindings, $time);

        $closure($query, $bindings, $time, $something);
        $this->assertTrue(true);
    }
}
