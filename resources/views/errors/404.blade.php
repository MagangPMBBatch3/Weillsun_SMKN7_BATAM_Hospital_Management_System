{{-- @extends('errors::minimal') --}}

@section('title', __('Not Found'))
@section('code', '404')

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            height: 100vh;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .page_404 {
            text-align: center;
            width: 100%;
        }

        .four_zero_four_bg {
            background-image: url("https://cdn.dribbble.com/users/285475/screenshots/2083086/dribbble_1.gif");
            height: 420px;               
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
        }

        .four_zero_four_bg h1 {
            font-size: 100px;            
            font-weight: 800;
            color: #111827;
            line-height: 1;
        }

        .contant_box_404 {
            margin-top: 10px;
        }

        .contant_box_404 h3 {
            font-size: 32px;            
            margin-bottom: 12px;
            color: #1f2937;
        }

        .contant_box_404 p {
            font-size: 20px;             
            color: #6b7280;
        }
    </style>
</head>

<body>
    <section class="page_404">
        <div class="four_zero_four_bg">
            <h1>404</h1>
        </div>

        <div class="contant_box_404">
            <h3>Looks like you're lost</h3>
            <p>The page you are looking for is not available.</p>
        </div>
    </section>
</body>

</html>

