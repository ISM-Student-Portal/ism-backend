<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>ISM-PORTAL</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <style>
        <blade media|%20only%20screen%20and%20(max-width%3A%20600px)%20%7B>.inner-body {
            width: 100% !important;
        }

        .footer {
            width: 100% !important;
        }


        <blade media|%20only%20screen%20and%20(max-width%3A%20500px)%20%7B>.button {
            width: 100% !important;
        }

        div {
            margin: .3em 3px;
        }
    </style>
</head>

<body>
    <div style="display: flex; flex-direction: row; font-size: 20px;">
        <div style="clear: both"></div>
        <div style="width: 100%">

            <div style="width: 100%; margin: 0 auto; ">
                <header>
                    <img src="../public/imgs/logo_header.png" style="float: right;" />
                </header>
                <div>
                    <div>
                        <div style="margin-top: 30px;">
                            ISM 2025
                        </div>
                    </div>
                    <div style="margin-top: 30px;"></div>
                    <br>
                    <h3 style="text-align: center; text-transform: uppercase;">Transcript</h3>
                    <br>

                    <h6>{{$user['first_name']}} {{$user['last_name']}}</h6>


                </div>

                <div>
                    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                        <thead>
                            <tr>
                                <th style="border: 1px solid #000; padding: 8px;">Course</th>
                                <th style="border: 1px solid #000; padding: 8px;">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transcript as $course)
                                <tr>
                                    <td style="border: 1px solid #000; padding: 8px;">{{ $course['course_name'] }}</td>
                                    <td style="border: 1px solid #000; padding: 8px;">{{ $course['grade'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                </div>



            </div>
        </div>
    </div>
    <div>
        <img src="../public/imgs/footer.png" style="width: 100%" />
    </div>
</body>

</html>