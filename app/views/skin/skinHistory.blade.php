@extends('layout.main')

@section('content')

	<div class="container">
		<div class="row">
			<div class="page-header">
				<h1>Skin History</h1>
			</div>
		</div>
	</div>

	@foreach($rp_range as $rp)

		<div class="col-lg-6 col-md-6 col-sm-6">

			<div class="panel-default panel">
				<div class="panel-heading">
					<h3 class="panel-title text-center">

						@if ($rp->rp==1820)
							3250 RP &amp; {{$rp->rp}} RP
						@else
							{{$rp->rp}} RP
						@endif

					</h3>
				</div>

				<table class="table table-bordered table-hover table-condensed">
					<thead>
						<tr>
							<th class="text-center">Skin</th>
							<th class="text-center">Passed Days</th>
							<th class="text-center">Est. Sale Date</th>
						</tr>
					</thead>
					<tbody>
					
						@foreach(${"rp".$rp->rp} as $skin)

							<tr>

								<td>
									
									<a href="{{ URL::to('skin', $skin->skin) }}">
										{{$skin->set}}
									</a>
									<a href="{{ URL::to('champion', $skin->champion) }}">
										<span class="champ">{{$skin->champion}}</span>
									</a>
									
								</td>
								
								<td class="text-center">{{$skin->days_past}}</td>


									@if ($skin->status == 2)
										<td class="text-center">Blacklisted</td>
									@else
										<!-- expected dates -->
										@if ($skin->active == 1)
											<td class="text-center">On Sale Now!</td>
										@elseif ($skin->passed == 1) 
											<td class="text-center">Just Passed</td>
										@else

											@if ($skin->expected_sale_date_raw >= $today)
												<td class="text-center" id="hoverOver" data-original-title="Tooltip on top" >{{$skin->expected_sale_date}}</td>
											@else 
												<td class="text-center">Soon</td>
											@endif

										@endif



									@endif


								

							</tr>

						@endforeach

				</table>

			</div>
		</div>

	@endforeach

@stop

@section('js')
	<script>
		$( document ).ready(function() {

			$( "#hoverOver" ).each(function( index ) {
				$(this).tooltip({
			    	placement: "top"
				});
			});

		});
	</script>
@stop