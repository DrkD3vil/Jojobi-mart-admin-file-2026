<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class FinanceSnapshot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:finance-snapshot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info(Order::sum('payable_total'));
    }
}
