<!doctype html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Payslip</title>
    </head>
    <style>
        body {
            padding: 0;
            font-family: Arial, sans-serif;
        }
        @font-face {
            font-family: 'AkagiPro-extrabold';
            src: url("{{ asset('fonts/akagipro-extrabold.ttf') }}") format('truetype');
            font-weight: bold;
            font-style: normal;
        }
        @font-face {
            font-family: 'AkagiPro-bold';
            src: url("{{ asset('fonts/akagipro-bold.ttf') }}") format('truetype');
            font-weight: bold;
            font-style: normal;
        }
        h2 {
            font-family: 'AkagiPro-extrabold';
        }
        .payroll_label {
            font-family: 'akagipro-bold';
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            text-align: left;
        }
        .email {
            color: rgb(138, 210, 90);
            text-decoration: underline;
        }
        .logo_container {
            text-align: center;
            margin-top: 20px;
            margin:auto;
        }
        .table_header {
            border-bottom: 2px solid black;
            border-top: 2px solid black;
            font-weight: bold;
            font-family: 'AkagiPro-extrabold';
        }
    </style>
    <body style="width: 210mm; height: 297mm; margin: 0; padding: 0; font-family: 'AkagiPro-extrabold'">
        @foreach ($payroll_details as $payroll)
            @include('reports.docs.payslip', ['payroll' => $payroll])
            <hr style="margin-top: 0px; margin-bottom: 0px;" >
            @include('reports.docs.payslip', ['payroll' => $payroll])
        @endforeach
    </body>
    {{-- <script>
        window.onload = function() {
            window.print();
        }
    </script> --}}
</html>
