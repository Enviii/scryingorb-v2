@extends('layout.main')

@section('content')

	<div class="container">
		<div class="row">
			<div class="page-header">
				<h1>Sale History</h1>
			</div>
		</div>
	</div>

	<div class="container">
		<div class="row">

			@foreach($ip_range as $ip)

				@if ($divCount == 4)
					<div class="clearfix visible-lg visible-md visible-sm"></div>
				@endif

					<div class="col-lg-6 col-md-6 col-sm-6">

						<div class="panel-default panel">
							<div class="panel-heading">
								<h3 class="panel-title text-info">{{$ip->ip}} IP</h3>
							</div>

							<table class="table table-bordered table-hover table-condensed">
								<thead>
									<tr>
										<th class="text-center">Champion</th>
										<th class="text-center">Passed Days</th>
										<th class="text-center">Est. Sale Date</th>
									</tr>
								</thead>
								<tbody>
									@foreach(${"ip".$ip->ip} as $champ)

										<tr>

											<td>
												<a href="{{ URL::to('champion', $champ->champion) }}">
													<span class="champ">{{$champ->champion}}</span>
													
													<a href="http://gameinfo.na.leagueoflegends.com/en/game-info/champions/{{ cleanandlower($champ->champion)}}">
														<span class="super-small"> riot</span>
													</a>

													<span class="super-small"> &#8226; </span>
													
													<a href="http://leagueoflegends.wikia.com/wiki/{{ clean($champ->champion)}}">
														<span class="super-small"> wikia</span>
													</a>

												</a>
											</td>

											<td class="text-center">{{$champ->days_past}}</td>


											@if ($champ->status == 2)
												<td class="text-center">Blacklisted</td>
											@else
												<!-- expected dates -->
												@if ($champ->active == 1)
													<td class="text-center">On Sale Now!</td>
												@elseif ($champ->passed == 1) 
													<td class="text-center">Just Passed</td>
												@else

													@if ($champ->expected_sale_date_raw >= $today)
														<td class="text-center" id="hoverOver" data-original-title="Tooltip on top" >{{$champ->expected_sale_date}}</td>
													@else 
														<td class="text-center">Soon</td>
													@endif

												@endif

											@endif

										</tr>

									@endforeach
								</tbody>
							</table>
						</div>
					</div>

				<?php $divCount+=1; ?>

			@endforeach

			
		</div>
	</div>
@stop