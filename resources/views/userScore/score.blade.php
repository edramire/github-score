@extends('layout.app')

@section('content')

@include('userScore.form_one')

@if (isset($message) || isset($userScore))

	<div class="container">

	@if (isset($message))
	<!--Error message-->
	<div class="alert alert-danger" role="alert">{{$message}}</div>
	@else

	<table class="table">
		<tr>
			<th>Username</td>
			<td>{{$userScore->name}}</td>
		</tr>
		<tr>
			<th>Events Score</td>
			<td>{{$userScore->eventScore}}</td>
		</tr>
		<tr>
			<th>Followers</td>
			<td>{{$userScore->followers}}</td>
		</tr>
		<tr>
			<th>Total stars</td>
			<td>{{$userScore->stars}}</td>
		</tr>
		<tr>
			<th>Total score</td>
			<td>{{$userScore->totalScore()}}</td>
		</tr>

	</table>

	@endif
	</div>

@endif

@endsection