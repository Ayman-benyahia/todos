<?php

    class Tasks {

        public $conn = null;

        function __construct() {
            $servername = "localhost";
            $username = "ayman benyahia";
            $dbname = "todos";
            $password = "QQAAAPUtVf94v2K2";

            try {
                $this->conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                // set the PDO error mode to exception
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
            } 
            catch(PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            }
        }

        function create($message, $completed) { 
            $query = "INSERT INTO tasks (message, completed) VALUES(:message, :completed)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":message", $message);
            $stmt->bindParam(":completed", $completed);
            $stmt->execute();
        }

        function read() {
            $query = "SELECT * FROM tasks";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        function delete($id) {  
            $query = "DELETE FROM tasks WHERE id=:id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam("id", $id);
            $stmt->execute();
        }

        function deleteCompleted() {  
            $query = "DELETE FROM tasks WHERE completed=1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
        }

        function complete($id, $completed) {  
            $query = "UPDATE tasks SET completed=:completed WHERE id=:id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":completed", $completed);
            $stmt->execute();
        }

        function readCompleted() {
            $query = "SELECT * FROM tasks WHERE completed=1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        function readActives() {
            $query = "SELECT * FROM tasks WHERE completed=0";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    $model = new Tasks();
    $tasks = [];
    $tasksCount = 0;

    // Create new task
    if(isset($_POST["add"])) {
        $model->create($_POST["task"], 0);
    }

    // Delete task 
    if(isset($_GET["delete"])) {
        $model->delete($_GET["id"]);
    }

    // Mark task
    if(isset($_GET["complete"])) {
        if($_GET["complete"] === "on") $_GET["complete"] = 1;
        $model->complete($_GET["id"], $_GET["complete"]);
    }

    // Read completed tasks
    if(isset($_GET["completed"])) {
        $tasks = $model->readCompleted();
        $tasksCount = count($tasks);
    }

    // Read active tasks
    if(isset($_GET["active"])) {
        $tasks = $model->readActives();
        $tasksCount = count($tasks);
    }

    // Clear 
    if(isset($_GET["clear"])) {
        $model->deleteCompleted();
    }

    // Read all tasks
    if($tasksCount <= 0) {
        $tasks = $model->read();
        $tasksCount = count($tasks);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="http://localhost:8080/todos/">
    <link rel="stylesheet" href="style.css">
    <title>Todo list</title>
</head>
<body>
    <section class="todo">
        <header class="todo__head">
            <h1>Todos</h1>
            <form class="input-bar" action="" method="POST">
                <input class="control control--large" type="text" name="task" placeholder="Write a task...">
                <input class="button button--large" name="add" type="submit" value="+ Add">
            </form>
        </header>

        <main class="todo__body">
            <?php foreach($tasks as $task) { ?>
                <form class="task" method="GET">
                    <input class="task__mark" type="checkbox" name="complete" <?=$task["completed"] == 0 ? "": "checked"; ?>>
                    <h5 class="task__message" style="text-decoration:<?=$task["completed"] === "0" ? ";": "line-through;"; ?>"><?=$task["message"]?></h5>
                    <input type="hidden" name="id" value="<?=$task["id"]?>">
                    <input class="button button--small" name="delete" type="submit" value="x">
                </form>
            <?php } ?>

            <form class="todo__bottom">
                <p><?=$tasksCount?> task found.</p>
                <div class="todo__filter">
                    <a href="index.php" class="button" style="padding: 1rem;">All</a>
                    <input class="button" type="submit" name="active" value="Active">
                    <input class="button" type="submit" name="completed" value="Completed">
                </div>
                <input class="button" type="submit" 
                    name="clear" value="Clear completed">
            </form>
        </main>

        <footer class="todo__footer">
            <p>Lorem ipsum, dolor sit amet consectetur 
            adipisicing elit. Et, consequatur.</p>
        </footer>
    </section>

    <script src="main.js"></script>
</body>
</html>