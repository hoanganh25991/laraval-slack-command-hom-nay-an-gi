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

Route::get('/', function () {
    return view('welcome');
});

Route::post('', function(Request $req){

	// return $req->get('name');
	// return 'hoanganh';
	$requestInfo = json_encode($req->all());

	// $curl = curl_init();

	// curl_setopt_array($curl, array(
	//   CURLOPT_URL => "https://hooks.slack.com/commands/1234/5678",
	//   CURLOPT_RETURNTRANSFER => true,
	//   CURLOPT_ENCODING => "",
	//   CURLOPT_MAXREDIRS => 10,
	//   CURLOPT_TIMEOUT => 30,
	//   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	//   CURLOPT_CUSTOMREQUEST => "POST",
	//   CURLOPT_POSTFIELDS => "-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"token\"\r\n\r\no9G92qIXH1WPrIufVfNYIGf9\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"text\"\r\n\r\n{$requestInfo}\r\n-----011000010111000001101001--",
	//   CURLOPT_HTTPHEADER => array(
	//     "cache-control: no-cache",
	//     "content-type: multipart/form-data; boundary=---011000010111000001101001",
	//     "postman-token: f3b3fa12-7bfc-185b-a748-0c98b250b3db"
	//   ),
	// ));

	// $response = curl_exec($curl);
	// $err = curl_error($curl);

	// curl_close($curl);

	// Handle log
	$reqLog = fopen('req.log', 'a');
	fwrite($reqLog, $requestInfo.PHP_EOL);
	// if ($err) {
	//   // echo "cURL Error #:" . $err;
	//   fwrite($reqLog, $err.PHP_EOL);
	// } else {
	//   // echo $response;
	//   fwrite($reqLog, $response.PHP_EOL);
	// }
	fclose($reqLog);

	return response(['text' => $requestInfo], 200, ['Content-Type' => 'application/json']);
});