<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Related Job Post</title>
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
            padding: 20px;
        }
        #main .greeting{
            font-size: 20px;
        }
        #main p{
            margin: 0;
        }
        #main a .button{
            background: linear-gradient(180deg, #527593, #092240);
            color: white;
            padding: 10px 30px;
            margin: 20px auto;
            border: none;
            transition: .5s;
            cursor: pointer;

        }
        #main a .button:hover{
            background: linear-gradient(90deg, #527593, #092240);
        }
        #main p span{
            text-decoration: underline;
        }
        #main .details{
            margin-top: 30px;
            margin-bottom: 30px;
        }
        #main .details .detail{
            display: flex;
            margin: 20px 0;
            border-bottom: 1px solid #BEBEBE;
        }
        #main .details .detail .label{
            font-weight: 500;
        }
        #main .details .detail .value{
            font-weight: bold;
            margin-left: 5px;
        }
        #main .details .message .label{
            font-weight: 500;
        }
        #main .details .message .value{
            font-weight: bold;
            margin-top: 10px;
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
            <h1 class="greeting">Hello {{ $user['full_name'] }},</h1>
            <p>We have a new job post that match your job categories. Here are the details: </p>

            <div class="details">
                <div class="detail">
                    <p class="label">Job Title: </p>
                    <p class="value">{{ $post->title }}</p>
                </div>
                <div class="detail">
                    <p class="label">Position: </p>
                    <p class="value">{{ $post->position }}</p>
                </div>
                <div class="detail">
                    <p class="label">Client: </p>
                    <p class="value">{{ $owner->full_name }}</p>
                </div>
                @if(isset($locations) && !empty($locations))
                <div class="detail">
                    <p class="label">Location: </p>
                    <p class="label">, </p>
                    @foreach($locations as $location)
                    <p class="value">{{ $location }}</p>
                    @endforeach
                </div>
                @endif
                <div class="detail">
                    @if (isset($post->rate) && !empty($post->rate))
                    <p class="label">Salary: </p>
                    <p class="value">{{ $post->rate }} / day</p>
                    @else
                    <p class="label">Project Budget: </p>
                    <p class="value">{{ $post->budget }}</p>
                    @endif
                </div>
                <div class="message">
                    <p class="label">Description: </p>
                    <div>{!! $post->description !!}</div>
                </div>
            </div>

            <a href="{{ $post->post_url }}"><button class="button">Apply to New Job Post</button></a>

            <p>Best regards,</p>
            <p>{{env('APP_NAME')}}</p>
            <div class="if-not">
                <p>If you didn't register to Handi, please disregard this message. Thank you.</p>
            </div>
        </div>

        <div id="footer">
            <p>&copy; {{date('Y')}} {{env('APP_NAME')}}. All Rights Reserved.</p>
        </div>
    </div>

    </div>
</body>

</html>