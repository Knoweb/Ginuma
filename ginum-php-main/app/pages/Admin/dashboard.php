<?php
// enable error display
ini_set('display_errors', '1');
ini_set('error_reporting', E_ALL);
?>
<main>
    <style>
        :root {
            --poppins: 'Poppins', sans-serif;
            --lato: 'Lato', sans-serif;

            --light: #F9F9F9;
            --blue: #3C91E6;
            --light-blue: #CFE8FF;
            --grey: #eee;
            --dark-grey: #AAAAAA;
            --dark: #342E37;
            --red: #DB504A;
            --yellow: #FFCE26;
            --light-yellow: #FFF2C6;
            --orange: #FD7238;
            --light-orange: #FFE0D3;
        }

        html {
            overflow-x: hidden;
        }

        body.dark {
            --light: #0C0C1E;
            --grey: #060714;
            --dark: #FBFBFB;
        }

        body {
            background: var(--grey);
            overflow-x: hidden;
        }

        #content main .box-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            grid-gap: 24px;
            margin-top: 36px;
        }

        #content main .box-info li {
            padding: 24px;
            background: var(--light);
            border-radius: 20px;
            display: flex;
            align-items: center;
            grid-gap: 24px;
        }

        #content main .box-info li .bx {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            font-size: 36px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #content main .box-info li:nth-child(1) .bx {
            background: var(--light-blue);
            color: var(--blue);
        }

        #content main .box-info li:nth-child(2) .bx {
            background: var(--light-yellow);
            color: var(--yellow);
        }

        #content main .box-info li:nth-child(3) .bx {
            background: var(--light-orange);
            color: var(--orange);
        }

        #content main .box-info li:nth-child(4) .bx {
            background: var(--light-orange);
            color: var(--orange);
        }

        #content main .box-info li .text h3 {
            font-size: 24px;
            font-weight: 600;
            color: var(--dark);
        }

        #content main .box-info li .text p {
            color: var(--dark);
        }

        @media (max-width: 768px) {
            .chart-container {
                height: 300px;
            }
        }

        @media (max-width: 480px) {
            .chart-container {
                height: 250px;
            }
        }

        /* CONTENT */
    </style>
    <div class="head-title">
        <div class="left">
            <h1>Dashboard</h1>
        </div>
    </div>
    <ul class="box-info">
        <li>
            <i class='bx bxs-calendar-check'></i>
            <span class="text">
                <h3>
                    <?php
                    $sql = "SELECT COUNT(*) as count FROM company_tbl WHERE status=1";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        echo $row['count'];
                    } else {
                        echo "0";
                    }
                    ?>
                </h3>
                <p>Active Users</p>
            </span>
        </li>
        <li>
            <i class='bx bxs-group'></i>
            <span class="text">
                <h3>
                    <?php
                    $sql = "SELECT COUNT(*) as count FROM company_tbl WHERE status=0";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        echo $row['count'];
                    } else {
                        echo "0";
                    }
                    ?>
                </h3>
                <p>Pending Requests</p>
            </span>
        </li>
        <li>
            <i class='bx bxs-dollar-circle'></i>
            <span class="text">
                <h3>
                    <?php
                    $sql = "SELECT COUNT(*) as count FROM company_tbl WHERE status=2";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        echo $row['count'];
                    } else {
                        echo "0";
                    }
                    ?>
                </h3>
                <p>Rejected Requests</p>
            </span>
        </li>
        <li>
            <i class='bx bxs-dollar-circle'></i>
            <span class="text">
                <h3>
                    <?php
                    $sql = "SELECT COUNT(*) as count FROM company_tbl WHERE status=3";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        echo $row['count'];
                    } else {
                        echo "0";
                    }
                    ?>
                </h3>
                <p>Not Paid</p>
            </span>
        </li>
    </ul>
</main>