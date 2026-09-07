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
            padding-top: 1.5rem;
            padding-bottom: 1.5em;
        }

        .py-3 {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }

        .py-4 {
            padding-top: {{$data['company']->margen_top}};

            padding-bottom: {{$data['company']->margen_dow}};

            margin-left: {{$data['company']->margen_left}};

            margin-right: {{$data['company']->margen_right}};
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




        .table thead tr th {
            border-bottom: 1px dotted #000;

            background: {{$data['company']->color_tabla}};
            color: {{$data['company']->color_texto}};
        }

        .table tbody tr td {
            border-bottom: 1px dotted #000;
            border-left: 1px dotted #000;
            border-right: 1px dotted #000;
        }
    </style>

</head>

<body>
    <div class="py-4">
        <div class="px-14 py-6">
            <img src="data:image/png;base64,{{$data['imagen']}}" style="width: 10%;">
            <table class="w-full border-collapse border-spacing-0">
                <tbody>
                    <tr>
                        <td class="w-full align-top">
                            <p class="whitespace-nowrap font-bold text-main text-start">
                                {!! html_entity_decode($data['company']->company_name) !!}
                            </p>
                        </td>

                        <td class="align-top">
                            <div class="text-sm">
                                <table class="border-collapse border-spacing-0">
                                    <tbody>
                                        <tr>
                                            <td class="border-r pe-4">
                                                <div>
                                                    <p class="whitespace-nowrap text-slate-400 text-end">Fecha Inicio</p>
                                                    <p class="whitespace-nowrap font-bold text-main text-end">{{$data['caja']['date_inicial']}}</p>
                                                </div>
                                            </td>
                                            <td class="ps-4">
                                                <div>
                                                    <p class="whitespace-nowrap text-slate-400 text-end">Hora Inicio</p>
                                                    <p class="whitespace-nowrap font-bold text-main text-end">{{$data['caja']['hour_inicial']}}</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <br>
        <div class="px-14 py-10 text-sm text-neutral-700">
            <table style="width: 100%;" class="table">
                <thead style="font-size: 11px;">
                    <tr>
                        <th colspan="2" style="width: 50px;text-align: center;">
                            DETALLE CAJAS
                        </th>
                    </tr>
                    <tr>
                        <th style="width: 50px;text-align: center;">CONCEPTO </th>
                        <th style="width: 100px;text-align: center;">VALOR</th>

                    </tr>
                </thead>
                <tbody style="font-size: 11px;">
                    <tr>
                        <td>APERTURA DE CAJA</td>
                        <td style="text-align: center;">{{$data['caja']->valor_inicial}}</td>
                    </tr>
                    <tr>
                        <td>CIERRE DE CAJA</td>
                        <td style="text-align: center;">{{$data['caja']->total_final}}</td>
                    </tr>

                </tbody>
            </table>
        </div>
        <br>
        <div class="px-14 py-10 text-sm text-neutral-700">
            <table style="width: 100%;" class="table">
                <thead style="font-size: 11px;">
                    <tr>
                        <th colspan="4" style="width: 50px;text-align: center;">
                            DETALLE OTROS VALORES
                        </th>
                    </tr>
                    <tr>
                        <th style="width: 50px;text-align: center;">TRANSACCION </th>
                        <th style="width: 50px;text-align: center;">CONCEPTO </th>
                        <th style="width: 50px;text-align: center;">DESCRIPCION </th>
                        <th style="width: 100px;text-align: center;">VALOR</th>

                    </tr>
                </thead>
                <tbody style="font-size: 11px;">
                    @php
                    $total = 0;
                    $ret = 0;
                    @endphp
                    @foreach ($data['otrosValores'] as $det)
                    @php
                    $total += $det->valor;
                    $ret += $det->valor;
                    @endphp
                    <tr>
                        <td>Otros Valores</td>
                        
                        <td>{{$det->name}}</td>
                        <td>{{$det->descripcion}}</td>
                        <td style="text-align: right;">$ {{$det->valor}}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="3"></td>
                        <td style="text-align: right;">$ {{number_format($total, 2)}}</td>
                    
                    </tr>
                </tbody>
            </table>
        </div>
        <br>

        <div class="px-14 py-10 text-sm text-neutral-700">
            <table style="width: 100%;" class="table">
                <thead style="font-size: 11px;">
                    <tr>
                        <th colspan="6" style="width: 50px;text-align: center;">
                            DEPOSITOS
                        </th>
                    </tr>
                    <tr>
                        <th style="width: 50px;text-align: center;">TRANSACCION </th>
                        <th style="width: 100px;text-align: center;">DOCUMENTO</th>
                        <th style="width: 100px;text-align: center;">NOMBRE</th>
                        <th style="width: 100px;text-align: center;">OBSERVACION</th>
                        <th style="width: 50px;text-align: center;">VALOR</th>
                        <th style="width: 50px;text-align: center;">STATUS</th>
                    </tr>
                </thead>
                <tbody style="font-size: 11px;">
                    @php
                    $total = 0;
                    $valdep = 0;
                    @endphp
                    @foreach ($data['detalleDepositos'] as $det)
                    @php
                    $total += $det->valor_movimiento;
                    $valdep += $det->valor_movimiento;
                    @endphp
                    <tr>
                        <td>DEPOSITOS</td>
                        <td style="text-align: center;">{{$det->comprobante}}</td>
                        <td>
                            {{ $det->customer_name}}<br>
                            <b>{{$det->customer_ruc}}</b>
                        </td>
                        <td>{{$det->observation}}</td>
                        <td style="text-align: right;">${{$det->valor_movimiento}}</td>
                        <td style="text-align: center;">PROCESADO </td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="4"></td>
                        <td style="text-align: right;">$ {{number_format($total, 2)}}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="px-14 py-10 text-sm text-neutral-700">
            <table style="width: 100%;" class="table">
                <thead style="font-size: 11px;">
                    <tr>
                        <th colspan="6" style="width: 50px;text-align: center;">
                            SUMA CLIETES
                        </th>
                    </tr>
                    <tr>
                        <th style="width: 50px;text-align: center;">TRANSACCION </th>
                        <th style="width: 100px;text-align: center;">DOCUMENTO</th>
                        <th style="width: 100px;text-align: center;">NOMBRE</th>
                        <th style="width: 100px;text-align: center;">OBSERVACION</th>
                        <th style="width: 50px;text-align: center;">VALOR</th>
                        <th style="width: 50px;text-align: center;">STATUS</th>
                    </tr>
                </thead>
                <tbody style="font-size: 11px;">
                    @php
                    $totalSumaClientes = 0;
                    $valdepSumaClientes = 0;
                    @endphp
                    @foreach ($data['detalleSumaClientes'] as $det)
                    @php
                    $totalSumaClientes += $det->valor_movimiento;
                    $valdepSumaClientes += $det->valor_movimiento;
                    @endphp
                    <tr>
                        <td>DEPOSITOS</td>
                        <td style="text-align: center;">{{$det->comprobante}}</td>
                        <td>
                            {{ $det->customer_name}}<br>
                            <b>{{$det->customer_ruc}}</b>
                        </td>
                        <td>{{$det->observation}}</td>
                        <td style="text-align: right;">${{$det->valor_movimiento}}</td>
                        <td style="text-align: center;">PROCESADO </td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="4"></td>
                        <td style="text-align: right;">$ {{number_format($totalSumaClientes, 2)}}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="px-14 py-10 text-sm text-neutral-700">
            <table style="width: 100%;" class="table">
                <thead style="font-size: 11px;">
                    <tr>
                        <th colspan="6" style="width: 50px;text-align: center;">
                            SUMA EMPRESA
                        </th>
                    </tr>
                    <tr>
                        <th style="width: 50px;text-align: center;">TRANSACCION </th>
                        <th style="width: 100px;text-align: center;">DOCUMENTO</th>
                        <th style="width: 100px;text-align: center;">NOMBRE</th>
                        <th style="width: 100px;text-align: center;">OBSERVACION</th>
                        <th style="width: 50px;text-align: center;">VALOR</th>
                        <th style="width: 50px;text-align: center;">STATUS</th>
                    </tr>
                </thead>
                <tbody style="font-size: 11px;">
                    @php
                    $totalSumaEmpresa = 0;
                    $valdepSumaEmpresa = 0;
                    @endphp
                    @foreach ($data['detalleSumaEmpresa'] as $det)
                    @php
                    $totalSumaEmpresa += $det->valor_movimiento;
                    $valdepSumaEmpresa += $det->valor_movimiento;
                    @endphp
                    <tr>
                        <td>DEPOSITOS</td>
                        <td style="text-align: center;">{{$det->comprobante}}</td>
                        <td>
                            {{ $det->customer_name}}<br>
                            <b>{{$det->customer_ruc}}</b>
                        </td>
                        <td>{{$det->observation}}</td>
                        <td style="text-align: right;">${{$det->valor_movimiento}}</td>
                        <td style="text-align: center;">PROCESADO </td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="4"></td>
                        <td style="text-align: right;">$ {{number_format($totalSumaEmpresa, 2)}}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="px-14 py-10 text-sm text-neutral-700">
            <table style="width: 100%;" class="table">
                <thead style="font-size: 11px;">
                    <tr>
                        <th colspan="6" style="width: 50px;text-align: center;">
                            RECAUDACION
                        </th>
                    </tr>
                    <tr>
                        <th style="width: 50px;text-align: center;">TRANSACCION </th>
                        <th style="width: 100px;text-align: center;">CODE</th>
                        <th style="width: 100px;text-align: center;">NOMBRE</th>
                        <th style="width: 100px;text-align: center;">OBSERVACION</th>
                        <th style="width: 50px;text-align: center;">VALOR</th>
                        <th style="width: 50px;text-align: center;">STATUS</th>
                    </tr>
                </thead>
                <tbody style="font-size: 11px;">
                    @php
                    $total = 0;
                    $recau = 0;
                    @endphp
                    @foreach ($data['detalleRecaudaciones'] as $det)
                    @php
                    $total += $det->valor_movimiento;
                    $recau += $det->valor_movimiento;
                    @endphp
                    <tr>
                        <td>RECAUDACION </td>
                        <td style="text-align: center;">{{$det->code}}</td>
                        <td>
                            {{ $det->customer_name}}<br>
                            <b>{{$det->customer_ruc}}</b>
                        </td>
                        <td>{{$det->observation}}</td>
                        <td style="text-align: right;">$ {{$det->valor_movimiento}}</td>
                        <td style="text-align: center;">PROCESADO </td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="4"></td>
                        <td style="text-align: right;">$ {{number_format($total, 2)}}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="px-14 py-10 text-sm text-neutral-700">
            <table style="width: 100%;" class="table">
                <thead style="font-size: 11px;">
                    <tr>
                        <th colspan="6" style="width: 50px;text-align: center;">
                            RETIRO
                        </th>
                    </tr>
                    <tr>
                        <th style="width: 50px;text-align: center;">TRANSACCION </th>
                        <th style="width: 100px;text-align: center;">DOCUMENTO</th>
                        <th style="width: 100px;text-align: center;">NOMBRE</th>
                        <th style="width: 100px;text-align: center;">OBSERVACION</th>
                        <th style="width: 50px;text-align: center;">VALOR</th>
                        <th style="width: 50px;text-align: center;">STATUS</th>
                    </tr>
                </thead>
                <tbody style="font-size: 11px;">
                    @php
                    $total = 0;
                    $ret = 0;
                    @endphp
                    @foreach ($data['detalleRetiros'] as $det)
                    @php
                    $total += $det->valor_movimiento;
                    $ret += $det->valor_movimiento;
                    @endphp
                    <tr>
                        <td>RETIRO</td>
                        <td style="text-align: center;">{{$det->comprobante}}</td>
                        <td>
                            {{ $det->customer_name}}<br>
                            <b>{{$det->customer_ruc}}</b>
                        </td>
                        <td>{{$det->observation}}</td>
                        <td style="text-align: right;">$ {{$det->valor_movimiento}}</td>
                        <td style="text-align: center;">PROCESADO </td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="4"></td>
                        <td style="text-align: right;">$ {{number_format($total, 2)}}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="px-14 py-10 text-sm text-neutral-700">
            <table style="width: 100%;" class="table">
                <thead style="font-size: 11px;">
                    <tr>
                        <th colspan="6" style="width: 50px;text-align: center;">
                            ENTREGA DE CREDITOS
                        </th>
                    </tr>
                    <tr>
                        <th style="width: 50px;text-align: center;">TRANSACCION </th>
                        <th style="width: 100px;text-align: center;">DOCUMENTO</th>
                        <th style="width: 100px;text-align: center;">NOMBRE</th>
                        <th style="width: 100px;text-align: center;">OBSERVACION</th>
                        <th style="width: 50px;text-align: center;">VALOR</th>
                        <th style="width: 50px;text-align: center;">STATUS</th>
                    </tr>
                </thead>
                <tbody style="font-size: 11px;">
                    @php
                    $totalEntregaCredit = 0;
                    $retEntregaCredit = 0;
                    @endphp
                    @foreach ($data['detalleEntregaCreditos'] as $det)
                    @php
                    $totalEntregaCredit += $det->valor_movimiento;
                    $retEntregaCredit += $det->valor_movimiento;
                    @endphp
                    <tr>
                        <td>RETIRO</td>
                        <td style="text-align: center;">{{$det->comprobante}}</td>
                        <td>
                            {{ $det->customer_name}}<br>
                            <b>{{$det->customer_ruc}}</b>
                        </td>
                        <td>{{$det->observation}}</td>
                        <td style="text-align: right;">$ {{$det->valor_movimiento}}</td>
                        <td style="text-align: center;">PROCESADO </td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="4"></td>
                        <td style="text-align: right;">$ {{number_format($totalEntregaCredit, 2)}}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="px-14 py-10 text-sm text-neutral-700">
            <table style="width: 100%;" class="table">
                <thead style="font-size: 11px;">
                    <tr>
                        <th colspan="6" style="width: 50px;text-align: center;">
                            GASTOS
                        </th>
                    </tr>
                    <tr>
                        <th style="width: 50px;text-align: center;">TRANSACCION </th>
                        <th style="width: 100px;text-align: center;">CODIGO</th>
                        <th style="width: 100px;text-align: center;">NOMBRE</th>
                        <th style="width: 100px;text-align: center;">OBSERVACION</th>
                        <th style="width: 50px;text-align: center;">VALOR</th>
                        <th style="width: 50px;text-align: center;">STATUS</th>
                    </tr>
                </thead>
                <tbody style="font-size: 11px;">
                    @php
                    $total = 0;
                    $gast = 0;
                    @endphp
                    @foreach ($data['detalleGastos'] as $det)
                    @php
                    $total += $det->valor_movimiento;
                    $gast += $det->valor_movimiento;
                    @endphp
                    <tr>
                        <td>GASTOS</td>
                        <td style="text-align: center;">{{$det->code}}</td>
                        <td>
                            {!! $det->customer_name !!}<br>
                            <b>{{$det->customer_ruc}}</b>
                        </td>
                        <td>{{$det->observation}}</td>
                        <td style="text-align: right;">$ {{$det->valor_movimiento}}</td>
                        <td style="text-align: center;">PROCESADO </td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="4"></td>
                        <td style="text-align: right;">$ {{number_format($total, 2)}}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="px-14 py-10 text-sm text-neutral-700">
            <table style="width: 100%;" class="table">
                <thead style="font-size: 11px;">
                    <tr>
                        <th colspan="3" style="width: 50px;text-align: center;">
                            RESUMEN
                        </th>
                    </tr>
                    <tr>
                        <th style="width: 50px;text-align: center;">DETALLE </th>
                        <th style="width: 100px;text-align: center;">INGRESOS</th>
                        <th style="width: 100px;text-align: center;">EGRESOS</th>
                    </tr>
                </thead>
                <tbody style="font-size: 11px;">
                    <tr>
                        <td>DEPOSITO</td>
                        <td style="text-align: right;">${{number_format($valdep,2)}}</td>
                        <td style="text-align: right;">$ 0.00</td>
                    </tr>
                    <tr>
                        <td>SUMA CLIENTES</td>
                        <td style="text-align: right;">${{number_format($valdepSumaClientes,2)}}</td>
                        <td style="text-align: right;">$ 0.00</td>
                    </tr>
                    <tr>
                        <td>SUMA EMPRESA</td>
                        <td style="text-align: right;">${{number_format($valdepSumaEmpresa,2)}}</td>
                        <td style="text-align: right;">$ 0.00</td>
                    </tr>
                    <tr>
                        <td>RECAUDACION</td>
                        <td style="text-align: right;">${{number_format($recau ,2)}}</td>
                        <td style="text-align: right;">$ 0.00</td>
                    </tr>
                    <tr>
                        <td>RETIRO </td>
                        <td style="text-align: right;">$ 0.00</td>
                        <td style="text-align: right;">${{number_format($ret, 2)}}</td>
                    </tr>
                    <tr>
                        <td>ENTREGA DE CREDITOS </td>
                        <td style="text-align: right;">$ 0.00</td>
                        <td style="text-align: right;">${{number_format($retEntregaCredit, 2)}}</td>
                    </tr>
                    <tr>
                        <td>GASTOS</td>
                        <td style="text-align: right;">$ 0.00</td>
                        <td style="text-align: right;">${{number_format($gast, 2)}}</td>
                    </tr>
                    <tr>
                        <td style="text-align: right;"><b>TOTAL</b></td>
                        <td style="text-align: right;"><b>$ {{number_format( ($valdep +$recau +$valdepSumaClientes+$valdepSumaEmpresa),2)}}</b></td>
                        <td style="text-align: right;"><b>$ {{number_format($ret+$gast +$retEntregaCredit, 2)}}</b></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="px-14 py-10 text-sm text-neutral-700">
            <table style="width: 100%;" class="table">
                <thead>
                    <tr>
                        <th colspan="4" style="width: 50px;text-align: center;">
                            Arqueo de Caja
                        </th>
                    </tr>
                    <tr>
                        <th style="width: 100px;text-align: left;">Lista</th>
                        <th style="width: 50px;text-align: right;">Valor</th>
                        <th style="width: 50px;text-align: center;">Cantidad</th>
                        <th style="width: 50px;text-align: right;">Total</th>

                    </tr>
                </thead>
                <tbody style="font-size: 11px;">
                    @foreach ($data['denom'] as $deno)
                    <tr>
                        <td class="small">{{$deno['nombre']}}</td>
                        <td class="small" style="text-align: right;">$ {{$deno['valor']}}</td>
                        <td class="small" style="text-align: center;">{{$deno['cantidad']}}</td>
                        <td class="small" style="text-align: right;">$ {{number_format($deno['totalValor'],2)}}</td>

                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align: right;">TOTAL</td>
                        <td style="text-align: right;">${{$data['caja']['total']}}</td>
                    </tr>
                </tfoot>



                </tbody>
            </table>
        </div>
        <div class="px-14 py-10 text-sm text-neutral-700">
            <center>
                <table style="width: 100%;">
                    <tr>
                        <td>_______________________________________</td>
                        <td></td>
                        <td>_______________________________________</td>
                    </tr>
                    <tr>
                        <td><b>ENTREGADO POR:</b></td>
                        <td></td>
                        <td><b>RECIBIDO POR</b></td>
                    </tr>
                    <tr>
                        <td><b>CI:</b></td>
                        <td></td>
                        <td><b>CI:</b></td>
                    </tr>
                </table>
            </center>
        </div>
        <div class="px-14 text-sm text-neutral-700">
            <p class="text-main font-bold">INFORME CIERRE</p>
            <p>Usuario Inicia: {{$data['caja']['user_name_inicial']}}</p>
            <p>Fecha Inicia: {{$data['caja']['date_inicial'] .' '. $data['caja']['hour_inicial']}}</p>
            <p>Usuario Cierra: {{$data['caja']['user_finish_name']}}</p>
            <p>Fecha Cierra: {{$data['caja']['date_finish'] .' '. $data['caja']['hour_finish']}}</p>

        </div>

        <div class="px-14 py-10 text-sm text-neutral-700">
            <p class="text-main font-bold">Notas</p>
            <p class="italic">Cierre de caja sin Novedad.</p>
        </div>

        <footer class="bottom-0 left-0 bg-slate-100 w-full text-neutral-600 text-center text-xs py-3">
            {!! html_entity_decode($data['company']->company_name) !!}
            <span class="text-slate-300 px-2">|</span>
            {{$data['company']['email']}}
            <span class="text-slate-300 px-2">|</span>
            {{$data['company']['phone']}}
        </footer>
    </div>
</body>

</html>