@extends('layout.app')

@section('content')

@include('score.form_two')

@if (isset($message) || (isset($score1) && isset($score2)))
	<div class="container">

	@if (isset($message))
	<!--Error message-->
	<div class="alert alert-danger" role="alert">{{$message}}</div>
	@else

	@if (isset($winner))
		<div class="well">
			<h3 class="text-center">{{$winner->username}} wins!</h3>
		</div>
	@else
		<div class="well">
			<h3 class="text-center">Duelist are tied.</h3>
		</div>
	@endif

	<table class="table">
		<tr>
			<th>Username</td>
			<td>{{$score1->username}}</td>
			<td>{{$score2->username}}</td>
		</tr>
		<tr>
			<th>Events Score</td>
			<td>{{$score1->eventScore}}</td>
			<td>{{$score2->eventScore}}</td>
		</tr>
		<tr>
			<th>Followers</td>
			<td>{{$score1->followers}}</td>
			<td>{{$score2->followers}}</td>
		</tr>
		<tr>
			<th>Total stars</td>
			<td>{{$score1->stars}}</td>
			<td>{{$score2->stars}}</td>
		</tr>
		<tr>
			<th>Total score</td>
			<td>{{$score1->score}}</td>
			<td>{{$score2->score}}</td>
		</tr>

	</table>

	@endif
	</div>

@endif

@endsection