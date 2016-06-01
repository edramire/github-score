@extends('layout.app')

@section('content')

@include('userScore.form_one')


@if (isset($message) || isset($userScore))

	@if (isset($message))
	<!--Error message-->
	<p>{{$message}}</p>
	@else

	<div class="container">
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
	</div>

	@endif

@endif

@endsection