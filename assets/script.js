var sort_type = "";
var title = "";
var star_name = "";

get_movies();

function get_movies(sort_type, title, star_name) {

    $.get("api/index.php?action=get_movies", { "sort_type": sort_type, "title": title, "star_name": star_name }, function (data) {

        var json_data = jQuery.parseJSON(data);

        var movies_cards = "";

        $.each(json_data, function (key, value) {

            var id = value["id"];
            var title = value["title"];
            var release_year = value["release_year"];
            var format = value["format"];
            var stars = value["stars"];

            movies_cards += `<div class="col-md-2 card-main-div">
                            <div class="card custom-card">
                                <button type="button" class="btn-close delete-movie-button" onclick="confirm_delete(`+ id + `, '` + title + `')"></button>
                                <div class="card-body">
                                    <h7><b>ID:</b> `+ id + `</h7><br>
                                    <h7><b>Title:</b> `+ title + `</h7><br>
                                    <h7><b>Release year:</b> `+ release_year + `</h7><br>
                                    <h7><b>Format:</b> `+ format + `</h7><br>
                                    <h7><b>Stars:</b> `+ stars + `</h7>
                                </div>
                            </div>
                        </div>`;
        });

        $("#movies").html(movies_cards);

    });
}

$("#sorting-checkbox-div").click(function () {
    var current_state = $("#sorting-checkbox").prop("checked");

    if (!current_state) {
        sort_type = "alphabetical";
    }
    else {
        sort_type = "";
    }

    get_movies(sort_type, title, star_name);

    $("#sorting-checkbox").prop("checked", !current_state);
});

function find_by_movie_title(inputElement) {
    var inputValue = inputElement.value;

    title = inputValue;

    get_movies(sort_type, title, star_name);
}

function find_by_star_name(inputElement) {
    var inputValue = inputElement.value;

    star_name = inputValue;

    get_movies(sort_type, title, star_name);
}

function logout() {
    $.get("api/index.php?action=logout");
    window.location.href = "index.php";
}

function confirm_delete(id, movie_title) {
    var result = confirm('Ви впененні у видаленні фільму "' + movie_title + '" ?');

    if (result) {
        $.get("api/index.php?action=delete_movie", { "id": id });
        get_movies(sort_type, title, star_name);
    }
}

function check_new_movie_form() {
    if (!$("#new_movie_title").val() || !$("#new_movie_year").val() || !$("#new_movie_format").val() || !$("#new_movie_stars").val()) {
        $("#create-new-movie-button").prop("disabled", true);
    }
    else {
        $("#create-new-movie-button").prop("disabled", false);
    }
}

function prepare_new_movie_modal() {
    $("#new_movie_title").val("");
    $("#new_movie_year").val("");
    $("#new_movie_format").val("");
    $("#new_movie_stars").val("");

    $("#create-new-movie-button").prop("disabled", true);
}

function create_new_movie() {
    var new_movie_title = $("#new_movie_title").val();
    var new_movie_release_year = $("#new_movie_year").val();
    var new_movie_format = $("#new_movie_format").val();
    var new_movie_stars = $("#new_movie_stars").val();

    $.post("api/create_movie", { "title": new_movie_title, "release_year": new_movie_release_year, "format": new_movie_format, "stars": new_movie_stars }, function () {
        alert("Фільм успішно додано!");
        get_movies(sort_type, title, star_name);
        $('#exampleModal').modal('hide');
    });
}

function create_movies_from_file() {
    var formData = new FormData();
    formData.append('file', $('#new_movies_from_file')[0].files[0]);

    $.ajax({
        url: 'api/index.php?action=upload_movies_list',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            alert("Фільми успішно додано!");
            get_movies(sort_type, title, star_name);
            $('#exampleModal').modal('hide');
        },
        error: function (error) {
            alert("При завантаженні виникла помилка! " + error);
            get_movies(sort_type, title, star_name);
            $('#exampleModal').modal('hide');
        }
    });
}