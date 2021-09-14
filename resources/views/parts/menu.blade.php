    <amp-sidebar id='mainSideBar' layout='nodisplay'>
        <figure class="primary-bg">
            <amp-img class="circle" src="{{ asset('img/menu_pic_01.jpg')  }}" width="60" height="60" layout="fixed"></amp-img>
            <figcaption>
                <h3 class="light-color">Dobrej Matroš</h3>
                <h5 class="light-color-2">Čistej, neřezanej, GMO-free, v bio kvalitě.</h5>
            </figcaption>

            <button on='tap:mainSideBar.toggle' class="fa fa-caret-left light-color"></button>
        </figure><!-- NAVBAR USER CARD ENDS -->

        <nav id="menu" itemscope itemtype="http://schema.org/SiteNavigationElement">
            <a href="{{ route('home') }}"><i class="fa fa-home"></i>Domů</a>
            <amp-accordion>
                <section>
                    <h6><span><i class="fa fa-user-o"></i>účet</span></h6>
                    <div>
                        @auth
                            <a href="{{ route('editUser') }}">Nastavení</a>
                            <a href="{{ route('ordersList') }}">Objednávky</a>
                            <a href="{{ route('logout') }}">Odhlásit se</a>
                        @endauth
                        @guest
                            <a href="{{ route('login') }}">Přihlásit se</a>
                            <a href="{{ route('register') }}">Registrovat</a>
                        @endguest
                    </div>
                </section>
            </amp-accordion>
            <amp-accordion>
                <section>
                    <h6><span><i class="fa fa-cubes"></i>Produkty</span></h6>
                    <div>
                        <a href="{{ route('rapeIndex') }}">Rapé</a>
                        <a href="{{ route('pipeIndex') }}">Foukačky na Rapé</a>
                        <a href="{{ route('kratomIndex') }}">Kratom</a>
                    </div>
                </section>
            </amp-accordion>
            <a href="{{ route('cart') }}"><i class="fa fa-shopping-cart"></i>Košík</a>
            <a href="{{ route('orderIndex') }}"><i class="fa fa-money"></i>Pokladna</a>
            <amp-accordion>
                <section>
                    <h6><span><i class="fa fa-users"></i>E-shop</span></h6>
                    <div>
                        <a href="{{ route('about') }}">O nás</a>
                        <a href="{{ route('terms') }}">Obchodní podmínky</a>
                        <a href="{{ route('policy') }}">Zásady ochrany osobních údajů</a>
                    </div>
                </section>
            </amp-accordion>
            <a href="{{ route('contact') }}"><i class="fa fa-envelope"></i>Kontakt</a>
        </nav><!-- MENU ENDS -->

        <div class="divider colored"></div>

        <div>
            <p class="margin-top-0"><strong>Adresa:</strong> Slovinska 51<br>60200 Brno-Královo pole</p>
            <p><strong>Telefon:</strong> <a href="tel:+420603111111">+420 603 111 111</a></p>
            <p class="margin-bottom-0"><strong>E-Mail:</strong> <a href="mailto:eshop@mg.dobrejmatros.com">eshop@mg.dobrejmatros.com</a></p>
        </div><!-- CONTACT INFORMATION ENDS -->

    </amp-sidebar><!-- SIDEBAR ENDS -->