<?php
session_start();

// Data structure: array
$todoList = isset($_SESSION["todoList"]) ? $_SESSION["todoList"] : [];

// User-defined function: appendData
function appendData($task, $urgency, $todoList) {
    $todoList[] = array("task" => $task, "urgency" => $urgency);
    return $todoList;
}

// User-defined function: deleteData
function deleteData($index, $todoList) {
    if (array_key_exists($index, $todoList)) {
        unset($todoList[$index]);
    }
    return array_values($todoList); // Re-index the array
}

// User-defined function: editData
function editData($index, $task, $urgency, $todoList) {
    if (array_key_exists($index, $todoList)) {
        $todoList[$index] = array("task" => $task, "urgency" => $urgency);
    }
    return $todoList;
}

// Processing form submission to add or edit a task
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["task"]) && isset($_POST["urgency"])) {
        $task = trim($_POST["task"]);
        $urgency = $_POST["urgency"];
        if (empty($task)) {
            echo '<script>alert("Error: there is no data to add in array")</script>';
        } else {
            error_log("Task to add: " . $task . ", Urgency: " . $urgency); // Log the task value for debugging
            if (isset($_POST["index"]) && $_POST["index"] !== '') {
                $index = intval($_POST["index"]);
                $todoList = editData($index, $task, $urgency, $todoList); // Edit the task
            } else {
                $todoList = appendData($task, $urgency, $todoList); // Append the task
            }
            $_SESSION["todoList"] = $todoList; // System-defined function: session management
            error_log("Updated todoList: " . print_r($todoList, true)); // Log the updated list
        }
    }
}

// Processing deletion of a task
if (isset($_GET['delete'])) {
    $indexToDelete = intval($_GET['delete']);
    $todoList = deleteData($indexToDelete, $todoList); // Delete the task
    $_SESSION["todoList"] = $todoList;
    header("Location: " . $_SERVER['PHP_SELF']); // Redirect to avoid resubmission
    exit;
}

// Processing editing of a task
$taskToEdit = '';
$urgencyToEdit = '';
$indexToEdit = '';
if (isset($_GET['edit'])) {
    $indexToEdit = intval($_GET['edit']);
    if (array_key_exists($indexToEdit, $todoList)) {
        $taskToEdit = $todoList[$indexToEdit]['task'];
        $urgencyToEdit = $todoList[$indexToEdit]['urgency'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple To-Do List</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    </head>
    <body>
    <div class="container mt-5">
        <h1 class="text-center">To-Do List</h1>
        <h4>
            <?php
                $currentDateTime = new DateTime('now');
                $currentDate = $currentDateTime->format('l, F j, Y');
                echo $currentDate;
            ?>
        </h4>
        <div class="card">
            <div class="card-header">Add a new task</div>
            <div class="card-body">
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="form-group">
                        <input type="text" class="form-control" name="task" placeholder="Enter your task here" value="<?php echo htmlspecialchars($taskToEdit); ?>">
                        <select class="form-control mt-2" name="urgency">
                            <option value="Low" <?php if ($urgencyToEdit === 'Low') echo 'selected'; ?>>Low</option>
                            <option value="Medium" <?php if ($urgencyToEdit === 'Medium') echo 'selected'; ?>>Medium</option>
                            <option value="High" <?php if ($urgencyToEdit === 'High') echo 'selected'; ?>>High</option>
                        </select>
                        <input type="hidden" name="index" value="<?php echo htmlspecialchars($indexToEdit); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo $taskToEdit ? 'Edit Task' : 'Add Task'; ?></button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">Tasks</div>
            <ul class="list-group list-group-flush">
            <?php
                foreach ($todoList as $index => $taskArray) {
                    $urgencyClass = '';
                    if ($taskArray["urgency"] == 'Low') {
                        $urgencyClass = 'urgency-low';
                    } elseif ($taskArray["urgency"] == 'Medium') {
                        $urgencyClass = 'urgency-medium';
                    } elseif ($taskArray["urgency"] == 'High') {
                        $urgencyClass = 'urgency-high';
                    }
                    echo '<li class="list-group-item d-flex justify-content-between align-items-center">' . 
                        htmlspecialchars($taskArray["task"]) . 
                        '<span class="badge badge-pill ml-2 ' . $urgencyClass . '">' . 
                        htmlspecialchars($taskArray["urgency"]) . 
                        '</span>' . 
                        '<div>' . 
                        '<a href="' . $_SERVER['PHP_SELF'] . '?edit=' . $index . '" class="btn btn-info btn-sm mr-2">Edit</a>' .
                        '<a href="' . $_SERVER['PHP_SELF'] . '?delete=' . $index . '" class="btn btn-danger btn-sm">Delete</a>' .
                        '</div></li>';
                }
            ?>
            </ul>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>