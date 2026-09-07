<!DOCTYPE html>
<html lang="en" class="no-js">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Codev</title>
    <link rel="icon" href="codev/blanco.png" type="image/png" />
    <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans:400,600" rel="stylesheet">
    <link rel="stylesheet" href="{{ URL::to('systeminds/dist/css/style.css') }}">
    <script src="https://unpkg.com/animejs@3.0.1/lib/anime.min.js"></script>
    <script src="https://unpkg.com/scrollreveal@4.0.0/dist/scrollreveal.min.js"></script>
</head>

<body class="is-boxed has-animations">
    <div class="body-wrap">
        <header class="site-header">
            <div class="container">
                <div class="site-header-inner">
                    <div class="brand header-brand">
                        <h1 class="m-0">
                            <a href="{{ URL::to('/') }}">
                                <img class="header-logo-image" src="{{ URL::to('codev/blanco.png') }}" alt="Logo"
                                    style="width: 125px">
                            </a>
                        </h1>
                    </div>
                </div>
            </div>
        </header>

        <main>
            <section class="hero">
                <div class="container">
                    <div class="hero-inner">
                        <div class="hero-copy">
                            <h1 class="hero-title mt-0">
                                <img class="header-logo-image" src="{{ URL::to('codev/codevBlanco.png') }}"
                                    alt="Logo" style="width: 190px">
                            </h1>
                            <p class="hero-paragraph">Sistema para Cajas de Ahorros, automatización de procesos que
                                garantizarán un perfecto manejo y control de tu caja.</p>
                            <div class="hero-cta">
                                <a class="button button-primary" href="{{ URL::to('login') }}">Iniciar
                                    Sesion</a>
                                <a class="button" style="background-color: #ffbc00 !important;"
                                    href="{{ URL::to('login2') }}">Acceso Clientes</a>
                                <hr>
                                {{-- <a class="button button-info"  href="{{ URL::to('simulador-publico') }}"
                                    target="_blank">Simulador Créditos</a> --}}

                            </div>
                        </div>
                        <div class="hero-figure anime-element">
                            <svg class="placeholder" width="528" height="396" viewBox="0 0 528 396">
                                <rect width="528" height="396" style="fill:transparent;" />
                            </svg>
                            <div class="hero-figure-box hero-figure-box-01" data-rotation="45deg"></div>
                            <div class="hero-figure-box hero-figure-box-02" data-rotation="-45deg"></div>
                            <div class="hero-figure-box hero-figure-box-03" data-rotation="0deg"></div>
                            <div class="hero-figure-box hero-figure-box-04" data-rotation="-135deg"></div>
                            <div class="hero-figure-box hero-figure-box-05"></div>
                            <div class="hero-figure-box hero-figure-box-06"></div>
                            <div class="hero-figure-box hero-figure-box-07"></div>
                            <div class="hero-figure-box hero-figure-box-08" data-rotation="-22deg"></div>
                            <div class="hero-figure-box hero-figure-box-09" data-rotation="-52deg"></div>
                            <div class="hero-figure-box hero-figure-box-10" data-rotation="-50deg"></div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="features section">
                <div class="container">
                    <div class="features-inner section-inner has-bottom-divider">
                        <div class="features-wrap">
                            <div class="feature text-center is-revealing">
                                <div class="feature-inner">
                                    <div class="feature-icon">
                                        <img src="{{ URL::to('systeminds/dist/images/desarrollo.png') }}"
                                            alt="Feature 01">
                                    </div>
                                    <h4 class="feature-title mt-24">Sistema Cajas de Ahorro</h4>
                                    <p class="text-sm mb-0">El sistema insignia de nuestra empresa nos destaca por le
                                        facilidad de uso, la seguridad que tiene con la informacion de nuestros clientes
                                        y la adptabilidad para todas las cajas de Ahorro</p>
                                </div>
                            </div>
                            <div class="feature text-center is-revealing">
                                <div class="feature-inner">
                                    <div class="feature-icon">
                                        <img src="{{ URL::to('systeminds/dist/images/regla.png') }}" alt="Feature 06"
                                            style="width: 35%;">
                                    </div>
                                    <h4 class="feature-title mt-24">Sistemas a medida</h4>
                                    <p class="text-sm mb-0">Un sistema a medida de tus necesidades, te permite la facil
                                        adaptabilidad a cuaqluier giro de negocio y te ayuda a crecer de manera
                                        exponencial ya que los procesos los realiza el sistema, optimizando al máximo
                                        tus recursos y sacandoles un provecho del 100%.</p>
                                </div>
                            </div>
                            <div class="feature text-center is-revealing">
                                <div class="feature-inner">
                                    <div class="feature-icon">
                                        <img src="{{ URL::to('systeminds/dist/images/contabilidad.png') }}"
                                            alt="Feature 03">
                                    </div>
                                    <h4 class="feature-title mt-24">Contabilidad y Asesoria en Cajas de Ahorro</h4>
                                    <p class="text-sm mb-0">Asesoria, legalizaión de cajas ahorro, para un correcto
                                        funcionamiento de la misma y garantizar un crecimiento significativo.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- <section class="pricing section">
                <div class="container-sm">
                    <div class="pricing-inner section-inner">
                        <div class="pricing-header text-center">
                            <h2 class="section-title mt-0">Paquete Básico</h2>
                            <!-- <p class="section-paragraph mb-0">Desarrollo de un sistema personalizado, para tu pequeña, mediana o gran Empresa</p> -->
                        </div>
                        <div class="pricing-tables-wrap">
                            <div class="pricing-table">
                                <div class="pricing-table-inner is-revealing">
                                    <div class="pricing-table-main">
                                        <div class="pricing-table-header pb-24">
                                            <div class="pricing-table-price"><span class="pricing-table-price-currency h2">$</span><span class="pricing-table-price-amount h1">500</span><span class="text-xs"> Implementación/ 25 Mensuales</span></div>
                                        </div>
                                        <div class="pricing-table-features-title text-xs pt-24 pb-24">Qué optendrás?</div>
                                        <ul class="pricing-table-features list-reset text-xs">
                                            <li>
                                                <span>Sistema transaccional automatiza créditos y
                                                    libretas ahorro</span>
                                            </li>
                                            <li>
                                                <span>Control de socios</span>
                                            </li>
                                            <li>
                                                <span>Control de cartera vencida</span>
                                            </li>
                                            <li>
                                                <span>Dos usuarios ( 1 Tesorería y 1 Gerente )</span>
                                            </li>
                                            <li>
                                                <span>Transacciones y socios ilimitados</span>
                                            </li>
                                            <li>
                                                <span>Dashboard Gerencial básico</span>
                                            </li>

                                        </ul>
                                    </div>
                                    <div class="pricing-table-cta mb-8">
                                        <a class="button button-primary button-shadow button-block" href="https://wa.me/593939085606?text=Quiero%20más%20información" target="_blank">Compralo ya</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="pricing section">
                <div class="container-sm">
                    <div class="pricing-inner section-inner">
                        <div class="pricing-header text-center">
                            <h2 class="section-title mt-0">Paquete Plata</h2>
                            <!-- <p class="section-paragraph mb-0">Desarrollo de un sistema personalizado, para tu pequeña, mediana o gran Empresa</p> -->
                        </div>
                        <div class="pricing-tables-wrap">
                            <div class="pricing-table">
                                <div class="pricing-table-inner is-revealing">
                                    <div class="pricing-table-main">
                                        <div class="pricing-table-header pb-24">
                                            <div class="pricing-table-price"><span
                                                    class="pricing-table-price-currency h2">$</span><span
                                                    class="pricing-table-price-amount h1">1000</span><span
                                                    class="text-xs"> Implementación/ 50 Mensuales</span></div>
                                        </div>
                                        <div class="pricing-table-features-title text-xs pt-24 pb-24">Qué optendrás?
                                        </div>
                                        <ul class="pricing-table-features list-reset text-xs">
                                            <li>
                                                <span>Incluye todas las prestaciones del paquete básico</span>
                                            </li>
                                            <li>
                                                <span>Hasta 150 usuarios ( modalidad de administración y
                                                    visualización individual como cliente de la Caja)</span>
                                            </li>
                                            <li>
                                                <span>Módulo Resultado de la Caja</span>
                                            </li>
                                            <li>
                                                <span>Módulo Control de Socios</span>
                                            </li>
                                            <li>
                                                <span>Dashboard Gerencia Completo
                                                </span>
                                            </li>

                                        </ul>
                                    </div>
                                    <div class="pricing-table-cta mb-8">
                                        <a class="button button-primary button-shadow button-block"
                                            href="https://wa.me/593939085606?text=Quiero%20más%20información"
                                            target="_blank">Compralo ya</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="pricing section">
                <div class="container-sm">
                    <div class="pricing-inner section-inner">
                        <div class="pricing-header text-center">
                            <h2 class="section-title mt-0">Paquete Premiun</h2>
                            <!-- <p class="section-paragraph mb-0">Desarrollo de un sistema personalizado, para tu pequeña, mediana o gran Empresa</p> -->
                        </div>
                        <div class="pricing-tables-wrap">
                            <div class="pricing-table">
                                <div class="pricing-table-inner is-revealing">
                                    <div class="pricing-table-main">
                                        <div class="pricing-table-header pb-24">
                                            <div class="pricing-table-price"><span
                                                    class="pricing-table-price-currency h2">$</span><span
                                                    class="pricing-table-price-amount h1">2000</span><span
                                                    class="text-xs"> Implementación/ 100 Mensuales</span></div>
                                        </div>
                                        <div class="pricing-table-features-title text-xs pt-24 pb-24">Qué optendrás?
                                        </div>
                                        <ul class="pricing-table-features list-reset text-xs">
                                            <li>
                                                <span>Incluye todas las prestaciones del paquete básico
                                                    y plata</span>
                                            <li>
                                                <span>Usuarios ILIMITADOS</span>
                                            </li>
                                            <li>
                                                <span>Mensajería Whatsapp ilimitada ( seguimiento de
                                                    cartera vencida y mensajes por transacciones)</span>
                                            </li>
                                            <li>
                                                <span>Dashboard Gerencial Completo</span>
                                            </li>
                                            <li>
                                                <span>Estado de Resultados</span>
                                            </li>
                                            <li>
                                                <span>Estado de Situación Financiera</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="pricing-table-cta mb-8">
                                        <a class="button button-primary button-shadow button-block"
                                            href="https://wa.me/593939085606?text=Quiero%20más%20información"
                                            target="_blank">Compralo ya</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section> --}}

            <section class="cta section">
                <div class="container">
                    <div class="cta-inner section-inner">
                        <h3 class="section-title mt-0">Todavía no estas convencido?</h3>
                        <div class="cta-cta">
                            <a class="button button-primary button-wide-mobile"
                                href="https://wa.me/593939085606?text=Quiero%20más%20información"
                                target="_blank">Contáctanos</a>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="site-footer">
            <div class="container">
                <div class="site-footer-inner">
                    <div class="brand footer-brand">
                        <a href="{{ URL::to('/') }}">
                            <img class="header-logo-image" src="{{ URL::to('codev/blanco.png') }}" alt="Logo"
                                style="width: 25px">
                        </a>
                    </div>
                    <ul class="footer-links list-reset">
                        <li>
                            <a href="https://wa.me/593939085606?text=Quiero%20más%20información"
                                target="_blank">Contáctanos</a>
                        </li>
                        <!-- <li>
                            <a href="#">Acerca de nosotros</a>
                        </li>
                        <li>
                            <a href="#">Nuestro equipo</a>
                        </li> -->
                    </ul>
                    <ul class="footer-social-links list-reset">
                        <li>
                            <a href="https://www.facebook.com/codevsistema" target="_blank">
                                <span class="screen-reader-text">Facebook</span>
                                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20"
                                    viewBox="0 0 50 50" style="fill:#228BE6;">
                                    <path
                                        d="M32,11h5c0.552,0,1-0.448,1-1V3.263c0-0.524-0.403-0.96-0.925-0.997C35.484,2.153,32.376,2,30.141,2C24,2,20,5.68,20,12.368 V19h-7c-0.552,0-1,0.448-1,1v7c0,0.552,0.448,1,1,1h7v19c0,0.552,0.448,1,1,1h7c0.552,0,1-0.448,1-1V28h7.222 c0.51,0,0.938-0.383,0.994-0.89l0.778-7C38.06,19.518,37.596,19,37,19h-8v-5C29,12.343,30.343,11,32,11z">
                                    </path>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.instagram.com/sistemaamedida" target="_blank">
                                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20"
                                    viewBox="0 0 50 50" style="fill:#228BE6;">
                                    <path
                                        d="M 16 3 C 8.83 3 3 8.83 3 16 L 3 34 C 3 41.17 8.83 47 16 47 L 34 47 C 41.17 47 47 41.17 47 34 L 47 16 C 47 8.83 41.17 3 34 3 L 16 3 z M 37 11 C 38.1 11 39 11.9 39 13 C 39 14.1 38.1 15 37 15 C 35.9 15 35 14.1 35 13 C 35 11.9 35.9 11 37 11 z M 25 14 C 31.07 14 36 18.93 36 25 C 36 31.07 31.07 36 25 36 C 18.93 36 14 31.07 14 25 C 14 18.93 18.93 14 25 14 z M 25 16 C 20.04 16 16 20.04 16 25 C 16 29.96 20.04 34 25 34 C 29.96 34 34 29.96 34 25 C 34 20.04 29.96 16 25 16 z">
                                    </path>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.tiktok.com/@covedsistema" target="_blank">
                                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20"
                                    viewBox="0 0 50 50" style="fill:#228BE6;">
                                    <path
                                        d="M41,4H9C6.243,4,4,6.243,4,9v32c0,2.757,2.243,5,5,5h32c2.757,0,5-2.243,5-5V9C46,6.243,43.757,4,41,4z M37.006,22.323 c-0.227,0.021-0.457,0.035-0.69,0.035c-2.623,0-4.928-1.349-6.269-3.388c0,5.349,0,11.435,0,11.537c0,4.709-3.818,8.527-8.527,8.527 s-8.527-3.818-8.527-8.527s3.818-8.527,8.527-8.527c0.178,0,0.352,0.016,0.527,0.027v4.202c-0.175-0.021-0.347-0.053-0.527-0.053 c-2.404,0-4.352,1.948-4.352,4.352s1.948,4.352,4.352,4.352s4.527-1.894,4.527-4.298c0-0.095,0.042-19.594,0.042-19.594h4.016 c0.378,3.591,3.277,6.425,6.901,6.685V22.323z">
                                    </path>
                                </svg>
                            </a>
                        </li>
                    </ul>
                    <div class="footer-copyright">&copy; {{ date('Y') }} CODEV, todos los derechos Reservados
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <script src="{{ URL::to('systeminds/dist/js/main.min.js') }}"></script>
</body>

</html>
