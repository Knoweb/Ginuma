<?php
if ($_SESSION['role'] == 'Company Admin') {

    function timeAgo($timestamp)
    {
        // Create DateTime objects for current time and input timestamp
        $now = new DateTime();
        $inputTime = new DateTime($timestamp);

        // Ensure that $inputTime is less than or equal to $now
        if ($inputTime > $now) {
            return "In the future";
        }

        // Calculate the difference between the two DateTime objects
        $diff = $now->diff($inputTime);

        // Determine how to format the difference
        $result = [];

        if ($diff->d > 0) {
            $result[] = $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
        }
        if ($diff->h > 0) {
            $result[] = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
        }
        if ($diff->i > 0 && empty($result)) {
            $result[] = $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
        }
        if (empty($result)) {
            $result[] = 'Just now';
        }

        return implode(' and ', $result) . ' ago';
    }

    function colorCodeWithText(string $status): array
    {
        $data = [];
        if ($status == 'pending') {
            $data = ['class' => 'pending', 'text' => 'Pending'];
        } elseif ($status == 'approved') {
            $data = ['class' => 'unread', 'text' => 'Approved'];
        } elseif ($status == 'rejected') {
            $data = ['class' => 'important', 'text' => 'Rejected'];
        }

        return $data;
    }

    ?>


    <style>
        .job-box-filter label {
            width: 100%;
        }

        .job-box-filter select.input-sm {
            display: inline-block;
            max-width: 120px;
            margin: 0 5px;
            border: 1px solid #e8eef1;
            border-radius: 2px;
            height: 34px;
            font-size: 15px;
        }

        .job-box-filter label input.form-control {
            max-width: 200px;
            display: inline-block;
            border: 1px solid #e8eef1;
            border-radius: 2px;
            height: 34px;
            margin-left: 5px;
            font-size: 15px;
        }

        .text-right {
            text-align: right;
        }

        .job-box-filter {
            padding: 12px 15px;
            background: #ffffff;
            border-bottom: 1px solid #e8eef1;
            margin-bottom: 20px;
        }

        .job-box {
            background: #ffffff;
            display: inline-block;
            width: 100%;
            padding: 0 0px 40px 0px;
            border: 1px solid #e8eef1;
        }

        .job-box-filter a.filtsec {
            margin-top: 8px;
            display: inline-block;
            margin-right: 15px;
            padding: 4px 10px;
            font-family: 'Quicksand', sans-serif;
            transition: all ease 0.4s;
            background: #edf0f3;
            border-radius: 50px;
            font-size: 13px;
            color: #81a0b1;
            border: 1px solid #e2e8ef;
        }

        .job-box-filter a.filtsec.active {
            color: #ffffff;
            background: #16262c;
            border-color: #16262c;
        }

        .job-box-filter a.filtsec i {
            color: #03A9F4;
            margin-right: 5px;
        }

        .job-box-filter a.filtsec:hover,
        .job-box-filter a.filtsec:focus {
            color: #ffffff;
            background: #07b107;
            border-color: #07b107;
        }

        .job-box-filter a.filtsec:hover i,
        .job-box-filter a.filtsec:focus i {
            color: #ffffff;
        }

        .job-box-filter h4 i {
            margin-right: 10px;
        }

        .inbox-message ul {
            padding: 0;
            margin: 0;
        }

        .inbox-message ul li {
            list-style: none;
            position: relative;
            padding: 15px 20px;
            border-bottom: 1px solid #e8eef1;
        }

        .inbox-message ul li:hover,
        .inbox-message ul li:focus {
            background: #eff6f9;
        }

        .inbox-message .message-avatar {
            position: absolute;
            left: 30px;
            top: 50%;
            transform: translateY(-50%);
        }

        .message-avatar img {
            display: inline-block;
            width: 54px;
            height: 54px;
            border-radius: 50%;
        }

        .inbox-message .message-body {
            margin-left: 85px;
            font-size: 15px;
            color: #62748F;
        }

        .message-body-heading h5 {
            font-weight: 600;
            display: inline-block;
            color: #62748F;
            margin: 0 0 7px 0;
            padding: 0;
        }

        .message-body h5 span {
            border-radius: 50px;
            line-height: 14px;
            font-size: 12px;
            color: #fff;
            font-style: normal;
            padding: 4px 10px;
            margin-left: 5px;
            margin-top: -5px;
        }

        .message-body h5 span.unread {
            background: #07b107;
        }

        .message-body h5 span.important {
            background: #dd2027;
        }

        .message-body h5 span.pending {
            background: #2196f3;
        }

        .message-body-heading span {
            float: right;
            color: #62748F;
            font-size: 14px;
        }

        .messages-inbox .message-body p {
            margin: 0;
            padding: 0;
            line-height: 27px;
            font-size: 15px;
        }

        a:hover {
            text-decoration: none;
        }
    </style>

    <div class="container">
        <h2>Edit Requests</h2>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="chat_container">
                    <div class="job-box">
                        <div class="inbox-message">
                            <ul>
                                <?php
                                $sql = "SELECT *, ert.status AS ertStatus FROM edit_requests ert INNER JOIN sub_logins_tbl slt ON (ert.sub_login_id=slt.sub_login_id) INNER JOIN employee_tbl et ON (slt.employee_id=et.employee_id) WHERE ert.company_id='" . $_SESSION['company_id'] . "' ORDER BY ert.status ASC";
                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $employee_name = $row['first_name'] . " " . $row['last_name'];
                                        $request_created = $row['created_at'];
                                        $employee_id = $row['employee_id'];
                                        $request_id = $row['request_id'];
                                        $section = $row['section'];
                                        $img = $row['img_path'];
                                        $data = colorCodeWithText($row['ertStatus']);
                                        ?>
                                        <li>
                                            <a href="./Company.php?page=notification&id=<?= $request_id ?>">
                                                <div class="message-avatar">
                                                    <img src="<?= $img ?: 'https://bootdey.com/img/Content/avatar/avatar1.png' ?>"
                                                        alt="Employee Image">
                                                </div>
                                                <div class="message-body">
                                                    <div class="message-body-heading">
                                                        <h5><?= $employee_name ?> <span
                                                                class="<?= $data['class'] ?>"><?= $data['text'] ?></span></h5>
                                                        <span><?= timeAgo($request_created) ?></span>
                                                    </div>
                                                    <p>I need to make changes to <strong><?= $section ?></strong>. Can you approve
                                                        the request?
                                                    </p>
                                                </div>
                                            </a>
                                        </li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // set the page title
            $(document).prop('title', 'Notifications | Ginum');

            // navbar highlighting
            $("#dashboard").removeClass('active');
            $("#notifications").addClass('active');
        })
    </script>

    <?php

}
?>