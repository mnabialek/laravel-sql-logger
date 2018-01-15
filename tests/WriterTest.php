<?php

namespace Mnabialek\LaravelSqlLogger\Tests;

use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Mnabialek\LaravelSqlLogger\Config;
use Mnabialek\LaravelSqlLogger\Formatter;
use Mnabialek\LaravelSqlLogger\Objects\SqlQuery;
use Mnabialek\LaravelSqlLogger\Writer;
use Mockery;

class WriterTest extends UnitTestCase
{
    /**
     * @var Application|\Mockery\Mock
     */
    private $app;

    /**
     * @var Formatter|\Mockery\Mock
     */
    private $formatter;

    /**
     * @var Config|\Mockery\Mock
     */
    private $config;

    /**
     * @var Writer
     */
    private $writer;

    /**
     * @var string
     */
    private $directory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    protected function setUp()
    {
        $this->app = Mockery::mock(Application::class);
        $this->formatter = Mockery::mock(Formatter::class);
        $this->config = Mockery::mock(Config::class);
        $this->writer = new Writer($this->app, $this->formatter, $this->config);
        $this->directory = __DIR__ . '/test-dir/directory';
        $this->filesystem = new Filesystem();
    }

    protected function tearDown()
    {
        $this->filesystem->deleteDirectory($this->directory);
        parent::tearDown();
    }

    /** @test */
    public function it_creates_directory_if_it_does_not_exist_for_1st_query()
    {
        $query = new SqlQuery(1, 'test', [], 5.41);
        $this->formatter->shouldReceive('getLine')->once()->with($query);
        $this->config->shouldReceive('logAllQueries')->once()->withNoArgs()->andReturn(false);
        $this->config->shouldReceive('logSlowQueries')->once()->withNoArgs()->andReturn(false);
        $this->config->shouldReceive('logDirectory')->once()->withNoArgs()->andReturn($this->directory);
        $this->assertFileNotExists($this->directory);
        $this->writer->save($query);
        $this->assertFileExists($this->directory);
        $this->assertEmpty($this->filesystem->allFiles($this->directory));
    }

    /** @test */
    public function it_does_not_create_directory_if_it_does_not_exist_for_2nd_query()
    {
        $query = new SqlQuery(2, 'test', [], 5.41);
        $this->formatter->shouldReceive('getLine')->once()->with($query);
        $this->config->shouldReceive('logAllQueries')->once()->withNoArgs()->andReturn(false);
        $this->config->shouldReceive('logSlowQueries')->once()->withNoArgs()->andReturn(false);
        $this->config->shouldNotReceive('logDirectory');
        $this->assertFileNotExists($this->directory);
        $this->writer->save($query);
        $this->assertFileNotExists($this->directory);
    }

    /** @test */
    public function it_creates_log_file()
    {
        $now = Carbon::parse('2015-02-03 06:41:31');
        Carbon::setTestNow($now);

        $lineContent = 'Sample log line';

        $query = new SqlQuery(1, 'test', [], 5.41);
        $this->formatter->shouldReceive('getLine')->once()->with($query)->andReturn($lineContent);
        $this->config->shouldReceive('logAllQueries')->once()->withNoArgs()->andReturn(true);
        $this->config->shouldReceive('allQueriesPattern')->once()->withNoArgs()->andReturn('#.*#i');
        $this->config->shouldReceive('logSlowQueries')->once()->withNoArgs()->andReturn(false);
        $this->config->shouldReceive('logDirectory')->times(2)->withNoArgs()->andReturn($this->directory);
        $this->config->shouldReceive('overrideFile')->once()->withNoArgs()->andReturn(false);
        $this->app->shouldReceive('runningInConsole')->once()->withNoArgs()->andReturn(false);
        $this->writer->save($query);
        $this->assertFileExists($this->directory);
        $this->assertCount(1, $this->filesystem->allFiles($this->directory));
        $expectedFileName = $this->directory . '/' . $now->toDateString() . '-log.sql';
        $this->assertFileExists($expectedFileName);
        $this->assertSame($lineContent, file_get_contents($expectedFileName));
    }

