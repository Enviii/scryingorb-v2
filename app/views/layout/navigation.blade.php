<div class="navbar navbar-default navbar-static-top">
	<!-- navbar-collapse collapse navbar-inverse-collapse -->
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="{{ URL::route('home') }}"><strong>Scrying Orb</strong></a>
		</div>
		<div class="navbar-collapse collapse">
			<ul class="nav navbar-nav">
				<li class="hidden-xs hidden-sm"><a href="{{ URL::route('home') }}">Home</a></li>
				<li class="hidden-xs hidden-sm"><a href="{{ URL::route('championHistory') }}">Champion History</a></li>
				<li class="hidden-xs hidden-sm"><a href="{{ URL::route('skinHistory') }}">Skin History</a></li>

			</ul>
			<form class="navbar-form navbar-right" id="the-basics">
				<input type="text" class="form-control col-lg-8 typeahead" id="type1" placeholder="Champs/Skins">

			</form>
		</div><!--/.navbar-collapse -->
	</div>
</div>