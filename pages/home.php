<?php
    session_start();

    // !isset() = is not set
    // if $_SESSION['signup_form_csrf_token'] is not set, generate a new token
    // when token is already available, we won't regenerate it again

    if ( !isset( $_SESSION['add_form_csrf_token'])) {
      // generate csrf token
      $_SESSION['add_form_csrf_token'] = bin2hex( random_bytes(32) );
  }

    $database = new PDO('mysql:host=devkinsta_db;dbname=randomtesting', 'root', 'viLXjKTLEziaMJLz'); //Your database password
    $query = $database->prepare('SELECT * FROM students');
    $query->execute();
    $students = $query->fetchAll();
    if (
        $_SERVER['REQUEST_METHOD'] === 'POST'
    ) {
        var_dump($_POST['action']);
        if($_POST['action'] === 'add') {
          if ( $_POST['add_form_csrf_token'] !== $_SESSION['add_form_csrf_token'] )
        {
            die("NOT TODAY KID!!!");
        }

        unset( $_SESSION['login_form_csrf_token'] );

            //add new student
            $statement = $database->prepare(
                'INSERT INTO students (`name`)
                values (:name)'
            );
            $statement->execute([
                'name' => $_POST['student']
            ]);
            header('Location: /');
            exit;
        }
        if($_POST['action'] === 'delete') {
            // delete student
            $statement = $database->prepare('DELETE FROM students WHERE id = :id');
            $statement->execute ([
                'id' => $_POST['student_id']
            ]);
        }
    }
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Simple Auth</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65"
      crossorigin="anonymous"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css"
    />
    <style type="text/css">
      body {
        background: #F1F1F1;
      }
    </style>
  </head>
  <body>
    <div class="card rounded shadow-sm mx-auto my-4" style="max-width: 500px;">
      <div class="card-body">
        <div class="d-flex justify-content-between">
            <h1>My Classroom</h1>
            <div class="d-flex justify-content-center">
                <?php if ( isset( $_SESSION['user'])) : ?>
                <a href="/logout" class="btn btn-link" id="logout">Logout</a>
                <?php else : ?>
                <a href="/login" class="btn btn-link" id="login">Login</a>
                <a href="/signup" class="btn btn-link" id="signup">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
        <?php if ( isset( $_SESSION['user'])) : ?>
            <form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="d-flex justify-content-between">
          <input
            type="text"
            class="form-control"
            placeholder="Add new student..."
            name="student"
            required
          />
          <input
            type="hidden"
            name="action"
            value="add"
            />
          <button class="btn btn-primary btn-sm rounded ms-2">Add</button>
          <input 
                type="hidden"
                name="add_form_csrf_token"
                value="<?php echo $_SESSION['add_form_csrf_token']; ?>"
                />
    </form>
        <?php endif; ?>
      </div>
    </div>
    <div class="card rounded shadow-sm mx-auto my-4" style="max-width: 500px;">
      <div class="card-body">
        <h3 class="card-title mb-3">Students</h3>
        <div class="mt-4">
            <?php foreach ( $students as $key => $student ) : ?>
                <div class="mb-2 d-flex justify-content-between gap-3">
                    <?php echo $key+1 .'.'; ?>
                    <?php echo $student['name']; ?>
                    <form method="POST"
                    action="<?php echo $_SERVER ['REQUEST_URI'];?>">
                    <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                    <?php if(isset($_SESSION['user'])) : ?>
                    <input type="hidden" name="action" value="delete">
                    <button class="btn btn-danger btn-sm">Remove</button>
                    <?php endif; ?>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
      </div>
    </div>
`
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
      crossorigin="anonymous"
    ></script>
  </body>