    /** @test */
    public function it_appends_to_existing_log_file()
    {
        $now = Carbon::parse('2015-02-03 06:41:31');
        Carbon::setTestNow($now);

        $expectedFileName = $this->directory . '/' . $now->toDateString() . '-log.sql';
        mkdir($this->directory, 0777, true);
        $initialContent = "Initial file content\n";
        file_put_contents($expectedFileName, $initialContent);

        $lineContent = 'Sample log line';

        $query = new SqlQuery(1, 'test', [], 5.41);
        $this->formatter->shouldReceive('getLine')->once()->with($query)->andReturn($lineContent);
        $this->config->shouldReceive('logAllQueries')->once()->withNoArgs()->andReturn(true);
        $this->config->shouldReceive('allQueriesPattern')->once()->withNoArgs()->andReturn('#.*#i');
        $this->config->shouldReceive('logSlowQueries')->once()->withNoArgs()->andReturn(false);
        $this->config->shouldReceive('logDirectory')->times(2)->withNoArgs()->andReturn($this->directory);
        $this->app->shouldReceive('runningInConsole')->once()->withNoArgs()->andReturn(false);
        $this->config->shouldReceive('overrideFile')->once()->withNoArgs()->andReturn(false);
        $this->assertFileExists($expectedFileName);
        $this->writer->save($query);
        $this->assertCount(1, $this->filesystem->allFiles($this->directory));
        $this->assertFileExists($expectedFileName);
        $this->assertSame($initialContent . $lineContent, file_get_contents($expectedFileName));
    }

    /** @test */
    public function it_replaces_current_file_content_for_1st_query_when_overriding_is_turned_on()
    {
        $now = Carbon::parse('2015-02-03 06:41:31');
        Carbon::setTestNow($now);

        $expectedFileName = $this->directory . '/' . $now->toDateString() . '-log.sql';
        mkdir($this->directory, 0777, true);
        $initialContent = "Initial file content\n";
        file_put_contents($expectedFileName, $initialContent);

        $lineContent = 'Sample log line';

        $query = new SqlQuery(1, 'test', [], 5.41);
        $this->formatter->shouldReceive('getLine')->once()->with($query)->andReturn($lineContent);
        $this->config->shouldReceive('logAllQueries')->once()->withNoArgs()->andReturn(true);
        $this->config->shouldReceive('allQueriesPattern')->once()->withNoArgs()->andReturn('#.*#i');
        $this->config->shouldReceive('logSlowQueries')->once()->withNoArgs()->andReturn(false);
        $this->config->shouldReceive('logDirectory')->times(2)->withNoArgs()->andReturn($this->directory);
        $this->app->shouldReceive('runningInConsole')->once()->withNoArgs()->andReturn(false);
        $this->config->shouldReceive('overrideFile')->once()->withNoArgs()->andReturn(true);
        $this->assertFileExists($expectedFileName);
        $this->writer->save($query);
        $this->assertCount(1, $this->filesystem->allFiles($this->directory));
        $this->assertFileExists($expectedFileName);
        $this->assertSame($lineContent, file_get_contents($expectedFileName));
    }

    /** @test */
    public function it_appends_to_current_file_content_for_2nd_query_when_overriding_is_turned_on()
    {
        $now = Carbon::parse('2015-02-03 06:41:31');
        Carbon::setTestNow($now);

        $expectedFileName = $this->directory . '/' . $now->toDateString() . '-log.sql';
        mkdir($this->directory, 0777, true);
        $initialContent = "Initial file content\n";
        file_put_contents($expectedFileName, $initialContent);

        $lineContent = 'Sample log line';

        $query = new SqlQuery(2, 'test', [], 5.41);
        $this->formatter->shouldReceive('getLine')->once()->with($query)->andReturn($lineContent);
        $this->config->shouldReceive('logAllQueries')->once()->withNoArgs()->andReturn(true);
        $this->config->shouldReceive('allQueriesPattern')->once()->withNoArgs()->andReturn('#.*#i');
        $this->config->shouldReceive('logSlowQueries')->once()->withNoArgs()->andReturn(false);
        $this->config->shouldReceive('logDirectory')->once()->withNoArgs()->andReturn($this->directory);
        $this->app->shouldReceive('runningInConsole')->once()->withNoArgs()->andReturn(false);
        $this->config->shouldNotReceive('overrideFile');
        $this->assertFileExists($expectedFileName);
        $this->writer->save($query);
        $this->assertCount(1, $this->filesystem->allFiles($this->directory));
        $this->assertFileExists($expectedFileName);
        $this->assertSame($initialContent . $lineContent, file_get_contents($expectedFileName));
    }

