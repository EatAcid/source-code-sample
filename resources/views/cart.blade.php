@extends('layouts.base')

@section('title', 'Košík - Dobrej Matroš')

@section('scriptsInclude')
<script async custom-element="amp-carousel" src="https://cdn.ampproject.org/v0/amp-carousel-0.1.js"></script>
<script async custom-element="amp-list" src="https://cdn.ampproject.org/v0/amp-list-0.1.js"></script>
<script async custom-template="amp-mustache" src="https://cdn.ampproject.org/v0/amp-mustache-0.2.js"></script>
<script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>
<script async custom-element="amp-fit-text" src="https://cdn.ampproject.org/v0/amp-fit-text-0.1.js"></script>
<script async custom-element="amp-bind" src="https://cdn.ampproject.org/v0/amp-bind-0.1.js"></script>
<script async custom-element="amp-lightbox" src="https://cdn.ampproject.org/v0/amp-lightbox-0.1.js"></script>
<script async custom-element="amp-selector" src="https://cdn.ampproject.org/v0/amp-selector-0.1.js"></script>
<script async custom-element="amp-form" src="https://cdn.ampproject.org/v0/amp-form-0.1.js"></script>
<script async custom-element="amp-access" src="https://cdn.ampproject.org/v0/amp-access-0.1.js"></script>
@endsection
@section('description')
	<meta name="description" content="XXXXXXXXX">
@endsection

@section('content')
@if($cart->countItems() >= 1)


<amp-state id="cartItem"><script type="application/json">{}</script></amp-state>
<!-- <amp-state id="dobirkou"><script type="application/json">{30}</script></amp-state> -->
<amp-state id="cartItemsList" src="{{ route('cartJSON') }}"></amp-state>
<amp-state id="cartInfo" src="{{ route('cartMetaJSON') }}"></amp-state>

<div class="container-fluid">
		<div class="space-2"></div>

		<div class="row">
			<div class="col-xs-12">
				<form method="post" 
						action-xhr="{{ route('updateCartList') }}" 
						target="_top" 
						on="submit-success: AMP.setState({
				              cartItemsList: event.response
				          }),cartInfo.refresh"
						>
					@csrf
					<amp-accordion class="accordion">
						<section>
							<header class="h3 accordion-title">PRODUKTY V KOŠÍKU 
								<small>
									( <span [text]="cartInfo.number_of_items">{{ $cart->countItems() }}</span> @if($cart->countItems() === 1) Položka @else Položky @endif )
								</small></header>
							<div class="padding-left-0 padding-right-0">

								<amp-list 
					              width="" 
					              height="{{ $cart->countItems() *100 }}" 
					              [height]="cartItemsList.items.length *100" 
					              layout="fixed-height" 
					              src="{{ route('cartJSON') }}" 
					              [src]="cartItemsList.items" 
					              binding="no" 
					              class="grid" 
					              >
				              		<template type="amp-mustache">		
										<div class="cart-product-item clearfix">
											<a href="@{{detail_path}}" class="preview">
												<amp-img
														src="@{{ picture }}"
														width="140"
														height="140"
														layout="responsive"></amp-img>
											</a>
											<div class="price font-2">@{{ price }} Kč</div>
											<a href="@{{detail_path}}" class="title">@{{ name }}</a>

											<button type="button"
									            class="remove-from-cart"
									            on="tap: AMP.setState({cartItem:
									                                { _token: '{{ csrf_token() }}',
									                                  slug: '@{{slug}}'
									                                }}), form-cart-delete.submit"><i class="fa fa-times"></i></button>


											<div class="clearfix options">
												<div class="pull-left">
													<label>
														<span>Množství:</span>
														<input class="numinput"
														  type="number"
												          name="quantities[]"
												          value="@{{ quantity }}" 
												          step="@{{ order_step }}"
												          min="@{{ order_min }}"
												          max="@{{ order_max }}" 
												          required>
												        <h4 class="d-inline">(@{{ unit_short }})</h4>
													</label>
												</div>
											</div>
										</div>
										<div class="space-2"></div>
									  
			                  		</template>
					            </amp-list>
								<button class="button button-large button-full primary-bg margin-0">POTVRDIT ZMĚNY</button>
								<div submit-success>
									<template type="amp-mustache">
										<div class="alert-box alert-box-success alert-box-with-icon">
											<i class="fa fa-check"></i>
											<p>Změny byly uloženy</p>
										</div>
									</template>
								</div>
								<div submit-error>
									<template type="amp-mustache">
										<div class="alert-box alert-box-error alert-box-with-icon">
											<i class="fa fa-times"></i>
											<p>@{{message}}</p>
										</div>
									</template>
								</div>
							</div>
						</section>
					</amp-accordion><!-- PRODUCTS IN CART ACCORDION ENDS -->
				</form>

