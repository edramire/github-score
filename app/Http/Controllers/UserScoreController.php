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

    public function score_form()
    {
		return view('userScore.score');    		
    }

    public function score(Request $request)
    {
    	$username = $request->input('username');

    	$result = $this->getUserScore($username);

    	return view('userScore.score',[
    		'userScore'=>$result['userScore'],
    		'message'=>$result['message']
    		]);
    }

    public function battle_form(Request $request){
    	return view('userScore.battle');
    }

    public function battle(Request $request){
		$username1 = $request->input('username1');
    	$result1 = $this->getUserScore($username1);
    	
    	$username2 = $request->input('username2');
    	$result2 = $this->getUserScore($username2);

    	$message=NULL;
    	if(!is_null($result1['message'])||!is_null($result2['message'])){
    		$message = $result1['message'].$result2['message'];
    	}

    	if($result1['userScore']->totalScore() > $result2['userScore']->totalScore()){
    		$winner = $result1['userScore'];
    	}else if($result1['userScore']->totalScore() < $result2['userScore']->totalScore()){
    		$winner = $result2['userScore'];
    	}else{
    		$winner = NULL;
    	}

    	return view('userScore.battle',[
    		'winner'=>$winner,
    		'userScore1'=>$result1['userScore'],
    		'userScore2'=>$result2['userScore'], 
    		'message'=>$message
    		]);
    }


    private function getUserScore($username)
    {
    	$result1 = $this->readEventsScore($username);
    	$result2 = $this->readUserFollowers($username);
    	$result3 = $this->readUserStars($username);

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
    		$followers = $result2;
    	}

    	if(is_string($result3)){
    		$message = $result3;
    	}else{
    		$stars = $result3;
    	}
    	
    	$userScore = new UserScore([
    		'name'=>$username, 
    		'eventScore'=>$score, 
    		'stars'=>$stars, 
    		'followers'=>$followers
    		]);

    	return array(
    		'userScore' => $userScore,
    		'message' => $message
    		);
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

    private function readUserFollowers($username)
    {
		$url = "https://api.github.com/users/".$username;
		$jsonArray = $this->getJsonArray($url);			

		if(isset($jsonArray['message'])){
			return $jsonArray['message'];
		}

		$followers = $jsonArray['followers'];

		return $followers;
    }

    private function readUserStars($username){
    	$url = "https://api.github.com/users/".$username."/repos";
		$jsonArray = $this->getJsonArray($url);

		if(isset($jsonArray['message'])){
			return $jsonArray['message'];
		}

		$totalStars = 0;
		foreach ($jsonArray as $repo) {
			$totalStars += intval($repo['stargazers_count']);
		}

		return $totalStars;
    }
}