    /** @test */
    public function it_creates_artisan_log_file_when_console_log_should_be_logged_to_separate_file_when_running_in_console()
    {
        $now = Carbon::parse('2015-02-03 06:41:31');
        Carbon::setTestNow($now);

        $lineContent = 'Sample log line';

        $query = new SqlQuery(1, 'test', [], 5.41);
        $this->formatter->shouldReceive('getLine')->once()->with($query)->andReturn($lineContent);
        $this->config->shouldReceive('logAllQueries')->once()->withNoArgs()->andReturn(true);
        $this->config->shouldReceive('allQueriesPattern')->once()->withNoArgs()->andReturn('#.*#i');
        $this->config->shouldReceive('logSlowQueries')->once()->withNoArgs()->andReturn(false);
        $this->config->shouldReceive('logDirectory')->times(2)->withNoArgs()->andReturn($this->directory);
        $this->config->shouldReceive('consoleSuffix')->once()->withNoArgs()->andReturn('-sample');
        $this->config->shouldReceive('overrideFile')->once()->withNoArgs()->andReturn(false);
        $this->app->shouldReceive('runningInConsole')->once()->withNoArgs()->andReturn(true);
        $this->writer->save($query);
        $this->assertFileExists($this->directory);
        $this->assertCount(1, $this->filesystem->allFiles($this->directory));
        $expectedFileName = $this->directory . '/' . $now->toDateString() . '-sample-log.sql';
        $this->assertFileExists($expectedFileName);
        $this->assertSame($lineContent, file_get_contents($expectedFileName));
    }

    /** @test */
    public function it_creates_normal_log_file_when_console_log_should_be_logged_to_separate_file_when_not_running_in_console()
    {
        $now = Carbon::parse('2015-02-03 06:41:31');
        Carbon::setTestNow($now);

        $lineContent = 'Sample log line';

        $query = new SqlQuery(1, 'test', [], 5.41);
        $this->formatter->shouldReceive('getLine')->once()->with($query)->andReturn($lineContent);
        $this->config->shouldReceive('logAllQueries')->once()->withNoArgs()->andReturn(true);
        $this->config->shouldReceive('allQueriesPattern')->once()->withNoArgs()->andReturn('#.*#i');
        $this->config->shouldReceive('logSlowQueries')->once()->withNoArgs()->andReturn(false);
        $this->config->shouldReceive('logDirectory')->times(2)->withNoArgs()->andReturn($this->directory);
        $this->config->shouldReceive('overrideFile')->once()->withNoArgs()->andReturn(false);
        $this->app->shouldReceive('runningInConsole')->once()->withNoArgs()->andReturn(false);
        $this->writer->save($query);
        $this->assertFileExists($this->directory);
        $this->assertCount(1, $this->filesystem->allFiles($this->directory));
        $expectedFileName = $this->directory . '/' . $now->toDateString() . '-log.sql';
        $this->assertFileExists($expectedFileName);
        $this->assertSame($lineContent, file_get_contents($expectedFileName));
    }

