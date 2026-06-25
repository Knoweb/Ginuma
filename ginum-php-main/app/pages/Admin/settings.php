<?php
// enable error display
ini_set('display_errors', '1');
ini_set('error_reporting', E_ALL);
?>
<div class="">
    <h3>Settings</h3>
    <?php if (isset($_GET['success'])) { ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Done',
                text: "<?= $_GET['success'] ?>"
            })
        </script>
    <?php } ?>
    <?php if (isset($_GET['error'])) { ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "<?= $_GET['error'] ?>"
            })
        </script>
    <?php } ?>
    <?php
    $sql = "SELECT * FROM super_admin_tbl WHERE s_admin_id='" . $_SESSION['s_admin_id'] . "'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $admin_id = $_SESSION['s_admin_id'];
        $email = $row['s_admin_username'];
        $name = $row['s_admin_name'];
        ?>
        <form action="../../backend/data/change-admin-settings.php?id=<?= $admin_id ?>" method="post" class="mt-3">
            <div class="mb-3">
                <label for="name" class="form-label">Email</label>
                <input type="text" class="form-control" id="name" name="admin_name" value="<?= $name ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="admin_email" value="<?= $email ?>" required>
            </div>
            <div class="mb-3">
                <label for="old_password" class="form-label">Old Password</label>
                <input type="password" class="form-control" id="admin_old_password" name="admin_old_password">
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="admin_new_password" name="admin_new_password">
            </div>
            <div class="mb-3">
                <label for="con_new_password" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" id="admin_con_new_password" name="admin_con_new_password">
            </div>
            <div class="mb-3">
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
        </form>
        <?php
    }
    ?>
</div>

<script>
    //change the page title
    $(document).prop('title', 'Settings');
</script>