<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>ISM-PORTAL</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="color-scheme" content="light">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rouge+Script&display=swap" rel="stylesheet">
    <meta name="supported-color-schemes" content="light">
    <style>
        <blade media|%20only%20screen%20and%20(max-width%3A%20600px)%20%7B>.inner-body {
            width: 100% !important;
        }

        .footer {
            width: 100% !important;
        }

        @font-face {
            font-family: 'rouge-script';
            src: url({{ storage_path('fonts/RougeScript-Regular') }});
            font-weight: 400;
            font-style: normal;
        }


        <blade media|%20only%20screen%20and%20(max-width%3A%20500px)%20%7B>.button {
            width: 100% !important;
        }

        div {
            margin: .3em 3px;
        }

        /* @font-face {
            font-family: 'rouge-script';
            src: url({{ storage_path('fonts/RougeScript-Regular') }});
            font-weight: 400;
            font-style: normal;
        } */
    </style>
</head>

<body>
    <div style="background: url('../public/imgs/oru_cert.jpeg'); background-size: cover; font-size: 20px;">
        <div style="width: 100%;height: 100%;">

            <p style="position: absolute; left: 7em; top: 20em;
  right: 0; 
  margin-inline: auto; 
  width: 70%; color: black; font-size: 74px; 
  font-family: 'Rouge Script', cursive;
  font-weight: 900;
  font-style: normal; text-align: center;">{{$name_on_cert ?? ''}}</p>
        </div>
    </div>


</body>

</html>