    /** @test */
    public function it_adds_query_to_slow_log_when_its_greater_than_given_time()
    {
        $now = Carbon::parse('2015-02-03 06:41:31');
        Carbon::setTestNow($now);

        $lineContent = 'Sample slow log line';

        $query = new SqlQuery(1, 'test', [], 5.41);
        $this->formatter->shouldReceive('getLine')->once()->with($query)->andReturn($lineContent);
        $this->config->shouldReceive('logAllQueries')->once()->withNoArgs()->andReturn(false);
        $this->config->shouldReceive('logSlowQueries')->once()->withNoArgs()->andReturn(true);
        $this->config->shouldReceive('slowLogTime')->once()->withNoArgs()->andReturn(5.22);
        $this->config->shouldReceive('slowQueriesPattern')->once()->withNoArgs()->andReturn('#.*#i');
        $this->config->shouldReceive('logDirectory')->times(2)->withNoArgs()->andReturn($this->directory);
        $this->app->shouldReceive('runningInConsole')->once()->withNoArgs()->andReturn(false);
        $this->writer->save($query);
        $this->assertFileExists($this->directory);
        $this->assertCount(1, $this->filesystem->allFiles($this->directory));
        $expectedFileName = $this->directory . '/' . $now->toDateString() . '-slow-log.sql';
        $this->assertFileExists($expectedFileName);
        $this->assertSame($lineContent, file_get_contents($expectedFileName));
    }

    /** @test */
    public function it_does_not_add_query_to_slow_log_when_its_lower_than_given_time()
    {
        $now = Carbon::parse('2015-02-03 06:41:31');
        Carbon::setTestNow($now);

        $lineContent = 'Sample slow log line';

        $query = new SqlQuery(1, 'test', [], 5.41);
        $this->formatter->shouldReceive('getLine')->once()->with($query)->andReturn($lineContent);
        $this->config->shouldReceive('logAllQueries')->once()->withNoArgs()->andReturn(false);
        $this->config->shouldReceive('logSlowQueries')->once()->withNoArgs()->andReturn(true);
        $this->config->shouldReceive('slowLogTime')->once()->withNoArgs()->andReturn(5.42);
        $this->config->shouldReceive('logDirectory')->once()->withNoArgs()->andReturn($this->directory);
        $this->writer->save($query);
        $this->assertFileExists($this->directory);
        $this->assertEmpty($this->filesystem->allFiles($this->directory));
    }

    /** @test */
    public function it_creates_artisan_log_file_for_slow_log_when_console_log_should_be_logged_to_separate_file_when_running_in_console()
    {
        $now = Carbon::parse('2015-02-03 06:41:31');
        Carbon::setTestNow($now);

        $lineContent = 'Sample log line';

        $query = new SqlQuery(1, 'test', [], 5.41);
        $this->formatter->shouldReceive('getLine')->once()->with($query)->andReturn($lineContent);
        $this->config->shouldReceive('logAllQueries')->once()->withNoArgs()->andReturn(false);
        $this->config->shouldReceive('logSlowQueries')->once()->withNoArgs()->andReturn(true);
        $this->config->shouldReceive('slowLogTime')->once()->withNoArgs()->andReturn(5.33);
        $this->config->shouldReceive('slowQueriesPattern')->once()->withNoArgs()->andReturn('#.*#i');
        $this->config->shouldReceive('logDirectory')->times(2)->withNoArgs()->andReturn($this->directory);
        $this->config->shouldReceive('consoleSuffix')->once()->withNoArgs()->andReturn('-example');
        $this->app->shouldReceive('runningInConsole')->once()->withNoArgs()->andReturn(true);
        $this->writer->save($query);
        $this->assertFileExists($this->directory);
        $this->assertCount(1, $this->filesystem->allFiles($this->directory));
        $expectedFileName = $this->directory . '/' . $now->toDateString() . '-example-slow-log.sql';
        $this->assertFileExists($expectedFileName);
        $this->assertSame($lineContent, file_get_contents($expectedFileName));
    }