<form id="form-cart-delete"
  method="POST"
  target="_top"
  action-xhr="{{ route('deleteCartItem') }}"
  on="submit-success: AMP.setState({
              cartItemsList: event.response
          }),cartInfo.refresh"
  novalidate>
  <input type="hidden"
    name="_token"
    value
    [value]="cartItem._token"
    >
  <input type="hidden"
    name="slug"
    value
    [value]="cartItem.slug">
</form>

			</div>
		</div>
	</div>

	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12">
				<h3 class="margin-0 font-2 pull-left">Celkem</h3>
				<h3 class="margin-0 pull-right"><span [text]="cartInfo.total_price">{{ $cart->countTotalPrice() }}</span> Kč</h3>

				<div class="space-2 clear-both"></div>


				<h3 class="margin-0 font-2">Způsob platby</h3>
				<div class="space"></div>

				<label class="radiocont">Dobírkou <i>(30 Kč)</i>
				  <input type="radio" checked="checked" name="zpusob_platby" value="dobirka">
				  <span class="checkmark"></span>
				</label>
				<label class="radiocont">Převodem na účet <i>(zdarma)</i>
				  <input type="radio" name="zpusob_platby" value="prevod">
				  <span class="checkmark"></span>
				</label>

			    <div class="space-2 clear-both"></div>


				<h3 class="margin-0 font-2">Doprava</h3>
				<div class="space"></div>

				<label class="radiocont">Zásilkovna: na výdejní místo <i>(30 Kč)</i>
				  <input type="radio" checked="checked" name="doprava" value="zasilkovna">
				  <span class="checkmark"></span>
				</label>
				<label class="radiocont">Zásilkovna: na adresu <i>(50 Kč)</i>
				  <input type="radio" name="doprava" value="adresa">
				  <span class="checkmark"></span>
				</label>



				<a href="{{ route('chooseShipPay') }}" class="button button-large button-full grass-bg margin-0">POKRAČOVAT V OBJEDNÁVCE</a>

				<div class="divider-30 colored"></div>

				<h3 class="margin-top-0">PŘIJÍMÁME:</h3>

				<img src="{{ asset('img/accept_payment_304x40.png') }}" width=152 height=20>
				<p class="margin-0">Phosfluorescently pontificate progressive opportunities rather than magnetic benefits. Dynamically pursue corporate expertise through transparent results. Energistically leverage existing quality infrastructures vis-a-vis 2.0 channels.</p>
			</div><!-- COL-XS-12 ENDS -->
		</div><!-- ROW ENDS -->

	</div><!-- CONTAINER-FLUID ENDS -->

@else
<div class="container-fluid">
	<div class="space-2"></div>

	<div class="row">
		<div class="col-xs-12">
			<div class="alert-box alert-box-warning alert-box-with-icon">
				<i class="fa fa-warning"></i>
				<p>Koukáš do prázdného košíku</p>
			</div>
			<div class="space"></div>
			<div class="details">
				<p>
					Něco do něho přihoď. Cítit se prázdnej není dobrý, ani pro tvůj košík. V klidu vybírej z našich produktů. Ty naplněj štěstím a láskou i ty nejzoufalejší duše. Zkrátka Dobrej Matroš.
				</p>
			</div>
			<div class="space-2"></div>
			@include('layouts.parts.productlist', ['list_items_show' => ['rape', 'pipe', 'kratom']])
		</div>
	</div>
</div>
@endif

@endsection


