<div class="container">
<form action="{{ url('battle') }}" method="POST" class="form-horizontal">
    {{ csrf_field() }}	
	
	<div class="form-group">
		<label for="username1" class="col-sm-3 control-label">Username 1</label>
        <div class="col-sm-6">
			<input type="text" class="form-control" id="username1" name='username1'>
        </div>
	</div>
	
	<div class="form-group">
		<label for="username2" class="col-sm-3 control-label">Username 2</label>
        <div class="col-sm-6">
			<input type="text" class="form-control" id="username2" name='username2'>
        </div>
	</div>

	<div class="form-group">
        <div class="col-sm-offset-3 col-sm-6">	
			<button type="submit" class="btn btn-default">
				<i class="fa fa-chain-broken"></i> Begin Battle Score
			</button>
		</div>
	</div>

</form>

</div>