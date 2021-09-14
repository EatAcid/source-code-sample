<amp-lightbox id="watchdog-lightbox" layout="nodisplay" class="light">
  <div class="filter-section middle">
    <h3 [text]="cartItem.name">Foukačka</h3>
      <form 
          method="post"
          action-xhr="{{ route('createWatchdog') }}" 
          id="watchdog-lightbox-form"
          target="_top"
          on="submit:watchdog-feedback.show;submit-success:watchdog-fieldset.hide"
            >
          <fieldset id="watchdog-fieldset">
            @csrf
            <input type="hidden" name="nazev" [value]="cartItem.slug">
            <input type="hidden" name="produkt" [value]="cartItem.prdct">
            
            <label for="email">
              Na jaký email tě kontaktuju?
            </label>
            <div class="clear-both"></div>
              <input class=""
                type="email"
                name="email" 
                @auth 
                value="{{ Auth::user()->email }}"
                @endauth
                required>

           <div class="space clear-both"></div>
           
            <label for="mnozstvi">
              kolik si toho asi vezmeš (<span [text]="cartItem.unit_short"></span>)?
            </label>
              <input class="modernuminp"
                type="number"
                name="mnozstvi"  
                min="1"
                [value]="cartItem.initval"
                required>

            <div class="space-2 clear-both"></div>
            <input type="submit"
              class="button button-full grass-bg margin-0" 
              value="MYSLI SI NA MĚ">
          </fieldset>
          <div id="watchdog-feedback">
            <div submit-success>
              <template type="amp-mustache">
                <div class="alert-box alert-box-success alert-box-with-icon">
                  <i class="fa fa-check"></i>
                  <b>@{{message}}</b>
                </div>
                Kdybys chtěl místo emailu napsat na fb, tak mi hoď do zpráv na <a href="https://m.me/XYZ">DobrejMatroš</a> tenhle tajnej kód: <strong class="alge-bg light-color fb-code">@{{code}}</strong>
              </template>
            </div>
            <div submit-error>
              <template type="amp-mustache">
                <div class="alert-box alert-box-error alert-box-with-icon">
                  <i class="fa fa-times"></i>
                  <p>@{{ message }}</p>
                  @{{#errors.email}} @{{.}} <br>@{{/errors.email}}
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
      <a on="tap:watchdog-lightbox.close,watchdog-feedback.hide" role="button" tabindex="0" class="cursor-pointer">
        Zavřít
      </a>
    </div>
  </div>
</amp-lightbox>