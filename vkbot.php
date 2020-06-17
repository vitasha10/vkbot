<?php
header('HTTP/1.1 200 OK');
class vkbot
{ 
    private $access_tocken = fgets(fopen(__DIR__."/token.txt", "r"), filesize(__DIR__."/token.txt")+1);
    private $version_kick = "5.81";
    private $version_msg_send = "5.87";
    //kick peoples
    public function kick($chat_id, $kick_id)
    {
        $request_params = array(
            'chat_id' => $chat_id,
            'member_id' => $kick_id,
            'access_token' => $this->access_tocken,
            'v' => $this->version_kick
        );
        $get_params = http_build_query($request_params);
        $log1 = file_get_contents('https://api.vk.com/method/messages.removeChatUser?' . $get_params);
        //$log2 = json_decode($log1, true);
        return $log1;
    }
    public function vk_msg_send($peer_id, $text)
    {
        $request_params = array(
            'message' => $text,
            'peer_id' => $peer_id,
            'access_token' => $this->access_tocken,
            'v' => $this->version_msg_send
        );
        $get_params = http_build_query($request_params);
        file_get_contents('https://api.vk.com/method/messages.send?' . $get_params);
    }
    public function vk_get($id)
    {
        $request_params = array(
            'user_ids' => $id,
            'access_token' => $this->access_tocken,
            'v' => $this->version_msg_send
        );
        $get_params = http_build_query($request_params);
        $log1 = file_get_contents('https://api.vk.com/method/users.get?' . $get_params);
        $log2 = json_decode($log1, true);
        return $log2['response'][0]['first_name'];
    }
    public function keyboard($peer_id, $text)
    {
        $request_params = array(
            'message' => $text,
            'peer_id' => $peer_id,
            "keyboard" => '{
            "one_time": false,
            "buttons": [
                [{
                        "action": {
                            "type": "text",
                            "payload": "{\"button\": \"1\"}",
                            "label": "Negative"
                        },
                        "color": "negative"
                    },
                    {
                        "action": {
                            "type": "text",
                            "payload": "{\"button\": \"2\"}",
                            "label": "Positive"
                        },
                        "color": "positive"
                    },
                    {
                        "action": {
                            "type": "text",
                            "payload": "{\"button\": \"2\"}",
                            "label": "Primary"
                        },
                        "color": "primary"
                    },
                    {
                        "action": {
                            "type": "text",
                            "payload": "{\"button\": \"2\"}",
                            "label": "Secondary"
                        },
                        "color": "secondary"
                    }
                ]
            ]
        }',
            'access_token' => $this->access_tocken,
            'v' => $this->version_msg_send
        );
        $get_params = http_build_query($request_params);
        file_get_contents('https://api.vk.com/method/messages.send?' . $get_params);
    }
}
$vk = new vkbot;
$link = mysqli_connect('localhost', '', '', '');
$link->set_charset('utf8');
$confirmation_token = "";
$data = json_decode(file_get_contents('php://input'));
switch ($data->type) {
    case 'confirmation':
        echo $confirmation_token;
        break;
    case 'message_new':
        $message_text = $data->object->text;
        $chat_id = $data->object->peer_id;
        $peer_id = $data->object->peer_id;
        $from_id = $data->object->from_id;
        //$log = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM messages WHERE `name`='{$message_text}'"));
        /*
        if ($message_text == "дз" or $message_text == "гдз") {
            date_default_timezone_set('Asia/Yekaterinburg');
            $date = date("d-m");
            $todate = substr(date('d-m', strtotime($order->date . '1 day')), 0, 10);
            $vk->vk_msg_send($chat_id, "https://robotsandfuture.ru/dayhomework?date={$todate}");
        } else */ if ($message_text == "r") {
            $vk->vk_msg_send($chat_id, "ты - {$chat_id}");
        } else if(strpos($message_text, "дз") !== false){
            $log1 = str_replace('дз', '', $message_text);
            $date = date("20y-m-d");
            if($log1 == " сегодня"){
                $log1 = $date;
            }
            $sql = mysqli_query($link, "SELECT * FROM `gdz` WHERE `date`='{$log1}'");
            $return = "ГДЗ:";
            while($dz = mysqli_fetch_array($sql)){
                $return = $return." \n".$dz['name']." \n".$dz['predmet']." \n".$dz['file']." \n";
            }
            $vk->vk_msg_send($chat_id, $return);

        }else if(strpos($message_text, "Дз") !== false){
            $log1 = str_replace('Дз', '', $message_text);
            $date = date("20y-m-d");
            if($log1 == " сегодня"){
                $log1 = $date;
            }
            $sql = mysqli_query($link, "SELECT * FROM `gdz` WHERE `date`='{$log1}'");
            $return = "";
            while($dz = mysqli_fetch_array($sql)){
                $return = $return." \n".$dz['name']." \n".$dz['predmet']." \n".$dz['file']." \n";
            }
            $vk->vk_msg_send($chat_id, $return);

        }/* if ((strpos($message_text, "аноним:") !== false) or (strpos($message_text, "Аноним:") !== false)) {
            $vk->vk_msg_send($chat_id, 'отправляю!');
            $vk->vk_msg_send('2000000002', $message_text);
            $vk->vk_msg_send('419846599', 'https://vk.com/id' . $chat_id . " " . vk_get($from_id) . ' отправил(а) ' . $message_text);
        } else if (strpos($message_text, "аноним(") !== false) {
            $log1 = str_replace('аноним(', '', $message_text);
            $log2 = strstr($log1, "):", true);
            $log3 = strstr($log1, "):", false);
            $log4 = str_replace("):", '', $log3);
            $vk->vk_msg_send($chat_id, 'отправляю!');
            $vk->vk_msg_send($log2, $log4);
            $vk->vk_msg_send('419846599', 'https://vk.com/id' . $from_id . " " . vk_get($from_id) . ' отправил(а) https://vk.com/id' . $log2 . " " . vk_get($log2) . ": " . $log4);
        } else*/ if (strpos($message_text, "бот8908:") !== false) {
            $log1 = str_replace('бот8908:', '', $message_text);
            $vk->vk_msg_send('2000000002', $log1);
            //$vk->vk_msg_send('419846599', 'https://vk.com/id' . $from_id . " " . vk_get($from_id) . " " . $log1);
        } /*else if (strpos($message_text, "Бот:") !== false) {
            $log1 = str_replace('Бот:', '', $message_text);
            $vk->vk_msg_send('2000000002', $log1);
            $vk->vk_msg_send($chat_id, 'отправляю!');
            $vk->vk_msg_send('419846599', 'https://vk.com/id' . $from_id . " " . vk_get($from_id) . " " . $log1);
        }*/ else /*if (strpos($message_text, "Аноним(") !== false) {
            $log1 = str_replace('Аноним(', '', $message_text);
            $log2 = strstr($log1, "):", true);
            $log3 = strstr($log1, "):", false);
            $log4 = str_replace("):", '', $log3);
            $vk->vk_msg_send($chat_id, 'отправляю!');
            $vk->vk_msg_send($log2, $log4);
            $vk->vk_msg_send('419846599', 'https://vk.com/id' . $from_id . " " . vk_get($from_id) . ' отправил(а) https://vk.com/id' . $log2 . " " . vk_get($log2) . ": " . $log4);
        } else if (strpos($message_text, "admin add") !== false) {
            $log1 = str_replace('admin add ', '', $message_text);
            $log2 = strstr($log1, "'''", true);
            $log3 = strstr($log1, "'''", false);
            $log4 = str_replace("'''", '', $log3);
            $sql = mysqli_query($link, "INSERT INTO messages (`name`, `data`) VALUES ('{$log2}', '{$log4}')");
            if ($sql == '1') {
                $vk->vk_msg_send($chat_id, 'Запомнил!');
            } else {
                $vk->vk_msg_send($chat_id, 'Что-то сломалось!');
            }
        } else*/ if (strpos($message_text, "/kick") !== false) {
            if ($from_id == '419846599' or $from_id == '161425920') {
                $log1 = str_replace('/kick ', '', $message_text);
                $chat = $peer_id - 2000000000;
                $vk->vk_msg_send($chat_id, 'Будь добрее! Пока)');
                $vk->kick($chat, $log1);
            }
        } else /*if (strpos($message_text, "admin dell") !== false) {
            $log1 = str_replace('admin dell ', '', $message_text);
            $sql = mysqli_query($link, "DELETE FROM messages WHERE name='{$log1}'");
            if ($sql == '1') {
                $vk->vk_msg_send($chat_id, 'Забыл!');
            } else {
                $vk->vk_msg_send($chat_id, 'Что-то сломалось!');
            }
        } else if (strpos($message_text, "url") !== false) {
            $log1 = str_replace('url ', '', $message_text);
            $json = json_decode('{}');
            $json->url = $log1;
            //$json = json_decode($json, true);
            $log100 = file_get_contents("https://api.vitasha.tk/v2/api/sites/get/ViViVi/{$json}");
            $vk->vk_msg_send($chat_id, $log100);    
        } else*/ {
            //$vk->vk_msg_send($chat_id, $log['data']);
           
        }
        echo 'ok';
        break;
}