<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        :root {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            margin:           0;
            background-color: #f5f5f5;
            color:            black;
        }

        @media (prefers-color-scheme: dark) {
            body {
                background-color: #1f1f1f;
                color:            #fff;
            }
        }

        header {
            background-color: #5f3dc4;
            color:            #fff;
            padding:          8px;
            text-align:       center;
        }

        main {
            padding: 2rem;
        }

        span {
            display:       block;
            margin-bottom: .25rem;
        }

        p {
            margin-bottom: 2rem;
            white-space:   pre-wrap;
        }

        a {
            font-weight:     bold;
            color:           #3b82f6;
            text-decoration: none;
        }

        .message {
            word-break: break-word;
            max-width:  500px;
        }


    </style>
    <title>{{$senderEmail}} - {{$senderSubject}}</title>
</head>
<body>
<header>
    <h1>Contact Form</h1>
</header>
<main>
    <div class="sender">
        <span><strong>Name:</strong> {{$senderName}}</span>
        <span><strong>Email:</strong> <a href="mailto:{{$senderEmail}}">{{$senderEmail}}</a></span>
    </div>
    <p><strong>Subject:</strong> {{$senderSubject}}</p>
    <p class="message"><strong>Message:</strong><br/>{{$senderMessage}}</p>
</main>
</body>
</html>
