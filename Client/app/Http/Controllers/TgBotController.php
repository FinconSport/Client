<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\TelegramLogs;

class TgBotController extends Controller {
    
    // 首頁
    public function index(Request $request) {
    	
      $input = $this->getRequest($request);

      //字串處理 \n
      $text = $input['text'];
      $text = str_replace("(", "\n", $text);
      $text = str_replace(")", " ❤️", $text);
      $message = urlencode($text);

   
      $token = "6205808787:AAG6ZcMhFbXTWlvXvm4DGfVGZxTkY3ZqCvQ";
      $chat_id = "-873155069";

      $url = "https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . $chat_id . "&text=". $message;
      file_get_contents($url);

    }

    // step 1
    public function RMQStep1(Request $request) {
    	
      $input = $this->getRequest($request);

      $token = "6205808787:AAG6ZcMhFbXTWlvXvm4DGfVGZxTkY3ZqCvQ";
      $chat_id = "-873155069";
      $photoUrl = "https://sportc.asgame.net/image/tg_bot/001.jpg";  // 替换为远程图片的URL

      $apiUrl = "https://api.telegram.org/bot$token/sendPhoto";
      
      // 构建POST请求的参数
      $postFields = array(
          'chat_id' => $chat_id,
          'photo'   => $photoUrl,
      );
      
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $apiUrl);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      
      $response = curl_exec($ch);
      curl_close($ch);
      
    }

    // step 2
    public function RMQStep2(Request $request) {
    	
      $input = $this->getRequest($request);

      //字串處理 \n
      $text = "LSport :「我可是活了500年的RMQ」";
      $message = urlencode($text);

   
      $token = "6205808787:AAG6ZcMhFbXTWlvXvm4DGfVGZxTkY3ZqCvQ";
      $chat_id = "-873155069";

      $url = "https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . $chat_id . "&text=". $message;
      file_get_contents($url);

    }
    // step 3
    public function RMQStep3(Request $request) {
    	
      $input = $this->getRequest($request);

      //字串處理 \n
      $text = "「RMQ ... 站你面前的是1000年的Golang」";
      $message = urlencode($text);

   
      $token = "6205808787:AAG6ZcMhFbXTWlvXvm4DGfVGZxTkY3ZqCvQ";
      $chat_id = "-873155069";

      $url = "https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . $chat_id . "&text=". $message;
      file_get_contents($url);

    }

    
    // step 4
    public function RMQStep4(Request $request) {
    	
      $input = $this->getRequest($request);

      //字串處理 \n
      $text = "「讓RMQ變正常的魔法 ❤️」";
      $message = urlencode($text);

   
      $token = "6205808787:AAG6ZcMhFbXTWlvXvm4DGfVGZxTkY3ZqCvQ";
      $chat_id = "-873155069";

      $url = "https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . $chat_id . "&text=". $message;
      file_get_contents($url);

    }

    // bill
    public function bill(Request $request) {
    	
      $input = $this->getRequest($request);

      //字串處理 \n
      $text = $input['text'];
      $text = str_replace("(", "\n🐲", $text);
      $text = str_replace(")", " 🐉", $text);
      $message = urlencode($text);
   
      $token = "6398366780:AAFf3M3LReIoAcDVuiN3L8zyqByHKWmbMhE";
      $chat_id = "-362800147";

      $url = "https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . $chat_id . "&text=". $message;
      file_get_contents($url);

    }


}