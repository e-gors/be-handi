<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Registration</title>
    <style type="css/text">
        body{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        #container{
            max-width: 600px;
            margin: 0 auto;
        }
        #header{
            height: 100px;
            background: linear-gradient(180deg, #527593, #092240);
            text-align: center;
        }
        #logo{
            width: 160px;
        }
        #main{
            background-color: #DFE0E4;
            padding: 20px;
        }
        #main p{
            margin: 0;
        }
        #main a .button{
            background: linear-gradient(180deg, #527593, #092240);
            color: white;
            padding: 10px 30px;
            margin-top: 30px;
            border: none;
            transition: .5s;
        }
        #main a .button:hover{
            background: linear-gradient(90deg, #527593, #092240);
        }
        #main p span{
            text-decoration: underline;
        }
        #main .thank-you{
            margin-top: 30px;
        }
        #main .if-not{
            margin-top: 50px;
            background-color: #FF6666;
            color: white;
            padding: 5px 10px;
        }
        #main .if-not p {
            font-size: 11px;
        }
        #footer{
            background: linear-gradient(0deg, #527593, #092240);
        }
        #footer p{
            padding: 20px 0;
            color: white;
            text-align: center;
        }
    </style>
</head>

<body>
    <div id="container">
        <div class="" id="header">
            <img src="{{ asset('storage/assets/handi-logo.png') }}" alt="logo" id="logo">
        </div>
        <div id="main">
            <h1>Hey there {{ $user->first_name }},</h1>
            <p>Welcome to {{ env('APP_NAME') }}, we are helping people to connect for the benefits of each other where others are searching for job while others are looking for skilled workers.</p>
            <p style="margin-top: 20px;">If you are interested in our company, please click the button bellow for your account confirmation. Thank you.</p>
            <a href="{{ env('APP_BASE_URL') . '/confirmed/'. $user->uuid }}"><button class="button">Verify Email</button></a>
            <div class="thank-you">
                <p>Thank you,</p>
                <p>{{env('APP_NAME')}} Company</p>
            </div>
            <div class="if-not">
                <p>If you didn't register to Handi, please disregards this message. Thank you.</p>
            </div>
        </div>
        <div id="footer">
            <p>&copy; {{date('Y')}} {{env('APP_NAME')}}. All Rights Reserved.</p>
        </div>
    </div>
</body>

</html>