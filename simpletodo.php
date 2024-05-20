<?php
    session_start();

    $todoList = array();

    if (isset($_SESSION["todoList"])) $todoList = $_SESSION["todoList"];

    function appendData($data) {
        $todoList = $data;
        return $todoList;
    }

    function deleteData($toDelete, $todoList) {
        foreach ($todoList as $index => $taskName) {
            if ($taskName === $toDelete) {
                unset( $todoList[$index] );
            }

        }

        return $todoList;
    }

    if($_SERVER["REQUEST_METHOD"] =="POST") {
        if (empty( $_POST["task"] )){
            echo '<script>alert("Error: there is no data to add in array")</script>';
            exit;
        }

        array_push($todoList, appendData($_POST["task"]));
        $_SESSION["todoList"] = $todoList;
    }

    if (isset($_GET['task'])) {
        $_SESSION["todoList"] = deleteData($_GET['task'], $todoList);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>To-Do List</title>
</head>
<body>
    
    <div class="todo-container">
        <h1>To-Do List</h1>
        <form method="post">
            <div class="todo-item">
                <input type="text" name="task" id="todo1" placeholder="Enter to-do">
                <button type="submit">Add Task</button>
            </div>
        </form>
    </div>
 
  <div class="task-list">
    <h2>Tasks:</h2>
    <ul id="taskList">
        <?php
        foreach($todoList as $index => $task ){
            echo '<li class="task-list-item">' . $task . '<a class="delete-btn" href="?delete=' . $index . '">Delete</a></li>';
        }
        ?>
    </ul>
  </div>
</body>
</html>
