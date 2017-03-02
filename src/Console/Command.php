<?php
namespace Humphries\Console;

use Humphries\Utility\ExceptionFactory;
use Symfony\Component\Console\Command\Command as SymphonyCommand;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class Command extends SymphonyCommand
{

    /**
     * @var InputInterface
     */
    protected $input;
    /**
     * @var SymfonyStyle
     */
    protected $output;

    /**
     * @var string
     */
    protected $name = null;

    /**
     * @var string
     */
    protected $description = '';

    /**
     * Command constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        parent::__construct();
    }

    /**
     * Determine if the given argument is present.
     *
     * @param  string|int $name
     *
     * @return bool
     */
    public function hasArgument($name)
    {
        return $this->input->hasArgument($name);
    }

    /**
     * Get the value of a command argument.
     *
     * @param  string $key
     *
     * @return string|array
     */
    public function argument($key = null)
    {
        if (is_null($key)) {
            return $this->input->getArguments();
        }

        return $this->input->getArgument($key);
    }

    /**
     * Determine if the given option is present.
     *
     * @param  string $name
     *
     * @return bool
     */
    public function hasOption($name)
    {
        return $this->input->hasOption($name);
    }

    /**
     * Get the value of a command option.
     *
     * @param  string $key
     *
     * @return string|array
     */
    public function option($key = null)
    {
        if (is_null($key)) {
            return $this->input->getOptions();
        }

        return $this->input->getOption($key);
    }

    /**
     * Add a required option.
     *
     * @param string $name
     * @param string $description
     * @return $this
     */
    public function addRequiredOption($name, $description)
    {
        return $this->addOption($name, null, InputOption::VALUE_REQUIRED, $description);
    }

    /**
     * Add an optional option.
     *
     * @param string $name
     * @param string $description
     *
     * @return $this
     */
    public function addOptionalOption($name, $description)
    {
        return $this->addOption($name, null, InputOption::VALUE_OPTIONAL, $description);
    }

    /**
     * Add a flag option.
     *
     * @param string $name
     * @param string $description
     *
     * @return $this
     */
    public function addFlagOption($name, $description)
    {
        return $this->addOption($name, null, InputOption::VALUE_NONE, $description);
    }

    /**
     * Confirm a question with the user.
     *
     * @param  string $question
     * @param  bool   $default
     *
     * @return bool
     */
    public function confirm($question, $default = false)
    {
        return $this->output->confirm($question, $default);
    }

    /**
     * Prompt the user for input.
     *
     * @param  string $question
     * @param  string $default
     *
     * @return string
     */
    public function ask($question, $default = null)
    {
        return $this->output->ask($question, $default);
    }

    /**
     * Prompt the user for input with auto completion.
     *
     * @param  string $question
     * @param  array  $choices
     * @param  string $default
     *
     * @return string
     */
    public function anticipate($question, array $choices, $default = null)
    {
        return $this->askWithCompletion($question, $choices, $default);
    }

    /**
     * Prompt the user for input with auto completion.
     *
     * @param  string $question
     * @param  array  $choices
     * @param  string $default
     *
     * @return string
     */
    public function askWithCompletion($question, array $choices, $default = null)
    {
        $question = new Question($question, $default);
        $question->setAutocompleterValues($choices);

        return $this->output->askQuestion($question);
    }

    /**
     * Prompt the user for input but hide the answer from the console.
     *
     * @param  string $question
     * @param  bool   $fallback
     *
     * @return string
     */
    public function secret($question, $fallback = true)
    {
        $question = new Question($question);
        $question->setHidden(true)->setHiddenFallback($fallback);

        return $this->output->askQuestion($question);
    }

    /**
     * Give the user a single choice from an array of answers.
     *
     * @param  string $question
     * @param  array  $choices
     * @param  string $default
     * @param  mixed  $attempts
     * @param  bool   $multiple
     *
     * @return string
     */
    public function choice($question, array $choices, $default = null, $attempts = null, $multiple = null)
    {
        $question = new ChoiceQuestion($question, $choices, $default);
        $question->setMaxAttempts($attempts)->setMultiselect($multiple);

        return $this->output->askQuestion($question);
    }

    /**
     * Format input to textual table.
     *
     * @param  array  $headers
     * @param  array  $rows
     * @param  string $style
     *
     * @return void
     */
    public function table(array $headers, array $rows, $style = 'default')
    {
        $table = new Table($this->output);
        $table->setHeaders($headers)->setRows($rows)->setStyle($style)->render();
    }

    /**
     * Write a string as information output.
     *
     * @param  string          $string
     * @param  null|int|string $verbosity
     *
     * @return void
     */
    public function info($string, $verbosity = null)
    {
        $this->line($string, 'info', $verbosity);
    }

    /**
     * Write a string as standard output.
     *
     * @param  string          $string
     * @param  string          $style
     * @param  null|int|string $verbosity
     *
     * @return void
     */
    public function line($string, $style = null, $verbosity = null)
    {
        $styled = $style ? "<$style>$string</$style>" : $string;
        $this->output->writeln($styled);
    }

    /**
     * Write a string as comment output.
     *
     * @param  string          $string
     * @param  null|int|string $verbosity
     *
     * @return void
     */
    public function comment($string, $verbosity = null)
    {
        $this->line($string, 'comment', $verbosity);
    }

    /**
     * Write a string as question output.
     *
     * @param  string          $string
     * @param  null|int|string $verbosity
     *
     * @return void
     */
    public function question($string, $verbosity = null)
    {
        $this->line($string, 'question', $verbosity);
    }

    /**
     * Write a string as error output.
     *
     * @param  string          $string
     * @param  null|int|string $verbosity
     *
     * @return void
     */
    public function error($string, $verbosity = null)
    {
        $this->line($string, 'error', $verbosity);
    }

    /**
     * Write a string as warning output.
     *
     * @param  string          $string
     * @param  null|int|string $verbosity
     *
     * @return void
     */
    public function warn($string, $verbosity = null)
    {
        if (!$this->output->getFormatter()->hasStyle('warning')) {
            $style = new OutputFormatterStyle('yellow');
            $this->output->getFormatter()->setStyle('warning', $style);
        }
        $this->line($string, 'warning', $verbosity);
    }

    /**
     * Call another console command.
     *
     * @param  string  $command
     * @param  array   $arguments
     * @return int
     */
    public function call($command, array $arguments = [])
    {
        $instance = $this->getApplication()->find($command);

        $arguments['command'] = $command;

        return $instance->run(new ArrayInput($arguments), $this->output);
    }

    /**
     * Handles the command
     *
     * @return void
     */
    abstract public function handle();

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface  $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     *
     * @throws LogicException When this abstract method is not implemented
     *
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = new SymfonyStyle($input, $output);
        $this->handle();
    }

    /**
     * Confirm before proceeding with the action.
     *
     * @param  string    $warning
     * @param  \Closure|bool|null  $callback
     * @return bool
     */
    public function confirmToProceed($warning = 'Application In Production!', $callback = null)
    {
        $callback = is_null($callback) ? $this->getDefaultConfirmCallback() : $callback;

        $shouldConfirm = $callback instanceof \Closure ? call_user_func($callback) : $callback;

        if ($shouldConfirm) {
            if ($this->option('force')) {
                return true;
            }

            $this->comment(str_repeat('*', strlen($warning) + 12));
            $this->comment('*     '.$warning.'     *');
            $this->comment(str_repeat('*', strlen($warning) + 12));
            $this->output->writeln('');

            $confirmed = $this->confirm('Do you really wish to run this command?');

            if (! $confirmed) {
                $this->comment('Command Cancelled!');

                return false;
            }
        }

        return true;
    }

    /**
     * Forces exec.
     *
     * @param InputInterface $input
     * @param SymfonyStyle $output
     * @return int|null
     */
    public function forceExec($input, $output)
    {
        return $this->execute($input, $output);
    }

    /**
     * Command Configuration
     *
     * @return $this
     */
    protected function configure()
    {
        if (empty($this->name)) return ExceptionFactory::InvalidPropertyException('The property name must not be empty.');

        return $this->setName($this->name)
            ->setDescription($this->description);
    }

    /**
     * Get the default confirmation callback.
     *
     * @return \Closure
     */
    protected function getDefaultConfirmCallback()
    {
        return function () {
            return $this->getApp()->getEnvironment() == 'production';
        };
    }

}