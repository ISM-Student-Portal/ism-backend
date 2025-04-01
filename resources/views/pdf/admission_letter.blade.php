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
                    <div style="margin-top: 30px;">Dear {{ $first_name}} {{ $last_name}},</div>
                    <br>
                    <h3 style="text-align: center; text-transform: uppercase;">Letter Of Admission</h3>
                    <div>
                        We are so excited to welcome you to International School of Ministry 2025!
                    </div>
                    <br>


                    <div>It is going to be an intensive 3-Months of solid training where you will receive insightful and
                        transformative teachings on the foundational rudiments of ministry. The course will equip,
                        empower and sharpen you to deliver God’s counsel to your generation as you will come out
                        refined.</div>
                    <br>
                    <div>The course will include thorough assessments, projects, practicals and ministry work. It will
                        indeed be a training ground and a platform to grow into all God has called you to be.</div>
                    <br>
                    <div>You will be tutored by a faculty of seasoned facilitators as led by the Principal, Apostle Femi
                        Lazarus. Be rest assured you are in great hands.</div>
                    <br>
                    <div><i><b>Your Registration number is {{ $matric_no }}</b></i></div>
                    <div><i><b>Your Group number is {{ $group_no }}</b></i></div>
                    <br>

                    {{-- <div>Your Group number is <b>{{ $group_no }}</b></div> --}}
                    <div>Do well to keep this Registration no. safe as this will be used for your online assessments and
                        tests during the session of the school. Your group number determines your project topic and
                        field
                        work assignments.</div>
                    <br>
                    <div>ISM 2024 will run as both online and onsite.</div>
                    <br>
                    <div>On-site classes:
                        <br>
                        <ul>
                            <li>Ibadan Campus: Light Nation Auditorium, Oshuntokun Avenue, Bodija, Ibadan</li><br>
                            <li>Abuja Campus: Light Place Event Center, Beside Nigerian Institute of Transport
                                Technology,
                                Off Asuquo Okon Street,
                                Utako, Federal Capacity Territory.</li>
                        </ul>
                    </div>
                    <br>
                    <div>Online classes will be streamed online and you can attend at the comfort of your home or office
                        or
                        anywhere.</div>
                    <br>
                    <div>The session will run from April 5th - June 13th 2025. Lectures commences on Saturday, 5th April
                        2025 with an introductory/Orientation class. The course schedule and curriculum for the entire
                        two months of the school will be sent to you.</div>
                    <br>
                    <div>We welcome you once again to this year’s edition of International School of Ministry and we
                        wish you a life transforming experience!</div>
                    <div>For further enquires and questions: +2349030959735, +2349034646810</div>
                    <br>
                    <div>Regards.</div>
                    <br>
                </div>



            </div>
        </div>
    </div>
    <div>
        <img src="../public/imgs/footer.png" style="width: 100%" />
    </div>


</body>

</html>