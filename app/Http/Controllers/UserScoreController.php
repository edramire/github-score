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

    public function score(Request $request)
    {
    	$username = $request->input('username');
    	$result1 = $this->readEventsScore($username);
    	$result2 = $this->readUserData($username);

		$message = NULL;
		$score = 0;
		$stars = 0;
		$followers = 0;

    	if(is_string($result1)){
    		$message = $result1;
    	}else{
    		$score = $result1;
    	}

    	if(is_string($result2)){
    		$message = $result2;
    	}else{
    		$followers = $result2['followers'];
    		$stars = $result2['stars'];
    	}

    	$userScore = new UserScore([
    		'name'=>$username, 
    		'eventScore'=>$score, 
    		'stars'=>$stars, 
    		'followers'=>$followers
    		]);

    	return view('userScore.score',[
    		'userScore'=>$userScore,
    		'message'=>$message
    		]);
    }


    private function readEventsScore($username)
    {
		$url = "https://api.github.com/users/".$username."/events";		
		$jsonArray = $this->getJsonArray($url);		

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

    private function getJsonArray($url){
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
		$content = file_get_contents($url, false, $context);
		$jsonArray = json_decode($content,true);

		return $jsonArray;
    }

    private function readUserData($username)
    {
		$url = "https://api.github.com/users/".$username;
		$jsonArray = $this->getJsonArray($url);			

		if(isset($jsonArray['message'])){
			return $jsonArray['message'];
		}

		$followers = $jsonArray['followers'];

		$url = "https://api.github.com/users/".$username."/repos";
		$jsonArray = $this->getJsonArray($url);

		if(isset($jsonArray['message'])){
			return $jsonArray['message'];
		}

		$totalStars = 0;

		foreach ($jsonArray as $repo) {
			$totalStars += intval($repo['stargazers_count']);
		}

		return array('followers'=>$followers, 'stars'=>$totalStars);
    }
}
