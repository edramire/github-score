@extends('layout.app')

@section('content')

@include('score.form_one')

@if (isset($message) || isset($score))

	<div class="container">

	@if (isset($message))
	<!--Error message-->
	<div class="alert alert-danger" role="alert">{{$message}}</div>
	@else

	<table class="table">
		<tr>
			<th>Username</td>
			<td>{{$score->username}}</td>
		</tr>
		<tr>
			<th>Events Score</td>
			<td>{{$score->eventScore}}</td>
		</tr>
		<tr>
			<th>Followers</td>
			<td>{{$score->followers}}</td>
		</tr>
		<tr>
			<th>Total stars</td>
			<td>{{$score->stars}}</td>
		</tr>
		<tr>
			<th>Total score</td>
			<td>{{$score->score}}</td>
		</tr>

	</table>

	@endif
	</div>

@endif

@endsection