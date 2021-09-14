@extends('layouts.base')

@section('title', 'Rapé - Dobrej Matroš')

@section('scriptsInclude')
	<script async custom-element="amp-carousel" src="https://cdn.ampproject.org/v0/amp-carousel-0.1.js"></script>
  <script async custom-element="amp-form" src="https://cdn.ampproject.org/v0/amp-form-0.1.js"></script>
	<script async custom-element="amp-list" src="https://cdn.ampproject.org/v0/amp-list-0.1.js"></script>
	<script async custom-template="amp-mustache" src="https://cdn.ampproject.org/v0/amp-mustache-0.2.js"></script>
	<script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>
	<script async custom-element="amp-fit-text" src="https://cdn.ampproject.org/v0/amp-fit-text-0.1.js"></script>
	<script async custom-element="amp-bind" src="https://cdn.ampproject.org/v0/amp-bind-0.1.js"></script>
	<script async custom-element="amp-lightbox" src="https://cdn.ampproject.org/v0/amp-lightbox-0.1.js"></script>
	<script async custom-element="amp-selector" src="https://cdn.ampproject.org/v0/amp-selector-0.1.js"></script>
@endsection
@section('description')
	<meta name="description" content="XXXXXXXXX">
@endsection

@section('content')
		<div class="space-2"></div>

		<div class="row">
			<div class="col-xs-12">
				<div class="bordered-title">
					<h1 class="h3">Rapé</h1>
					<h2 class="h5">Grid Style Category</h2>
				</div>
			</div>
		</div>

    <amp-state id="cartItem"><script type="application/json">{}</script></amp-state>
    <amp-state id="clcPrice"><script type="application/json">{0}</script></amp-state>
    <amp-state id="cartInfo" src="{{ route('cartMetaJSON') }}"></amp-state>
    <amp-state id="products">
      <script type="application/json">
		      {
            "sortChoiceValue": "price-ascendent",
            "searchType": "all",
            "listSrc": "{{ route('rapeJSON') }}?sort=price-ascendent&_=RANDOM"
          }
      </script>
    </amp-state>

<div class="row">
  <div class="col-xs-12">
    <div id="main-wrap">
        <div class="content">
          <div id="info-wrap">
            <label for="sort" class="select">Seřadit podle 
              <select id="sort" 
              on="change:AMP.setState({
                products: {
                sortChoiceValue: event.value,
                listSrc: '{{ route('rapeJSON') }}?sort='+event.value+'&type='+products.searchType+'&_=RANDOM'
                }
              })">
                <option value="price-ascendent">Cena (nejlevnější)</option>
                <option value="price-descendent">Cena (nejdražší)</option>
              </select>
            </label>
            
            <button class="button button-small button-row grass-bg" on="tap:filter-lightbox">
              Filtrovat
            </button>
          </div>

            <amp-list 
              width="" 
              height="" 
              layout="container" 
              src="{{ route('rapeJSON') }}?sort=price-ascendent&_=RANDOM" 
              [src]="products.listSrc" 
              binding="no" 
              class="grid" 
              data-amp-replace="RANDOM"
              >

              <div class="row">
                <div class="col-xs-12">
                  <template type="amp-mustache">
                    <div class="bones-products-grid cols-2">
                      <div class="bones-product-list-item @{{^order_max}} sold-out @{{/order_max}}">
                        <a href="/rape/@{{slug}}" class="preview">
                          <amp-img
                              src="@{{picture}}"
                              width="165"
                              height="165"
                              layout="responsive"></amp-img>
                          @{{^order_max}} <span class="badge font-2 grey-bg">vyprodáno</span> @{{/order_max}}
                        </a>
                        <a href="/rape/@{{slug}}"><h2 class="text-center">@{{name}}</h2></a>
                        <div class="prices text-center">
                          <span class="current font-2">@{{price_pp}} Kč/g</span>
                        </div>
                        @{{#order_max}}
                          <button class="button button-full grass-bg margin-0" 
                              on="tap:AMP.setState({cartItem:{
                                    name: '@{{name}}',
                                    slug: '@{{slug}}',
                                    prdct: 'Rape',
                                    initval: '3',
                                    order_min: '@{{order_min}}',
                                    order_max: '@{{order_max}}',
                                    order_step: '@{{order_step}}',
                                    price_pp: '@{{price_pp}}',
                                    unit_short: '@{{unit_short}}'
                                  },clcPrice:3*@{{price_pp}}}),order-lightbox-form.clear,order-fieldset.show,order-lightbox">
                          VHODIT DO KOŠÍKU</button>
                        @{{/order_max}} 
                        @{{^order_max}}
                          <button class="button button-full alge-bg margin-0"
                            on="tap:AMP.setState({cartItem:{
                                    name: '@{{name}}',
                                    slug: '@{{slug}}',
                                    prdct: 'Rape',
                                    initval: '3',
                                    unit_short: '@{{unit_short}}'
                                  }}),watchdog-fieldset.show,watchdog-lightbox"
                          >
                          STŘEŽIT SKLAD</button>
                        @{{/order_max}}
                      </div>
                    </div>
                  </template>
                </div>
              </div>
            </amp-list>
        </div>
      </div>


      <amp-lightbox id="filter-lightbox" layout="nodisplay" class="light">
        <div class="filter-section middle">
          <h3>Filtrovat výsledky</h3>
            <label for="typeFlt"><h4 class="d-inline">Typ</h4></label>
            <select id="typeFlt" 
            on="change:AMP.setState({
              products: {
              searchType: event.value,
              listSrc: '{{ route('rapeJSON') }}?sort='+products.sortChoiceValue+'&type='+event.value+'&_=RANDOM'
              }
            }),filter-lightbox.close">
              <option value="all">jakýkoliv</option>
              <option value="uzemňující">uzemňující</option>
              <option value="meditační">meditační</option>
              <option value="psychoaktivní">psychoaktivní</option>
            </select>

          <div class="space-2"></div>
          <button class="button button-small button-row grass-bg" tabindex="0" on="tap:filter-lightbox.close">OK</button>
        </div>
      </amp-lightbox>


      @include('layouts.parts.orderlightbox')

      @include('layouts.parts.watchdoglightbox')

	</div>
</div>






<div class="space-2"></div>
<div class="row">
    <div class="col-xs-12">
        <div class="bordered-title">
            <h1 class="h3">Další skvělej matroš</h1>
        </div>
        <p>
           Koukni na další produkty z Dobrýho Matroše. Každým nákupem se podílíš na rozšíření našeho sortimentu :) Zčekni tenhle čerstvý matroš.
        </p>
        <div class="space-2"></div>
        @include('layouts.parts.productlist', ['list_items_show' => ['pipe', 'kratom']])
    </div>
</div>



@endsection


