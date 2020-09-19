<?php namespace Plus\Trc\Console;

use Illuminate\Console\Command;
use Plus\Trc\Classes\TrcBonus;

class TrcRg extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'trc:rg';

    /**
     * @var string The console command description.
     */
    protected $description = '充币计划任务.建议10分钟运行一次';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $trc_bonus=new TrcBonus();
        $trc_bonus->rg();
        $this->output->writeln('Hello world!');
    }

    /**
     * Get the console command arguments.
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }
}
