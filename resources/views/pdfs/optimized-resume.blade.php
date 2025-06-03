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
        h1, h2, h3, h4 {
            margin: 0;
        }
        .name {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .job-title {
            margin-top: 1rem;
            font-size: 1rem;
            margin-bottom: 1rem;
            font-weight: bold;
        }
        .section-title {
            font-size: 1.35rem;
            margin-top: 1rem;
        }
        .exp-title {
            font-size: 1.1rem;
            margin: 0.75rem 0 0 0;
        }
        .exp-period {
            font-size: .8rem;
            margin-bottom: .9rem;
        }
        ul {
            list-style: disc;
            padding-left: 1rem;
            margin: 0;
        }

        p {
            margin: 0;
        }

        .footer {
            font-size: .9rem;
            margin-top: 1rem;
        }

        em {
            display: block;
            margin-top: 0.75rem;
        }
    </style>
</head>
<body>
    <div>
        {!! $optimization->optimized_result !!}
    </div>
</body>
</html>
