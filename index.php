<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результат ТЗ Білогуб С.В.</title>

    <script src="https://code.jquery.com/jquery-latest.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="assets/style.css">
  </head>

  <body>

    <div class="container-fluid">
      <div class="menu mb-3">
        <div class="input-group mb-3">
          <button class="btn btn-outline-success" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal" onclick="prepare_new_movie_modal()">Додати фільм</button>

          <input type="text" placeholder="Пошук за назвою" class="form-control" aria-label="Text input with checkbox" oninput="find_by_movie_title(this)">
          <input type="text" placeholder="Пошук за ім'ям актора" class="form-control" aria-label="Text input with checkbox" oninput="find_by_star_name(this)">

          <div class="input-group-text cursor-pointer" id="sorting-checkbox-div">
            В алфавітному порядку:&nbsp;<input disabled class="form-check-input mt-0" type="checkbox" value="" id="sorting-checkbox">
          </div>

          <button class="btn btn-outline-danger" type="button" id="button-addon2" onclick="logout()">Вийти з аккаунту</button>
        </div>
      </div>
      <div id="movies" class="row">

      </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Додати фільм</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <label class="btn btn-primary">
              Завантажити файл <input id="new_movies_from_file" type="file" class="display-none" onchange="create_movies_from_file()">
            </label>
            
            <hr>
            Або заповнити форму:
            <hr>

            <div class="mb-3">
              <label for="new_movie_title" class="form-label">Назва</label>
              <input type="text" class="form-control" id="new_movie_title" placeholder="Назва" oninput="check_new_movie_form()">
            </div>

            <div class="mb-3">
              <label for="new_movie_year" class="form-label">Рік випуску</label>
              <input type="number" class="form-control" id="new_movie_year" placeholder="Рік випуску" oninput="check_new_movie_form()">
            </div>

            <div class="mb-3">
              <label for="exampleFormControlInput1" class="form-label">Формат</label>
              <select id="new_movie_format" class="form-select" onchange="check_new_movie_form()">
                <option value="" selected disabled>Формат</option>
                <option value="VHS">VHS</option>
                <option value="DVD">DVD</option>
                <option value="Blu-Ray">Blu-Ray</option>
              </select>
            </div>

            <div class="mb-3">
              <label for="new_movie_stars" class="form-label">Зірки (через кому)</label>
              <input type="text" class="form-control" id="new_movie_stars" placeholder="Зірки (через кому)" oninput="check_new_movie_form()">
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Вийти</button>
            <button type="button" id="create-new-movie-button" class="btn btn-primary" disabled title="З не до кінця заповненною інформацією відправляти не можна!" onclick="create_new_movie()">Додати</button>
          </div>
        </div>
      </div>
    </div>

    <script type="text/javascript" src="assets/script.js?>"></script>

  </body>

</html>