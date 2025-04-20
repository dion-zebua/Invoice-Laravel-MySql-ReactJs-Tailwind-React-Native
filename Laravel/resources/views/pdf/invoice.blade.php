<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Document</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <style>
        @font-face {
            font-family: 'Poppins';
            font-weight: normal;
            font-style: normal;
        }

        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            padding: 60px 0 30px 0;
        }

        .poppins-regular {
            font-family: "Poppins", sans-serif !important;
            font-weight: 400;
            font-style: normal;
        }

        .page-break {
            page-break-before: always;
            margin-top: 60px;
        }

        #bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .status {
            background: rgb(93, 161, 224);
            position: fixed;
            top: 0;
            left: 0;
            width: 640px;
            padding: 10px 80px;
            text-transform: capitalize;
            font-style: italic;
            color: #dddddd;
            text-align: center;
        }

        .status #status-0 {
            float: left;
        }

        .status #status-2 {
            float: right;
        }

        #container {
            padding: 0 60px;
            /* background: url({{ public_path('img/bg-invoice.jpg') }});
            background-size: cover; */
            /* height: 100vh; */
            width: 680px;
            position: relative;
            z-index: 9;
        }

        #header {
            display: hidden;
        }

        #header img {
            width: auto;
            height: auto;
            max-width: 200px;
            max-height: 100px;
            margin-bottom: 10px;
            margin-top: 20px;
        }

        #header p {
            margin-bottom: -5px !important;
        }

        .address-details tr td {
            width: 50%;
        }

        .address-details div.border {
            border-bottom: 1px solid #6e6e6e;
            opacity: .5;
            height: 30px;
            margin-bottom: 10px;
            margin-top: 20px;
            font-weight: bold;
        }

        .address-details p.detail {
            opacity: .65;
            height: 30px;
            margin-bottom: 0px !important;
        }

        .address-details p {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 270px;
            margin-bottom: -5px !important;
        }


        .address-details .detail-left p {
            margin-right: auto !important;
        }

        .address-details .detail-right p {
            margin-left: auto !important;
        }

        #main table {
            white-space: nowrap;
            margin: 20px 0 10px 0;
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }

        #main table td,
        #main table th {
            border: 1px solid rgb(130, 130, 130);
            padding: 0px 8px 5px 8px;
            text-align: left;
            color: rgb(109, 109, 109);
        }

        #main table.total {
            page-break-inside: avoid;
        }

        #main table.total th {
            border: 0 !important;
            padding-bottom: 10px;
        }

        #main table th {
            background: rgb(238, 244, 251);
            color: rgb(50, 50, 50) !important;
        }

        .payment {
            padding-left: 50px !important;
            padding-right: 30px !important;
        }

        .payment ul {
            margin-top: 10px;
            padding-left: 20px;
            list-style-type: none;
        }

        .payment ul span {
            font-style: italic;
            margin-bottom: 2px;
            display: inline-block;
        }

        #footer table {
            margin: 50px 0 0px 0;
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            text-align: center;
        }

        #footer img {
            width: 100px;
            margin: 20px 0 15px 0;
        }


        h1 {
            opacity: .60;
            font-size: 17x px;
            margin-bottom: 5px;
        }

        p {
            line-height: 1.2;
            opacity: .5;
            letter-spacing: 0.3px;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .my-bold {
            font-weight: bold;
            opacity: 1;
        }
    </style>
</head>

<body class="poppins-regular" style="font-size: 14px;">
    {{-- <img id="bg" src="{{ public_path('img/bg-invoice.jpg') }}" alt="background"> --}}
    <div id="container">
        <div class="status">
            @for ($i = 0; $i < 3; $i++)
                <span id="status-{{ $i }}">
                    {{ $data->status }}
                    {{ Carbon\Carbon::parse($data->expire)->isPast() ? ', expired' : '' }}
                </span>
            @endfor
        </div>

        <div id="header">
            {{-- {{ asset($data->user->logo['result']) }} --}}
            <table style="width: 100%;">
                <tbody>
                    <tr>
                        <td style="width: 50%;">
                            <img style="max-width: 110px; max-height: 70px;" class="logo"
                                src="{{ $data->user->logo['result'] }}" alt="logo">
                            <p>Generator invoice</p>
                        </td>
                        <td style="width: 50%; text-align: right;">

                            <p style="font-weight: bold">#INV-{{ $data->code }}</p>
                            <p>Create {{ Carbon\Carbon::parse($data->created_at)->format('Y-m-d') }}</p>
                            <p>Expire {{ Carbon\Carbon::parse($data->expire)->format('Y-m-d') }}</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="details">
            <table class="address-details" style="width: 100%;">
                <tbody>
                    <tr>
                        <td style="padding-right: 30px;">

                            <div class="border">Invoice From :</div>
                        </td>
                        <td style="text-align: right; padding-left: 30px;">
                            <div class="border">Invoice To :</div>

                        </td>
                    </tr>
                    <tr>
                        <td class="detail-left">
                            <p class="detail">
                                From : {{ Str::limit($data->user->sales, 30, '...') }}
                            </p>
                            <p>
                                {{ Str::limit($data->user->name, 30, '...') }}
                            </p>

                            <p>
                                {{ Str::limit($data->user->address, 30, '...') }}
                            </p>
                            <p>
                                {{ Str::limit($data->user->email, 30, '...') }}
                            </p>
                            <p>
                                {{ Str::limit($data->user->telephone, 30, '...') }}
                            </p>
                        </td>
                        <td class="detail-right" style="text-align: right;">
                            <p class="detail">
                                To : {{ Str::limit($data->to_sales, 30, '...') }}
                            </p>
                            <p>
                                {{ Str::limit($data->to_name, 30, '...') }}
                            </p>
                            <p>
                                {{ Str::limit($data->to_address, 30, '...') }}
                            </p>
                            <p>
                                {{ Str::limit($data->to_email, 30, '...') }}
                            </p>
                            <p>
                                {{ Str::limit($data->to_telephone, 30, '...') }}
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>


        <div id="main">
            <table>
                <thead>
                    <tr>
                        <th style="max-width: 25px;">No</th>
                        <th style="min-width: 130px;">Product</th>
                        <th style="width: 30px;">Quantity</th>
                        <th style="max-width: 80px;">Unit</th>
                        <th style="max-width: 105px;">Price</th>
                        <th style="max-width: 105px;">Amount</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($products as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ Str::limit($item->name, 1000, '...') }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ Str::limit($item->unit, 10, '...') }}</td>
                            <td>{{ getRupiah($item->price) }}</td>
                            <td>{{ getRupiah($item->amount) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <table class="total">
                <thead style="">
                    <tr>
                        <th style=""></th>
                        <th style="width: 130px !important; "></th>
                        <th style="width: 130px !important;"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="3" style="border: 0; padding-bottom: 10px;"></td>
                    </tr>
                    <tr>
                        <td rowspan="3" class="payment">
                            <span class="my-bold">Grand Total in Words:</span><br>
                            <ul style="">
                                <li>

                                    <span>
                                        {{ $data->numberToWords }}
                                    </span>
                                </li>
                            </ul>
                        </td>
                        <td class="my-bold">Subtotal</td>
                        <td>{{ getRupiah($data->sub_total) }}</td>
                    </tr>
                    <tr>
                        <td>Discount</td>
                        <td>{{ getRupiah($data->discount) }}</td>
                    </tr>
                    <tr>
                        <td class="my-bold">Total</td>
                        <td>{{ getRupiah($data->total) }}</td>
                    </tr>
                    <tr>
                        <td rowspan="4" class="payment">
                            <span class="my-bold">Payment Methode:</span><br>
                            <ul style="">
                                <li>

                                    <span>
                                        {{ $data->user->payment_methode }}
                                    </span><br>
                                    <span>
                                        {{ $data->user->payment_number }}
                                    </span><br>
                                    <span>
                                        {{ $data->user->payment_name }}
                                    </span>
                                </li>
                            </ul>
                        </td>
                        <td>Tax</td>
                        <td>{{ getRupiah(($data->total * 11) / 100) }}</td>
                    </tr>
                    <tr>
                        <td class="my-bold">Grand Total</td>
                        <td>{{ getRupiah($data->grand_total) }}</td>
                    </tr>
                    <tr>
                        <td>Down Payment</td>
                        <td>{{ getRupiah($data->down_payment) }}</td>
                    </tr>
                    <tr>
                        <td>Remaining Balance</td>
                        <td>{{ getRupiah($data->remaining_balance) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>


        <div id="footer" style="page-break-inside: avoid;">
            <table>
                <tbody>
                    <tr>
                        <td></td>
                        <td>{{ Str::limit($data->to_name, 30, '...') }}</td>
                    </tr>
                    <tr>
                        <td>Thank you</td>
                        <td>
                            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAADICAIAAAAiOjnJAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAE/0lEQVR4nO3dQW7jOhBF0Z+P3v+W0zMPBIeQLF5SHZwzDGzJiR+IEkMWv76/v/+D2f7f/QH4nQSLhGCRECwSgkVCsEj8efvTr6+vNbc/THZcuu/4vZeuPJ5zGV952Ysv2fUNvhixSAgWCcEiIVgk3hfvB7uKykv33VWtX/oYl+478crrv0EjFgnBIiFYJASLxKni/aArwMc3unOpiaX9WPcPgDsvPljwDRqxSAgWCcEiIVgkPineOxMnpu94yMz7sgeOghGLhGCRECwSgkXiWcX7wcSF2xMn8S+V2BOr9Tv7A9YzYpEQLBKCRUKwSHxSvHeTvLsWqu96765l7Aum6Y1YJASLhGCRECwSp4r3XZO8d2rbg+694z/OxLn1bitswYhFQrBICBYJwSLx9ai10t1Gyl1Xfsgq/vWMWCQEi4RgkRAsEp8U78u6sS/r/jhxYnrZDPjEFvkHUy5lxCIhWCQEi4RgkZg/8z6xWfnEintZx8plv/7Yriu/GLFICBYJwSIhWCTeF+9db5Y77kzE71qgcufX7ybxuw45L0YsEoJFQrBICBaJ9xtWlx1AOr7yJV21/pD/JUxcrXTJZ1+3EYuEYJEQLBKCRWLCzHu3nHzX7PnEfzxculHHshl+CcEiIVgkBIvEhEOaJrYjf+bBQ93Kn64XzeG9XT/LnxixSAgWCcEiIVgkNs+87+pO/pBHiokT4gfbu+sYsUgIFgnBIiFYJD6Zeb8zjXvpyg85SmlZP8vuRuv79hixSAgWCcEiIVgkTm1Y7Y767PrY7HqGmLgHdVe1rtsMzyVYJASLhGCReF+8d13CuyJ6/CHvVL6XJqaXFf4PWfnzEyMWCcEiIVgkBIvEqUOanrmP9OCZZxiNdUvRl93IzDtLCRYJwSIhWCRObVidaOKimokHn451y1cmWrbZ9SQjFgnBIiFYJASLxCeHNB3cWVUyvm9XJneV/jMb9azv6mPEIiFYJASLhGCRONVtZuK09R3deaRjdwrhievWJz6OTGx68xMjFgnBIiFYJASLRH5I00F3SuquNi/j945dalE5fu8lC557jFgkBIuEYJEQLBKnivdlBekly/pZXrJsc++y01mteedBBIuEYJEQLBKfLJu5s+hi/OJLup4wEw946to9TmxCWXwMIxYJwSIhWCQEi8SpbjO/oIPKRF3jmn/iL6l4ZyfBIiFYJASLxCdr3pf1PZ/YfGb84oNlvdqXre1ZduztixGLhGCRECwSgkXik+K9KxsvXWrXipRxP5llS27GLj0WFH9YIxYJwSIhWCQEi8SEmfeJfV3uvHdXg/VnHot656Hh0sewbIalBIuEYJEQLBLv17zv0i3NvmPiJP7Ex5EFm07vMGKRECwSgkVCsEi8n3nf1e/wId1XOsvWnk9cPm/mnQcRLBKCRUKwSMzv8z627GihiWXysrbvE+fWu6/Mmnd2EiwSgkVCsEhs3rA6vvLEA566hSK7WuIcLFsmZOadnQSLhGCRECwSnxTvuyzbkjo2sQDfdbDUxC3HPzFikRAsEoJFQrBIPLp47/rYXHrxspbx4yvf+W/BxK2wJ38jIxYJwSIhWCQEi0R+SNOuK3c7Nu/caHzlZQ8r4ytPWXpvxCIhWCQEi4RgkThVvC9rxjK+7651Mrs+xq6uk3c+1YsRi4RgkRAsEoJF4ll93vk1jFgkBIuEYJEQLBKCRUKwSAgWib+p5NCpmGw+jQAAAABJRU5ErkJggg=="
                                alt="QR">
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td style="opacity: 0.6;">Head of Sales</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
