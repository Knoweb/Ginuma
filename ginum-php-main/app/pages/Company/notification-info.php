<style>
    .container-fluid {
        margin-top: 20px;
    }

    .shadow-lg {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2), 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    table,
    th,
    td {
        border: 1px solid #ddd;
    }

    th,
    td {
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    h3 {
        margin-top: 20px;
    }

    .row {
        display: flex;
        justify-content: space-between;
    }

    .col-md-6 {
        flex: 1;
        padding: 0 15px;
    }
</style>

<?php
ini_set('display_errors', '1');
ini_set('error_reporting', E_ALL);
error_log(E_ALL);
function safeHtml(string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function compareData(array $oldData, array $newData): array
{
    $changes = [];

    // Define keys to exclude
    $keysToExclude = [
        'status',
        'date_added',
        'date_updated',
        'date_created',
        'created_at',
        'company_id',
        'employee_id',
        'customer_id',
        'sub_login_id',
        'request_id',
        'supplier_id'
    ];

    // Collect all keys from old and new data
    $allKeys = array_unique(array_merge(array_keys($oldData), array_keys($newData)));

    // Check for changes
    foreach ($allKeys as $key) {
        if (in_array($key, $keysToExclude, true)) {
            continue; // Skip excluded keys
        }

        $oldValue = isset($oldData[$key]) ? $oldData[$key] : 'N/A';
        $newValue = isset($newData[$key]) ? $newData[$key] : 'N/A';

        if ($oldValue !== $newValue) {
            $changes[$key] = ['old' => $oldValue, 'new' => $newValue];
        }
    }

    return $changes;
}

function getTitle($key)
{
    // Replace underscores with spaces
    $title = str_replace('_', ' ', $key);

    // Capitalize the first letter of each word
    $title = ucwords($title);

    return $title;
}

if ($_SESSION['role'] == 'Company Admin' && isset($_GET['id'])) {
    $request_id = $_GET['id'];
    $company_id = $_SESSION['company_id'];

    // Fetch the request details from the database
    $sql = "SELECT *, ert.status as eStatus FROM edit_requests ert 
            INNER JOIN sub_logins_tbl slt ON (ert.sub_login_id=slt.sub_login_id) 
            INNER JOIN employee_tbl et ON (slt.employee_id=et.employee_id) 
            WHERE ert.company_id=? AND ert.request_id=?";
    $stmt = $conn->prepare(query: $sql);
    $stmt->bind_param("ii", $company_id, $request_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $section = $row['section'];
        $employee_name = $row['first_name'] . " " . $row['last_name'];
        $old_data = json_decode(json: $row['old_data'], associative: true);
        $new_data = json_decode(json: $row['new_data'], associative: true);

        // Compare data
        $changes = compareData($old_data, $new_data);
        ?>
        <h1>Request Info</h1>

        <div class="container-fluid mt-3">
            <div class="shadow-lg p-3 mb-5 bg-body-tertiary rounded">
                <form action="" method="post">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="section" class="form-label">Section</label>
                                <input type="text" name="section" id="section" class="form-control"
                                    value="<?= safeHtml($section) ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="employee" class="form-label">Requested By</label>
                                <input type="text" name="employee" id="employee" class="form-control"
                                    value="<?= safeHtml($employee_name) ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <?php if ($row['eStatus'] == 'pending'): ?>
                                    <input type="text" name="" id="status" class="form-control text-primary" value="Pending"
                                        readonly>
                                <?php elseif ($row['eStatus'] == 'approved'): ?>
                                    <input type="text" name="" id="status" class="form-control text-success" value="Approved"
                                        readonly>
                                <?php elseif ($row['eStatus'] == 'rejected'): ?>
                                    <input type="text" name="" id="status" class="form-control text-danger" value="Rejected"
                                        readonly>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <h3>Details Comparison</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Old Value</th>
                                <th>New Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $allKeys = array_unique(array_merge(array_keys($old_data), array_keys($new_data)));
                            foreach ($allKeys as $key):
                                if (!in_array($key, ['status', 'date_added', 'date_updated', 'date_created', 'created_at', 'company_id', 'employee_id', 'customer_id', 'sub_login_id', 'request_id', 'supplier_id'])):
                                    $oldValue = $old_data[$key] ?? 'N/A';
                                    $newValue = $new_data[$key] ?? 'N/A';
                                    ?>
                                    <tr>
                                        <td><?= safeHtml(getTitle($key)) ?></td>
                                        <td><?= safeHtml($oldValue) ?></td>
                                        <td><?= safeHtml($newValue) ?></td>
                                    </tr>
                                    <?php
                                endif;
                            endforeach;
                            ?>
                        </tbody>
                    </table>

                    <div class="row">
                        <div class="col-md-12">
                            <h3>Changes</h3>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Field</th>
                                        <th>Old Value</th>
                                        <th>New Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($changes as $key => $change): ?>
                                        <tr>
                                            <td><?= safeHtml(getTitle($key)) ?></td>
                                            <td><?= safeHtml($change['old']) ?></td>
                                            <td><?= safeHtml($change['new']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-outline-success" id="approve" type="button">Approve</button>
                        <button class="btn btn-outline-danger" id="reject" type="button">Reject</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            $(document).ready(function () {
                // Set the page title
                $(document).prop('title', 'Notification | Ginum');

                // Highlight the navbar link
                $("#dashboard").removeClass("active");
                $("#notifications").addClass("active");

                // Function to handle AJAX requests
                function sendRequest(status) {
                    $.ajax({
                        url: '../../backend/data/approve-edit-request.php',
                        method: 'POST',
                        data: {
                            request_id: "<?= $request_id ?>",
                            status: status
                        },
                        success: function (response) {
                            try {
                                const data = JSON.parse(response);
                                if (data.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: status === 'approved' ? 'Approved' : 'Done',
                                        text: data.message
                                    });
                                } else if (data.status === 'error') {
                                    console.error(data.message);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: data.message
                                    });
                                }
                            } catch (e) {
                                console.error('Error parsing response:', e);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Unexpected response format'
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Oh no...',
                                text: status === 'approved' ? 'Unable to approve the request!' : 'Unable to reject the form'
                            });
                        }
                    });
                }

                // Bind click events to buttons
                $("#approve").click(() => sendRequest('approved'));

                $("#reject").click(function () {
                    Swal.fire({
                        icon: 'question',
                        title: "Do you want to reject the request?",
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonText: "Reject",
                        denyButtonText: `Don't reject`
                    }).then((result) => {
                        if (result.isConfirmed) {
                            sendRequest('rejected');
                            // window.location.href = './Company.php?page=show-notifications';
                        } else if (result.isDenied) {
                            Swal.fire("Changes are not saved", "", "info");
                        }
                    });
                });
            });
        </script>



        <?php
    }
}
?>