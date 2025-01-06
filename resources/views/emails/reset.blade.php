<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>パスワードリセット</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #ff8c00;
            font-size: 24px;
            text-align: center;
        }
        h3 {
            color: #ff8c00;
            font-size: 20px;
            text-align: center;
        }
        p {
            font-size: 16px;
        }
        .button {
            display: block;
            width: calc(100% - 40px);
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
            color: #888;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>やきゅまーれ</h1>
        <h3>パスワードリセットのご案内</h3>
        <p>このメールはパスワードのリセットリクエストにより送信されています。</p>
        <a href="{{ $url }}" class="button">パスワードのリセット</a>
        <p>このパスワードリセット用リンクは 60分で有効期限が切れます。</p>
        <p>パスワードのリセットリクエストをしていない場合には、本メールへの対応は不要です。</p>
        <p>よろしくお願いいたします<br>やきゅまーれ</p>
        <p>もし"パスワードのリセット"がクリックできない場合には下記URLをコピー＆ペースしてアクセスしてください<br>{{ $url }}</p>
        <div class="footer">
            <p>© 2024 やきゅまーれ. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
