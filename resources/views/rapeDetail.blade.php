@extends('layouts.base')

@section('title', 'Rapé - Dobrej Matroš')

@section('scriptsInclude')
<script async custom-element="amp-carousel" src="https://cdn.ampproject.org/v0/amp-carousel-0.1.js"></script>
<script async custom-element="amp-form" src="https://cdn.ampproject.org/v0/amp-form-0.1.js"></script>
<script async custom-element="amp-lightbox" src="https://cdn.ampproject.org/v0/amp-lightbox-0.1.js"></script>				
<!-- <script async custom-element="amp-video" src="https://cdn.ampproject.org/v0/amp-video-0.1.js"></script>   					 -->
<script async custom-element="amp-youtube" src="https://cdn.ampproject.org/v0/amp-youtube-0.1.js"></script>
<script async custom-element="amp-form" src="https://cdn.ampproject.org/v0/amp-form-0.1.js"></script>
<script async custom-element="amp-access" src="https://cdn.ampproject.org/v0/amp-access-0.1.js"></script>
<script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>
<script async custom-element="amp-list" src="https://cdn.ampproject.org/v0/amp-list-0.1.js"></script>
<script async custom-template="amp-mustache" src="https://cdn.ampproject.org/v0/amp-mustache-0.2.js"></script>
<script async custom-element="amp-bind" src="https://cdn.ampproject.org/v0/amp-bind-0.1.js"></script>
@endsection
@section('description')
	<meta name="description" content="XXXXXXXXX">
@endsection

@section('content')
<amp-state id="cartInfo" src="{{ route('cartMetaJSON') }}"></amp-state>
<amp-state id="ratings" src="{{ route('ratingsJSON', ['model_name' => 'Rape', 'slug' => $product->model->slug]) }}"></amp-state>
<amp-state id="cartItem"><script type="application/json">{}</script></amp-state>
<amp-state id="clcPrice">
  <script type="application/json">
    {{ $product->model->price_pp * 3 }}
  </script>
</amp-state>

	<div class="space-2"></div>
		<div class="row">
			<div class="col-sm-12">
				<div class="blog-item clearfix">
					<amp-carousel class="preview"
								  layout="responsive"
								  type="slides"
								  autoplay
								  delay="8500"
								  width="320"
								  height="180">

						@foreach($product->pictures() as $picture)
							<amp-img src="{{ asset($picture->full_path) }}" width=345 height=345></amp-img>
						@endforeach
					</amp-carousel>
					<h2 class="margin-0 thin">{{ $product->model->name }}</h2>

					<div>
						<h2 class="current primary-color d-inline thicc">{{ $product->model->price_pp }} Kč/g</h2>
					</div>

					<div class="space"></div>
					<div class="divider colored"></div>
					<div class="space"></div>

					@include('layouts.parts.detailproductorder')

					<div class="divider colored"></div>
					<div class="space"></div>

					<div><h4 class="d-inline">TYP: {{ $product->model->type }}</h4></div>
					<div class="space"></div>

					<h4 class="margin-0">POPIS</h4>
					<p>
						<i class="fa fa-info-circle" aria-hidden="true"></i> <a href="{{ route('rapeDescription') }}"><i>Detailní info o rapé</i></a>
					</p>
					{!! $product->model->description !!}


					<div class="space"></div>
					<div class="divider colored"></div>
					<div class="space"></div>

					<div>
						<h4 class="d-inline">VYLOUČENÍ ODPOVĚDNOSTI</h4>
						<p>
							Tento produkt je etnobotanický vzorek. Není schválen jako výživový doplněk, potravina, ani lék či léčivo. Nezodpovídáme za škody způsobené nesprávným užitím.
						</p><p>
							Neprodejné osobám mladším 18 let.
						</p><p>
							Výrobek není vhodný pro děti, těhotné a kojící matky. 
						</p>
					</div>

					<div class="space"></div>
					<div class="divider colored"></div>
					<div class="space"></div>

					<div>
						<h4 class="d-inline">DOBREJ MATROŠ</h4>
						<div class="space"></div>
        				@include('layouts.parts.productlist', ['list_items_show' => ['rape', 'pipe', 'kratom']])
					</div>
				</div>
				
				@include('layouts.parts.ratings', ['model_name' => 'Rape'])
			</div>
		</div>


		@include('layouts.parts.watchdoglightbox')
		
@endsection


