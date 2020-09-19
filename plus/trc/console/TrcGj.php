<?php namespace Plus\Trc\Console;

use Illuminate\Console\Command;
use Plus\Trc\Classes\TrcBonus;

class TrcGj extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'trc:gj';

    /**
     * @var string The console command description.
     */
    protected $description = '归集计划任务.建议20分钟运行一次';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $trc_bonus=new TrcBonus();
        $trc_bonus->gj();
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
