<div class="container mt-2">
    <h1>Registered Companies</h1>
    <table class="table table-striped table-responsive mt-5">
        <thead>
            <th scope="col">No</th>
            <th scope="col">Company Name</th>
            <th scope="col">Status</th>
            <th scope="col">Privilege Level</th>
            <th scope="col">Actions</th>
        </thead>
        <tbody>
            <?php
            /*
             * These are the status codes that company would have to
             * 0 - Pending -> Pending for accept or reject the request
             * 1 - Active -> Company registration form accepted by Administrator and Active 
             * 2 - Rejected -> Company registration form rejected by Administrator
             * 3 - Not Paid -> Company not paid for this application
             */

            $sql = "SELECT * FROM company_tbl ct INNER JOIN privilege_tbl pt ON (ct.privilege_id=pt.privilege_id) WHERE ct.status=1";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $i = 1;

                while ($row = $result->fetch_assoc()) {
                    $id = $row['company_id'];
                    $email = $row['email'];
                    $name = $row['company_name'];
                    $status = $row['status'];

                    // Determine the status name and badge class based on the status code
                    switch ($status) {
                        case 0:
                            $statusName = "Pending";
                            $badgeClass = "badge rounded-pill text-bg-info";
                            break;
                        case 1:
                            $statusName = "Active";
                            $badgeClass = "badge rounded-pill text-bg-success";
                            break;
                        case 2:
                            $statusName = "Rejected";
                            $badgeClass = "badge rounded-pill text-bg-danger";
                            break;
                        case 3:
                            $statusName = "Not Paid";
                            $badgeClass = "badge rounded-pill text-bg-warning";
                            break;
                        default:
                            $statusName = "Unknown";
                            $badgeClass = "badge rounded-pill text-bg-secondary";
                            break;
                    }
                    ?>
                    <tr>
                        <th scope="row"><?= $i ?></th>
                        <td><?= $name ?></td>
                        <td><span class="<?= $badgeClass ?>"><?= $statusName ?></span></td>
                        <td><?= $row['privilege_name'] ?></td>
                        <td>
                            <a class="btn btn-sm btn-primary" href="./Admin.php?page=profile&id=<?= $id ?>">Profile</a>
                            <button class="btn btn-sm btn-warning" onclick="openChangePrivilegeModal('<?= $id ?>')">Change
                                Privilege</button>
                            <button class="btn btn-sm btn-danger" onclick="openChangeStatusModal('<?= $id ?>')">Change
                                Status</button>
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

<div class="modal fade" id="changePrivilegeModal" tabindex="-1" aria-labelledby="changePrivilegeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePrivilegeModalLabel">Change Privilege Level</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="changePrivilegeForm">
                    <div class="mb-3">
                        <label for="privilegeLevel" class="form-label">Select Privilege Level</label>
                        <select class="form-select" id="privilegeLevel" name="privilegeLevel">
                            <option value="">-- Select an Option -- </option>
                            <?php
                            $sql = "SELECT * FROM privilege_tbl";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    ?>
                                    <option value="<?= $row['privilege_id'] ?>"><?= $row['privilege_name'] ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <!-- <input type="hidden" id="companyId" name="companyId"> -->
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="changeStatusModal" tabindex="-1" aria-labelledby="changeStatusModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeStatusModalLabel">Change Company Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="changePrivilegeForm">
                    <div class="mb-3">
                        <label for="status" class="form-label">Select Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">-- Select an Option -- </option>
                            <option value="0">Pending</option>
                            <option value="1">Active</option>
                            <option value="2">Reject</option>
                            <option value="3">Not Paid</option>
                        </select>
                    </div>
                    <!-- <input type="hidden" id="companyId" name="companyId"> -->
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Define the global variable
    let company_id;

    // Change the page title
    $(document).prop('title', 'All Companies | Ginum');

    // This function is used to open the modal
    function openChangePrivilegeModal(companyId) {
        // Set the global company_id variable
        company_id = companyId;
        // Store the companyId in a data attribute on the modal for later use
        $('#changePrivilegeModal').data('companyId', companyId).modal('show');
    }

    function openChangeStatusModal(companyId) {
        // Set the global company_id variable
        company_id = companyId;
        // Store the companyId in a data attribute on the modal for later use
        $('#changeStatusModal').data('companyId', companyId).modal('show');
    }

    // This function makes an AJAX POST request to change the privilege level
    function getPrivilegeLevel() {
        $.ajax({
            url: '../../backend/data/change-privilege-level.php',
            type: 'POST',
            data: {
                privilegeLevel: $("#privilegeLevel").val(),
                companyId: company_id
            },
            success: function (response) {
                // Log the response from the backend
                console.log(response);
                // Close the modal
                $('#changeStatusModal').modal('hide');
                // Optionally, display a success message
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Status changed successfully.'
                });

                // Reload the page
                location.reload();
            },
            error: function (xhr, status, error) {
                // Log any errors
                console.error(xhr.responseText);
            }
        });
    }

    // This function makes an AJAX POST request to change the status
    function setStatus(status) {
        $.ajax({
            url: `../../backend/data/change-company-status.php?id=${company_id}&status=${status}'`,
            type: 'POST',
            data: {
            },
            success: function (response) {
                // Log the response from the backend
                console.log(response);
                // Close the modal
                $('#changePrivilegeModal').modal('hide');
                // Optionally, display a success message
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Privilege level changed successfully.'
                });

                // Reload the page
                location.reload();
            },
            error: function (xhr, status, error) {
                // Log any errors
                console.error(xhr.responseText);
            }
        });
    }

    // Event handler for changes in the privilege level dropdown
    $('#privilegeLevel').change(function () {
        // Get the selected privilege level
        let privilegeLevel = $(this).val();
        // Make an AJAX request using the global company_id
        getPrivilegeLevel();
    });

    // Event handler for changes in the status dropdown
    $('#status').change(function () {
        // Get the selected privilege level
        let status = $(this).val();
        // Make an AJAX request using the global company_id
        setStatus(status);
    });
</script>