<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Download</title>

    <style>
        body {
            margin: 0;
        }
        body, * {
            font-family: sans-serif;
            font-size: 16px;
        }
        body div {
            max-width: 210mm;
        }
        .title {
            font-size: 1.2rem;
        }

        .signature p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div>
        <h2 class="title">Cover Letter for {{ $optimization->role_company }}</h2>
        <p class="introduction">Dear Hiring Manager,</p>
        @foreach($optimization->ai_response['cover_letter'] as $paragraph)
            <p class="content">{{$paragraph}}</p>
        @endforeach
        <div class="signature">
            <p>Regards,</p>
            <p>{{$optimization->user->name}}</p>
        </div>
    </div>
</body>
</html>
