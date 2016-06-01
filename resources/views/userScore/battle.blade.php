@extends('layout.app')

@section('content')

@include('userScore.form_two')

@if (isset($message) || (isset($userScore1) && isset($userScore2)))

	@if (isset($message))
	<!--Error message-->
	<p>{{$message}}</p>
	@else

	<div class="container">

	@if (isset($winner))
		<div class="well">
			<h3>Winner is {{$winner->name}}.</h3>
		</div>
	@else
		<div class="well">
			<h3>Duelist are tied.</h3>
		</div>
	@endif

	<table class="table">
		<tr>
			<th>Username</td>
			<td>{{$userScore1->name}}</td>
			<td>{{$userScore2->name}}</td>
		</tr>
		<tr>
			<th>Events Score</td>
			<td>{{$userScore1->eventScore}}</td>
			<td>{{$userScore2->eventScore}}</td>
		</tr>
		<tr>
			<th>Followers</td>
			<td>{{$userScore1->followers}}</td>
			<td>{{$userScore2->followers}}</td>
		</tr>
		<tr>
			<th>Total stars</td>
			<td>{{$userScore1->stars}}</td>
			<td>{{$userScore2->stars}}</td>
		</tr>
		<tr>
			<th>Total score</td>
			<td>{{$userScore1->totalScore()}}</td>
			<td>{{$userScore2->totalScore()}}</td>
		</tr>

	</table>
	</div>

	@endif

@endif

@endsection