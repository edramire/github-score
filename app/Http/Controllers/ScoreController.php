<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Score;
use DB;
use GuzzleHttp\Client;

class ScoreController extends Controller
{

    public function index()
    {
        return view('score.index');
    }

    public function scoreForm()
    {
        return view('score.score');
    }

    public function score(Request $request)
    {
        $username = $request->input('username');
        $result = $this->getScoreArray($username);
        $score = null;

        if (is_null($result['message'])) {
            $score = $this->firstOrCreateUser($result['score']);
        }

        return view('score.score', [
            'score' => $score,
            'message' => $result['message']
            ]);
    }

    public function battleForm(Request $request)
    {
        return view('score.battle');
    }

    public function battle(Request $request)
    {
        $username1 = $request->input('username1');
        $result1 = $this->getScoreArray($username1);

        $username2 = $request->input('username2');
        $result2 = $this->getScoreArray($username2);

        $message = null;
        $winner = null;
        $score1 = null;
        $score2 = null;

        if (!is_null($result1['message'])||!is_null($result2['message'])) {
            $message = $result1['message']." | ".$result2['message'];
        } else {
            $score1 = $this->firstOrCreateUser($result1['score']);
            $score2 = $this->firstOrCreateUser($result2['score']);

            if ($score1['score'] > $score2['score']) {
                $winner = $score1;
            } else if ($score2['score'] > $score1['score']) {
                $winner = $score2;
            } else {
                $winner = null;
            }
        }

        return view('score.battle', compact('winner', 'score1', 'score2', 'message'));
    }

    public function getAll()
    {
        $scores = DB::table('scores')->orderBy('score', 'desc')->get();

        return view('score.all', [
            'scores'=>$scores,
            ]);
    }

    /* inicio funciones de ayuda */

    private function firstOrCreateUser($scoreData)
    {
        $score = Score::firstOrNew(['username' => $scoreData['username']]);
        $score->eventScore = $scoreData['eventScore'];
        $score->followers = $scoreData['followers'];
        $score->stars = $scoreData['stars'];
        $score->save();
        return $score;
    }

    private function getScoreArray($username)
    {
        $result1 = $this->readEventsScore($username);
        $result2 = $this->readUserFollowers($username);
        $result3 = $this->readUserStars($username);

        $message = null;
        $eventScore = 0;
        $stars = 0;
        $followers = 0;

        if (is_string($result1)) {
            $message = $result1;
        } else {
            $eventScore = $result1;
        }

        if (is_string($result2)) {
            $message = $result2;
        } else {
            $followers = $result2;
        }

        if (is_string($result3)) {
            $message = $result3;
        } else {
            $stars = $result3;
        }

        return ['score' => compact('username', 'eventScore', 'stars', 'followers'), 'message' => $message];
    }

    private function readEventsScore($username)
    {
        $url = "https://api.github.com/users/".$username."/events";
        $res = $this->connectToGitHub($url);
        $code = $res->getStatusCode();

        if ($code != "200") {
            return "Error:".$code;
        }

        $jsonArray = json_decode($res->getBody(), true);

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

    private function readUserFollowers($username)
    {
        $url = "https://api.github.com/users/".$username;
        $res = $this->connectToGitHub($url);
        $code = $res->getStatusCode();

        if ($code != "200") {
            return "Error:".$code;
        }

        $jsonArray = json_decode($res->getBody(), true);

        $followers = $jsonArray['followers'];

        return $followers;
    }

    private function readUserStars($username)
    {
        $url = "https://api.github.com/users/".$username."/repos";
        $res = $this->connectToGitHub($url);
        $code = $res->getStatusCode();

        if ($code != "200") {
            return "Error:".$code;
        }

        $jsonArray = json_decode($res->getBody(), true);

        $totalStars = 0;
        foreach ($jsonArray as $repo) {
            $totalStars += intval($repo['stargazers_count']);
        }

        return $totalStars;
    }

    private function connectToGitHub($url)
    {
        return (new Client())->request('GET', $url, [
            'auth' => [env('GITHUB_USERNAME'), env('GITHUB_PASSWORD')],
            'http_errors' => false,
        ]);
    }
}
