<?php

session_start();

require "validation.php";

header("X-FRAME-OPTIONS:DENY");


if (!empty($_POST)) {
  echo "<pre>";
  var_dump($_POST);
  echo "</pre>";
}

function h($str)
{
  return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
}

//入力、確認、完了画面を分ける場合 input.php, confirm.php, thanks.php
// CSRF 偽物のinput.php->悪因のあるページ飛ばしてしまう
//一つの画面で行うとき input.php

$pageFlag = 0;
$errors = validation($_POST);

if (!empty($_POST["btn_confirm"]) && empty($errors)) {
  $pageFlag = 1;
}

if (!empty($_POST["btn_submit"])) {
  $pageFlag = 2;
}

?>

<!doctype html>
<html lang="ja">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"
    integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">

  <title>Form</title>
</head>

<body>

  <?php if ($pageFlag === 0): ?>
    <?php
    if (!isset($_SESSION["csrfToken"])) {
      $csrfToken = bin2hex(random_bytes(32));
      $_SESSION["csrfToken"] = $csrfToken;
    }
    $token = $_SESSION["csrfToken"];
    ?>

    <?php if (!empty($errors) && !empty($_POST["btn_confirm"])): ?>
      <?php echo "<ul>"; ?>
      <?php
      foreach ($errors as $error) {
        echo "<li>" . $error . "</li>";
      }
      ?>
      <?php echo "</ul>"; ?>
    <?php endif; ?>

    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <form method="POST" action="input.php">
            入力画面
            <div class="form-group">
              <label for="your_name">氏名</label>
              <input type="text" class="form-control" id="your_name" name="your_name" value="<?php if (!empty($_POST["your_name"])) {
                echo h($_POST["your_name"]);
              } ?>" required>
            </div>

            <div class="form-group">
              <label for="email">メールアドレス</label>
              <input type="email" class="form-control" id="email" name="email" value="<?php if (!empty($_POST["email"])) {
                echo h($_POST["email"]);
              } ?>" required>
            </div>

            <div class="form-group">
              <label for="url"> ホームページ</label>
              <input type="url" class="form-control" id="url" name="url" value="<?php if (!empty($_POST["url"])) {
                echo h($_POST["url"]);
              } ?>">
            </div>

            性別
            <div class="form-check form-inline">
              <input type="radio" class="form-check-input" id="gender1" name="gender" value="0" <?php if (isset($_POST["gender"]) && $_POST["gender"] === "0") {
                echo "checked";
              } ?>>
              <label for="gender1" class="form-check-label">男性</label>
              <input type="radio" class="form-check-input" id="gender2" name="gender" value="1" <?php if (isset($_POST["gender"]) && $_POST["gender"] === "1") {
                echo "checked";
              } ?>>
              <label for="gender2" class="form-check-label">女性</label>
            </div>

            <div class="form-group">
              <label for="age">年齢</label>
              <select class="form-control" id="age" name="age">
                <option value="">選択してください</option>
                <option value="1" <?php if (isset($_POST["age"]) && $_POST["age"] === "1") {
                  echo "selected";
                } ?>>～19歳
                </option>
                </option>
                <option value="2" <?php if (isset($_POST["age"]) && $_POST["age"] === "2") {
                  echo "selected";
                } ?>>20～29歳
                </option>
                <option value="3" <?php if (isset($_POST["age"]) && $_POST["age"] === "3") {
                  echo "selected";
                } ?>>30～39歳
                </option>
                <option value="4" <?php if (isset($_POST["age"]) && $_POST["age"] === "4") {
                  echo "selected";
                } ?>>40～49歳
                </option>
                <option value="5" <?php if (isset($_POST["age"]) && $_POST["age"] === "5") {
                  echo "selected";
                } ?>>50歳～
                </option>
              </select>
            </div>

            <div class="form-group">
              <label for="contact">お問い合わせ内容</label>
              <textarea class="form-control" id="contact" row="3" name="contact">
                <?php if (!empty($_POST["contact"])) {
                  echo h($_POST["contact"]);
                } ?>
              </textarea>
            </div>

            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="caution" name="caution" value="1">
              <label for="caution" class="form-check-label">注意事項にチェック</label>
            </div>

            <input class="btn btn-info" type="submit" name="btn_confirm" value="確認する">
            <input type="hidden" name="csrf" value="<?php echo $token; ?>">
          </form>

        </div><!-- .col-md-6 -->
      </div>
    </div>
  <?php endif; ?>

  <?php if ($pageFlag === 1): ?>
    <?php if ($_POST["csrf"] === $_SESSION["csrfToken"]): ?>
      確認画面
      <div class="col-md-6 offset-md-3">
        <form method="POST" action="input.php">
          <div class="form-group">
            <label for="name">氏名</label>
            <input type="text" class="form-control" id="name" name="your_name" value="<?php echo h($_POST["your_name"]); ?>"
              readonly>
          </div>

          <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo h($_POST["email"]); ?>" readonly>
          </div>

          <div class="form-group">
            <label for="url">ホームページ</label>
            <input type="url" class="form-control" id="url" name="url" value="<?php echo h($_POST["url"]); ?>" readonly>
          </div>

          <div class="form-group">
            <label for="gender">性別</label>
            <input type="text" class="form-control" id="gender" name="gender" value="<?php
            if ($_POST["gender"] === "0") {
              echo "男性";
            }
            if ($_POST["gender"] === "1") {
              echo "女性";
            }
            ?>" readonly>
          </div>

          <div class="form-group">
            <label for="age">年齢</label>
            <input type="text" class="form-control" id="age" name="age" value="<?php
            if ($_POST["age"] === "1") {
              echo "～19歳";
            }
            if ($_POST["age"] === "2") {
              echo "20～29歳";
            }
            if ($_POST["age"] === "3") {
              echo "30～39歳";
            }
            if ($_POST["age"] === "4") {
              echo "40～49歳";
            }
            if ($_POST["age"] === "5") {
              echo "50歳～";
            }
            ?>" readonly>
          </div>

          <div class="form-group">
            <label for="contact">お問い合わせ内容</label>
            <input type="text" class="form-control" id="contact" name="contact" value="<?php echo h($_POST["contact"]); ?>" readonly>
          </div>

          <input type="submit" class="btn btn-primary" name="back" value="戻る">
          <input type="submit" class="btn btn-success" name="btn_submit" value="送信する">
          <input type="hidden" name="your_name" value="<?php echo h($_POST["your_name"]); ?>">
          <input type="hidden" name="email" value="<?php echo h($_POST["email"]); ?>">
          <input type="hidden" name="url" value="<?php echo h($_POST["url"]); ?>">
          <input type="hidden" name="gender" value="<?php echo h($_POST["gender"]); ?>">
          <input type="hidden" name="age" value="<?php echo h($_POST["age"]); ?>">
          <input type="hidden" name="contact" value="<?php echo h($_POST["contact"]); ?>">

          <input type="hidden" name="csrf" value="<?php echo h($_POST["csrf"]); ?>">
        </form>
      </div><!-- .col-md-6 offset-md-3 -->
    <?php endif; ?>

  <?php endif; ?>

  <?php if ($pageFlag === 2): ?>
    <?php if ($_POST["csrf"] === $_SESSION["csrfToken"]): ?>
      完了画面
      <br>

      <?php require '../mainte/insert.php';

      insertContact($_POST);
      ?>
  
      送信が完了しました

      <?php unset($_SESSION["csrfToken"]); ?>

    <?php endif; ?>

  <?php endif; ?>

  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
    integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
    crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"
    integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut"
    crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"
    integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k"
    crossorigin="anonymous"></script>
</body>

</html>