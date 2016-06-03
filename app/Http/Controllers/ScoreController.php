<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Score;
use DB;

//use Log;


class ScoreController extends Controller
{
    private $sqlGetScore = "SELECT id, username, eventScore, followers, stars, score, updated_at FROM scores WHERE username = :username;";
    
    private $sqlCreateScore = "INSERT INTO scores(username, eventScore, followers, stars, score, updated_at) VALUES (:username, :eventScore, :followers, :stars, :score, :updated_at);";

    private $sqlUpdateScore = "UPDATE scores SET eventScore = :eventScore, followers = :followers, stars = :stars, score = :score, updated_at = :updated_at WHERE username = :username;";

    private $sqlGetAllScores = "SELECT id, username, eventScore, followers, stars, score, updated_at FROM scores";

    public function index()
    {
    	return view('score.index');
    }

    public function score_form()
    {
		return view('score.score');
    }

    public function score(Request $request)
    {
    	$username = $request->input('username');

    	$result = $this->getScore($username);
        if(is_null($result['message'])){
            $this->createOrUpdateScoreFromDB($result['score']);
        }

        //$score = Score::firstOrCreate(['username'=>$username]);
        
    	return view('score.score',[
    		'score'=>$result['score'],
    		'message'=>$result['message']
    		]);
    }

    public function battle_form(Request $request){
    	return view('score.battle');
    }

    public function battle(Request $request){
		$username1 = $request->input('username1');
    	$result1 = $this->getScore($username1);
    	
    	$username2 = $request->input('username2');
    	$result2 = $this->getScore($username2);

    	$message = NULL;
        $winner = NULL;
    	if(!is_null($result1['message'])||!is_null($result2['message'])){
    		$message = $result1['message']." | ".$result2['message'];
    	}else{
            $this->createOrUpdateScoreFromDB($result1['score']);
            $this->createOrUpdateScoreFromDB($result2['score']);

            if($result1['score']->getTotalScore() > $result2['score']->getTotalScore()){
                $winner = $result1['score'];
            }else if($result1['score']->getTotalScore() < $result2['score']->getTotalScore()){
                $winner = $result2['score'];
            }else{
                $winner = NULL;
            }
        }

    	return view('score.battle',[
    		'winner'=>$winner,
    		'score1'=>$result1['score'],
    		'score2'=>$result2['score'], 
    		'message'=>$message,
    		]);
    }

    public function getAll()
    {
        $results = $this->getAllScoresFromDB();

        $scores = array();

        foreach ($results as $index => $value) {
            $scores[$index] = new Score([
                'username'=>$value['username'], 
                'eventScore'=>$value['eventScore'], 
                'stars'=>$value['stars'], 
                'followers'=>$value['followers'],
                'score'=> $value['score'],
                'updated_at'=>$value['updated_at'],
            ]);
        }

        return view('score.all',[
            'scores'=>$scores,
            ]);
    }

    /* inicio funciones de ayuda */

    private function getScore($username)
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
    	
    	$score = new Score([
    		'username'=>$username, 
    		'eventScore'=>$score, 
    		'stars'=>$stars, 
    		'followers'=>$followers,
            'updated_at'=>date('Y-m-d H:i:s'),
    		]);

        $score->score = $score->getTotalScore();

    	return array(
    		'score' => $score,
    		'message' => $message
    		);
    }

    private function readEventsScore($username)
    {
		$url = "https://api.github.com/users/".$username."/events";
		$code = $this->get_http_response_code($url);
		if($code != "200"){
			return "Error:".$code;
		}
		$jsonArray = $this->getJsonArray($url);	

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
		$code = $this->get_http_response_code($url);
		if($code != "200"){
			return "Error:".$code;
		}
		$jsonArray = $this->getJsonArray($url);			

		$followers = $jsonArray['followers'];

		return $followers;
    }

    private function readUserStars($username){
    	$url = "https://api.github.com/users/".$username."/repos";
		$code = $this->get_http_response_code($url);
		if($code != "200"){
			return "Error:".$code;
		}
		$jsonArray = $this->getJsonArray($url);	
		
		$totalStars = 0;
		foreach ($jsonArray as $repo) {
			$totalStars += intval($repo['stargazers_count']);
		}

		return $totalStars;
    }

    private function get_http_response_code($url) {
		$opts = [
		    'http' => [
		        'method' => 'GET',
		        'header' => [
		            'User-Agent: PHP'
		        ]
		    ]
		];
    	stream_context_set_default($opts);
    	$headers = get_headers($url);
    	return substr($headers[0], 9, 3);
	}

    private function createOrUpdateScoreFromDB($score){
        $pdo = DB::connection()->getPdo();
        $query = $pdo->prepare($this->sqlGetScore);
        $query->execute([':username'=>$score->username]);
        $rows = $query->fetchAll();

        if(count($rows) > 0){
            $query2 = $pdo->prepare($this->sqlUpdateScore);
        }else{
            $query2 = $pdo->prepare($this->sqlCreateScore);
        }

        $query2->execute([
            ':username' => $score->username,
            ':eventScore' => $score->eventScore, 
            ':followers' => $score->followers, 
            ':stars' => $score->stars,
            ':score' => $score->score,
            ':updated_at' => $score->updated_at,
            ]);
    }


    private function getAllScoresFromDB(){
        $pdo = DB::connection()->getPdo();
        $query = $pdo->prepare($this->sqlGetAllScores);
        $query->execute();
        $rows = $query->fetchAll();
        return $rows;

    }
}
