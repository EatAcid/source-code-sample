<amp-lightbox id="order-lightbox" layout="nodisplay" class="light">
  <div class="filter-section middle">
    <h3 [text]="cartItem.name"></h3>
    <form method="post"
        action-xhr="{{ route('addCartItem') }}" 
        id="order-lightbox-form"
        target="_top"
        on="submit:order-feedback.show;submit-success:order-fieldset.hide,cartInfo.refresh"
          >
        <fieldset id="order-fieldset">
          @csrf
          <input type="hidden" name="nazev" [value]="cartItem.slug">
          <input type="hidden" name="produkt" [value]="cartItem.prdct">
          <label class="" for="mnozstvi">
            kolik toho chceš (<span [text]="cartItem.unit_short"></span>)?
          </label>
            <input class="modernuminp"
              type="number"
              name="mnozstvi"
              [value]="cartItem.initval" 
              [step]="cartItem.order_step"
              [min]="cartItem.order_min"
              [max]="cartItem.order_max" 
              on="change:AMP.setState({ clcPrice: event.value*cartItem.price_pp })"
              required>
            <div>
              <h3 class="margin-0 pull-right"><span [text]="clcPrice">0</span> Kč</h3>
            </div>
          <div class="clear-both"></div>
          <input type="submit"
            class="button button-full grass-bg margin-0" 
            value="POTVRDIT">
        </fieldset>
        <div id="order-feedback">
          <div submit-success>
            <template type="amp-mustache">
              <div class="alert-box alert-box-success alert-box-with-icon">
                <i class="fa fa-thumbs-o-up"></i>
                <b>@{{message}}</b> <br>
              </div>
              <p class="pull-right">
                <a href="{{ route('cart') }}">k pokladně</a>
              </p>
            </template>
          </div>
          <div submit-error>
            <template type="amp-mustache">
              <div class="alert-box alert-box-error alert-box-with-icon">
                <i class="fa fa-times"></i>
                <p>@{{ message }}</p>
                @{{#errors.mnozstvi}} @{{.}} <br>@{{/errors.mnozstvi}}
                @{{#errors.nazev}} @{{.}} <br>@{{/errors.nazev}}
                @{{#errors.produkt}} @{{.}} <br>@{{/errors.produkt}}
              </div>
            </template>
          </div>
        </div>
    </form>
    <div class="space-2"></div>
    <div>
      <a on="tap:order-lightbox.close,order-feedback.hide" role="button" tabindex="0" class="cursor-pointer">
        Zavřít
      </a>
    </div>
  </div>
</amp-lightbox>