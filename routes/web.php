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
	// fwrite($reqLog, date('Y-m-dd').'this from GET'.PHP_EOL);
	fclose($reqLog);

	$userName = $req->get('user_name');
	$userText = $req->get('text');
	// Parse user text into date
	// If not found $userText = today
	if(empty($userText) || false){
		// $userText = date('d');
		$userText = 'today';
	}

	$menusFileCache = base_path('menus.json');
	$menus = json_decode(file_get_contents($menusFileCache), true);

	// Check menu find out menu[0]
	$menu = $menus[0];

	$dishes = collect($menu['dishes']);
	$dishes = $dishes->map(function($dish){
		$tmp = [
			'title' => $dish['name'],
			'value' => $dish['price'],
			'short' => true
		];

		return $tmp;
	});

	$slackMsg = [
		'text' => "Hi, @{$userName}, menu for {$userText}",
		'attachments' => [
			[
				'text' => 'Quan Chanh Cam Tuyet',
				'fields' => $dishes
			]
		]	
	];

	$reqLog = fopen('req.log', 'a');
	fwrite($reqLog, date('Y-m-dd').json_encode($slackMsg).PHP_EOL);
	// fwrite($reqLog, date('Y-m-dd').'this from GET'.PHP_EOL);
	fclose($reqLog);

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
	
	return response($slackMsg, 200, ['Content-Type' => 'application/json']);
});

// 2016-11-01
// @bug POST not work, only GET
// Slack custom command ONLY SEND OUT GET
// Route::post('', function(Request $req){
// 	$requestInfo = json_encode($req->all());

// 	// Handle log
// 	$reqLog = fopen('req.log', 'a');
// 	fwrite($reqLog, date('Y-m-dd').$requestInfo.PHP_EOL);
// 	fclose($reqLog);

// 	return response(['text' => 'hello ban hien'], 200, ['Content-Type' => 'application/json']);
// });

Route::post('menu', function(Request $req){

});

