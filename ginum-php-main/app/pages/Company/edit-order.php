<?php
if (isset($_GET['id'])) {
	$id = $_GET['id'];
	$sql1 = "SELECT * FROM project_tbl WHERE project_id=$id";
	$result1 = $conn->query($sql1);
	if ($result1->num_rows > 0) {
		$row1 = $result1->fetch_assoc();
		$id = $row1['project_id'];
		$projectName = $row1['project_name'];
		$projectCode = $row1['project_code'];
		$description = $row1['description'];
		$startDate = $row1['start_date'];
		$endDate = $row1['end_date'];
		$work_status = $row1['work_status'];
		$billing_method = $row1['billing_method'];
		$budget = $row1['budget'];
		$customer_id = $row1['customer_id'];
		$department_id = $row1['department_id'];
		$priority = $row1['priority'];

		// fetch the task table
		$sql2 = "SELECT * FROM task_tbl WHERE project_id=$id";
		$result2 = $conn->query($sql2);
		if ($result2->num_rows > 0) {
			$row2 = $result2->fetch_all();
			$row2_length = count($row2);
			$taskIds = array();
			$sql3 = "SELECT task_id FROM task_tbl WHERE project_id=$id";
			$result3 = $conn->query($sql3);
			$query = "";
			if ($result3->num_rows > 0) {
				while ($row3 = $result3->fetch_assoc()) {
					array_push($taskIds, $row3['task_id']);
				}
				$query = http_build_query($taskIds, '', ',');

			}
		}
	}
	?>

	<div class="container py-4">
		<h2>Edit Order</h2>
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
		<div class="shadow-lg p-3 mb-5 bg-body-tertiary rounded">
			<form
				action="../../backend/data/edit-order-data.php?id=<?= $id ?>&task_length=<?= $row2_length ?>&taskIds=<?= $query ?>"
				method="post">
				<div class="row">
					<div class="col-md-6">
						<div class="mb-3">
							<label for="project_code" class="form-label">Order Code</label>
							<input type="text" name="project_code" id="project_code" value="<?= $projectCode ?>"
								class="form-control" required>
						</div>
					</div>
					<div class="col-md-6">
						<div class="mb-3">
							<label for="project_name" class="form-label">Order Name</label>
							<input type="text" name="project_name" id="project_name" value="<?= $projectName ?>"
								class="form-control" required>
						</div>
					</div>
					<div class="mb-3">
						<label for="project_description" class="form-label">Description</label>
						<textarea name="project_description" id="project_description" rows="5"
							class="form-control"><?= $description ?></textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="mb-3">
							<label for="customer_id" class="form-label">Customer</label>
							<select name="customer_id" id="customer_id" class="form-select" required>
								<?php
								$sql = "SELECT * FROM customer_tbl WHERE status=1";
								$result = $conn->query($sql);
								if ($result->num_rows > 0) {
									while ($row = $result->fetch_assoc()) {
										if ($row['customer_id'] == $customer_id) {
											?>
											<option value="<?= $row['customer_id'] ?>" selected><?= $row['name'] ?>
												<?php
										} else {
											?>
											<option value="<?= $row['customer_id'] ?>"><?= $row['name'] ?>

												<?php
										}
									}
								}
								?>
							</select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="mb-3">
							<label for="department_id" class="form-label">Department</label>
							<select name="department_id" id="department_id" class="form-select" required>
								<option>-- Select Department --</option>
								<?php
								$sql = "SELECT * FROM department_tbl";
								$result = $conn->query($sql);
								if ($result->num_rows > 0) {
									while ($row = $result->fetch_assoc()) {
										if ($row['department_id'] == $department_id) {
											?>
											<option value="<?= $row['department_id'] ?>" selected><?= $row['department_name'] ?>
												<?php
										} else {
											?>
											<option value="<?= $row['department_id'] ?>"><?= $row['department_name'] ?>

												<?php
										}
									}
								}
								?>
							</select>
						</div>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-md-6">
						<div class="mb-3">
							<label for="billing_method" class="form-label">Billing Method</label>
							<select name="billing_method" id="billing_method" class="form-select" required>
								<option value="Cash" <?= $billing_method == 'Cash' ? 'selected' : '' ?>>Cash</option>
								<option value="Cheque" <?= $billing_method == 'Cheque' ? 'selected' : '' ?>>Cheque</option>
								<option value="Online Banking Tranfer" <?= $billing_method == 'Online Banking Tranfer' ? 'selected' : '' ?>>
									Online Banking Tranfer</option>
								<option value="Credit" <?= $billing_method == 'Credit' ? 'selected' : '' ?>>Credit</option>
							</select>
						</div>
						<div class="mb-3">
							<label for="start_date" class="form-label">Start Date</label>
							<input type="date" name="start_date" id="start_date" class="form-control"
								value="<?= $startDate ?>">
						</div>
						<div class="mb-3">
							<label for="working_status" class="form-label">Working Status</label>
							<select name="working_status" id="working_status" class="form-select" required>
								<option value="Active" <?= $work_status == 'Active' ? 'selected' : '' ?>>Active</option>
								<option value="Completed" <?= $work_status == 'Completed' ? 'selected' : '' ?>>Completed
								</option>
								<option value="Running" <?= $work_status == 'Running' ? 'selected' : '' ?>>Running</option>
								<option value="Pending" <?= $work_status == 'Pending' ? 'selected' : '' ?>>Pending</option>
								<option value="Not Started" <?= $work_status == 'Not Started' ? 'selected' : '' ?>>Not Started
								</option>
								<option value="Canceled" <?= $work_status == 'Canceled' ? 'selected' : '' ?>>Canceled</option>
							</select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="mb-3">
							<label for="budget" class="form-label">Budget</label>
							<div class="input-group">
								<span class="input-group-text" id="basic-addon3"><?= $_SESSION['currency_code'] ?></span>
								<input type="text" class="form-control" name="budget" value="<?= $budget ?>" id="budget"
									aria-describedby="basic-addon3" required>
							</div>
						</div>
						<div class="mb-3">
							<label for="end_date" class="form-label">End Date</label>
							<input type="date" name="end_date" id="end_date" class="form-control" value="<?= $endDate ?>">
						</div>
						<div class="mb-3">
							<label for="priority" class="form-label">Priority</label>
							<select name="priority" id="priority" class="form-select" required>
								<option value="Low" class="text-primary" <?= $priority == 'Low' ? 'selected' : '' ?>>Low
								</option>
								<option value="Medium" class="text-warning" <?= $priority == 'Medium' ? 'selected' : '' ?>>
									Medium
								</option>
								<option value="High" class="text-danger" <?= $priority == 'High' ? 'selected' : '' ?>>High
								</option>
							</select>
						</div>
					</div>
				</div>
				<hr>
				<!-- Add Tasks -->
				<div class="mb-3">
					<label for="tasks" class="form-label">Tasks</label>
					<div class="table-responsive">
						<table class="table table-bordered" id="tasks">
							<thead>
								<tr>
									<th class="text-center">#</th>
									<th class="text-center">Task Name</th>
									<th class="text-center">Priority</th>
									<th class="text-center">Created Date</th>
									<th class="text-center">Due Date</th>
									<th class="text-center">Description</th>
									<th class="text-center">Status</th>
									<th class="text-center"></th>
								</tr>
							</thead>
							<tbody id="tbody">
								<?php
								$i = 1;
								if (count($row2) > 0) {
									foreach ($row2 as $row) {
										?>
										<tr class="rowClass">
											<td class="row-index text-center">
												<?= $i ?>
											</td>
											<td class="text-center">
												<input type='text' name='taskNames[]' class='form-control' title='Task Name'
													value="<?= $row[2] ?>" required>
											</td>
											<td class="text-center">
												<select name="task_priority[]" id="task_priority" class="form-select">
													<option value="Low" class="text-primary" <?= $row[3] == 'Low' ? 'selected' : '' ?>>
														Low
													</option>
													<option value="Medium" class="text-warning" <?= $row[3] == 'Medium' ? 'selected' : '' ?>>Medium</option>
													<option value="High" class="text-danger" <?= $row[3] == 'High' ? 'selected' : '' ?>>High</option>
												</select>
											</td>
											<td class="text-center">
												<input type='date' name='createdDates[]' class='form-control quantity'
													title='Created Date' value="<?= $row[4] ?>" required>
											</td>
											<td class="text-center">
												<input type='date' name='dueDates[]' class='form-control' title='Due Date'
													value="<?= $row[5] ?>">
											</td>
											<td class="text-center">
												<input type='text' name='task_descriptions[]' class='form-control'
													title='Description' value="<?= $row[6] ?>">
											</td>
											<td class="text-center">
												<input type='text' class='form-control' title='Task Status'
													value="<?= $row[7] == 1 ? 'Done' : 'Not Finished' ?>" readonly>
											</td>
											<td class="text-center">
												<a class="btn btn-warning"
													href="../../backend/data/mark-task-completed.php?task_id=<?= $row[0] ?>&project_id=<?= $id ?>&state=<?= $row[7] == 1 ? '1' : '2' ?>"
													type="button"><?= $row[7] == 1 ? "<i class='bx bx-undo'></i>" : "<i class='bx bx-check'></i>" ?></a>
											</td>
										</tr>
										<?php
										$i++;
									}
								} else {
									// no tasks
								}
								?>
							</tbody>
						</table>
						<button class="btn btn-md btn-primary" id="addBtn1" type="button">
							<i class='bx bx-plus'></i>
						</button>
					</div>
				</div>
				<hr>
				<div class="mb-3">
					<!-- Add expenses -->
					<label for="expenses" class="form-label">Expenses</label>
					<div class="table-responsive">
						<table class="table table-bordered" id="expenses">
							<thead>
								<tr>
									<th>#</th>
									<th>Debit</th>
									<th>Credit</th>
									<th>Amount (<?= $_SESSION['currency_code'] ?>) </th>
									<th>Description</th>
									<th></th>
								</tr>
							</thead>
							<tbody id="tbody2">
								<?php
								$j = 1;
								?>
							</tbody>
						</table>
						<button class="btn btn-md btn-primary" id="addBtn2" type="button">
							<i class='bx bx-plus'></i>
						</button>
					</div>
				</div>
				<div class="mb-3 mt-4">
					<!-- Edit Button -->
					<button type="submit" id="editButton" class="btn btn-warning">Edit</button>

					<!-- Pending State Button (initially hidden) -->
					<button id="pendingButton" class="btn btn-warning" type="button" disabled style="display: none;">
						<span class="spinner-grow spinner-grow-sm" aria-hidden="true"></span>
						<span role="status">Pending...</span>
					</button>
					<button type="reset" class="btn btn-secondary">Clear</button>
				</div>
			</form>
		</div>
	</div>

	<script>
		$(document).ready(function () {
			// for page title
			$(document).prop('title', 'Edit Project | Ginum');

			// navbar
			$("#dashboard").removeClass("active");
			$("#orders").addClass("active");
			// for table
			let staterPoint = $(".row-index").text();
			let elements = staterPoint.split('\n').map(element => element.trim());

			let starterPoint = Number(elements[elements.length - 1]) + 1;

			// Adding row on click to Add New Row button
			$('#addBtn1').click(function () {
				let dynamicRowHTML1 = `
					<tr class="rowClass"> 
					<td class="row-index text-center"> 
						${starterPoint}
					</td>
					<td class="text-center"> 
						<input type='text' name='taskNames[]' class='form-control' title='Task Name'>
					</td> 
					<td class="text-center"> 
						<select name="task_priority[]" id="task_priority" class="form-select">
								<option value="Low" class="text-primary">Low</option>
								<option value="Medium" class="text-warning">Medium</option>
								<option value="High" class="text-danger" selected>High</option>
						</select>
					</td> 
					<td class="text-center"> 
						<input type='date' name='createdDates[]' class='form-control quantity'  title='Created Date'>
					</td> 
					<td class="text-center"> 
						<input type='date' name='dueDates[]' class='form-control' title='Due Date'>
					</td> 
					<td class="text-center"> 
						<input type='text' name='task_descriptions[]' class='form-control' title='Description'>
					</td> 
					<td class="text-center"> 
						<input type='text' name='status' class='form-control' title='Task Status'>
					</td> 
					<td class="text-center"> 
						<button class="btn btn-danger remove" type="button"><i class='bx bx-x'></i></button> 
					</td> 
					</tr>`;
				$('#tbody').append(dynamicRowHTML1);
				starterPoint++;
			});
			// Remove row on click of Remove button
			$('#tbody').on('click', '.remove', function () {
				$(this).closest('tr').remove();
				calculateTotal();
			});
			let count = 1;
			$('#addBtn2').click(function () {
				let dynamicRowHTML2 = `
					<tr class="rowClass"> 
						<td class="row-index text-center"> 
							${count}
						</td>
						<td class="text-center"> 
							<select name="debit_acc_ids[]" class="form-select">
								<?php
								$sql = "SELECT DISTINCT sat.*, at.account_name 
					FROM sub_account_tbl sat 
					INNER JOIN account_tbl at ON sat.account_id = at.account_id";
								$result = $conn->query($sql);
								if ($result->num_rows > 0) {
									while ($row = $result->fetch_assoc()) {
										?>
									<option value="<?= $row['sub_account_id'] ?>">
										<?= $row['account_name'] ?>: <?= $row['sub_account_name'] ?>
									</option>
																					<?php
									}
								}
								?>
								</select>
						</td> 
						<td class="text-center"> 
							<select name="credit_acc_ids[]" class="form-select">
								<?php
								$sql = "SELECT DISTINCT sat.*, at.account_name 
					FROM sub_account_tbl sat 
					INNER JOIN account_tbl at ON sat.account_id = at.account_id";
								$result = $conn->query($sql);
								if ($result->num_rows > 0) {
									while ($row = $result->fetch_assoc()) {
										?>
									<option value="<?= $row['sub_account_id'] ?>">
										<?= $row['account_name'] ?>: <?= $row['sub_account_name'] ?>
									</option>
																																										<?php
									}
								}
								?>
								</select>
											</td>  
	
						<td class="text-center"> 
							<input type="text" name="amounts[]" class="form-control">
						</td> 
						<td class="text-center"> 
							<input type="text" name="descriptions[]" class="form-control" id="description">
						</td>
						<td class="text-center"> 
							<button class="btn btn-danger remove2" type="button"><i class='bx bx-x'></i></button> 
						</td> 
					</tr>`;
				$('#tbody2').append(dynamicRowHTML2);
				count++;
			});
			// Remove row on click of Remove button
			$('#tbody').on('click', '.remove', function () {
				$(this).closest('tr').remove();
				calculateTotal();
			});
			const role = "<?= $_SESSION['role'] ?>";

			// make the pending button visible
			if (status == 'pending' && role == 'User') {
				$('#editButton').hide();
				$('#pendingButton').show();
				$("#cls").hide();
			}

			$('#editForm').off('submit').on('submit', function (event) {
				event.preventDefault(); // Prevent the form from submitting immediately

				// Show the pending button and hide the original button
				if (role == 'User') {
					$('#editButton').hide();
					$('#pendingButton').show();
				}

				// Submit the form using AJAX
				$.ajax({
					type: 'POST',
					url: $(this).attr('action'),
					data: $(this).serialize(),
					success: function (response) {
						console.log(response);
						let data = JSON.parse(response);
						Swal.fire({
							icon: 'success',
							title: data['message']
						});
					},
					error: function () {
						// Handle error
						Swal.fire({
							icon: 'error',
							title: 'An error occurred while processing your request.'
						});
						// Show the original button again if an error occurs
						if (role == 'User') {
							$('#editButton').show();
							$('#pendingButton').hide();
						}
					}
				});
			});
		});
	</script>

	<?php
} else {
	// redirect
}
?>