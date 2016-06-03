@extends('layout.app')

@section('content')

<div class="container">
@if (empty($scores))
	<div class="well">
		<h3>There aren't scores registered yet.</h3>
	</div>
@else
	<table class="table">
		<tr>
			<th>Username</td>
			<th>Events Score</td>
			<th>Followers</td>
			<th>Total stars</td>
			<th>Total score</td>
			<th>Updated at</td>
		</tr>

		@foreach ($scores as $score)
		<tr>
			<td>{{$score->username}}</td>
			<td>{{$score->eventScore}}</td>
			<td>{{$score->followers}}</td>
			<td>{{$score->stars}}</td>
			<td>{{$score->score}}</td>
			<td>{{$score->updated_at}}</td>
		</tr>
		@endforeach	
		
	</table>	
@endif
</div>

@endsection