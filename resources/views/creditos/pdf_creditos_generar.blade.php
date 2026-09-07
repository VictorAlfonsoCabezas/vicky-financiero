<!DOCTYPE html>
<html lang="en">

    <head>
        <style>
            h1,
            h2,
            h3,
            h4,
            h5,
            h6 {
                font-size: inherit;
                font-weight: inherit;
            }

            /*
        Reset links to optimize for opt-in styling instead of opt-out.
            */

            a {
                color: inherit;
                text-decoration: inherit;
            }

            /*
        Add the correct font weight in Edge and Safari.
            */

            b,
            strong {
                font-weight: bolder;
            }

            /*
        1. Use the user's configured `mono` font family by default.
        2. Correct the odd `em` font sizing in all browsers.
            */

            code,
            kbd,
            samp,
            pre {
                font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
                /* 1 */
                font-size: 1em;
                /* 2 */
            }

            /*
        Add the correct font size in all browsers.
            */

            small {
                font-size: 80%;
            }

            /*
        Prevent `sub` and `sup` elements from affecting the line height in all browsers.
            */

            sub,
            sup {
                font-size: 75%;
                line-height: 0;
                position: relative;
                vertical-align: baseline;
            }

            sub {
                bottom: -0.25em;
            }

            sup {
                top: -0.5em;
            }

            /*
        1. Remove text indentation from table contents in Chrome and Safari. (https://bugs.chromium.org/p/chromium/issues/detail?id=999088, https://bugs.webkit.org/show_bug.cgi?id=201297)
        2. Correct table border color inheritance in all Chrome and Safari. (https://bugs.chromium.org/p/chromium/issues/detail?id=935729, https://bugs.webkit.org/show_bug.cgi?id=195016)
        3. Remove gaps between table borders by default.
            */

            table {
                text-indent: 0;
                /* 1 */
                border-color: inherit;
                /* 2 */
                border-collapse: collapse;
                /* 3 */
            }

            /*
        1. Change the font styles in all browsers.
        2. Remove the margin in Firefox and Safari.
        3. Remove default padding in all browsers.
            */

            button,
            input,
            optgroup,
            select,
            textarea {
                font-family: inherit;
                /* 1 */
                font-feature-settings: inherit;
                /* 1 */
                font-variation-settings: inherit;
                /* 1 */
                font-size: 100%;
                /* 1 */
                font-weight: inherit;
                /* 1 */
                line-height: inherit;
                /* 1 */
                color: inherit;
                /* 1 */
                margin: 0;
                /* 2 */
                padding: 0;
                /* 3 */
            }

            /*
        Remove the inheritance of text transform in Edge and Firefox.
            */

            button,
            select {
                text-transform: none;
            }

            /*
        1. Correct the inability to style clickable types in iOS and Safari.
        2. Remove default button styles.
            */

            button,
            [type='button'],
            [type='reset'],
            [type='submit'] {
                -webkit-appearance: button;
                /* 1 */
                background-color: transparent;
                /* 2 */
                background-image: none;
                /* 2 */
            }

            /*
        Use the modern Firefox focus style for all focusable elements.
            */

            :-moz-focusring {
                outline: auto;
            }

            /*
        Remove the additional `:invalid` styles in Firefox. (https://github.com/mozilla/gecko-dev/blob/2f9eacd9d3d995c937b4251a5557d95d494c9be1/layout/style/res/forms.css#L728-L737)
            */

            :-moz-ui-invalid {
                box-shadow: none;
            }

            /*
        Add the correct vertical alignment in Chrome and Firefox.
            */

            progress {
                vertical-align: baseline;
            }

            /*
        Correct the cursor style of increment and decrement buttons in Safari.
            */

            ::-webkit-inner-spin-button,
            ::-webkit-outer-spin-button {
                height: auto;
            }

            /*
        1. Correct the odd appearance in Chrome and Safari.
        2. Correct the outline style in Safari.
            */

            [type='search'] {
                -webkit-appearance: textfield;
                /* 1 */
                outline-offset: -2px;
                /* 2 */
            }

            /*
        Remove the inner padding in Chrome and Safari on macOS.
            */

            ::-webkit-search-decoration {
                -webkit-appearance: none;
            }

            /*
        1. Correct the inability to style clickable types in iOS and Safari.
        2. Change font properties to `inherit` in Safari.
            */

            ::-webkit-file-upload-button {
                -webkit-appearance: button;
                /* 1 */
                font: inherit;
                /* 2 */
            }

            /*
        Add the correct display in Chrome and Safari.
            */

            summary {
                display: list-item;
            }

            /*
        Removes the default spacing and border for appropriate elements.
            */

            blockquote,
            dl,
            dd,
            h1,
            h2,
            h3,
            h4,
            h5,
            h6,
            hr,
            figure,
            p,
            pre {
                margin: 0;
            }

            fieldset {
                margin: 0;
                padding: 0;
            }

            legend {
                padding: 0;
            }

            ol,
            ul,
            menu {
                list-style: none;
                margin: 0;
                padding: 0;
            }

            /*
        Reset default styling for dialogs.
            */

            dialog {
                padding: 0;
            }

            /*
        Prevent resizing textareas horizontally by default.
            */

            textarea {
                resize: vertical;
            }

            /*
        1. Reset the default placeholder opacity in Firefox. (https://github.com/tailwindlabs/tailwindcss/issues/3300)
        2. Set the default placeholder color to the user's configured gray 400 color.
            */

            input::placeholder,
            textarea::placeholder {
                opacity: 1;
                /* 1 */
                color: #9ca3af;
                /* 2 */
            }

            /*
        Set the default cursor for buttons.
            */

            button,
            [role="button"] {
                cursor: pointer;
            }

            /*
        Make sure disabled buttons don't get the pointer cursor.
            */

            :disabled {
                cursor: default;
            }

            /*
        1. Make replaced elements `display: block` by default. (https://github.com/mozdevs/cssremedy/issues/14)
        2. Add `vertical-align: middle` to align replaced elements more sensibly by default. (https://github.com/jensimmons/cssremedy/issues/14#issuecomment-634934210)
        This can trigger a poorly considered lint error in some tools but is included by design.
            */

            img,
            svg,
            video,
            canvas,
            audio,
            iframe,
            embed,
            object {
                display: block;
                /* 1 */
                vertical-align: middle;
                /* 2 */
            }

            /*
        Constrain images and videos to the parent width and preserve their intrinsic aspect ratio. (https://github.com/mozdevs/cssremedy/issues/14)
            */

            img,
            video {
                max-width: 100%;
                height: auto;
            }

            /* Make elements with the HTML hidden attribute stay hidden by default */

            [hidden] {
                display: none;
            }

            *,
            ::before,
            ::after {
                --tw-border-spacing-x: 0;
                --tw-border-spacing-y: 0;
                --tw-translate-x: 0;
                --tw-translate-y: 0;
                --tw-rotate: 0;
                --tw-skew-x: 0;
                --tw-skew-y: 0;
                --tw-scale-x: 1;
                --tw-scale-y: 1;
                --tw-pan-x: ;
                --tw-pan-y: ;
                --tw-pinch-zoom: ;
                --tw-scroll-snap-strictness: proximity;
                --tw-gradient-from-position: ;
                --tw-gradient-via-position: ;
                --tw-gradient-to-position: ;
                --tw-ordinal: ;
                --tw-slashed-zero: ;
                --tw-numeric-figure: ;
                --tw-numeric-spacing: ;
                --tw-numeric-fraction: ;
                --tw-ring-inset: ;
                --tw-ring-offset-width: 0px;
                --tw-ring-offset-color: #fff;
                --tw-ring-color: rgb(59 130 246 / 0.5);
                --tw-ring-offset-shadow: 0 0 #0000;
                --tw-ring-shadow: 0 0 #0000;
                --tw-shadow: 0 0 #0000;
                --tw-shadow-colored: 0 0 #0000;
                --tw-blur: ;
                --tw-brightness: ;
                --tw-contrast: ;
                --tw-grayscale: ;
                --tw-hue-rotate: ;
                --tw-invert: ;
                --tw-saturate: ;
                --tw-sepia: ;
                --tw-drop-shadow: ;
                --tw-backdrop-blur: ;
                --tw-backdrop-brightness: ;
                --tw-backdrop-contrast: ;
                --tw-backdrop-grayscale: ;
                --tw-backdrop-hue-rotate: ;
                --tw-backdrop-invert: ;
                --tw-backdrop-opacity: ;
                --tw-backdrop-saturate: ;
                --tw-backdrop-sepia: ;
            }

            ::backdrop {
                --tw-border-spacing-x: 0;
                --tw-border-spacing-y: 0;
                --tw-translate-x: 0;
                --tw-translate-y: 0;
                --tw-rotate: 0;
                --tw-skew-x: 0;
                --tw-skew-y: 0;
                --tw-scale-x: 1;
                --tw-scale-y: 1;
                --tw-pan-x: ;
                --tw-pan-y: ;
                --tw-pinch-zoom: ;
                --tw-scroll-snap-strictness: proximity;
                --tw-gradient-from-position: ;
                --tw-gradient-via-position: ;
                --tw-gradient-to-position: ;
                --tw-ordinal: ;
                --tw-slashed-zero: ;
                --tw-numeric-figure: ;
                --tw-numeric-spacing: ;
                --tw-numeric-fraction: ;
                --tw-ring-inset: ;
                --tw-ring-offset-width: 0px;
                --tw-ring-offset-color: #fff;
                --tw-ring-color: rgb(59 130 246 / 0.5);
                --tw-ring-offset-shadow: 0 0 #0000;
                --tw-ring-shadow: 0 0 #0000;
                --tw-shadow: 0 0 #0000;
                --tw-shadow-colored: 0 0 #0000;
                --tw-blur: ;
                --tw-brightness: ;
                --tw-contrast: ;
                --tw-grayscale: ;
                --tw-hue-rotate: ;
                --tw-invert: ;
                --tw-saturate: ;
                --tw-sepia: ;
                --tw-drop-shadow: ;
                --tw-backdrop-blur: ;
                --tw-backdrop-brightness: ;
                --tw-backdrop-contrast: ;
                --tw-backdrop-grayscale: ;
                --tw-backdrop-hue-rotate: ;
                --tw-backdrop-invert: ;
                --tw-backdrop-opacity: ;
                --tw-backdrop-saturate: ;
                --tw-backdrop-sepia: ;
            }

            .fixed {
                position: fixed;
            }

            .bottom-0 {
                bottom: 0px;
            }

            .left-0 {
                left: 0px;
            }

            .table {
                display: table;
            }

            .h-12 {
                height: 3rem;
            }

            .w-1\/2 {
                width: 50%;
            }

            .w-full {
                width: 100%;
            }

            .border-collapse {
                border-collapse: collapse;
            }

            .border-spacing-0 {
                --tw-border-spacing-x: 0px;
                --tw-border-spacing-y: 0px;
                border-spacing: var(--tw-border-spacing-x) var(--tw-border-spacing-y);
            }

            .whitespace-nowrap {
                white-space: nowrap;
            }

            .border-b {
                border-bottom-width: 1px;
            }

            .border-b-2 {
                border-bottom-width: 2px;
            }

            .border-r {
                border-right-width: 1px;
            }

            .border-main {
                border-color: #5c6ac4;
            }

            .bg-main {
                background-color: #5c6ac4;
            }

            .bg-slate-100 {
                background-color: #f1f5f9;
            }

            .p-3 {
                padding: 0.75rem;
            }

            .px-14 {
                padding-left: 3.5rem;
                padding-right: 3.5rem;
            }

            .px-2 {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            .py-10 {
                padding-top: 2.5rem;
                padding-bottom: 2.5rem;
            }

            .py-3 {
                padding-top: 0.75rem;
                padding-bottom: 0.75rem;
            }

            .py-4 {
                padding-top: 1rem;
                padding-bottom: 1rem;
            }

            .py-6 {
                padding-top: 1.5rem;
                padding-bottom: 1.5rem;
            }

            .pb-3 {
                padding-bottom: 0.75rem;
            }

            .ps-2 {
                padding-left: 0.5rem;
            }

            .ps-3 {
                padding-left: 0.75rem;
            }

            .ps-4 {
                padding-left: 1rem;
            }

            .pe-3 {
                padding-right: 0.75rem;
            }

            .pe-4 {
                padding-right: 1rem;
            }

            .text-center {
                text-align: center;
            }

            .text-end {
                text-align: right;
            }

            .align-top {
                vertical-align: top;
            }

            .text-sm {
                font-size: 0.875rem;
                line-height: 1.25rem;
            }

            .text-xs {
                font-size: 0.75rem;
                line-height: 1rem;
            }

            .font-bold {
                font-weight: 700;
            }

            .italic {
                font-style: italic;
            }

            .text-main {
                color: #5c6ac4;
            }

            .text-neutral-600 {
                color: #525252;
            }

            .text-neutral-700 {
                color: #404040;
            }

            .text-slate-300 {
                color: #cbd5e1;
            }

            .text-slate-400 {
                color: #94a3b8;
            }

            .text-white {
                color: #fff;
            }

            @page {
                margin: 0;
            }

            @media print {
                body {
                    -webkit-print-color-adjust: exact;
                }
            }
            .tableDetalle thead tr th{
                border-bottom: 1px dotted #000;
                background: #CCC
            }
            .tableDetalle tbody tr td{
                border-bottom: 1px dotted #000;
                border-left: 1px dotted #000;
                border-right: 1px dotted #000;
            }
        </style>
    </head>
    <body>
        <div class="text-align-left" style="position: absolute; z-index: 0; top: 60px; left: 0; width: 100%; opacity: 0.07;">
            <img id="bcTarget" class="img_bar_code" src="data:image/png;base64,{{$data['imagen']}}" alt="" width="100%" />
        </div>
        <div class="text-align-left" style="position: absolute; z-index: 1; top: 60px; left: 0; width: 100%; opacity: 0.1;">
            <div style="background: transparent; color: black; font-size: 80px; position: absolute; top: 20%; left: 50%; transform: translate(-50%, -50%);">
                {{$data['texMarca']}}
            </div>
        </div>
        <div>
            <div class="py-4">
                <!--cabecera-->
                <div class="px-14 py-6">
                    <table class="w-full border-collapse border-spacing-0">
                        <tbody>
                            <tr>
                                <td style="text-align: center;width: 80px;">
                                    <div id="txtDireccion">
                                        <img src="data:image/png;base64,{{$data['imagen']}}" alt="" style="width: 90px;"/>
                                    </div>
                                </td>
                                <td colspan="4" style="text-align: center;"><br>
                                    <p class="whitespace-nowrap font-bold text-main text-start">{!! html_entity_decode($data['company']->company_name) !!}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!--datos del cliente-->
                <div class="px-14 py-6" style="text-align: center;">
                    {{$data['customer']->nombres .' '.$data['customer']->apellidos}}
                </div>
                <!--inicio para tabla-->
                <div class="bg-slate-100 px-14 py-6 text-sm">
                    <table class="w-full border-collapse border-spacing-0">
                        <tbody>
                            <tr>
                                <td class="w-1/2 align-top">
                                    <div class="text-sm text-neutral-600">
                                        <center>
                                            <p class="font-bold">"TABLA DE AMORTIZACION"</p><br>
                                            <p class="font-bold">"CREDITO {{$data['prestamoTipo']->name}}"</p>
                                        </center>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-1/2 align-top">
                                    <div class="text-sm text-neutral-600">
                                        <center>
                                            <p class="font-bold">{{$data['carpeta']}}</p>
                                        </center>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-1/2 align-top">
                                    <div class="text-sm text-neutral-600">
                                        <center>
                                            <p class="font-bold">
                                            {!! html_entity_decode($data['ahorro']) !!}
                                            </p>
                                        </center>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!--tabla-->
                <div class="px-14 py-10 text-sm text-neutral-700">
                    <table class="w-full border-collapse border-spacing-0 tableDetalle">
                        <thead>                           
                            <tr>
                                <th style="width: 50px">CUOTAS</th>
                                <th style="width: 100px">FECHAS</th>
                                <th style="width: 50px">INTERES PERIODO</th>
                                <th style="width: 50px">CAPITAL AMORTIZADO</th>
                                @if($data['tipoPrestamo'] != 'A')
                                <th>DESGRAVAMEN</th>
                                @endif
                                <th>VALOR CUOTA</th>
                                <th>SALDO</th>
                            </tr>                           
                        </thead>
                        <tbody style="font-size: 11px;">
                            @foreach ($data['detalle'] as $value)
                            <tr>
                                <td style="text-align: center;">{{ $value['cuotas'] }}</td>
                                <td style="text-align: center;">{{ $value['fechas'] }}</td>
                                <td style="text-align: center;">{{ $value['interes'] }}</td>
                                <td style="text-align: center;">{{ $value['amoritizado'] }}</td>
                                @if($data['tipoPrestamo'] != 'A')
                                <td style="text-align: center;">{{ $value['desgravamen'] }}</td>
                                @endif
                                <td style="text-align: center;">{{number_format($value['cuotaPago'] , 2) }}</td>
                                <td style="text-align: center;">{{ str_replace("-", "", $value['deuda']) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <footer class="bottom-0 left-0 bg-slate-100 w-full text-neutral-600 text-center text-xs py-3">
                    {!! html_entity_decode($data['company']->company_name) !!}
                    <span class="text-slate-300 px-2">|</span>
                    {{$data['company']['email']}}
                    <span class="text-slate-300 px-2">|</span>
                    {{$data['company']['phone']}}
                </footer>


            </div>
        </div>
    </body>

</html>