    /** @test */
    public function it_creates_normal_log_file_for_slow_log_when_console_log_should_be_logged_to_separate_file_when_not_running_in_console()
    {
        $now = Carbon::parse('2015-02-03 06:41:31');
        Carbon::setTestNow($now);

        $lineContent = 'Sample log line';

        $query = new SqlQuery(1, 'test', [], 5.41);
        $this->formatter->shouldReceive('getLine')->once()->with($query)->andReturn($lineContent);
        $this->config->shouldReceive('logAllQueries')->once()->withNoArgs()->andReturn(false);
        $this->config->shouldReceive('logSlowQueries')->once()->withNoArgs()->andReturn(true);
        $this->config->shouldReceive('slowLogTime')->once()->withNoArgs()->andReturn(5.33);
        $this->config->shouldReceive('slowQueriesPattern')->once()->withNoArgs()->andReturn('#.*#i');
        $this->config->shouldReceive('logDirectory')->times(2)->withNoArgs()->andReturn($this->directory);
        $this->app->shouldReceive('runningInConsole')->once()->withNoArgs()->andReturn(false);
        $this->writer->save($query);
        $this->assertFileExists($this->directory);
        $this->assertCount(1, $this->filesystem->allFiles($this->directory));
        $expectedFileName = $this->directory . '/' . $now->toDateString() . '-slow-log.sql';
        $this->assertFileExists($expectedFileName);
        $this->assertSame($lineContent, file_get_contents($expectedFileName));
    }

    /** @test */
    public function it_creates_2_files_when_both_log_set_to_true()
    {
        $now = Carbon::parse('2015-02-03 06:41:31');
        Carbon::setTestNow($now);

        $lineContent = 'Sample log line';

        $query = new SqlQuery(1, 'test', [], 5.41);
        $this->formatter->shouldReceive('getLine')->once()->with($query)->andReturn($lineContent);
        $this->config->shouldReceive('logAllQueries')->once()->withNoArgs()->andReturn(true);
        $this->config->shouldReceive('allQueriesPattern')->once()->withNoArgs()->andReturn('#.*#i');
        $this->config->shouldReceive('logSlowQueries')->once()->withNoArgs()->andReturn(true);
        $this->config->shouldReceive('slowLogTime')->once()->withNoArgs()->andReturn(5.33);
        $this->config->shouldReceive('slowQueriesPattern')->once()->withNoArgs()->andReturn('#.*#i');
        $this->config->shouldReceive('logDirectory')->times(3)->withNoArgs()->andReturn($this->directory);
        $this->app->shouldReceive('runningInConsole')->times(2)->withNoArgs()->andReturn(false);
        $this->config->shouldReceive('overrideFile')->once()->withNoArgs()->andReturn(false);
        $this->writer->save($query);
        $this->assertFileExists($this->directory);
        $this->assertCount(2, $this->filesystem->allFiles($this->directory));
        $expectedFileName = $this->directory . '/' . $now->toDateString() . '-log.sql';
        $this->assertFileExists($expectedFileName);
        $this->assertSame($lineContent, file_get_contents($expectedFileName));
        $expectedSlowFileName = $this->directory . '/' . $now->toDateString() . '-slow-log.sql';
        $this->assertFileExists($expectedSlowFileName);
        $this->assertSame($lineContent, file_get_contents($expectedSlowFileName));
    }

    /** @test */
    public function it_saves_select_query_to_file_when_pattern_set_to_select_queries()
    {
        $now = Carbon::parse('2015-02-03 06:41:31');
        Carbon::setTestNow($now);

        $lineContent = 'Sample log line';

        $query = new SqlQuery(1, 'select * FROM test', [], 5.41);
        $this->formatter->shouldReceive('getLine')->once()->with($query)->andReturn($lineContent);
        $this->config->shouldReceive('logAllQueries')->once()->withNoArgs()->andReturn(true);
        $this->config->shouldReceive('allQueriesPattern')->once()->withNoArgs()->andReturn('#^SELECT .*$#i');
        $this->config->shouldReceive('logSlowQueries')->once()->withNoArgs()->andReturn(true);
        $this->config->shouldReceive('slowLogTime')->once()->withNoArgs()->andReturn(5.33);
        $this->config->shouldReceive('slowQueriesPattern')->once()->withNoArgs()->andReturn('#^SELECT .*$#i');
        $this->config->shouldReceive('logDirectory')->times(3)->withNoArgs()->andReturn($this->directory);
        $this->app->shouldReceive('runningInConsole')->times(2)->withNoArgs()->andReturn(false);
        $this->config->shouldReceive('overrideFile')->once()->withNoArgs()->andReturn(false);
        $this->writer->save($query);
        $this->assertFileExists($this->directory);
        $this->assertCount(2, $this->filesystem->allFiles($this->directory));
        $expectedFileName = $this->directory . '/' . $now->toDateString() . '-log.sql';
        $this->assertFileExists($expectedFileName);
        $this->assertSame($lineContent, file_get_contents($expectedFileName));
        $expectedSlowFileName = $this->directory . '/' . $now->toDateString() . '-slow-log.sql';
        $this->assertFileExists($expectedSlowFileName);
        $this->assertSame($lineContent, file_get_contents($expectedSlowFileName));
    }

