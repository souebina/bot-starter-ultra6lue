<?php

require_once __DIR__ . '/vendor/autoload.php';

$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('CHANNEL_SECRET')]);

$signature = $_SERVER["HTTP_" . \LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];
try {
  $events = $bot->parseEventRequest(file_get_contents('php://input'), $signature);
} catch(\LINE\LINEBot\Exception\InvalidSignatureException $e) {
  error_log("parseEventRequest failed. InvalidSignatureException => ".var_export($e, true));
} catch(\LINE\LINEBot\Exception\UnknownEventTypeException $e) {
  error_log("parseEventRequest failed. UnknownEventTypeException => ".var_export($e, true));
} catch(\LINE\LINEBot\Exception\UnknownMessageTypeException $e) {
  error_log("parseEventRequest failed. UnknownMessageTypeException => ".var_export($e, true));
} catch(\LINE\LINEBot\Exception\InvalidEventRequestException $e) {
  error_log("parseEventRequest failed. InvalidEventRequestException => ".var_export($e, true));
}

foreach ($events as $event) {

  if ($event instanceof \LINE\LINEBot\Event\PostbackEvent) {
  replyTextMessage($bot, $event->getReplyToken(), "Postback受信「" . $event->getPostbackData() . "」");
  continue;
}

  if (!($event instanceof \LINE\LINEBot\Event\MessageEvent)) {
    error_log('Non message event has come');
    continue;
  }
  if (!($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage)) {
    error_log('Non text message has come');
    continue;
  }
  # $bot->replyText($event->getReplyToken(), $event->getText());

#$profile = $bot->getProfile($event->getUserId())->getJSONDecodedBody();
#$message = $profile["displayName"] . "さん、おはようございます！今日も頑張りましょう！";
#$bot->replyMessage($event->getReplyToken(),
#  (new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder())
#    ->add(new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message))
#    ->add(new \LINE\LINEBot\MessageBuilder\StickerMessageBuilder(1, 114))
#);

# 1.テキストメッセージの送信
# replyTextMessage($bot, $event->getReplyToken(), "TextMessage");

# 2.画像の送信
# replyImageMessage($bot, $event->getReplyToken(), "https://" . $_SERVER["HTTP_HOST"] . "/imgs/original.jpg", "https://" . $_SERVER["HTTP_HOST"] . "/imgs/preview.jpg");

# 3.位置情報の送信
# replyLocationMessage($bot, $event->getReplyToken(), "LINE", "東京都渋谷区渋谷2-21-1 ヒカリエ27階", 35.659025, 139.703473);

# 4.スタンプの送信
# replyStickerMessage($bot, $event->getReplyToken(), 1, 1);

# 5.複数メッセージの送信
# replyMultiMessage($bot, $event->getReplyToken(),
#    new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("TextMessage"),
#    new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder("https://" . $_SERVER["HTTP_HOST"] . "/imgs/original.jpg", "https://" . $_SERVER["HTTP_HOST"] . "/imgs/preview.jpg"),
#    new \LINE\LINEBot\MessageBuilder\LocationMessageBuilder("LINE", "東京都渋谷区渋谷2-21-1 ヒカリエ27階", 35.659025, 139.703473),
#    new \LINE\LINEBot\MessageBuilder\StickerMessageBuilder(1, 1)
#  );

# 6.ボタンテンプレートの送信
# replyButtonsTemplate($bot,
#    $event->getReplyToken(),
#    "お天気お知らせ - 今日は天気予報は晴れです",
#    "https://" . $_SERVER["HTTP_HOST"] . "/imgs/template.jpg",
#    "お天気お知らせ",
#    "今日は天気予報は晴れです",
#    new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder (
#      "明日の天気", "tomorrow"),
#    new LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder (
#      "週末の天気", "weekend"),
#    new LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder (
#      "Webで見る", "http://google.jp")
#    );
#}

# 7. Comfirmテンプレートの送信
replyConfirmTemplate($bot,
    $event->getReplyToken(),
    "Webで詳しく見ますか？",
    "Webで詳しく見ますか？",
    new LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder (
      "見る", "http://google.jp"),
    new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder (
      "見ない", "ignore"),
    new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder (
      "非表示", "never")
    );

# 1.テキストメッセージの送信には、Bot->replyText関数が手軽ですが、他のメッセージと合わせて送ることも考慮しTextMessageBuilderを使って送信しましょう
function replyTextMessage($bot, $replyToken, $text) {
  $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text));
  if (!$response->isSucceeded()) {
    error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
  }
}

# 2.画像の送信には、ImageMessageBuilderを使います
function replyImageMessage($bot, $replyToken, $originalImageUrl, $previewImageUrl) {
  $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($originalImageUrl, $previewImageUrl));
  if (!$response->isSucceeded()) {
    error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
  }
}

# 3.位置情報の送信には、LocationMessageBuilderを使います
function replyLocationMessage($bot, $replyToken, $title, $address, $lat, $lon) {
  $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\LocationMessageBuilder($title, $address, $lat, $lon));
  if (!$response->isSucceeded()) {
    error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
  }
}

# 4.スタンプの送信には、StickerMessageBuilderを使います
function replyStickerMessage($bot, $replyToken, $packageId, $stickerId) {
  $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\StickerMessageBuilder($packageId, $stickerId));
  if (!$response->isSucceeded()) {
    error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
  }
}

# 5.MultiMessageBuilderをreplyMessage関数に渡すことで、最大5個のメッセージを組み合わせて送信することができます
function replyMultiMessage($bot, $replyToken, ...$msgs) {
  $builder = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
  foreach($msgs as $value) {
    $builder->add($value);
  }
  $response = $bot->replyMessage($replyToken, $builder);
  if (!$response->isSucceeded()) {
    error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
  }
}

# 6.Buttonsテンプレートは、画像、タイトル、テキスト、アクションの配列で構成されています。このうち画像とタイトルは省略可能です。省略した時は引数にnullを指定します
function replyButtonsTemplate($bot, $replyToken, $alternativeText, $imageUrl, $title, $text, ...$actions) {
  $actionArray = array();
  foreach($actions as $value) {
    array_push($actionArray, $value);
  }
  $builder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
    $alternativeText,
    new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder ($title, $text, $imageUrl, $actionArray)
  );
  $response = $bot->replyMessage($replyToken, $builder);
  if (!$response->isSucceeded()) {
    error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
  }
}

# 7.ComfirmテンプレートはYES／NOやOK／キャンセルなどのシンプルなテキスト＋ボタンタイプのダイアログを出すテンプレートとなっています
function replyConfirmTemplate($bot, $replyToken, $alternativeText, $text, ...$actions) {
  $actionArray = array();
  foreach($actions as $value) {
    array_push($actionArray, $value);
  }
  $builder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
    $alternativeText,
    new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder ($text, $actionArray)
  );
  $response = $bot->replyMessage($replyToken, $builder);
  if (!$response->isSucceeded()) {
    error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
  }
}

 ?>
