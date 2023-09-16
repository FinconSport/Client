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


    // josh 機器人
    public function josh(Request $request) {

      $input = file_get_contents('php://input');
      $input = json_decode($input,true);

      ////////////////////////////////
      
      $token = "6042430857:AAETPqfYoVzS5iheEES3PEV6OEdoEz5SFno";

      ////////////////////////////////

      $data = array();
      $data['json'] = json_encode($input,true);
      $data['create_time'] = date("Y-m-d H:i:s");
      TelegramLogs::insert($data);

      ////////////////////////////////

      $message = $input['message'];

      // josh 限定功能
      if (($input['message']['chat']['id'] == 5888233461) || ($input['message']['chat']['id'] == 5866476806)) {

        // JOSH
        if (($input['message']['chat']['id'] == 5888233461)) {
          $chat_id = "-320077779";
        }

        // Fayi
        if (($input['message']['chat']['id'] == 5866476806)) {
          $chat_id = "-987586548";
        }

          // 檢查訊息是否為文字訊息
          if (isset($message['text'])) {
            // 取得文字訊息的內容
            $text = $message['text'];
            
            // 使用 sendMessage 方法轉發文字訊息到目標群組
            $url = "https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . urlencode($chat_id) . "&text=" . urlencode($text);
            file_get_contents($url);
          }
          
          // 檢查訊息是否為圖片訊息
          if (isset($message['photo'])) {
            // 取得圖片的 file ID
            $photo = $message['photo'][0];
            $file_id = $photo['file_id'];
            
            // 使用 sendPhoto 方法轉發圖片到目標群組
            $url = "https://api.telegram.org/bot" . $token . "/sendPhoto?chat_id=" . urlencode($chat_id) . "&photo=".$file_id ."&caption=test123";
            file_get_contents($url);
          }
          
          // 檢查訊息是否為檔案訊息
          if (isset($message['document'])) {
            // 取得檔案的 file ID
            $document = $message['document'];
            $file_id = $document['file_id'];
            
            // 使用 sendDocument 方法轉發檔案到目標群組
            $url = "https://api.telegram.org/bot" . $token . "/sendDocument?chat_id=" . urlencode($chat_id) . "&document=".$file_id ."&caption=test123";
            file_get_contents($url);
          }




      } else {

      //  $message = "#".$input['message']['chat']['id'] . " => " . $input['message']['text'];
        $message = "請輸入「我是白癡」";
        $revice_message = $input['message']['text'];
        if ($revice_message == "我是白癡") {
          $message = "為什麼要放棄治療...";
        }

        $user_id = $input['message']['from']['id'];

        $url = "https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . urlencode($user_id) . "&text=". urlencode($message);
        file_get_contents($url);
        
      }

      /////////////////////////////////

      echo json_encode($input,true);


    }

}