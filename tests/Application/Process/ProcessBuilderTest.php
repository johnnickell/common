<?php declare(strict_types=1);

namespace Novuso\Common\Test\Application\Process;

use Novuso\Common\Application\Process\ProcessBuilder;
use Novuso\System\Exception\DomainException;
use Novuso\System\Exception\MethodCallException;
use Novuso\System\Test\TestCase\UnitTestCase;

/**
 * @covers \Novuso\Common\Application\Process\ProcessBuilder
 */
class ProcessBuilderTest extends UnitTestCase
{
    public function test_that_prefix_is_prepended_to_command_arguments()
    {
        $process = ProcessBuilder::create(['vendor/bin/phpunit'])
            ->prefix('php')
            ->getProcess();
        $this->assertSame("'php' 'vendor/bin/phpunit'", $process->command());
    }

    public function test_that_empty_arg_is_not_added_to_command()
    {
        $process = ProcessBuilder::create('script')
            ->arg('')
            ->getProcess();
        $this->assertSame("'script'", $process->command());
    }

    public function test_that_option_adds_expected_argument_to_command()
    {
        $process = ProcessBuilder::create()
            ->arg('script')
            ->option('force')
            ->getProcess();
        $this->assertSame("'script' '--force'", $process->command());
    }

    public function test_that_option_adds_expected_arg_value_to_command()
    {
        $process = ProcessBuilder::create()
            ->arg('script')
            ->option('format', 'xml')
            ->getProcess();
        $this->assertSame("'script' '--format' 'xml'", $process->command());
    }

    public function test_that_empty_option_is_not_added_to_command()
    {
        $process = ProcessBuilder::create()
            ->arg('script')
            ->option('')
            ->getProcess();
        $this->assertSame("'script'", $process->command());
    }

    public function test_that_short_adds_expected_argument_to_command()
    {
        $process = ProcessBuilder::create()
            ->arg('script')
            ->short('abc')
            ->getProcess();
        $this->assertSame("'script' '-abc'", $process->command());
    }

    public function test_that_short_adds_expected_arg_value_to_command()
    {
        $process = ProcessBuilder::create()
            ->arg('script')
            ->short('f', 'xml')
            ->getProcess();
        $this->assertSame("'script' '-f' 'xml'", $process->command());
    }

    public function test_that_empty_short_is_not_added_to_command()
    {
        $process = ProcessBuilder::create()
            ->arg('script')
            ->short('')
            ->getProcess();
        $this->assertSame("'script'", $process->command());
    }

    public function test_that_clear_args_clears_previous_arguments()
    {
        $process = ProcessBuilder::create(['foo', 'bar', 'baz'])
            ->arg('script')
            ->clearArgs()
            ->arg('php')
            ->short('l')
            ->getProcess();
        $this->assertSame("'php' '-l'", $process->command());
    }

    public function test_that_directory_changes_working_directory_of_process()
    {
        $process = ProcessBuilder::create('php')
            ->short('l')
            ->directory('/path/to/project')
            ->getProcess();
        $this->assertSame('/path/to/project', $process->directory());
    }

    public function test_that_directory_set_to_null_removes_working_directory()
    {
        $process = ProcessBuilder::create('php')
            ->short('l')
            ->directory('/path/to/project')
            ->directory(null)
            ->getProcess();
        $this->assertNull($process->directory());
    }

    public function test_that_input_set_to_resource_adds_resource_to_process()
    {
        $fp = fopen('php://stdin', 'r');
        $process = ProcessBuilder::create('php')
            ->input($fp)
            ->getProcess();
        $this->assertTrue(is_resource($process->input()));
        fclose($fp);
    }

    public function test_that_input_set_to_string_adds_string_to_process()
    {
        $process = ProcessBuilder::create('php')
            ->input('input')
            ->getProcess();
        $this->assertSame('input', $process->input());
    }

    public function test_that_input_set_to_null_removes_input()
    {
        $process = ProcessBuilder::create('php')
            ->input('input')
            ->input(null)
            ->getProcess();
        $this->assertNull($process->input());
    }

    public function test_that_timeout_number_adds_timeout_to_process()
    {
        $process = ProcessBuilder::create('php')
            ->timeout(10)
            ->getProcess();
        $this->assertSame(10.0, $process->timeout());
    }

    public function test_that_timeout_set_to_null_removes_timeout()
    {
        $process = ProcessBuilder::create('php')
            ->timeout(10)
            ->timeout(null)
            ->getProcess();
        $this->assertNull($process->timeout());
    }

    public function test_that_inherit_env_true_merges_environment_variables()
    {
        $process = ProcessBuilder::create('php')
            ->inheritEnv(true)
            ->getProcess();
        $this->assertTrue(count($process->environment()) > 0);
    }

    public function test_that_inherit_env_false_does_not_merge_environment()
    {
        $process = ProcessBuilder::create('php')
            ->inheritEnv(false)
            ->setEnv('FOO', 'bar')
            ->getProcess();
        $this->assertSame(['FOO' => 'bar'], $process->environment());
    }

    public function test_that_stdout_adds_callback_function_to_process()
    {
        $process = ProcessBuilder::create('php')
            ->stdout(function ($data) {
                echo $data;
            })
            ->getProcess();
        $this->assertTrue(is_callable($process->stdout()));
    }

    public function test_that_stderr_adds_callback_function_to_process()
    {
        $process = ProcessBuilder::create('php')
            ->stderr(function ($data) {
                echo $data;
            })
            ->getProcess();
        $this->assertTrue(is_callable($process->stderr()));
    }

    public function test_that_disable_output_disables_process_output()
    {
        $process = ProcessBuilder::create('php')
            ->disableOutput()
            ->getProcess();
        $this->assertTrue($process->isOutputDisabled());
    }

    public function test_that_enable_output_enables_process_output()
    {
        $process = ProcessBuilder::create('php')
            ->disableOutput()
            ->enableOutput()
            ->getProcess();
        $this->assertFalse($process->isOutputDisabled());
    }

    public function test_that_get_process_throws_exception_without_arguments()
    {
        $this->expectException(MethodCallException::class);

        ProcessBuilder::create()->getProcess();
    }

    public function test_that_timeout_throws_exception_for_invalid_timeout()
    {
        $this->expectException(DomainException::class);

        ProcessBuilder::create('php')->timeout(-1);
    }
}
