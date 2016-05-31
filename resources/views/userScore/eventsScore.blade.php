@extends('layout.app')

@section('content')

@include('userScore.form_one')

@if (!is_null($message))
<!--Message error-->
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
</table>
</div>

@endif

@endsection