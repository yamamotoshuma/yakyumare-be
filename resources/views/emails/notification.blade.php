<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['subject'] }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            padding: 20px;
            margin-bottom: 10px;
            text-align: center;
        }
        .content {
            padding: 20px;
            text-align: center;
            margin-bottom: 15px;
        }
        .message{
            text-align: center;
            white-space: pre-wrap;
            word-wrap:break-word;
            margin-bottom: 10px;
        }
        .button {
            display: block;
            width: 250px;
            max-width: 100%;
            text-align: center;
            background-color: #ff8c00;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px auto 0;
            font-size: 18px;
            box-sizing: border-box;
        }
        .button:hover {
            background-color: #e07b00;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ $data['subject'] }}</h2>
        </div>
        <div class="content">
            @if ($data['url'] != null)
                <p class="message">{{ $data['message'] }}</p>
                <a href="{{ $data['url'] }}" class="button">確認する</a>
            @else
                <p class="message" style="text-align: left !important;">{{ $data['message'] }}</p>
            @endif
        </div>
        <div class="footer">
            <p>このメールは自動生成されたメールです。<br />返信しないでください。</p>
            <p style="margin-top: 15px;">© 2024 やきゅまーれ. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