    /** @test */
    public function it_doesnt_save_select_query_to_file_when_pattern_set_to_insert_or_update_queries()
    {
        $now = Carbon::parse('2015-02-03 06:41:31');
        Carbon::setTestNow($now);

        $lineContent = 'Sample log line';

        $query = new SqlQuery(1, 'select * FROM test', [], 5.41);
        $this->formatter->shouldReceive('getLine')->once()->with($query)->andReturn($lineContent);
        $this->config->shouldReceive('logAllQueries')->once()->withNoArgs()->andReturn(true);
        $this->config->shouldReceive('allQueriesPattern')->once()->withNoArgs()->andReturn('#^(?:UPDATE |INSERT ).*$#i');
        $this->config->shouldReceive('logSlowQueries')->once()->withNoArgs()->andReturn(true);
        $this->config->shouldReceive('slowLogTime')->once()->withNoArgs()->andReturn(5.33);
        $this->config->shouldReceive('slowQueriesPattern')->once()->withNoArgs()->andReturn('#^(?:UPDATE |INSERT ).*$#i');
        $this->config->shouldReceive('logDirectory')->once()->withNoArgs()->andReturn($this->directory);
        $this->writer->save($query);
        $this->assertFileExists($this->directory);
        $this->assertCount(0, $this->filesystem->allFiles($this->directory));
    }

    /** @test */
    public function it_saves_insert_query_to_file_when_pattern_set_to_insert_or_update_queries()
    {
        $now = Carbon::parse('2015-02-03 06:41:31');
        Carbon::setTestNow($now);

        $lineContent = 'Sample log line';

        $query = new SqlQuery(1, 'INSERT INTO test(one, two, three) values(?, ?, ?)', [], 5.41);
        $this->formatter->shouldReceive('getLine')->once()->with($query)->andReturn($lineContent);
        $this->config->shouldReceive('logAllQueries')->once()->withNoArgs()->andReturn(true);
        $this->config->shouldReceive('allQueriesPattern')->once()->withNoArgs()->andReturn('#^(?:UPDATE |INSERT ).*$#i');
        $this->config->shouldReceive('logSlowQueries')->once()->withNoArgs()->andReturn(true);
        $this->config->shouldReceive('slowLogTime')->once()->withNoArgs()->andReturn(5.33);
        $this->config->shouldReceive('slowQueriesPattern')->once()->withNoArgs()->andReturn('#^(?:UPDATE |INSERT ).*$#i');
        $this->config->shouldReceive('logDirectory')->times(3)->withNoArgs()->andReturn($this->directory);
        $this->app->shouldReceive('runningInConsole')->times(2)->withNoArgs()->andReturn(false);
        $this->config->shouldReceive('overrideFile')->once()->withNoArgs()->andReturn(false);
        $this->writer->save($query);
        $this->assertFileExists($this->directory);
        $this->assertCount(2, $this->filesystem->allFiles($this->directory));
        $expectedFileName = $this->directory . '/' . $now->toDateString() . '-log.sql';
        $this->assertFileExists($expectedFileName);
        $this->assertSame($lineContent, file_get_contents($expectedFileName));
        $expectedSlowFileName = $this->directory . '/' . $now->toDateString() . '-slow-log.sql';
        $this->assertFileExists($expectedSlowFileName);
        $this->assertSame($lineContent, file_get_contents($expectedSlowFileName));
    }

