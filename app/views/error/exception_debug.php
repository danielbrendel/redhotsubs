<!doctype html>
<html>
    <head>
        <meta charset="utf-8">

        <title>Asatru PHP - Exception</title>

        <style>
            html, body {
                width: 100%;
                height: 100%;
                background-color: rgb(30, 30, 30);
                color: rgb(100, 100, 100);
            }

            .ex_box {
                position: absolute;
                width: 98%;
                height: auto;
            }

            .ex_header {
                position: relative;
                margin-top: 5px;
                margin-left: 10px;
                margin-right: 10px;
                font-size: 1.2em;
                color: rgb(121, 73, 68);
            }

            .ex_msg {
                position: relative;
                margin-top: 5px;
                margin-left: 10px;
                margin-right: 10px;
                font-size: 1.2em;
            }

            .ex_msg strong {
                color: rgb(128, 0, 0);
            }

            .ex_trace_box {
                position: relative;
                margin-top: 35px;
                margin-left: 10px;
                margin-right: 10px;
                margin-bottom: 35px;
                border: 1px solid white;
            }

            .ex_trace_title {
                position: relative;
                margin-left: 5px;
            }

            .ex_trace_content {
                position: relative;
                margin-top: 15px;
                margin-left: 15px;
                margin-right: 15px;
                margin-bottom: 15px;
                font-size: 1.2em;
            }
        </style>
    </head>

    <body>
        <div class="ex_box">
            <div class="ex_header">
                <h1><strong>Exception</strong> at <?= $exception->getFile(); ?>:<?= $exception->getLine(); ?></h1>
            </div>

            <div class="ex_msg">
                Reported error: <strong><?= $exception->getMessage(); ?></strong>
            </div>

            <div class="ex_trace_box">
                <div class="ex_trace_title">
                    Stack trace:
                </div>

                <div class="ex_trace_content">
                    <?= preg_replace("/\n/", '<br>', $exception->getTraceAsString()); ?>
                </div>
            </div>
        </div>
    </body>
</html>