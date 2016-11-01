<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Illuminate\Http\Request;

Route::get('', function(Request $req){
	$requestInfo = json_encode($req->all());
	// {
	// 	"token":"o9G92qIXH1WPrIufVfNYIGf9",
	// 	"team_id":"T0267LQRD",
	// 	"team_domain":"originally",
	// 	"channel_id":"D276QQFKN",
	// 	"channel_name":"directmessage",
	// 	"user_id":"U276TC8K0",
	// 	"user_name":"hoanganh25991",
	// 	"command":"\/menu",
	// 	"text":"",
	// 	"response_url":"https:\/\/hooks.slack.com\/commands\/T0267LQRD\/98689833444\/6sC0UlMGrthOD5kr17zbfcsl"
	// }
	// // Handle log
	$reqLog = fopen('req.log', 'a');
	fwrite($reqLog, date('Y-m-dd').$requestInfo.PHP_EOL);
	fclose($reqLog);

	$userName = $req->get('user_name');

	$acceptedUserCommand = collect(['menu', 'order']);
	$userText = preg_replace("/\s+/", " ", $req->get('text'));
	$userTextArr = explode(' ', $userText);
	$userTextArr['user_name'] = $userName;
	if(!$acceptedUserCommand->contains($userTextArr[0])){
		$userTextArr = ['menu', 'today'];
	}
	// Parse user text into date
	// If not found $userText = today
	if($userTextArr[0] == 'menu' || false){
		// $userText = date('d');
		$userTextArr[1] = 'today';
	}

	$response = ['text' => 'i hear you'];
	switch($userTextArr[0]){
		case 'menu':
			if(!isset($userTextArr[1])){
				$userTextArr[1] = 'today';
			}

			$response = loadMenu($userTextArr);
			break;
		case 'order':
			$reponse = 'in develop process';
			break;
	}

	return response($response, 200, ['Content-Type' => 'application/json']);
});

// 2016-11-01
// @bug POST not work, only GET
// Slack custom command ONLY SEND OUT GET
function loadMenu($userTextArr){
	// $userName = $req->get('user_name');
	// $userText = $req->get('text');
	$menusFileCache = base_path('menus.json');
	$menus = json_decode(file_get_contents($menusFileCache), true);

	// Check menu find out menu[0]
	$menu = $menus[0];

	$dishes = collect($menu['dishes']);
	$dishes = $dishes->map(function($dish, $index){
		// $tmp = [
		// 	'value' => "[{$index}]{$dish['name']}\t{$dish['price']}",
		// 	'short' => true
		// ];
		
		$tmp = [
			'fields' => [
				[
					'value' =>  "[{$index}] {$dish['name']}\t\t\t{$dish['price']},000",
					'short' => true
				]
			],
			"color" => "good"
		];

		return $tmp;
	});

	// {
	// 	"text": "Jenskin console output",
	// 	"attachments": [
	//         {
	//             "fallback": "ReferenceError - UI is not defined: https://honeybadger.io/path/to/event/",
	//             "text": "<https://honeybadger.io/path/to/event/|ReferenceError> - UI is not defined",
	//             "fields": [
	//                 {
	//                     "title": "Project",
	//                     "value": "Awesome Project",
	//                     "short": true
	//                 },
	//                 {
	//                     "title": "Environment",
	//                     "value": "production",
	//                     "short": true
	//                 }
	//             ],
	//             "color": "good"
	//             // "color": "warning"
	//         }
	//     ]
	// }
	$slackMsg = [
		'text' => "Hi, @{$userTextArr['user_name']}, menu for {$userTextArr[1]}\nQuan Chanh Cam Tuyet\n`Type /lunch order [num] to order`>",
		'attachments' => $dishes
	];

	$reqLog = fopen('req.log', 'a');
	fwrite($reqLog, date('Y-m-dd').json_encode($slackMsg).PHP_EOL);
	// fwrite($reqLog, date('Y-m-dd').'this from GET'.PHP_EOL);
	fclose($reqLog);

	return $slackMsg;
}

