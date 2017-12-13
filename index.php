<?php
	 /*
	 ربات افزودن شمارنده به انتهای پیامها بدون احتیاج به کانال
	 سازنده : @oYSoF , کانال : @TGsoldierCH
	 */
	 define('TOKEN', 'محل توکن ربات');
	 $admin = 'شناسه مدیر ربات';
	 $admin_username = 'oYSoF'; # نام کاربری مدیر ربات بدون @
	 $ch = 'TGsoldierCH'; # نام کاربری کانال بدون @
	 
	 function bot($method, $data = []){
		 $ch = curl_init();
		 curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot'.TOKEN.'/'.$method);
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		 curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		 $results =  json_decode(curl_exec($ch), true);
		 curl_close($ch);
		 return $results;
	 }
	 function sendAction($chat_id, $action){
		 bot('sendChataction',[
		 'chat_id'=>$chat_id,
		 'action'=>$action
		 ]);
	 }
	 function sendMessage($chat_id, $text, $message_id = null){
		 return bot('sendMessage',[
		 'chat_id'=>$chat_id,
		 'text'=>$text,
		 'reply_to_message_id'=>$message_id
		 ]);
	 }
	 function forwardMessage($chat_id, $from_chat_id, $message_id){
		 return bot('ForwardMessage',[
		 'chat_id'=>$chat_id,
		 'from_chat_id'=>$from_chat_id,
		 'message_id'=>$message_id
		 ]);
	 }
	 $update = json_decode(file_get_contents('php://input'));
	 $chat_id = $update->message->chat->id;
	 $user_id = $update->message->from->id;
	 $user_first = $update->message->from->first_name;
	 $user_last = $update->message->from->last_name;
	 $username = $update->message->from->username;
	 $msg_id = $update->message->message_id;
	 $msg_text = $update->message->text;
	 $bot_username = json_decode(file_get_contents("http://api.telegram.org/botTOKEN/getMe"))->result->username;
	 $step = file_get_contents('step.txt');
	 $members = file_get_contents('members.txt');
	 $channel = file_get_contents('channel.txt');
	 if (strpos($members, "$user_id,") === false && $user_id != $admin){
		 file_put_contents('members.txt', "$members$user_id,");
	 }
	 if (preg_match('/^\/(start)/i', $msg_text)){
		 sendAction($chat_id, 'typing');
		 bot('sendMessage',[
		 'chat_id'=>$chat_id,
		 'text'=>"سلام $user_first 😃✋🏻\n\n🏖به ربات شمارنده گذار خوش آمدید.🚸شما با استفاده از این ربات می توانید بدون ارسال پیام به کانال به زیر آن شمارنده اضافه کنید.\n💯برای اضافه کردن شمارنده به زیر پیام خود تنها کافیست آن را برای من فرستاده یا هدایت کنید.\n\n📣 @$ch\n🤖 @$bot_username",
		 'reply_markup'=>json_encode(['inline_keyboard'=>[
		 [['text'=>'📣کانال','url'=>"https://telegram.me/$ch"],['text'=>'😎سازنده','url'=>"https://telegram.me/$admin_username"]]
		 ]
		 ])
		 ]);
		 forwardMessage($chat_id, '@TGsoldierBots', 8);
	 }
	 elseif ($msg_text == '🔙صفحۀ اصلی'){
		 sendAction($chat_id, 'typing');
		 bot('sendMessage',[
		 'chat_id'=>$chat_id,
		 'text'=>'خوش آمدید😃',
		 'reply_markup'=>json_encode(['remove_keyboard'=>true])
		 ]);
	 }
	 elseif (preg_match('/^\/(panel)/i', $msg_text) && $user_id == $admin || $msg_text == '🔙بازگشت' && $user_id == $admin){
		 sendAction($chat_id, 'typing');
		 file_put_contents('step.txt', 'None');
		 bot('sendMessage',[
		 'chat_id'=>$chat_id,
		 'text'=>"سلام قربان 😃✋🏻\n🤖به بخش مدیریت ربات خوش آمدید.",
		 'reply_markup'=>json_encode(['keyboard'=>[
		 [['text'=>'📊آمار'],['text'=>'📣تنظیم کانال']],
		 [['text'=>'🚀هدایت همگانی'],['text'=>'🗣پیام همگانی']],
		 [['text'=>'🔙صفحۀ اصلی']],
		 ],
		 'resize_keyboard'=>true
		 ])
		 ]);
	 }
	 elseif ($msg_text == '📣تنظیم کانال' && $user_id == $admin){
		 sendAction($chat_id, 'typing');
		 file_put_contents('step.txt', 'setChannel');
		 bot('sendMessage',[
		 'chat_id'=>$chat_id,
		 'text'=>"🏖لطفا یک پیام از کانال مورد نظرتان برای من هدایت کنید.\n\n❗️فرقی ندارد کانال خصوصی یا عمومی باشد.\n❗️حتما من باید مدیر کانال باشم.",
		 'reply_markup'=>json_encode(['keyboard'=>[
		 [['text'=>'🔙بازگشت']]
		 ],
		 'resize_keyboard'=>true
		 ])
		 ]);
	 }
	 elseif ($msg_text == '📊آمار' && $user_id == $admin){
		 sendAction($chat_id, 'typing');
		 $members = count(explode(',', $members))-1;
		 sendMessage($chat_id, "👥تعداد کاربران : $members");
	 }
	 elseif ($msg_text == '🗣پیام همگانی' && $user_id == $admin){
		 sendAction($chat_id, 'typing');
		 file_put_contents('step.txt', 'SendToAll');
		 bot('sendMessage',[
		 'chat_id'=>$chat_id,
		 'text'=>'✉️لطفا پیام خود را جهت ارسال بفرستید.',
		 'reply_markup'=>json_encode(['keyboard'=>[
		 [['text'=>'🔙بازگشت']]
		 ],
		 'resize_keyboard'=>true
		 ])
		 ]);
	 }
	 elseif ($msg_text == '🚀هدایت همگانی' && $user_id == $admin){
		 sendAction($chat_id, 'typing');
		 file_put_contents('step.txt', 'ForwardToAll');
		 bot('sendMessage',[
		 'chat_id'=>$chat_id,
		 'text'=>'🚀لطفا پیام خود را جهت هدایت بفرستید.',
		 'reply_markup'=>json_encode(['keyboard'=>[
		 [['text'=>'🔙بازگشت']]
		 ],
		 'resize_keyboard'=>true
		 ])
		 ]);
	 }
	 elseif ($step == 'setChannel' && $user_id == $admin){
		 sendAction($chat_id, 'typing');
		 if ($update->message->forward_from_chat->type == 'channel'){
			 $channel_id = $update->message->forward_from_chat->id;
			 $message_id = sendMessage($channel_id, '✅کانال با موفقیت تنظیم شد.')->result->message_id;
			 if ($message_id != null){
				 file_put_contents('step.txt', 'None');
				 file_put_contents('channel.txt', $channel_id);
				 forwardMessage($chat_id, $channel_id, $message_id);
			 }
			 else
				 sendMessage($chat_id, '❌باید من مدیر کانال باشم.', $msg_id);
		 }
		 else
			 sendMessage($chat_id, '❌کانال نامعتبر می باشد.', $msg_id);
	 }
	 elseif ($step == 'SendToAll' && $user_id == $admin){
		 sendAction($chat_id, 'typing');
		 file_put_contents('step.txt', 'None');
		 sendMessage($chat_id, 'در حال ارسال پیام شما ...', $msg_id);
		 $members = explode(',', $members);
		 $members_count = count($members)-1;
		 for($x = 0; $x <= $members_count; $x++){
			 sendMessage($members[$x], $msg_text, null);
		 }
		 sendAction($chat_id, 'typing');
		 bot('sendMessage',[
		 'chat_id'=>$chat_id,
		 'text'=>"✉️پیام شما برای $members_count کاربر ارسال گردید✅",
		 'reply_to_message_id'=>$msg_id,
		 'reply_markup'=>json_encode(['keyboard'=>[
		 [['text'=>'📊آمار'],['text'=>'📣تنظیم کانال']],
		 [['text'=>'🚀هدایت همگانی'],['text'=>'🗣پیام همگانی']],
		 [['text'=>'🔙صفحۀ اصلی']],
		 ],
		 'resize_keyboard'=>true
		 ])
		 ]);
	 }
	 elseif ($step == 'ForwardToAll' && $user_id == $admin){
		 sendAction($chat_id, 'typing');
		 file_put_contents('step.txt', 'None');
		 sendMessage($chat_id, 'در حال هدایت پیام شما ...', $msg_id);
		 $members = explode(',', $members);
		 $members_count = count($members)-1;
		 for($x = 0; $x <= $members_count; $x++){
			 forwardMessage($members[$x], $chat_id, $msg_id);
		 }
		 sendAction($chat_id, 'typing');
		 bot('sendMessage',[
		 'chat_id'=>$chat_id,
		 'text'=>"🚀پیام شما برای $members_count کاربر هدایت شد✅",
		 'reply_to_message_id'=>$msg_id,
		 'reply_markup'=>json_encode(['keyboard'=>[
		 [['text'=>'📊آمار'],['text'=>'📣تنظیم کانال']],
		 [['text'=>'🚀هدایت همگانی'],['text'=>'🗣پیام همگانی']],
		 [['text'=>'🔙صفحۀ اصلی']],
		 ],
		 'resize_keyboard'=>true
		 ])
		 ]);
	 }
	 else
		 forwardMessage($chat_id, $channel, forwardMessage($channel, $chat_id, $msg_id)->result->message_id);
?>
