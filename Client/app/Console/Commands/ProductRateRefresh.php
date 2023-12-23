<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ShopProduct;
use App\Models\ShopProductComment;


class ProductRateRefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'productrate:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '產品評價計算星數';

    public function __construct()
    {
    	parent::__construct();
    }

    /**
     * Pull orderdata from mssql into mysql.
     *
     * @return boolean
     */
    public function handle()
    {
    	
      // 取得每個產品的評價數, 及Rate的平均值
      $return = ShopProductComment::selectRaw('product_id,count(id) counts,AVG(rate) new_rate')->groupBy('product_id')->get(); 

      foreach ($return as $k => $v) {

        $rate = floor($v['new_rate']);
        
        ShopProduct::where("id",$v['product_id'])->update([
          "product_rate" => $rate
        ]);
        
      }

    }
    
}
