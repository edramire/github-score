<div class="container">
<form action="{{ url('score') }}" method="POST" class="form-horizontal">
    {{ csrf_field() }}

	<div class="form-group">
		<label for="username" class="col-sm-3 control-label">Username</label>
        <div class="col-sm-6">
			<input type="text" class="form-control" id="username" name='username'>
        </div>
	</div>

	<div class="form-group">
        <div class="col-sm-offset-3 col-sm-6">	
			<button type="submit" class="btn btn-default">
				<i class="fa fa-search"></i> Find Score
			</button>
		</div>
	</div>
</form>

</div>