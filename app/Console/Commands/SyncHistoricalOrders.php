<?php
namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class SyncHistoricalOrders extends Command
{
    protected $signature = 'crm:sync-historical-orders';
    protected $description = 'Sincroniza pedidos antigos (sem negócio no CRM) criando contatos e negócios retroativamente';

    public function handle(): int
    {
        $synced = Order::syncHistoricalOrders();

        $this->info("{$synced} pedido(s) sincronizado(s) com o CRM.");

        return self::SUCCESS;
    }
}
