<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <style>
            body, html {
                border: 0;
                padding: 0;
                margin: 0;
                margin-left: .5rem;
                margin-right: .5rem;
                min-height: 100vh;
                background-color: #16323b;
                font-family: Merriweather Sans,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;
            }

            .container {
                display: flex;
                display: -webkit-box;
                display: -moz-box;
                display: -ms-flexbox;
                display: -webkit-flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
            }

            img.logo {
                max-height: 120px;
            }

            span.err {
                color: #fff;
                text-align: center;
            }

            span.err.err-main {
                font-size: 1.20rem;
                margin-top: .5rem;
                text-transform: uppercase;
                font-weight: bold;
            }

            span.err.err-sign {
                font-size: 1rem;
            }
        </style>
    </head>
    <body>
        <main class="container">
            <a href="https://www.vatsim.net"><img class="logo" alt="VATSIM Network" src="{{ asset('assets/img/vatsim_white.png') }}"></a>
            <span class="err err-main">Ongoing maintenance. Please try again later.</span>
            <span class="err err-sign">We apologize for any inconveniences caused.</span>
        </main>
    </body>
</html>