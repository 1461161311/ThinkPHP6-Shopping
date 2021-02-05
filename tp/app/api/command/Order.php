<?php
declare (strict_types = 1);

namespace app\api\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Order extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('order')
            ->setDescription('the order command');
    }

    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        $output->writeln('order');
    }
}
