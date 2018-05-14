<style>
	a:hover {
		text-decoration: none;
	}
</style>

<div class="widget widget-stats bg-grey">
	{{-- info/data --}}
	<div class="stats-info d-flex">
		<div class="btn btn-dark m-5 m-r-10">
			{!! HTML::decode (HTML::linkRoute('Contract.create',
				'<h3><div class="text-center"><i style="color: white;" class="img-center fa fa-address-book-o"></i></div></h3>
				<div style="color: white;" class="username text-ellipsis text-center">Add Contract</div>'))
			!!}
		</div>
		<div class="btn btn-dark m-5 m-r-10 m-l-10">
			{!! HTML::decode (HTML::linkRoute('Ticket.create',
				'<h3><div class="text-center" style="color: white;"><i class="img-center fa fa-ticket"></i></div></h3>
				<div style="color: white;" class="username text-ellipsis text-center">Add Ticket</div>'))
			!!}
		</div>
		<div class="btn btn-dark m-5 m-l-10">
			{!! HTML::decode (HTML::linkRoute('CustomerTopo.show_bad',
				'<h3><div class="text-center" style="color: white;"><i class="img-center fa fa-hdd-o text-danger"></i></div></h3>
				<div style="color: white;" class="username text-ellipsis text-center">Bad Modems</div>'))
			!!}
		</div>
    </div>
    {{-- reference link --}}
	<div class="stats-link"><a href="#">Schnellstart</a></div>
</div>

