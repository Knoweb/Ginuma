<section class=" gradient-custom">
    <div class="container py-5">
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
        <?php if (isset($_GET['warning'])) { ?>
            <script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Careful!',
                    text: "<?= $_GET['warning'] ?>"
                })
            </script>
        <?php } ?>
        <div class="container">
            <h3 class="mb-3 pb-2 pb-md-0">Add Department</h3>
            <div class="shadow-lg p-3 mb-5 bg-body-tertiary rounded">
                <form class="row g-3" method="post" action="../../backend/data/add-department-data.php"
                    enctype="multipart/form-data">
                    <div class="col-md-12">
                        <label for="dpt_name" class="form-label">Department Name</label>
                        <input type="text" class="form-control" name="dpt_name" id="dpt_name" required>
                    </div>
                    <div class="col-md-12">
                        <label for="dpt_code" class="form-label">Department Code</label>
                        <input type="tel" name="dpt_code" class="form-control" id="dpt_code" required>
                    </div>
                    <div class="col-md-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" rows="4" class="form-control"></textarea>
                    </div>

                    <div class="col-12 mt-5">
                        <button type="submit" id="submit" name="add" class="btn btn-primary">Add</button>
                        <button type="reset" class="btn btn-secondary">Clear</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</section>

<script>
    $(document).prop('title', 'Add Department | Ginum');
    $(document).ready(function () {
        // navbar
        $("#dashboard").removeClass("active");
        $("#department").addClass("active");
    })
</script>