    /** @test */
    public function it_uses_raw_query_without_bindings_when_using_query_pattern()
    {
        $now = Carbon::parse('2015-02-03 06:41:31');
        Carbon::setTestNow($now);

        $lineContent = 'Sample log line';

        $query = new SqlQuery(1, 'UPDATE test SET x = ? WHERE id = ?', [2, 3], 5.41);
        $this->formatter->shouldReceive('getLine')->once()->with($query)->andReturn($lineContent);
        $this->config->shouldReceive('logAllQueries')->once()->withNoArgs()->andReturn(true);
        $this->config->shouldReceive('allQueriesPattern')->once()->withNoArgs()->andReturn('#^(?:UPDATE test SET x = \? |INSERT ).*$#i');
        $this->config->shouldReceive('logSlowQueries')->once()->withNoArgs()->andReturn(true);
        $this->config->shouldReceive('slowLogTime')->once()->withNoArgs()->andReturn(5.33);
        $this->config->shouldReceive('slowQueriesPattern')->once()->withNoArgs()->andReturn('#^(?:UPDATE test SET x = \? |INSERT ).*$#i');
        $this->config->shouldReceive('logDirectory')->times(3)->withNoArgs()->andReturn($this->directory);
        $this->app->shouldReceive('runningInConsole')->times(2)->withNoArgs()->andReturn(false);
        $this->config->shouldReceive('overrideFile')->once()->withNoArgs()->andReturn(false);
        $this->writer->save($query);
        $this->assertFileExists($this->directory);
        $this->assertCount(2, $this->filesystem->allFiles($this->directory));
        $expectedFileName = $this->directory . '/' . $now->toDateString() . '-log.sql';
        $this->assertFileExists($expectedFileName);
        $this->assertSame($lineContent, file_get_contents($expectedFileName));
        $expectedSlowFileName = $this->directory . '/' . $now->toDateString() . '-slow-log.sql';
        $this->assertFileExists($expectedSlowFileName);
        $this->assertSame($lineContent, file_get_contents($expectedSlowFileName));
    }

    /** @test */
    public function it_uses_different_patterns_for_log_and_slow_log_queries()
    {
        $now = Carbon::parse('2015-02-03 06:41:31');
        Carbon::setTestNow($now);

        $lineContent = 'Sample log line';

        $query = new SqlQuery(1, 'UPDATE test SET x = ? WHERE id = ?', [2, 3], 5.41);
        $this->formatter->shouldReceive('getLine')->once()->with($query)->andReturn($lineContent);
        $this->config->shouldReceive('logAllQueries')->once()->withNoArgs()->andReturn(true);
        $this->config->shouldReceive('allQueriesPattern')->once()->withNoArgs()->andReturn('#^(?:UPDATE test SET x = \? |INSERT ).*$#i');
        $this->config->shouldReceive('logSlowQueries')->once()->withNoArgs()->andReturn(true);
        $this->config->shouldReceive('slowLogTime')->once()->withNoArgs()->andReturn(5.33);
        $this->config->shouldReceive('slowQueriesPattern')->once()->withNoArgs()->andReturn('#^SELECT.*$#i');
        $this->config->shouldReceive('logDirectory')->times(2)->withNoArgs()->andReturn($this->directory);
        $this->app->shouldReceive('runningInConsole')->once()->withNoArgs()->andReturn(false);
        $this->config->shouldReceive('overrideFile')->once()->withNoArgs()->andReturn(false);
        $this->writer->save($query);
        $this->assertFileExists($this->directory);
        $this->assertCount(1, $this->filesystem->allFiles($this->directory));
        $expectedFileName = $this->directory . '/' . $now->toDateString() . '-log.sql';
        $this->assertFileExists($expectedFileName);
        $this->assertSame($lineContent, file_get_contents($expectedFileName));
    }
}
