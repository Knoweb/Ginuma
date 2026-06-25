<div class="container mt-2">
    <h1>Requests</h1>
    <table class="table table-striped table-responsive mt-5">
        <thead>
            <th scope="col">No</th>
            <th scope="col">Company Name</th>
            <th scope="col">Email</th>
            <th scope="col">Actions</th>
        </thead>
        <tbody>
            <?php
            /*
             * These are the status codes that company would be have to
             * 0 - Pending -> Pending for accept or reject the request
             * 1 - Active -> Company registration form accepted by Administrator and Active 
             * 2 - Rejected -> Company registration form rejected by Administrator
             * 3 - Not Paid -> Company not paid for this application
             */

            $sql = "SELECT * FROM company_tbl WHERE status=0";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $i = 1;

                while ($row = $result->fetch_assoc()) {
                    $id = $row['company_id'];
                    $email = $row['email'];
                    $name = $row['company_name'];

                    ?>
                    <tr>
                        <th scope="row"><?= $i ?></th>
                        <td><?= $name ?></td>
                        <td><?= $email ?></td>
                        <td>
                            <a class="btn btn-sm btn-primary" href="./Admin.php?page=profile&id=<?= $id ?>">Profile</a>
                            <a class="btn btn-sm btn-success"
                                href="../../backend/data/change-company-status.php?status=1&id=<?= $id ?>">Accept</a>
                            <button class="btn btn-sm btn-danger" onclick="confirmation(<?= $id ?>)">Reject</button>
                        </td>
                    </tr>
                    <?php
                    $i++;
                }
            }
            ?>
        </tbody>
    </table>
</div>

<script>
    //change the page title
    $(document).prop('title', 'Company Requests');

    function confirmation(id) {
        Swal.fire({
            icon: 'question',
            title: "Do you want to save the changes?",
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: "Save",
            denyButtonText: `Don't save`
        }).then((result) => {
            if (result.isConfirmed) {
                // reject the request
                // redirect to the backend controller
                window.location.href = href = `../../backend/data/change-company-status.php?status=2&id=${id}`;
                // Swal.fire("Saved!", "", "success");
            } else if (result.isDenied) {
                Swal.fire("Changes are not saved", "", "info");
            }
        });
    }
</script>