<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\UserScore;

class UserScoreController extends Controller
{
    public function index()
    {
    	return view('userScore.index');
    }

    public function eventsScore(Request $request)
    {
    	$username = $request->input('username');
    	$result = $this->readEventsScore($username);
		$message = NULL;
		$score = 0;
    	if(is_string($result)){
    		$message = $result;
    	}else{
    		$score = $result;
    	}

    	$userScore = new UserScore(['name'=>$username, 'eventScore'=>$score]);

    	return view('userScore.eventsScore',[
    		'userScore'=>$userScore,
    		'message'=>$message
    		]);
    }


    private function readEventsScore($username)
    {
		//crear opciones de contexto, exigido por github
		$opts = [
		    'http' => [
		        'method' => 'GET',
		        'header' => [
		            'User-Agent: PHP'
		        ]
		    ]
		];

		$context = stream_context_create($opts);
		$url = "https://api.github.com/users/".$username."/events";
		
		$content = file_get_contents($url, false, $context);
		$jsonArray = json_decode($content,true);

		if(isset($jsonArray['message'])){
			return $jsonArray['message'];
		}

		$score = 0;

		foreach ($jsonArray as $event) {
			$string = $event['type'];
			switch ($string) {
				case 'PushEvent':
					$score += 5;
					break;
				case 'CreateEvent':
					$score += 4;
					break;
				case 'IssuesEvent':
					$score += 3;
					break;
				case 'CommitCommentEvent':
					$score += 2;
					break;
				default:
					$score += 1;
					break;
			}
		}

		return $score;
    }
}
