@extends('layout.main')

@section('content')
    <div class="container" id="buttonWell">
		<div id="headerButton" class="row">
			
			@if ($today=="Monday" || $today=="Thursday")

				<div class="col-md-12">
					<div id="old_sale" class="col-xs-4"> 
						<button id="old_sale" class="btn btn-default btn-block">Last Sale <br><small>()</small></button>
					</div>

					<div id="current_sale" class="col-xs-4">
						<button id="current_sale" class="btn btn-primary btn-block">Current Sale <br><small>()</small></button>
					</div>

					<div id="next_sale" class="col-xs-4">
						<button id="next_sale" class="btn btn-default btn-block">Next Sale <br><small>May 31 to Jun 2</small></button>
					</div>
				</div>

			@else

				<div class="col-md-12">
					<div id="old_sale" class="col-md-4 col-md-offset-1 col-sm-4 col-sm-offset-2 col-xs-6"> 
						<button id="old_sale" class="btn btn-default btn-block">Last Sale <br><small>()</small></button>
					</div>

					<div id="current_sale" class="col-md-4 col-md-offset-2 col-sm-4 col-xs-6">
						<button id="current_sale" class="btn btn-primary btn-block">Current Sale <br><small>()</small></button>
					</div>
				</div>

			@endif

		</div>
	</div> <!-- end buttonWell container -->

	<br>


	<div id="showSelection">
		@include('saleContent')
	</div> <!-- / #showSelection -->


@stop

@section('js')
	<script>
		$( document ).ready(function() {

		    console.log( "ready!" );

		    function getCurrentSale() {

				$.get("{{URL::route('getCurrentSale')}}", function(data){
					$("#showSelection").html(data);
				});
				
		    }

		    

		    function getOldSale() {

				$.get("{{URL::route('getOldSale')}}", function(data){
					$("#showSelection").html(data);
				});

/*				$.ajax({
					type: "POST",
					url: "{{URL::route('getOldSale')}}",
					//data: data,
					dataType: "json",
					success: function(data) {
						console.log(data);

					}
				});	*/
		    }

		    $( "button#old_sale" ).click(function() {
				getOldSale();
			});

			$( "button#current_sale" ).click(function() {
				getCurrentSale();
			});

		});
	</script>
@stop